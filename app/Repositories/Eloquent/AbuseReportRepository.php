<?php

namespace App\Repositories\Eloquent;

use App\Models\AbuseReport;
use App\Repositories\Contracts\AbuseReportRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AbuseReportRepository implements AbuseReportRepositoryInterface
{
    protected string $cachePrefix = 'abuse_reports_';
    protected int $cacheTtl = 300;

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AbuseReport::with(['subnet', 'lease', 'resolvedByUser']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['subnet_id'])) {
            $query->where('subnet_id', $filters['subnet_id']);
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function findById(int $id): ?AbuseReport
    {
        return AbuseReport::find($id);
    }

    public function findByIdWithRelations(int $id, array $relations = []): ?AbuseReport
    {
        return AbuseReport::with($relations)->find($id);
    }

    public function create(array $data): AbuseReport
    {
        $report = AbuseReport::create($data);
        $this->clearCache();
        return $report;
    }

    public function update(AbuseReport $report, array $data): AbuseReport
    {
        $report->update($data);
        $this->clearCache();
        return $report->fresh();
    }

    public function delete(AbuseReport $report): bool
    {
        $deleted = $report->delete();
        $this->clearCache();
        return $deleted;
    }

    public function getOpenReports(): Collection
    {
        return AbuseReport::open()
            ->with(['subnet', 'lease'])
            ->orderBy('severity', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getCriticalReports(): Collection
    {
        return AbuseReport::critical()
            ->open()
            ->with(['subnet', 'lease'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getBySubnet(int $subnetId): Collection
    {
        return AbuseReport::where('subnet_id', $subnetId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByLease(int $leaseId): Collection
    {
        return AbuseReport::where('lease_id', $leaseId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function resolve(AbuseReport $report, int $userId, string $notes, string $action = 'resolved'): AbuseReport
    {
        $report->update([
            'status' => $action,
            'resolved_at' => now(),
            'resolved_by' => $userId,
            'resolution_notes' => $notes,
        ]);

        $this->clearCache();
        return $report->fresh();
    }

    public function countByStatus(): array
    {
        return Cache::remember($this->cachePrefix . 'count_by_status', $this->cacheTtl, function () {
            return AbuseReport::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    public function countBySeverity(): array
    {
        return Cache::remember($this->cachePrefix . 'count_by_severity', $this->cacheTtl, function () {
            return AbuseReport::selectRaw('severity, COUNT(*) as count')
                ->where('status', '!=', 'resolved')
                ->groupBy('severity')
                ->pluck('count', 'severity')
                ->toArray();
        });
    }

    public function getRecentByType(int $days = 30): array
    {
        return AbuseReport::selectRaw('type, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    protected function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'count_by_status');
        Cache::forget($this->cachePrefix . 'count_by_severity');
    }
}
