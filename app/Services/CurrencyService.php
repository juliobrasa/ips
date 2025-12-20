<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    protected string $baseCurrency = 'EUR';

    protected array $supportedCurrencies = [
        'EUR' => ['symbol' => '€', 'name' => 'Euro'],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
        'CHF' => ['symbol' => 'CHF', 'name' => 'Swiss Franc'],
        'PLN' => ['symbol' => 'zł', 'name' => 'Polish Zloty'],
        'CZK' => ['symbol' => 'Kč', 'name' => 'Czech Koruna'],
        'SEK' => ['symbol' => 'kr', 'name' => 'Swedish Krona'],
        'NOK' => ['symbol' => 'kr', 'name' => 'Norwegian Krone'],
        'DKK' => ['symbol' => 'kr', 'name' => 'Danish Krone'],
        'BTC' => ['symbol' => '₿', 'name' => 'Bitcoin'],
    ];

    /**
     * Get all supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    /**
     * Convert amount from base currency
     */
    public function convert(float $amount, string $toCurrency, ?string $fromCurrency = null): float
    {
        $fromCurrency = $fromCurrency ?? $this->baseCurrency;

        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);

        return round($amount * $rate, 2);
    }

    /**
     * Get exchange rate
     */
    public function getExchangeRate(string $from, string $to): float
    {
        $cacheKey = "exchange_rate:{$from}:{$to}";

        return Cache::remember($cacheKey, 3600, function () use ($from, $to) {
            // Check database first
            $storedRate = DB::table('currency_rates')
                ->where('from_currency', $from)
                ->where('to_currency', $to)
                ->where('fetched_at', '>', now()->subHours(6))
                ->value('rate');

            if ($storedRate) {
                return (float) $storedRate;
            }

            // Fetch from API
            return $this->fetchExchangeRate($from, $to);
        });
    }

    /**
     * Fetch exchange rate from external API
     */
    protected function fetchExchangeRate(string $from, string $to): float
    {
        try {
            // Using exchangerate-api.com (free tier)
            $response = Http::timeout(10)->get("https://api.exchangerate-api.com/v4/latest/{$from}");

            if ($response->successful()) {
                $data = $response->json();
                $rate = $data['rates'][$to] ?? null;

                if ($rate) {
                    $this->storeRate($from, $to, $rate);
                    return (float) $rate;
                }
            }
        } catch (\Exception $e) {
            Log::error('Exchange rate fetch failed', [
                'from' => $from,
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback to stored rate or 1:1
        return DB::table('currency_rates')
            ->where('from_currency', $from)
            ->where('to_currency', $to)
            ->value('rate') ?? 1.0;
    }

    /**
     * Store exchange rate
     */
    protected function storeRate(string $from, string $to, float $rate): void
    {
        DB::table('currency_rates')->updateOrInsert(
            ['from_currency' => $from, 'to_currency' => $to],
            ['rate' => $rate, 'fetched_at' => now(), 'updated_at' => now()]
        );
    }

    /**
     * Update all exchange rates
     */
    public function updateAllRates(): array
    {
        $updated = [];

        try {
            $response = Http::timeout(10)->get("https://api.exchangerate-api.com/v4/latest/{$this->baseCurrency}");

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? [];

                foreach ($this->supportedCurrencies as $currency => $info) {
                    if (isset($rates[$currency])) {
                        $this->storeRate($this->baseCurrency, $currency, $rates[$currency]);
                        $updated[$currency] = $rates[$currency];

                        // Also store inverse rate
                        $inverseRate = 1 / $rates[$currency];
                        $this->storeRate($currency, $this->baseCurrency, $inverseRate);
                    }
                }

                // Clear cache
                foreach ($this->supportedCurrencies as $currency => $info) {
                    Cache::forget("exchange_rate:{$this->baseCurrency}:{$currency}");
                    Cache::forget("exchange_rate:{$currency}:{$this->baseCurrency}");
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to update exchange rates', ['error' => $e->getMessage()]);
        }

        return $updated;
    }

    /**
     * Format currency amount
     */
    public function format(float $amount, string $currency): string
    {
        $info = $this->supportedCurrencies[$currency] ?? ['symbol' => $currency];

        // Format based on currency
        if ($currency === 'BTC') {
            return $info['symbol'] . number_format($amount, 8);
        }

        return $info['symbol'] . number_format($amount, 2);
    }

    /**
     * Get user's preferred currency
     */
    public function getUserCurrency(?int $userId): string
    {
        if (!$userId) {
            return $this->baseCurrency;
        }

        return DB::table('users')
            ->where('id', $userId)
            ->value('preferred_currency') ?? $this->baseCurrency;
    }

    /**
     * Set user's preferred currency
     */
    public function setUserCurrency(int $userId, string $currency): bool
    {
        if (!isset($this->supportedCurrencies[$currency])) {
            return false;
        }

        return DB::table('users')
            ->where('id', $userId)
            ->update(['preferred_currency' => $currency]) > 0;
    }

    /**
     * Get prices in user's currency
     */
    public function convertPriceForUser(float $amount, int $userId): array
    {
        $userCurrency = $this->getUserCurrency($userId);
        $convertedAmount = $this->convert($amount, $userCurrency);

        return [
            'original' => [
                'amount' => $amount,
                'currency' => $this->baseCurrency,
                'formatted' => $this->format($amount, $this->baseCurrency),
            ],
            'converted' => [
                'amount' => $convertedAmount,
                'currency' => $userCurrency,
                'formatted' => $this->format($convertedAmount, $userCurrency),
            ],
        ];
    }

    /**
     * Get all current rates
     */
    public function getAllRates(): array
    {
        $rates = DB::table('currency_rates')
            ->where('from_currency', $this->baseCurrency)
            ->pluck('rate', 'to_currency')
            ->toArray();

        // Add base currency
        $rates[$this->baseCurrency] = 1.0;

        return $rates;
    }

    /**
     * Validate currency code
     */
    public function isValidCurrency(string $currency): bool
    {
        return isset($this->supportedCurrencies[$currency]);
    }
}
