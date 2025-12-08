<?php

namespace App\Repositories\Contracts;

use App\Models\AbuseReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AbuseReportRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?AbuseReport;

    public function findByIdWithRelations(int $id, array $relations = []): ?AbuseReport;

    public function create(array $data): AbuseReport;

    public function update(AbuseReport $report, array $data): AbuseReport;

    public function delete(AbuseReport $report): bool;

    public function getOpenReports(): Collection;

    public function getCriticalReports(): Collection;

    public function getBySubnet(int $subnetId): Collection;

    public function getByLease(int $leaseId): Collection;

    public function resolve(AbuseReport $report, int $userId, string $notes, string $action = 'resolved'): AbuseReport;

    public function countByStatus(): array;

    public function countBySeverity(): array;

    public function getRecentByType(int $days = 30): array;
}
