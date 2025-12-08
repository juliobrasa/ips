<?php

namespace App\Jobs;

use App\Models\Subnet;
use App\Services\IpReputationService;
use App\Events\ReputationCheckCompleted;
use App\Events\SubnetFlaggedForAbuse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckIpReputation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 120;

    public function __construct(
        public Subnet $subnet,
        public bool $notifyOnIssues = true
    ) {}

    public function handle(IpReputationService $reputationService): void
    {
        Log::info("Checking reputation for subnet {$this->subnet->cidr_notation}");

        try {
            $report = $reputationService->getSummaryReport($this->subnet->ip_address);

            $previousScore = $this->subnet->reputation_score;

            $this->subnet->update([
                'reputation_score' => $report['overall_score'],
                'last_reputation_check' => now(),
                'blocklist_results' => $report['blocklist_details'],
            ]);

            // Fire event for reputation check completed
            event(new ReputationCheckCompleted($this->subnet, $report));

            // Check if subnet needs to be flagged
            if (!$report['can_be_listed'] && $this->subnet->status === 'available') {
                $this->subnet->update(['status' => 'suspended']);

                if ($this->notifyOnIssues) {
                    event(new SubnetFlaggedForAbuse($this->subnet, $report));
                }

                Log::warning("Subnet {$this->subnet->cidr_notation} suspended due to reputation issues", [
                    'score' => $report['overall_score'],
                    'blocklist_count' => $report['blocklist_count'],
                ]);
            }

            // Log significant score changes
            if ($previousScore !== null && abs($previousScore - $report['overall_score']) >= 10) {
                Log::info("Significant reputation score change for {$this->subnet->cidr_notation}", [
                    'previous' => $previousScore,
                    'current' => $report['overall_score'],
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to check reputation for subnet {$this->subnet->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function tags(): array
    {
        return ['reputation-check', 'subnet:' . $this->subnet->id];
    }
}
