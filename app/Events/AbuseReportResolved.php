<?php

namespace App\Events;

use App\Models\AbuseReport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbuseReportResolved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public AbuseReport $report,
        public string $action
    ) {}
}
