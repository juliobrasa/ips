<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lease\AssignAsnRequest;
use App\Http\Resources\LeaseResource;
use App\Models\Lease;
use App\Repositories\Contracts\LeaseRepositoryInterface;
use App\Events\LeaseTerminated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeaseController extends Controller
{
    public function __construct(
        protected LeaseRepositoryInterface $leaseRepository
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $company = $request->user()->company;

        if (!$company) {
            abort(403, __('You need to create a company profile first.'));
        }

        // Get leases where user is lessee or holder
        if ($company->isHolder() && !$company->isLessee()) {
            $leases = $this->leaseRepository->getByHolderCompany($company->id, $request->get('per_page', 15));
        } else {
            $leases = $this->leaseRepository->getByLesseeCompany($company->id, $request->get('per_page', 15));
        }

        return LeaseResource::collection($leases);
    }

    public function show(Lease $lease): LeaseResource
    {
        $this->authorizeLeaseAccess($lease);

        return new LeaseResource($lease->load(['subnet.company', 'lesseeCompany', 'loa', 'invoices']));
    }

    public function assignAsn(AssignAsnRequest $request, Lease $lease): JsonResponse
    {
        $this->authorizeLeaseAccess($lease, 'lessee');

        if (!$lease->isActive()) {
            return response()->json([
                'message' => __('Can only assign ASN to active leases.'),
            ], 422);
        }

        $lease->update(['asn' => $request->asn]);

        return response()->json([
            'message' => __('ASN assigned successfully.'),
            'data' => new LeaseResource($lease->fresh()),
        ]);
    }

    public function renew(Request $request, Lease $lease): JsonResponse
    {
        $this->authorizeLeaseAccess($lease, 'lessee');

        $request->validate([
            'months' => 'required|integer|min:1|max:36',
        ]);

        if (!$lease->isActive()) {
            return response()->json([
                'message' => __('Can only renew active leases.'),
            ], 422);
        }

        $lease = $this->leaseRepository->extend($lease, $request->months);

        // TODO: Create invoice for renewal

        return response()->json([
            'message' => __('Lease renewed successfully.'),
            'data' => new LeaseResource($lease),
        ]);
    }

    public function terminate(Request $request, Lease $lease): JsonResponse
    {
        $this->authorizeLeaseAccess($lease);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if (!$lease->isActive()) {
            return response()->json([
                'message' => __('Can only terminate active leases.'),
            ], 422);
        }

        $lease = $this->leaseRepository->terminate($lease, $request->reason);

        event(new LeaseTerminated($lease, $request->reason));

        return response()->json([
            'message' => __('Lease terminated successfully.'),
            'data' => new LeaseResource($lease),
        ]);
    }

    public function getLoa(Lease $lease): JsonResponse
    {
        $this->authorizeLeaseAccess($lease);

        if (!$lease->loa) {
            return response()->json([
                'message' => __('No LOA available for this lease.'),
            ], 404);
        }

        return response()->json([
            'id' => $lease->loa->id,
            'loa_number' => $lease->loa->loa_number,
            'verification_code' => $lease->loa->verification_code,
            'download_url' => route('loa.download', $lease->loa),
            'created_at' => $lease->loa->created_at->toIso8601String(),
        ]);
    }

    protected function authorizeLeaseAccess(Lease $lease, ?string $requiredRole = null): void
    {
        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            abort(403, __('Access denied.'));
        }

        $isLessee = $lease->lessee_company_id === $company->id;
        $isHolder = $lease->subnet->company_id === $company->id;

        if (!$isLessee && !$isHolder && !$user->isAdmin()) {
            abort(403, __('Access denied.'));
        }

        if ($requiredRole === 'lessee' && !$isLessee && !$user->isAdmin()) {
            abort(403, __('Only the lessee can perform this action.'));
        }

        if ($requiredRole === 'holder' && !$isHolder && !$user->isAdmin()) {
            abort(403, __('Only the holder can perform this action.'));
        }
    }
}
