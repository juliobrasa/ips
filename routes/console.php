<?php

use App\Jobs\MonitorSubnetReputation;
use App\Jobs\CheckIpReputation;
use App\Models\Subnet;
use App\Services\DelistingService;
use App\Models\BlacklistDelistingRequest;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reputation monitoring command
Artisan::command('ip:monitor-reputation {--hours=24 : Check IPs older than X hours} {--batch=50 : Batch size}', function (int $hours = 24, int $batch = 50) {
    $this->info("Starting reputation monitoring (threshold: {$hours}h, batch: {$batch})...");

    MonitorSubnetReputation::dispatch($hours, $batch)->onQueue('reputation');

    $this->info('Monitoring job dispatched to queue.');
})->purpose('Monitor IP reputation for subnets needing checks');

// Check single IP reputation
Artisan::command('ip:check {ip : IP address to check}', function (string $ip) {
    $this->info("Checking reputation for {$ip}...");

    $service = app(\App\Services\IpReputationService::class);
    $report = $service->getSummaryReport($ip);

    $this->table(
        ['Metric', 'Value'],
        [
            ['Score', $report['overall_score'] . '/100'],
            ['Status', $report['overall_status']],
            ['Blocklist Count', $report['blocklist_count']],
            ['Can List', $report['can_be_listed'] ? 'Yes' : 'No'],
            ['Recommendation', $report['recommendation']],
        ]
    );

    if ($report['blocklist_count'] > 0) {
        $this->warn('Listed on blocklists:');
        foreach ($report['blocklist_details'] as $detail) {
            $this->line("  - {$detail['blocklist']} (severity: {$detail['severity']})");
        }
    }
})->purpose('Check reputation for a single IP address');

// Check delisting status
Artisan::command('ip:check-delisting', function () {
    $this->info('Checking pending delisting requests...');

    $delistingService = app(DelistingService::class);
    $pending = BlacklistDelistingRequest::whereIn('status', ['pending', 'in_progress'])
        ->with('subnet')
        ->get();

    $this->info("Found {$pending->count()} pending requests.");

    foreach ($pending as $request) {
        $isListed = $delistingService->checkIfStillListed(
            $request->subnet->ip_address,
            $request->blocklist
        );

        $request->update(['last_checked_at' => now()]);

        if (!$isListed) {
            $request->markAsDelisted();
            $this->info("  [{$request->subnet->cidr_notation}] Delisted from {$request->blocklist}!");
        } else {
            $this->line("  [{$request->subnet->cidr_notation}] Still listed on {$request->blocklist}");
        }
    }

    $this->info('Done.');
})->purpose('Check status of pending delisting requests');

// Cleanup old audit logs
Artisan::command('audit:cleanup {--days=90 : Delete logs older than X days}', function (int $days = 90) {
    $deleted = \App\Models\AuditLog::where('created_at', '<', now()->subDays($days))->delete();
    $this->info("Deleted {$deleted} audit logs older than {$days} days.");
})->purpose('Clean up old audit logs');

// Schedule tasks
Schedule::command('ip:monitor-reputation --hours=24 --batch=50')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('ip:check-delisting')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('audit:cleanup --days=90')
    ->weekly()
    ->sundays()
    ->at('04:00');
