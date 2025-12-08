<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\RequestBlacklistDelisting;
use App\Models\Subnet;
use App\Models\BlacklistDelistingRequest;
use App\Services\IpReputationService;
use App\Services\DelistingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    public function __construct(
        protected IpReputationService $reputationService,
        protected DelistingService $delistingService
    ) {}

    public function status(Subnet $subnet): JsonResponse
    {
        $this->authorize('view', $subnet);

        $blocklistDetails = $this->reputationService->getBlocklistDetails($subnet->ip_address);

        // Get pending delisting requests
        $pendingRequests = BlacklistDelistingRequest::where('subnet_id', $subnet->id)
            ->where('status', 'pending')
            ->get()
            ->keyBy('blocklist');

        $status = collect($blocklistDetails)->map(function ($detail) use ($pendingRequests) {
            $delistingRequest = $pendingRequests->get($detail['blocklist']);

            return [
                'blocklist' => $detail['blocklist'],
                'listed' => true,
                'severity' => $detail['severity'],
                'weight' => $detail['weight'],
                'delisting_url' => $detail['delisting_url'],
                'delisting_status' => $delistingRequest ? $delistingRequest->status : 'not_requested',
                'delisting_requested_at' => $delistingRequest?->requested_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'subnet' => $subnet->cidr_notation,
            'reputation_score' => $subnet->reputation_score,
            'last_check' => $subnet->last_reputation_check?->toIso8601String(),
            'listed_blocklists' => $status->values(),
            'total_listed' => $status->count(),
        ]);
    }

    public function requestDelisting(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subnet_id' => 'required|exists:subnets,id',
            'blocklist' => 'required|string',
            'contact_email' => 'nullable|email',
            'reason' => 'nullable|string|max:1000',
        ]);

        $subnet = Subnet::findOrFail($validated['subnet_id']);
        $this->authorize('update', $subnet);

        // Check if already requested recently
        $existingRequest = BlacklistDelistingRequest::where('subnet_id', $subnet->id)
            ->where('blocklist', $validated['blocklist'])
            ->where('requested_at', '>', now()->subHours(24))
            ->first();

        if ($existingRequest) {
            return response()->json([
                'message' => __('A delisting request was already submitted in the last 24 hours.'),
                'existing_request' => [
                    'id' => $existingRequest->id,
                    'status' => $existingRequest->status,
                    'requested_at' => $existingRequest->requested_at->toIso8601String(),
                ],
            ], 422);
        }

        // Create the request record
        $delistingRequest = BlacklistDelistingRequest::create([
            'subnet_id' => $subnet->id,
            'blocklist' => $validated['blocklist'],
            'status' => 'pending',
            'contact_email' => $validated['contact_email'],
            'reason' => $validated['reason'],
            'requested_by' => $request->user()->id,
            'requested_at' => now(),
        ]);

        // Dispatch job to process the request
        RequestBlacklistDelisting::dispatch(
            $subnet,
            $validated['blocklist'],
            $request->user()->id
        )->onQueue('delisting');

        return response()->json([
            'message' => __('Delisting request submitted.'),
            'data' => [
                'id' => $delistingRequest->id,
                'subnet' => $subnet->cidr_notation,
                'blocklist' => $validated['blocklist'],
                'status' => 'pending',
                'delisting_url' => $this->delistingService->getDelistingUrl($validated['blocklist']),
            ],
        ], 201);
    }

    public function listRequests(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->company;

        $query = BlacklistDelistingRequest::with('subnet')
            ->orderBy('created_at', 'desc');

        if (!$user->isAdmin() && $company) {
            $subnetIds = $company->subnets()->pluck('id');
            $query->whereIn('subnet_id', $subnetIds);
        }

        $requests = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $requests->items(),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    public function showRequest(BlacklistDelistingRequest $request): JsonResponse
    {
        $this->authorize('view', $request->subnet);

        return response()->json([
            'data' => [
                'id' => $request->id,
                'subnet' => [
                    'id' => $request->subnet->id,
                    'cidr_notation' => $request->subnet->cidr_notation,
                ],
                'blocklist' => $request->blocklist,
                'status' => $request->status,
                'contact_email' => $request->contact_email,
                'reason' => $request->reason,
                'request_url' => $request->request_url,
                'response_message' => $request->response_message,
                'requested_at' => $request->requested_at?->toIso8601String(),
                'last_checked_at' => $request->last_checked_at?->toIso8601String(),
                'delisted_at' => $request->delisted_at?->toIso8601String(),
            ],
        ]);
    }
}
