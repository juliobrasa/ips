<?php

namespace App\Repositories\Contracts;

use App\Models\Subnet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SubnetRepositoryInterface
{
    public function getByCompany(int $companyId, int $perPage = 10): LengthAwarePaginator;

    public function getAvailableForMarketplace(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function findById(int $id): ?Subnet;

    public function findByIdWithRelations(int $id, array $relations = []): ?Subnet;

    public function create(array $data): Subnet;

    public function update(Subnet $subnet, array $data): Subnet;

    public function delete(Subnet $subnet): bool;

    public function getVerifiedClean(): Collection;

    public function getByStatus(string $status): Collection;

    public function getNeedingReputationCheck(int $hoursThreshold = 24): Collection;

    public function getWithBlocklistIssues(): Collection;

    public function updateReputationData(Subnet $subnet, array $reputationData): Subnet;

    public function countByStatus(): array;

    public function getTotalIpCount(): int;
}
