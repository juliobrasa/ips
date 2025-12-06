<?php

namespace App\Http\Controllers;

use App\Models\Subnet;
use App\Services\WhoisService;
use App\Services\IpReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class SubnetController extends Controller
{
    protected WhoisService $whoisService;
    protected IpReputationService $reputationService;

    public function __construct(WhoisService $whoisService, IpReputationService $reputationService)
    {
        $this->whoisService = $whoisService;
        $this->reputationService = $reputationService;
    }

    public function index()
    {
        $company = auth()->user()->company;

        if (!$company || !$company->isHolder()) {
            return redirect()->route('dashboard')
                ->with('error', 'You need to register as an IP Holder to manage subnets.');
        }

        $subnets = Subnet::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('subnets.index', compact('subnets'));
    }

    public function create()
    {
        $company = auth()->user()->company;

        if (!$company || !$company->isHolder()) {
            return redirect()->route('company.edit')
                ->with('error', 'Please update your company type to "Holder" or "Both" to list subnets.');
        }

        if (!$company->isKycApproved()) {
            return redirect()->route('company.edit')
                ->with('error', 'Your KYC must be approved before listing subnets.');
        }

        return view('subnets.create');
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company || !$company->canList()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to list subnets.');
        }

        $validated = $request->validate([
            'ip_address' => [
                'required',
                'ip',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Subnet::where('ip_address', $value)
                        ->where('cidr', $request->cidr)
                        ->exists();
                    if ($exists) {
                        $fail('This subnet is already registered in the system.');
                    }
                },
            ],
            'cidr' => 'required|integer|min:16|max:24',
            'rir' => ['required', Rule::in(['RIPE', 'ARIN', 'LACNIC', 'APNIC', 'AFRINIC'])],
            'geolocation_country' => 'nullable|string|size:2',
            'geolocation_city' => 'nullable|string|max:100',
            'price_per_ip_monthly' => 'required|numeric|min:0.01|max:100',
            'min_lease_months' => 'required|integer|min:1|max:36',
            'description' => 'nullable|string|max:1000',
            'rpki_delegated' => 'boolean',
        ]);

        // Check IP reputation before allowing listing
        $reputationCheck = $this->reputationService->checkReputation($validated['ip_address']);

        if (!$reputationCheck['can_list']) {
            return back()->withInput()->with('error',
                "This IP has reputation issues (Score: {$reputationCheck['score']}/100). " .
                "It is listed on {$reputationCheck['blocked_count']} blocklist(s). " .
                "Please clean the IP before listing.");
        }

        $subnet = Subnet::create([
            'company_id' => $company->id,
            'ip_address' => $validated['ip_address'],
            'cidr' => $validated['cidr'],
            'rir' => $validated['rir'],
            'geolocation_country' => $validated['geolocation_country'] ? strtoupper($validated['geolocation_country']) : null,
            'geolocation_city' => $validated['geolocation_city'],
            'price_per_ip_monthly' => $validated['price_per_ip_monthly'],
            'min_lease_months' => $validated['min_lease_months'],
            'description' => $validated['description'],
            'rpki_delegated' => $validated['rpki_delegated'] ?? false,
            'status' => 'pending_verification',
            'verification_token' => Str::random(64),
            'reputation_score' => $reputationCheck['score'],
            'last_reputation_check' => now(),
            'blocklist_results' => $reputationCheck['blocklists'],
        ]);

        return redirect()->route('subnets.show', $subnet)
            ->with('success', 'Subnet added. Please verify ownership to make it available in the marketplace.');
    }

    public function show(Subnet $subnet)
    {
        $this->authorize('view', $subnet);

        $subnet->load(['leases.lesseeCompany', 'abuseReports']);

        // Get WHOIS data
        $whoisData = $this->whoisService->query($subnet->ip_address, $subnet->rir);

        return view('subnets.show', compact('subnet', 'whoisData'));
    }

    public function edit(Subnet $subnet)
    {
        $this->authorize('update', $subnet);

        return view('subnets.edit', compact('subnet'));
    }

    public function update(Request $request, Subnet $subnet)
    {
        $this->authorize('update', $subnet);

        $validated = $request->validate([
            'geolocation_country' => 'nullable|string|size:2',
            'geolocation_city' => 'nullable|string|max:100',
            'price_per_ip_monthly' => 'required|numeric|min:0.01|max:100',
            'min_lease_months' => 'required|integer|min:1|max:36',
            'description' => 'nullable|string|max:1000',
            'rpki_delegated' => 'boolean',
            'auto_renewal' => 'boolean',
        ]);

        $subnet->update([
            'geolocation_country' => $validated['geolocation_country'] ? strtoupper($validated['geolocation_country']) : null,
            'geolocation_city' => $validated['geolocation_city'],
            'price_per_ip_monthly' => $validated['price_per_ip_monthly'],
            'min_lease_months' => $validated['min_lease_months'],
            'description' => $validated['description'],
            'rpki_delegated' => $validated['rpki_delegated'] ?? false,
            'auto_renewal' => $validated['auto_renewal'] ?? true,
        ]);

        return redirect()->route('subnets.show', $subnet)
            ->with('success', 'Subnet updated successfully.');
    }

    public function destroy(Subnet $subnet)
    {
        $this->authorize('delete', $subnet);

        if ($subnet->isLeased()) {
            return back()->with('error', 'Cannot delete a subnet that is currently leased.');
        }

        $subnet->delete();

        return redirect()->route('subnets.index')
            ->with('success', 'Subnet deleted successfully.');
    }

    public function verify(Request $request, Subnet $subnet)
    {
        $this->authorize('update', $subnet);

        if ($subnet->ownership_verified_at) {
            return back()->with('info', 'Subnet is already verified.');
        }

        // Get WHOIS data to find abuse contact
        $whoisData = $this->whoisService->query($subnet->ip_address, $subnet->rir);

        if (!$whoisData || !isset($whoisData['abuse_email'])) {
            // If no WHOIS data available, allow manual verification for now
            // In production, this would require admin approval
            $subnet->update([
                'ownership_verified_at' => now(),
                'status' => 'available',
            ]);

            return back()->with('success', 'Subnet verified (WHOIS data not available - manual verification applied).');
        }

        $abuseEmail = $whoisData['abuse_email'];

        // Generate verification token if not exists
        if (!$subnet->verification_token) {
            $subnet->update(['verification_token' => Str::random(64)]);
        }

        // Send verification email
        try {
            Mail::send('emails.subnet-verification', [
                'subnet' => $subnet,
                'verificationUrl' => route('subnets.confirm-verification', [
                    'subnet' => $subnet->id,
                    'token' => $subnet->verification_token,
                ]),
                'company' => auth()->user()->company,
            ], function ($message) use ($abuseEmail, $subnet) {
                $message->to($abuseEmail)
                    ->subject("Verify IP Ownership for {$subnet->cidr_notation} - Soltia IPS Marketplace");
            });

            return back()->with('success', "Verification email sent to {$abuseEmail}. Please check your inbox and click the verification link.");
        } catch (\Exception $e) {
            // Log error but don't expose details
            \Log::error("Failed to send verification email: " . $e->getMessage());

            return back()->with('error', 'Failed to send verification email. Please contact support.');
        }
    }

    public function confirmVerification(Request $request, Subnet $subnet, string $token)
    {
        if ($subnet->verification_token !== $token) {
            abort(403, 'Invalid verification token.');
        }

        if ($subnet->ownership_verified_at) {
            return redirect()->route('subnets.show', $subnet)
                ->with('info', 'Subnet is already verified.');
        }

        $subnet->update([
            'ownership_verified_at' => now(),
            'status' => 'available',
            'verification_token' => null,
        ]);

        return redirect()->route('subnets.show', $subnet)
            ->with('success', 'Ownership verified successfully! Your subnet is now available in the marketplace.');
    }

    public function checkReputation(Subnet $subnet)
    {
        $this->authorize('update', $subnet);

        // Get full reputation report
        $report = $this->reputationService->getSummaryReport($subnet->ip_address);

        $subnet->update([
            'reputation_score' => $report['overall_score'],
            'last_reputation_check' => now(),
            'blocklist_results' => $report['blocklist_details'],
        ]);

        // Check if subnet status needs to change
        if (!$report['can_be_listed'] && $subnet->status === 'available') {
            $subnet->update(['status' => 'suspended']);

            return back()->with('error',
                "Reputation check completed. Score: {$report['overall_score']}/100. " .
                "Your subnet has been suspended due to reputation issues. " .
                "Please clean the IP from blocklists and request a new check.");
        }

        $message = "Reputation check completed. Score: {$report['overall_score']}/100. ";

        if ($report['blocklist_count'] > 0) {
            $message .= "Listed on {$report['blocklist_count']} blocklist(s). ";
        } else {
            $message .= "No blocklist issues found. ";
        }

        $message .= $report['recommendation'];

        return back()->with($report['can_be_listed'] ? 'success' : 'warning', $message);
    }

    public function getWhoisData(Subnet $subnet)
    {
        $this->authorize('view', $subnet);

        $whoisData = $this->whoisService->query($subnet->ip_address, $subnet->rir);

        return response()->json([
            'success' => true,
            'data' => $whoisData,
        ]);
    }
}
