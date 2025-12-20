<?php

namespace App\Http\Controllers;

use App\Models\RipeCredential;
use App\Models\Subnet;
use App\Services\RipeDatabaseService;
use App\Services\RipeStatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RipeController extends Controller
{
    public function __construct(
        protected RipeDatabaseService $ripeDatabase,
        protected RipeStatService $ripeStat
    ) {}

    /**
     * Show RIPE management dashboard
     */
    public function index(): View
    {
        $user = auth()->user();
        $company = $user->company;

        $credentials = $company
            ? RipeCredential::where('company_id', $company->id)->get()
            : collect();

        $subnets = $company
            ? Subnet::where('company_id', $company->id)
                ->whereNotNull('ripe_inetnum_key')
                ->get()
            : collect();

        return view('ripe.index', [
            'credentials' => $credentials,
            'subnets' => $subnets,
            'company' => $company,
        ]);
    }

    /**
     * Show credentials management page
     */
    public function credentials(): View
    {
        $user = auth()->user();
        $company = $user->company;

        $credentials = $company
            ? RipeCredential::where('company_id', $company->id)->get()
            : collect();

        return view('ripe.credentials', [
            'credentials' => $credentials,
        ]);
    }

    /**
     * Store new RIPE credential
     */
    public function storeCredential(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'api_key' => 'required|string',
            'maintainer' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            return back()->with('error', __('You must have a company to add RIPE credentials.'));
        }

        // Validate the API key
        if (!$this->ripeDatabase->validateApiKey($validated['api_key'])) {
            return back()->with('error', __('Invalid RIPE API key. Please check and try again.'));
        }

        $credential = RipeCredential::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'api_key' => $validated['api_key'],
            'maintainer' => $validated['maintainer'],
            'expires_at' => $validated['expires_at'] ?? null,
            'validated_at' => now(),
        ]);

        return redirect()->route('ripe.credentials')
            ->with('success', __('RIPE credential added successfully.'));
    }

    /**
     * Delete a credential
     */
    public function destroyCredential(RipeCredential $credential): RedirectResponse
    {
        $user = auth()->user();

        if ($credential->company_id !== $user->company?->id) {
            abort(403);
        }

        $credential->delete();

        return redirect()->route('ripe.credentials')
            ->with('success', __('RIPE credential deleted.'));
    }

    /**
     * Show subnet RIPE information
     */
    public function subnetInfo(Subnet $subnet): View
    {
        $user = auth()->user();

        if ($subnet->company_id !== $user->company?->id) {
            abort(403);
        }

        $ripeInfo = null;
        $statInfo = null;

        // Get RIPE Database info
        if ($subnet->ripe_inetnum_key) {
            try {
                $ripeInfo = $this->ripeDatabase->getInetnum($subnet->ripe_inetnum_key);
            } catch (\Exception $e) {
                $ripeInfo = ['error' => $e->getMessage()];
            }
        }

        // Get RIPEstat info
        try {
            $statInfo = $this->ripeStat->getComprehensiveInfo($subnet->ip_address);
        } catch (\Exception $e) {
            $statInfo = ['error' => $e->getMessage()];
        }

        return view('ripe.subnet-info', [
            'subnet' => $subnet,
            'ripeInfo' => $ripeInfo,
            'statInfo' => $statInfo,
        ]);
    }

    /**
     * Show subnet edit form for RIPE data
     */
    public function editSubnet(Subnet $subnet): View
    {
        $user = auth()->user();

        if ($subnet->company_id !== $user->company?->id) {
            abort(403);
        }

        $credentials = RipeCredential::where('company_id', $user->company->id)
            ->active()
            ->get();

        $statuses = config('ripe.inetnum_statuses', []);

        return view('ripe.subnet-edit', [
            'subnet' => $subnet,
            'credentials' => $credentials,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update subnet RIPE data
     */
    public function updateSubnet(Request $request, Subnet $subnet): RedirectResponse
    {
        $user = auth()->user();

        if ($subnet->company_id !== $user->company?->id) {
            abort(403);
        }

        $validated = $request->validate([
            'credential_id' => 'required|exists:ripe_credentials,id',
            'netname' => 'required|string|max:80|regex:/^[A-Za-z][A-Za-z0-9_-]*$/',
            'descr' => 'nullable|string|max:255',
            'country' => 'required|string|size:2',
            'admin_c' => 'required|string|max:30',
            'tech_c' => 'required|string|max:30',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'geoloc' => 'nullable|string|regex:/^-?\d+\.?\d*\s+-?\d+\.?\d*$/',
        ]);

        $credential = RipeCredential::findOrFail($validated['credential_id']);

        if ($credential->company_id !== $user->company->id) {
            abort(403);
        }

        $inetnumKey = $subnet->ripe_inetnum_key ?? $this->formatInetnumKey($subnet);

        try {
            $result = $this->ripeDatabase->upsertInetnum([
                'inetnum' => $inetnumKey,
                'netname' => $validated['netname'],
                'descr' => $validated['descr'],
                'country' => strtoupper($validated['country']),
                'admin_c' => $validated['admin_c'],
                'tech_c' => $validated['tech_c'],
                'status' => $validated['status'],
                'mnt_by' => $credential->maintainer,
                'remarks' => $validated['remarks'],
                'geoloc' => $validated['geoloc'],
            ], $credential->getDecryptedApiKey());

            // Update local record
            $subnet->update([
                'ripe_inetnum_key' => $inetnumKey,
                'ripe_netname' => $validated['netname'],
                'ripe_maintainer' => $credential->maintainer,
                'ripe_status' => $validated['status'],
                'ripe_last_synced_at' => now(),
            ]);

            return redirect()->route('ripe.subnet.info', $subnet)
                ->with('success', __('RIPE inetnum updated successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Failed to update RIPE: ') . $e->getMessage());
        }
    }

    /**
     * Show route objects management
     */
    public function routeObjects(Subnet $subnet): View
    {
        $user = auth()->user();

        if ($subnet->company_id !== $user->company?->id) {
            abort(403);
        }

        $routes = [];
        $credentials = RipeCredential::where('company_id', $user->company->id)
            ->active()
            ->get();

        try {
            $prefix = $subnet->ip_address . '/' . $subnet->cidr;
            $result = $this->ripeDatabase->getRoutesForPrefix($prefix);
            $routes = $result['objects'] ?? [];
        } catch (\Exception $e) {
            // No routes found or error
        }

        return view('ripe.route-objects', [
            'subnet' => $subnet,
            'routes' => $routes,
            'credentials' => $credentials,
        ]);
    }

    /**
     * Create a route object
     */
    public function createRoute(Request $request, Subnet $subnet): RedirectResponse
    {
        $user = auth()->user();

        if ($subnet->company_id !== $user->company?->id) {
            abort(403);
        }

        $validated = $request->validate([
            'credential_id' => 'required|exists:ripe_credentials,id',
            'origin' => 'required|string|regex:/^AS\d+$/i',
            'descr' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $credential = RipeCredential::findOrFail($validated['credential_id']);

        if ($credential->company_id !== $user->company->id) {
            abort(403);
        }

        $prefix = $subnet->ip_address . '/' . $subnet->cidr;

        try {
            $this->ripeDatabase->upsertRoute([
                'route' => $prefix,
                'origin' => strtoupper($validated['origin']),
                'descr' => $validated['descr'],
                'mnt_by' => $credential->maintainer,
                'remarks' => $validated['remarks'],
            ], $credential->getDecryptedApiKey());

            return redirect()->route('ripe.subnet.routes', $subnet)
                ->with('success', __('Route object created successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Failed to create route: ') . $e->getMessage());
        }
    }

    /**
     * Show geolocation information
     */
    public function geolocation(Subnet $subnet): View
    {
        $user = auth()->user();

        if ($subnet->company_id !== $user->company?->id) {
            abort(403);
        }

        $geoData = null;

        try {
            $geoData = $this->ripeStat->getGeolocation($subnet->ip_address);
        } catch (\Exception $e) {
            $geoData = ['error' => $e->getMessage()];
        }

        return view('ripe.geolocation', [
            'subnet' => $subnet,
            'geoData' => $geoData,
        ]);
    }

    /**
     * Show routing information
     */
    public function routing(Subnet $subnet): View
    {
        $user = auth()->user();

        if ($subnet->company_id !== $user->company?->id) {
            abort(403);
        }

        $prefix = $subnet->ip_address . '/' . $subnet->cidr;
        $routingData = [];

        try {
            $routingData['status'] = $this->ripeStat->getRoutingStatus($prefix);
            $routingData['bgp'] = $this->ripeStat->getBgpState($prefix);
            $routingData['overview'] = $this->ripeStat->getPrefixOverview($prefix);
        } catch (\Exception $e) {
            $routingData['error'] = $e->getMessage();
        }

        return view('ripe.routing', [
            'subnet' => $subnet,
            'routingData' => $routingData,
        ]);
    }

    /**
     * Sync subnet data from RIPE
     */
    public function syncSubnet(Subnet $subnet): RedirectResponse
    {
        $user = auth()->user();

        if ($subnet->company_id !== $user->company?->id) {
            abort(403);
        }

        if (!$subnet->ripe_inetnum_key) {
            return back()->with('error', __('Subnet is not linked to RIPE.'));
        }

        try {
            $result = $this->ripeDatabase->getInetnum($subnet->ripe_inetnum_key);

            if (!empty($result['objects'])) {
                $attrs = $result['objects'][0]['attributes'] ?? [];

                $subnet->update([
                    'ripe_netname' => $attrs['netname'] ?? $subnet->ripe_netname,
                    'ripe_status' => $attrs['status'] ?? $subnet->ripe_status,
                    'ripe_org' => $attrs['org'] ?? $subnet->ripe_org,
                    'ripe_maintainer' => is_array($attrs['mnt-by'] ?? null)
                        ? ($attrs['mnt-by'][0] ?? null)
                        : ($attrs['mnt-by'] ?? $subnet->ripe_maintainer),
                    'ripe_last_synced_at' => now(),
                ]);

                return back()->with('success', __('Subnet synchronized with RIPE.'));
            }

            return back()->with('warning', __('No data found in RIPE.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Sync failed: ') . $e->getMessage());
        }
    }

    /**
     * Format inetnum key from subnet
     */
    protected function formatInetnumKey(Subnet $subnet): string
    {
        $startIp = $subnet->ip_address;
        $endIp = long2ip(ip2long($startIp) + pow(2, 32 - $subnet->cidr) - 1);

        return "{$startIp} - {$endIp}";
    }
}
