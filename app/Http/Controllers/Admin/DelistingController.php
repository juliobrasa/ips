<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RequestBlacklistDelisting;
use App\Models\BlacklistDelistingRequest;
use App\Models\Subnet;
use App\Services\DelistingService;
use App\Services\IpReputationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DelistingController extends Controller
{
    public function __construct(
        protected DelistingService $delistingService,
        protected IpReputationService $reputationService
    ) {}

    public function index(): View
    {
        $requests = BlacklistDelistingRequest::with(['subnet.company', 'requestedByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => BlacklistDelistingRequest::where('status', 'pending')->count(),
            'in_progress' => BlacklistDelistingRequest::where('status', 'in_progress')->count(),
            'delisted' => BlacklistDelistingRequest::where('status', 'delisted')->count(),
            'failed' => BlacklistDelistingRequest::where('status', 'failed')->count(),
            'manual_required' => BlacklistDelistingRequest::where('status', 'manual_required')->count(),
        ];

        // Get available blocklists for reference
        $blocklists = $this->delistingService->getAllBlocklists();

        return view('admin.delisting.index', compact('requests', 'stats', 'blocklists'));
    }

    public function pending(): View
    {
        $requests = BlacklistDelistingRequest::with(['subnet.company', 'requestedByUser'])
            ->whereIn('status', ['pending', 'in_progress', 'manual_required'])
            ->orderBy('requested_at', 'asc')
            ->paginate(20);

        return view('admin.delisting.pending', compact('requests'));
    }

    public function show(BlacklistDelistingRequest $request): View
    {
        $request->load(['subnet.company', 'requestedByUser']);

        // Get current listing status
        $isStillListed = $this->delistingService->checkIfStillListed(
            $request->subnet->ip_address,
            $request->blocklist
        );

        // Get blocklist details
        $blocklistInfo = $this->delistingService->getBlocklistInfo($request->blocklist);

        return view('admin.delisting.show', compact('request', 'isStillListed', 'blocklistInfo'));
    }

    public function checkStatus(BlacklistDelistingRequest $request): RedirectResponse
    {
        $isStillListed = $this->delistingService->checkIfStillListed(
            $request->subnet->ip_address,
            $request->blocklist
        );

        $request->update(['last_checked_at' => now()]);

        if (!$isStillListed) {
            $request->markAsDelisted();

            // Update subnet reputation
            $this->refreshSubnetReputation($request->subnet);

            return back()->with('success', __('IP is no longer listed on :blocklist! Request marked as completed.', [
                'blocklist' => $request->blocklist,
            ]));
        }

        return back()->with('info', __('IP is still listed on :blocklist.', [
            'blocklist' => $request->blocklist,
        ]));
    }

    public function markCompleted(Request $request, BlacklistDelistingRequest $delistingRequest): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $delistingRequest->update([
            'status' => 'delisted',
            'delisted_at' => now(),
            'response_message' => $request->notes,
        ]);

        // Refresh subnet reputation
        $this->refreshSubnetReputation($delistingRequest->subnet);

        return back()->with('success', __('Delisting request marked as completed.'));
    }

    public function createRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subnet_id' => 'required|exists:subnets,id',
            'blocklist' => 'required|string',
            'contact_email' => 'nullable|email',
            'reason' => 'nullable|string|max:1000',
        ]);

        $subnet = Subnet::findOrFail($validated['subnet_id']);

        // Check for existing recent request
        $existingRequest = BlacklistDelistingRequest::where('subnet_id', $subnet->id)
            ->where('blocklist', $validated['blocklist'])
            ->where('requested_at', '>', now()->subHours(24))
            ->first();

        if ($existingRequest) {
            return back()->with('warning', __('A delisting request was already submitted in the last 24 hours.'));
        }

        // Create the request
        $delistingRequest = BlacklistDelistingRequest::create([
            'subnet_id' => $subnet->id,
            'blocklist' => $validated['blocklist'],
            'status' => 'pending',
            'contact_email' => $validated['contact_email'],
            'reason' => $validated['reason'],
            'requested_by' => auth()->id(),
            'requested_at' => now(),
        ]);

        // Dispatch job
        RequestBlacklistDelisting::dispatch(
            $subnet,
            $validated['blocklist'],
            auth()->id()
        )->onQueue('delisting');

        return back()->with('success', __('Delisting request created and processing started.'));
    }

    protected function refreshSubnetReputation(Subnet $subnet): void
    {
        $report = $this->reputationService->getSummaryReport($subnet->ip_address);

        $subnet->update([
            'reputation_score' => $report['overall_score'],
            'last_reputation_check' => now(),
            'blocklist_results' => $report['blocklist_details'],
        ]);

        // Update status if reputation improved
        if ($report['can_be_listed'] && $subnet->status === 'suspended') {
            $subnet->update(['status' => 'available']);
        }
    }
}
