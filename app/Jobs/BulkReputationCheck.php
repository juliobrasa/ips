<?php

namespace App\Jobs;

use App\Models\Subnet;
use App\Services\IpReputationService;
use App\Events\BulkReputationCheckCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BulkReputationCheck implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 120;
    public int $timeout = 600;

    public function __construct(
        public array $subnetIds,
        public ?int $initiatedBy = null
    ) {}

    public function handle(IpReputationService $reputationService): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $results = [];
        $flaggedCount = 0;
        $cleanCount = 0;

        foreach ($this->subnetIds as $subnetId) {
            $subnet = Subnet::find($subnetId);

            if (!$subnet) {
                continue;
            }

            try {
                $report = $reputationService->getSummaryReport($subnet->ip_address);

                $subnet->update([
                    'reputation_score' => $report['overall_score'],
                    'last_reputation_check' => now(),
                    'blocklist_results' => $report['blocklist_details'],
                ]);

                $results[$subnetId] = [
                    'success' => true,
                    'score' => $report['overall_score'],
                    'can_list' => $report['can_be_listed'],
                ];

                if ($report['can_be_listed']) {
                    $cleanCount++;
                } else {
                    $flaggedCount++;

                    if ($subnet->status === 'available') {
                        $subnet->update(['status' => 'suspended']);
                    }
                }

                // Small delay to avoid rate limiting
                usleep(500000); // 0.5 seconds

            } catch (\Exception $e) {
                Log::error("Bulk reputation check failed for subnet {$subnetId}: " . $e->getMessage());
                $results[$subnetId] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        Log::info("Bulk reputation check completed", [
            'total' => count($this->subnetIds),
            'clean' => $cleanCount,
            'flagged' => $flaggedCount,
        ]);

        event(new BulkReputationCheckCompleted($results, $this->initiatedBy));
    }

    public function tags(): array
    {
        return ['bulk-reputation-check', 'count:' . count($this->subnetIds)];
    }
}
