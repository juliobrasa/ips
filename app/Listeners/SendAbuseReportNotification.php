<?php

namespace App\Listeners;

use App\Events\AbuseReportCreated;
use App\Models\User;
use App\Notifications\AbuseReportNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendAbuseReportNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(AbuseReportCreated $event): void
    {
        $report = $event->report;
        $subnet = $report->subnet;

        // Notify the subnet holder
        if ($subnet && $subnet->company && $subnet->company->user) {
            $subnet->company->user->notify(new AbuseReportNotification($report, 'holder'));
        }

        // Notify the lessee if there's an active lease
        if ($report->lease && $report->lease->lesseeCompany && $report->lease->lesseeCompany->user) {
            $report->lease->lesseeCompany->user->notify(new AbuseReportNotification($report, 'lessee'));
        }

        // Notify admins for critical/high severity
        if (in_array($report->severity, ['critical', 'high'])) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AbuseReportNotification($report, 'admin'));
            }
        }

        Log::info("Abuse report notifications sent for report #{$report->id}");
    }
}
