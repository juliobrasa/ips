<?php

namespace App\Console\Commands;

use App\Services\AutomationService;
use Illuminate\Console\Command;

class RunAutomations extends Command
{
    protected $signature = 'automations:run {--type= : Specific automation type to run}';

    protected $description = 'Run scheduled automations (renewals, reminders, reputation checks, etc.)';

    public function handle(AutomationService $automations): int
    {
        $type = $this->option('type');

        $this->info('Running automations...');

        if ($type) {
            $result = match ($type) {
                'renewals' => ['auto_renewals' => $automations->processAutoRenewals()],
                'reminders' => ['expiration_reminders' => $automations->processExpirationReminders()],
                'expired' => ['expired_leases' => $automations->processExpiredLeases()],
                'reputation' => ['reputation_checks' => $automations->processReputationChecks()],
                'delisting' => ['auto_delisting' => $automations->processAutoDelisting()],
                default => null,
            };

            if (!$result) {
                $this->error("Unknown automation type: {$type}");
                return 1;
            }
        } else {
            $result = $automations->runAll();
        }

        foreach ($result as $name => $data) {
            $this->info("\n{$name}:");
            foreach ($data as $key => $value) {
                $this->line("  {$key}: {$value}");
            }
        }

        $this->info("\nAutomations completed successfully.");

        return 0;
    }
}
