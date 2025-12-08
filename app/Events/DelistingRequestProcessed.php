<?php

namespace App\Events;

use App\Models\Subnet;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DelistingRequestProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Subnet $subnet,
        public string $blocklist,
        public array $result
    ) {}
}
