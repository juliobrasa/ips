<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RipeCredential;
use App\Models\Subnet;
use App\Services\RipeDatabaseService;
use App\Services\RipeStatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RipeApiController extends Controller
{
    public function __construct(
        protected RipeDatabaseService $ripeDatabase,
        protected RipeStatService $ripeStat
    ) {}

    /**
     * Get comprehensive IP/prefix information from RIPEstat
     */
    public function getInfo(Request $request): JsonResponse
    {
        $request->validate([
            'resource' => 'required|string',
        ]);

        $resource = $request->input('resource');

        if (!$this->ripeStat->validateResource($resource)) {
            return response()->json([
                'error' => 'Invalid resource format',
                'message' => 'Resource must be an IP address, prefix (CIDR), ASN, or country code.',
            ], 422);
        }

        try {
            $data = $this->ripeStat->getComprehensiveInfo($resource);

            return response()->json([
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch information',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get geolocation data
     */
    public function getGeolocation(Request $request): JsonResponse
    {
        $request->validate([
            'resource' => 'required|string',
        ]);

        try {
            $data = $this->ripeStat->getGeolocation($request->input('resource'));

            return response()->json([
                'data' => $data['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch geolocation',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get abuse contact information
     */
    public function getAbuseContact(Request $request): JsonResponse
    {
        $request->validate([
            'resource' => 'required|string',
        ]);

        try {
            $data = $this->ripeStat->getAbuseContact($request->input('resource'));

            return response()->json([
                'data' => [
                    'abuse_contacts' => $data['data']['abuse_contacts'] ?? [],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch abuse contact',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get WHOIS information
     */
    public function getWhois(Request $request): JsonResponse
    {
        $request->validate([
            'resource' => 'required|string',
        ]);

        try {
            $data = $this->ripeStat->getWhois($request->input('resource'));

            return response()->json([
                'data' => $data['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch WHOIS',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get routing status
     */
    public function getRoutingStatus(Request $request): JsonResponse
    {
        $request->validate([
            'resource' => 'required|string',
        ]);

        try {
            $data = $this->ripeStat->getRoutingStatus($request->input('resource'));

            return response()->json([
                'data' => $data['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch routing status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get BGP state
     */
    public function getBgpState(Request $request): JsonResponse
    {
        $request->validate([
            'resource' => 'required|string',
        ]);

        try {
            $data = $this->ripeStat->getBgpState($request->input('resource'));

            return response()->json([
                'data' => $data['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch BGP state',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get announced prefixes for an ASN
     */
    public function getAnnouncedPrefixes(string $asn): JsonResponse
    {
        if (!preg_match('/^AS?\d+$/i', $asn)) {
            return response()->json([
                'error' => 'Invalid ASN format',
            ], 422);
        }

        try {
            $data = $this->ripeStat->getAnnouncedPrefixes($asn);

            return response()->json([
                'data' => $data['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch announced prefixes',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get AS overview
     */
    public function getAsOverview(string $asn): JsonResponse
    {
        if (!preg_match('/^AS?\d+$/i', $asn)) {
            return response()->json([
                'error' => 'Invalid ASN format',
            ], 422);
        }

        try {
            $data = $this->ripeStat->getAsOverview($asn);

            return response()->json([
                'data' => $data['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch AS overview',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get RIPE database object
     */
    public function getDatabaseObject(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:inetnum,inet6num,route,route6,aut-num,person,role,organisation,mntner',
            'key' => 'required|string',
        ]);

        try {
            $data = $this->ripeDatabase->getObject(
                $request->input('type'),
                $request->input('key')
            );

            return response()->json([
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch object',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search RIPE database
     */
    public function searchDatabase(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:3',
            'type' => 'nullable|string',
        ]);

        $options = [];
        if ($request->has('type')) {
            $options['type-filter'] = $request->input('type');
        }

        try {
            $data = $this->ripeDatabase->search(
                $request->input('query'),
                $options
            );

            return response()->json([
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update inetnum object (requires credential)
     */
    public function updateInetnum(Request $request, Subnet $subnet): JsonResponse
    {
        $user = $request->user();

        if ($subnet->company_id !== $user->company?->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'credential_id' => 'required|exists:ripe_credentials,id',
            'netname' => 'required|string|max:80',
            'descr' => 'nullable|string|max:255',
            'country' => 'required|string|size:2',
            'admin_c' => 'required|string|max:30',
            'tech_c' => 'required|string|max:30',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'geoloc' => 'nullable|string',
        ]);

        $credential = RipeCredential::findOrFail($validated['credential_id']);

        if ($credential->company_id !== $user->company->id || !$credential->isValid()) {
            return response()->json(['error' => 'Invalid credential'], 403);
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

            $subnet->update([
                'ripe_inetnum_key' => $inetnumKey,
                'ripe_netname' => $validated['netname'],
                'ripe_maintainer' => $credential->maintainer,
                'ripe_status' => $validated['status'],
                'ripe_last_synced_at' => now(),
            ]);

            return response()->json([
                'message' => 'Inetnum updated successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update inetnum',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create route object (requires credential)
     */
    public function createRoute(Request $request, Subnet $subnet): JsonResponse
    {
        $user = $request->user();

        if ($subnet->company_id !== $user->company?->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'credential_id' => 'required|exists:ripe_credentials,id',
            'origin' => 'required|string|regex:/^AS\d+$/i',
            'descr' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $credential = RipeCredential::findOrFail($validated['credential_id']);

        if ($credential->company_id !== $user->company->id || !$credential->isValid()) {
            return response()->json(['error' => 'Invalid credential'], 403);
        }

        $prefix = $subnet->ip_address . '/' . $subnet->cidr;

        try {
            $result = $this->ripeDatabase->upsertRoute([
                'route' => $prefix,
                'origin' => strtoupper($validated['origin']),
                'descr' => $validated['descr'],
                'mnt_by' => $credential->maintainer,
                'remarks' => $validated['remarks'],
            ], $credential->getDecryptedApiKey());

            return response()->json([
                'message' => 'Route object created successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create route',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get routes for a subnet
     */
    public function getRoutes(Subnet $subnet): JsonResponse
    {
        $user = request()->user();

        if ($subnet->company_id !== $user->company?->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $prefix = $subnet->ip_address . '/' . $subnet->cidr;

        try {
            $data = $this->ripeDatabase->getRoutesForPrefix($prefix);

            return response()->json([
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch routes',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List available RIPEstat endpoints
     */
    public function listEndpoints(): JsonResponse
    {
        return response()->json([
            'endpoints' => $this->ripeStat->getAvailableEndpoints(),
        ]);
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
