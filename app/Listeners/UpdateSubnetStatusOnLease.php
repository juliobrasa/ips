<?php

namespace App\Listeners;

use App\Events\LeaseCreated;
use App\Events\LeaseTerminated;
use Illuminate\Support\Facades\Log;

class UpdateSubnetStatusOnLease
{
    public function handleLeaseCreated(LeaseCreated $event): void
    {
        $lease = $event->lease;
        $subnet = $lease->subnet;

        if ($subnet) {
            $subnet->update(['status' => 'leased']);
            Log::info("Subnet {$subnet->cidr_notation} status updated to 'leased'");
        }
    }

    public function handleLeaseTerminated(LeaseTerminated $event): void
    {
        $lease = $event->lease;
        $subnet = $lease->subnet;

        if ($subnet && $subnet->ownership_verified_at) {
            $subnet->update(['status' => 'available']);
            Log::info("Subnet {$subnet->cidr_notation} status updated to 'available'");
        }
    }
}
