<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    protected $signature = 'currency:update';

    protected $description = 'Update exchange rates from external API';

    public function handle(CurrencyService $currency): int
    {
        $this->info('Updating exchange rates...');

        $rates = $currency->updateAllRates();

        if (empty($rates)) {
            $this->error('Failed to update exchange rates');
            return 1;
        }

        $this->info('Updated rates:');
        foreach ($rates as $code => $rate) {
            $this->line("  EUR -> {$code}: {$rate}");
        }

        $this->info('Exchange rates updated successfully.');

        return 0;
    }
}
