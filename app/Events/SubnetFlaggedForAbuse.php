<?php

namespace App\Events;

use App\Models\Subnet;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubnetFlaggedForAbuse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Subnet $subnet,
        public array $report
    ) {}
}
