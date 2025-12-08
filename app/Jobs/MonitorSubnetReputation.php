<?php

namespace App\Jobs;

use App\Models\Subnet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonitorSubnetReputation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function __construct(
        public int $hoursThreshold = 24,
        public int $batchSize = 50
    ) {}

    public function handle(): void
    {
        Log::info("Starting scheduled reputation monitoring");

        $subnets = Subnet::where(function ($query) {
            $query->whereNull('last_reputation_check')
                ->orWhere('last_reputation_check', '<', now()->subHours($this->hoursThreshold));
        })
        ->whereIn('status', ['available', 'leased'])
        ->limit($this->batchSize)
        ->pluck('id')
        ->toArray();

        if (empty($subnets)) {
            Log::info("No subnets need reputation check");
            return;
        }

        Log::info("Dispatching reputation checks for " . count($subnets) . " subnets");

        // Dispatch individual jobs for each subnet to allow better queue management
        foreach ($subnets as $subnetId) {
            $subnet = Subnet::find($subnetId);
            if ($subnet) {
                CheckIpReputation::dispatch($subnet)->onQueue('reputation');
            }
        }
    }

    public function tags(): array
    {
        return ['scheduled-monitoring', 'reputation'];
    }
}
