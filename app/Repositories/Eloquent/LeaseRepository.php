<?php

namespace App\Repositories\Eloquent;

use App\Models\Lease;
use App\Repositories\Contracts\LeaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class LeaseRepository implements LeaseRepositoryInterface
{
    protected string $cachePrefix = 'leases_';
    protected int $cacheTtl = 300;

    public function getByLesseeCompany(int $companyId, int $perPage = 10): LengthAwarePaginator
    {
        return Lease::where('lessee_company_id', $companyId)
            ->with(['subnet.company', 'invoices'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByHolderCompany(int $companyId, int $perPage = 10): LengthAwarePaginator
    {
        return Lease::whereHas('subnet', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->with(['subnet', 'lesseeCompany', 'invoices'])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    public function findById(int $id): ?Lease
    {
        return Lease::find($id);
    }

    public function findByIdWithRelations(int $id, array $relations = []): ?Lease
    {
        return Lease::with($relations)->find($id);
    }

    public function create(array $data): Lease
    {
        $lease = Lease::create($data);
        $this->clearCache();
        return $lease;
    }

    public function update(Lease $lease, array $data): Lease
    {
        $lease->update($data);
        $this->clearCache();
        return $lease->fresh();
    }

    public function getActiveLeases(): Collection
    {
        return Lease::where('status', 'active')
            ->with(['subnet', 'lesseeCompany'])
            ->get();
    }

    public function getExpiringLeases(int $days = 30): Collection
    {
        return Lease::where('status', 'active')
            ->where('end_date', '<=', now()->addDays($days))
            ->with(['subnet', 'lesseeCompany'])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    public function terminate(Lease $lease, string $reason = null): Lease
    {
        $lease->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'termination_reason' => $reason,
        ]);

        // Update subnet status
        $lease->subnet->update(['status' => 'available']);

        $this->clearCache();
        return $lease->fresh();
    }

    public function extend(Lease $lease, int $months): Lease
    {
        $lease->update([
            'end_date' => $lease->end_date->addMonths($months),
            'duration_months' => $lease->duration_months + $months,
        ]);

        $this->clearCache();
        return $lease->fresh();
    }

    public function countByStatus(): array
    {
        return Cache::remember($this->cachePrefix . 'count_by_status', $this->cacheTtl, function () {
            return Lease::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    public function getTotalMonthlyRevenue(): float
    {
        return Cache::remember($this->cachePrefix . 'total_monthly_revenue', $this->cacheTtl, function () {
            return Lease::where('status', 'active')
                ->sum('monthly_price');
        });
    }

    protected function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'count_by_status');
        Cache::forget($this->cachePrefix . 'total_monthly_revenue');
    }
}
