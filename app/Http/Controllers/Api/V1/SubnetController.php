<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subnet\StoreSubnetRequest;
use App\Http\Requests\Subnet\UpdateSubnetRequest;
use App\Http\Resources\SubnetResource;
use App\Jobs\CheckIpReputation;
use App\Jobs\VerifySubnetOwnership;
use App\Models\Subnet;
use App\Repositories\Contracts\SubnetRepositoryInterface;
use App\Services\IpReputationService;
use App\Services\WhoisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class SubnetController extends Controller
{
    public function __construct(
        protected SubnetRepositoryInterface $subnetRepository,
        protected IpReputationService $reputationService,
        protected WhoisService $whoisService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $company = $request->user()->company;

        if (!$company || !$company->isHolder()) {
            abort(403, __('You need to register as an IP Holder to manage subnets.'));
        }

        $subnets = $this->subnetRepository->getByCompany($company->id, $request->get('per_page', 15));

        return SubnetResource::collection($subnets);
    }

    public function store(StoreSubnetRequest $request): JsonResponse
    {
        $company = $request->user()->company;

        // Check IP reputation
        $reputationCheck = $this->reputationService->checkReputation($request->ip_address);

        if (!$reputationCheck['can_list']) {
            return response()->json([
                'message' => __('This IP has reputation issues and cannot be listed.'),
                'reputation' => [
                    'score' => $reputationCheck['score'],
                    'blocked_count' => $reputationCheck['blocked_count'],
                    'blocklists' => $this->reputationService->getBlocklistDetails($request->ip_address),
                ],
            ], 422);
        }

        $subnet = $this->subnetRepository->create([
            'company_id' => $company->id,
            'ip_address' => $request->ip_address,
            'cidr' => $request->cidr,
            'rir' => $request->rir,
            'geolocation_country' => $request->geolocation_country,
            'geolocation_city' => $request->geolocation_city,
            'price_per_ip_monthly' => $request->price_per_ip_monthly,
            'min_lease_months' => $request->min_lease_months,
            'description' => $request->description,
            'rpki_delegated' => $request->rpki_delegated ?? false,
            'status' => 'pending_verification',
            'verification_token' => Str::random(64),
            'reputation_score' => $reputationCheck['score'],
            'last_reputation_check' => now(),
            'blocklist_results' => $reputationCheck['blocklists'],
        ]);

        return response()->json([
            'message' => __('Subnet created successfully. Please verify ownership.'),
            'data' => new SubnetResource($subnet),
        ], 201);
    }

    public function show(Subnet $subnet): SubnetResource
    {
        $this->authorize('view', $subnet);

        return new SubnetResource($subnet->load(['company', 'leases', 'abuseReports']));
    }

    public function update(UpdateSubnetRequest $request, Subnet $subnet): JsonResponse
    {
        $subnet = $this->subnetRepository->update($subnet, $request->validated());

        return response()->json([
            'message' => __('Subnet updated successfully.'),
            'data' => new SubnetResource($subnet),
        ]);
    }

    public function destroy(Subnet $subnet): JsonResponse
    {
        $this->authorize('delete', $subnet);

        if ($subnet->isLeased()) {
            return response()->json([
                'message' => __('Cannot delete a subnet that is currently leased.'),
            ], 422);
        }

        $this->subnetRepository->delete($subnet);

        return response()->json([
            'message' => __('Subnet deleted successfully.'),
        ]);
    }

    public function verify(Subnet $subnet): JsonResponse
    {
        $this->authorize('update', $subnet);

        if ($subnet->ownership_verified_at) {
            return response()->json([
                'message' => __('Subnet is already verified.'),
            ], 422);
        }

        // Dispatch verification job
        VerifySubnetOwnership::dispatch($subnet);

        return response()->json([
            'message' => __('Verification process started. You will receive an email to confirm ownership.'),
        ]);
    }

    public function checkReputation(Subnet $subnet): JsonResponse
    {
        $this->authorize('update', $subnet);

        // Dispatch async reputation check
        CheckIpReputation::dispatch($subnet);

        // Also return immediate results
        $report = $this->reputationService->getSummaryReport($subnet->ip_address);

        $this->subnetRepository->updateReputationData($subnet, [
            'score' => $report['overall_score'],
            'blocklist_details' => $report['blocklist_details'],
        ]);

        return response()->json([
            'message' => __('Reputation check completed.'),
            'data' => [
                'score' => $report['overall_score'],
                'status' => $report['overall_status'],
                'blocklist_count' => $report['blocklist_count'],
                'can_be_listed' => $report['can_be_listed'],
                'blocklist_details' => $report['blocklist_details'],
                'recommendation' => $report['recommendation'],
            ],
        ]);
    }

    public function whois(Subnet $subnet): JsonResponse
    {
        $this->authorize('view', $subnet);

        $whoisData = $this->whoisService->query($subnet->ip_address, $subnet->rir);

        return response()->json([
            'data' => $whoisData,
        ]);
    }

    public function reputationHistory(Subnet $subnet): JsonResponse
    {
        $this->authorize('view', $subnet);

        // Get abuse reports as reputation history
        $history = $subnet->abuseReports()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($report) => [
                'type' => $report->type,
                'severity' => $report->severity,
                'status' => $report->status,
                'date' => $report->created_at->toIso8601String(),
            ]);

        return response()->json([
            'current_score' => $subnet->reputation_score,
            'last_check' => $subnet->last_reputation_check?->toIso8601String(),
            'blocklist_results' => $subnet->blocklist_results,
            'abuse_history' => $history,
        ]);
    }

    // Admin methods
    public function adminStats(): JsonResponse
    {
        return response()->json([
            'by_status' => $this->subnetRepository->countByStatus(),
            'total_ips' => $this->subnetRepository->getTotalIpCount(),
            'needing_check' => $this->subnetRepository->getNeedingReputationCheck()->count(),
            'with_issues' => $this->subnetRepository->getWithBlocklistIssues()->count(),
        ]);
    }

    public function suspend(Subnet $subnet): JsonResponse
    {
        $subnet->update(['status' => 'suspended']);

        return response()->json([
            'message' => __('Subnet suspended successfully.'),
            'data' => new SubnetResource($subnet->fresh()),
        ]);
    }

    public function unsuspend(Subnet $subnet): JsonResponse
    {
        if (!$subnet->ownership_verified_at) {
            return response()->json([
                'message' => __('Cannot unsuspend unverified subnet.'),
            ], 422);
        }

        $subnet->update(['status' => 'available']);

        return response()->json([
            'message' => __('Subnet unsuspended successfully.'),
            'data' => new SubnetResource($subnet->fresh()),
        ]);
    }
}
