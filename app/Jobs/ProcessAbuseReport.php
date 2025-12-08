<?php

namespace App\Jobs;

use App\Models\AbuseReport;
use App\Models\Subnet;
use App\Services\IpReputationService;
use App\Events\AbuseReportCreated;
use App\Notifications\AbuseReportNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAbuseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public AbuseReport $report,
        public bool $autoCheck = true
    ) {}

    public function handle(IpReputationService $reputationService): void
    {
        Log::info("Processing abuse report #{$this->report->id}");

        try {
            $subnet = $this->report->subnet;

            // Auto-check reputation if enabled
            if ($this->autoCheck && $subnet) {
                $reputationData = $reputationService->getSummaryReport($subnet->ip_address);

                $subnet->update([
                    'reputation_score' => $reputationData['overall_score'],
                    'last_reputation_check' => now(),
                    'blocklist_results' => $reputationData['blocklist_details'],
                ]);

                // Add reputation data to report evidence
                $evidence = $this->report->evidence ?? [];
                $evidence['auto_reputation_check'] = [
                    'score' => $reputationData['overall_score'],
                    'blocklist_count' => $reputationData['blocklist_count'],
                    'checked_at' => now()->toIso8601String(),
                ];
                $this->report->update(['evidence' => $evidence]);
            }

            // Set to investigating if severity is high
            if (in_array($this->report->severity, ['critical', 'high']) && $this->report->status === 'open') {
                $this->report->update(['status' => 'investigating']);
            }

            // Fire event for notifications
            event(new AbuseReportCreated($this->report));

            Log::info("Abuse report #{$this->report->id} processed successfully");

        } catch (\Exception $e) {
            Log::error("Failed to process abuse report #{$this->report->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function tags(): array
    {
        return ['abuse-report', 'report:' . $this->report->id];
    }
}
