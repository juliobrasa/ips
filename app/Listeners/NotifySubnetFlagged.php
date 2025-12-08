<?php

namespace App\Listeners;

use App\Events\SubnetFlaggedForAbuse;
use App\Models\User;
use App\Notifications\SubnetFlaggedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifySubnetFlagged implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SubnetFlaggedForAbuse $event): void
    {
        $subnet = $event->subnet;
        $report = $event->report;

        // Notify the subnet holder
        if ($subnet->company && $subnet->company->user) {
            $subnet->company->user->notify(new SubnetFlaggedNotification($subnet, $report));
        }

        // Notify the current lessee if subnet is leased
        $activeLease = $subnet->activeLease();
        if ($activeLease && $activeLease->lesseeCompany && $activeLease->lesseeCompany->user) {
            $activeLease->lesseeCompany->user->notify(new SubnetFlaggedNotification($subnet, $report, 'lessee'));
        }

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new SubnetFlaggedNotification($subnet, $report, 'admin'));
        }

        Log::info("Subnet flagged notifications sent for {$subnet->cidr_notation}");
    }
}
