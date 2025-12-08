<?php

namespace App\Repositories\Contracts;

use App\Models\Lease;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LeaseRepositoryInterface
{
    public function getByLesseeCompany(int $companyId, int $perPage = 10): LengthAwarePaginator;

    public function getByHolderCompany(int $companyId, int $perPage = 10): LengthAwarePaginator;

    public function findById(int $id): ?Lease;

    public function findByIdWithRelations(int $id, array $relations = []): ?Lease;

    public function create(array $data): Lease;

    public function update(Lease $lease, array $data): Lease;

    public function getActiveLeases(): Collection;

    public function getExpiringLeases(int $days = 30): Collection;

    public function terminate(Lease $lease, string $reason = null): Lease;

    public function extend(Lease $lease, int $months): Lease;

    public function countByStatus(): array;

    public function getTotalMonthlyRevenue(): float;
}
