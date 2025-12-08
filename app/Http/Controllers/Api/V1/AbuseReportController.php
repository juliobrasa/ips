<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AbuseReportResource;
use App\Jobs\ProcessAbuseReport;
use App\Models\AbuseReport;
use App\Models\Subnet;
use App\Repositories\Contracts\AbuseReportRepositoryInterface;
use App\Events\AbuseReportResolved;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class AbuseReportController extends Controller
{
    public function __construct(
        protected AbuseReportRepositoryInterface $abuseReportRepository
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $company = $user->company;

        $filters = $request->only(['status', 'severity', 'type']);

        // If not admin, filter by owned subnets
        if (!$user->isAdmin() && $company) {
            $subnetIds = $company->subnets()->pluck('id')->toArray();
            $filters['subnet_ids'] = $subnetIds;
        }

        $reports = $this->abuseReportRepository->getAll($filters, $request->get('per_page', 15));

        return AbuseReportResource::collection($reports);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subnet_id' => 'required|exists:subnets,id',
            'lease_id' => 'nullable|exists:leases,id',
            'type' => ['required', Rule::in(['spam', 'phishing', 'malware', 'ddos', 'scraping', 'fraud', 'other'])],
            'severity' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'source' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'evidence' => 'nullable|array',
        ]);

        // Verify user has access to this subnet
        $subnet = Subnet::findOrFail($validated['subnet_id']);
        $user = $request->user();
        $company = $user->company;

        $isHolder = $company && $subnet->company_id === $company->id;
        $isLessee = $company && $subnet->activeLease()?->lessee_company_id === $company->id;

        if (!$isHolder && !$isLessee && !$user->isAdmin()) {
            abort(403, __('You do not have permission to report abuse for this subnet.'));
        }

        $report = $this->abuseReportRepository->create([
            'subnet_id' => $validated['subnet_id'],
            'lease_id' => $validated['lease_id'],
            'type' => $validated['type'],
            'severity' => $validated['severity'],
            'source' => $validated['source'],
            'description' => $validated['description'],
            'evidence' => $validated['evidence'] ?? [],
            'status' => 'open',
        ]);

        // Process asynchronously
        ProcessAbuseReport::dispatch($report);

        return response()->json([
            'message' => __('Abuse report submitted successfully.'),
            'data' => new AbuseReportResource($report),
        ], 201);
    }

    public function show(AbuseReport $abuseReport): AbuseReportResource
    {
        $this->authorizeReportAccess($abuseReport);

        return new AbuseReportResource($abuseReport->load(['subnet', 'lease', 'resolvedByUser']));
    }

    public function update(Request $request, AbuseReport $abuseReport): JsonResponse
    {
        $this->authorizeReportAccess($abuseReport);

        if ($abuseReport->isResolved()) {
            return response()->json([
                'message' => __('Cannot update a resolved report.'),
            ], 422);
        }

        $validated = $request->validate([
            'description' => 'nullable|string|max:5000',
            'evidence' => 'nullable|array',
        ]);

        $abuseReport = $this->abuseReportRepository->update($abuseReport, $validated);

        return response()->json([
            'message' => __('Abuse report updated successfully.'),
            'data' => new AbuseReportResource($abuseReport),
        ]);
    }

    public function destroy(AbuseReport $abuseReport): JsonResponse
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403, __('Only admins can delete abuse reports.'));
        }

        $this->abuseReportRepository->delete($abuseReport);

        return response()->json([
            'message' => __('Abuse report deleted successfully.'),
        ]);
    }

    public function acknowledge(AbuseReport $abuseReport): JsonResponse
    {
        $this->authorizeReportAccess($abuseReport);

        if ($abuseReport->status !== 'open') {
            return response()->json([
                'message' => __('Report is already being handled.'),
            ], 422);
        }

        $this->abuseReportRepository->update($abuseReport, [
            'status' => 'investigating',
        ]);

        return response()->json([
            'message' => __('Report acknowledged and under investigation.'),
            'data' => new AbuseReportResource($abuseReport->fresh()),
        ]);
    }

    // Admin methods
    public function resolve(Request $request, AbuseReport $abuseReport): JsonResponse
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string|max:2000',
            'suspend_subnet' => 'boolean',
        ]);

        $report = $this->abuseReportRepository->resolve(
            $abuseReport,
            $request->user()->id,
            $validated['resolution_notes'],
            'resolved'
        );

        if ($validated['suspend_subnet'] ?? false) {
            $abuseReport->subnet->update(['status' => 'suspended']);
        }

        event(new AbuseReportResolved($report, 'resolved'));

        return response()->json([
            'message' => __('Abuse report resolved successfully.'),
            'data' => new AbuseReportResource($report),
        ]);
    }

    public function dismiss(Request $request, AbuseReport $abuseReport): JsonResponse
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string|max:2000',
        ]);

        $report = $this->abuseReportRepository->resolve(
            $abuseReport,
            $request->user()->id,
            $validated['resolution_notes'],
            'dismissed'
        );

        event(new AbuseReportResolved($report, 'dismissed'));

        return response()->json([
            'message' => __('Abuse report dismissed.'),
            'data' => new AbuseReportResource($report),
        ]);
    }

    protected function authorizeReportAccess(AbuseReport $report): void
    {
        $user = auth()->user();
        $company = $user->company;

        if ($user->isAdmin()) {
            return;
        }

        if (!$company) {
            abort(403, __('Access denied.'));
        }

        $isHolder = $report->subnet->company_id === $company->id;
        $isLessee = $report->lease?->lessee_company_id === $company->id;

        if (!$isHolder && !$isLessee) {
            abort(403, __('Access denied.'));
        }
    }
}
