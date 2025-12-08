<?php

namespace App\Events;

use App\Models\Lease;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaseTerminated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Lease $lease,
        public ?string $reason = null
    ) {}
}
