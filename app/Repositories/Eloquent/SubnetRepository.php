<?php

namespace App\Repositories\Eloquent;

use App\Models\Subnet;
use App\Repositories\Contracts\SubnetRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class SubnetRepository implements SubnetRepositoryInterface
{
    protected string $cachePrefix = 'subnets_';
    protected int $cacheTtl = 300; // 5 minutes

    public function getByCompany(int $companyId, int $perPage = 10): LengthAwarePaginator
    {
        return Subnet::where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAvailableForMarketplace(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Subnet::available()
            ->verified()
            ->clean()
            ->with('company');

        if (!empty($filters['rir'])) {
            $query->byRir($filters['rir']);
        }

        if (!empty($filters['country'])) {
            $query->byCountry($filters['country']);
        }

        if (!empty($filters['cidr'])) {
            $query->byCidr((int) $filters['cidr']);
        }

        if (!empty($filters['min_price']) && !empty($filters['max_price'])) {
            $query->priceRange((float) $filters['min_price'], (float) $filters['max_price']);
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function findById(int $id): ?Subnet
    {
        return Subnet::find($id);
    }

    public function findByIdWithRelations(int $id, array $relations = []): ?Subnet
    {
        return Subnet::with($relations)->find($id);
    }

    public function create(array $data): Subnet
    {
        $subnet = Subnet::create($data);
        $this->clearCache();
        return $subnet;
    }

    public function update(Subnet $subnet, array $data): Subnet
    {
        $subnet->update($data);
        $this->clearCache();
        return $subnet->fresh();
    }

    public function delete(Subnet $subnet): bool
    {
        $deleted = $subnet->delete();
        $this->clearCache();
        return $deleted;
    }

    public function getVerifiedClean(): Collection
    {
        return Cache::remember($this->cachePrefix . 'verified_clean', $this->cacheTtl, function () {
            return Subnet::verified()->clean()->get();
        });
    }

    public function getByStatus(string $status): Collection
    {
        return Subnet::where('status', $status)->get();
    }

    public function getNeedingReputationCheck(int $hoursThreshold = 24): Collection
    {
        return Subnet::where(function ($query) use ($hoursThreshold) {
            $query->whereNull('last_reputation_check')
                ->orWhere('last_reputation_check', '<', now()->subHours($hoursThreshold));
        })
        ->whereIn('status', ['available', 'leased'])
        ->get();
    }

    public function getWithBlocklistIssues(): Collection
    {
        return Subnet::where('reputation_score', '<', 80)
            ->whereNotNull('blocklist_results')
            ->get();
    }

    public function updateReputationData(Subnet $subnet, array $reputationData): Subnet
    {
        $subnet->update([
            'reputation_score' => $reputationData['score'],
            'last_reputation_check' => now(),
            'blocklist_results' => $reputationData['blocklists'] ?? $reputationData['blocklist_details'] ?? [],
        ]);

        $this->clearCache();
        return $subnet->fresh();
    }

    public function countByStatus(): array
    {
        return Cache::remember($this->cachePrefix . 'count_by_status', $this->cacheTtl, function () {
            return Subnet::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    public function getTotalIpCount(): int
    {
        return Cache::remember($this->cachePrefix . 'total_ip_count', $this->cacheTtl, function () {
            $subnets = Subnet::select('cidr')->get();
            return $subnets->sum(function ($subnet) {
                return pow(2, 32 - $subnet->cidr);
            });
        });
    }

    protected function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'verified_clean');
        Cache::forget($this->cachePrefix . 'count_by_status');
        Cache::forget($this->cachePrefix . 'total_ip_count');
    }
}
