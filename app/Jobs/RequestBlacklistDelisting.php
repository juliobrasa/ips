<?php

namespace App\Jobs;

use App\Models\Subnet;
use App\Models\BlacklistDelistingRequest;
use App\Services\IpReputationService;
use App\Services\DelistingService;
use App\Events\DelistingRequestProcessed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RequestBlacklistDelisting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 300;
    public int $timeout = 180;

    public function __construct(
        public Subnet $subnet,
        public string $blocklist,
        public ?int $requestedBy = null
    ) {}

    public function handle(DelistingService $delistingService): void
    {
        Log::info("Processing delisting request for {$this->subnet->cidr_notation} from {$this->blocklist}");

        try {
            $result = $delistingService->requestDelisting(
                $this->subnet->ip_address,
                $this->blocklist
            );

            // Create or update delisting request record
            BlacklistDelistingRequest::updateOrCreate(
                [
                    'subnet_id' => $this->subnet->id,
                    'blocklist' => $this->blocklist,
                ],
                [
                    'status' => $result['status'],
                    'request_url' => $result['request_url'] ?? null,
                    'response_message' => $result['message'] ?? null,
                    'requested_by' => $this->requestedBy,
                    'requested_at' => now(),
                    'last_checked_at' => now(),
                ]
            );

            event(new DelistingRequestProcessed($this->subnet, $this->blocklist, $result));

            Log::info("Delisting request processed for {$this->subnet->cidr_notation}", $result);

        } catch (\Exception $e) {
            Log::error("Failed to process delisting request: " . $e->getMessage());
            throw $e;
        }
    }

    public function tags(): array
    {
        return ['delisting-request', 'subnet:' . $this->subnet->id, 'blocklist:' . $this->blocklist];
    }
}
