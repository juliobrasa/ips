<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Subnet;
use App\Models\User;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutomationService
{
    public function __construct(
        protected NotificationService $notifications,
        protected IpQualityService $ipQuality
    ) {}

    /**
     * Process auto-renewals for expiring leases
     */
    public function processAutoRenewals(): array
    {
        $results = [
            'processed' => 0,
            'renewed' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        // Get leases expiring in the next 3 days with auto-renew enabled
        $leases = Lease::where('auto_renew', true)
            ->where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addDays(3)])
            ->with(['user', 'subnet'])
            ->get();

        foreach ($leases as $lease) {
            $results['processed']++;

            try {
                // Check if user has a valid payment method
                if (!$this->userHasValidPaymentMethod($lease->user)) {
                    $results['skipped']++;
                    $this->notifications->notifyUser($lease->user, 'auto_renew_failed', [
                        'lease_id' => $lease->id,
                        'reason' => 'No valid payment method',
                    ]);
                    continue;
                }

                // Attempt renewal
                $this->renewLease($lease);
                $results['renewed']++;

                $this->notifications->notifyUser($lease->user, 'lease_renewed', [
                    'lease_id' => $lease->id,
                    'subnet' => $lease->subnet->cidr,
                    'new_end_date' => $lease->ends_at->format('Y-m-d'),
                ]);

            } catch (\Exception $e) {
                $results['failed']++;
                Log::error('Auto-renewal failed', [
                    'lease_id' => $lease->id,
                    'error' => $e->getMessage(),
                ]);

                $this->notifications->notifyUser($lease->user, 'auto_renew_failed', [
                    'lease_id' => $lease->id,
                    'reason' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Process expiration reminders
     */
    public function processExpirationReminders(): array
    {
        $results = ['sent' => 0];

        $reminderDays = [30, 14, 7, 3, 1];

        foreach ($reminderDays as $days) {
            $leases = Lease::where('status', 'active')
                ->whereDate('ends_at', now()->addDays($days)->toDateString())
                ->whereNull("reminder_{$days}_sent_at")
                ->with(['user', 'subnet'])
                ->get();

            foreach ($leases as $lease) {
                $this->notifications->notifyUser($lease->user, 'lease_expiring', [
                    'lease_id' => $lease->id,
                    'subnet' => $lease->subnet->cidr,
                    'days_remaining' => $days,
                    'end_date' => $lease->ends_at->format('Y-m-d'),
                ]);

                $lease->update(["reminder_{$days}_sent_at" => now()]);
                $results['sent']++;
            }
        }

        return $results;
    }

    /**
     * Process auto-delisting for blacklisted IPs
     */
    public function processAutoDelisting(): array
    {
        $results = [
            'checked' => 0,
            'delisting_requested' => 0,
            'already_clean' => 0,
        ];

        // Get subnets marked for auto-delist
        $subnets = Subnet::where('auto_delist', true)
            ->where('status', 'available')
            ->where(function ($q) {
                $q->whereNull('last_delist_check')
                    ->orWhere('last_delist_check', '<', now()->subHours(24));
            })
            ->get();

        foreach ($subnets as $subnet) {
            $results['checked']++;

            // Check quality score
            $quality = $this->ipQuality->getSubnetScore($subnet->cidr);

            if ($quality['average_score'] >= 80) {
                $results['already_clean']++;
                $subnet->update(['last_delist_check' => now()]);
                continue;
            }

            // Check specific blacklists
            $parts = explode('/', $subnet->cidr);
            $networkIp = $parts[0];
            $blacklistResults = $this->ipQuality->checkBlacklists($networkIp);

            $listedOn = array_keys(array_filter($blacklistResults));

            if (empty($listedOn)) {
                $results['already_clean']++;
                $subnet->update(['last_delist_check' => now()]);
                continue;
            }

            // Create delisting requests
            foreach ($listedOn as $blacklist) {
                $this->createDelistingRequest($subnet, $blacklist);
            }

            $results['delisting_requested']++;
            $subnet->update(['last_delist_check' => now()]);
        }

        return $results;
    }

    /**
     * Process scheduled reputation checks
     */
    public function processReputationChecks(): array
    {
        $results = ['checked' => 0];

        $subnets = Subnet::where('status', 'leased')
            ->where(function ($q) {
                $q->whereNull('reputation_checked_at')
                    ->orWhere('reputation_checked_at', '<', now()->subDays(7));
            })
            ->limit(50)
            ->get();

        foreach ($subnets as $subnet) {
            try {
                $quality = $this->ipQuality->getSubnetScore($subnet->cidr);
                $this->ipQuality->storeScore($subnet, $quality);

                // Alert if score dropped significantly
                if ($quality['average_score'] < 60 && ($subnet->reputation_score ?? 100) >= 60) {
                    $this->notifications->notifyUser($subnet->user, 'reputation_alert', [
                        'subnet' => $subnet->cidr,
                        'new_score' => $quality['average_score'],
                        'old_score' => $subnet->reputation_score,
                    ]);
                }

                $results['checked']++;
            } catch (\Exception $e) {
                Log::error('Reputation check failed', [
                    'subnet_id' => $subnet->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Process expired leases
     */
    public function processExpiredLeases(): array
    {
        $results = ['expired' => 0];

        $leases = Lease::where('status', 'active')
            ->where('ends_at', '<', now())
            ->with(['user', 'subnet'])
            ->get();

        foreach ($leases as $lease) {
            DB::transaction(function () use ($lease) {
                $lease->update(['status' => 'expired']);
                $lease->subnet->update(['status' => 'available']);

                $this->notifications->notifyUser($lease->user, 'lease_expired', [
                    'lease_id' => $lease->id,
                    'subnet' => $lease->subnet->cidr,
                ]);
            });

            $results['expired']++;
        }

        return $results;
    }

    /**
     * Get automation rules for a user
     */
    public function getAutomationRules(User $user): array
    {
        return DB::table('automation_rules')
            ->where('user_id', $user->id)
            ->get()
            ->toArray();
    }

    /**
     * Save automation rule
     */
    public function saveRule(User $user, array $rule): int
    {
        return DB::table('automation_rules')->insertGetId([
            'user_id' => $user->id,
            'name' => $rule['name'],
            'trigger_type' => $rule['trigger_type'],
            'trigger_config' => json_encode($rule['trigger_config'] ?? []),
            'action_type' => $rule['action_type'],
            'action_config' => json_encode($rule['action_config'] ?? []),
            'is_active' => $rule['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Toggle automation rule
     */
    public function toggleRule(int $ruleId, bool $active): bool
    {
        return DB::table('automation_rules')
            ->where('id', $ruleId)
            ->update(['is_active' => $active, 'updated_at' => now()]) > 0;
    }

    /**
     * Check if user has valid payment method
     */
    protected function userHasValidPaymentMethod(User $user): bool
    {
        return DB::table('payment_methods')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Renew a lease
     */
    protected function renewLease(Lease $lease): void
    {
        $newEndDate = Carbon::parse($lease->ends_at)->addMonth();

        DB::transaction(function () use ($lease, $newEndDate) {
            $lease->update([
                'ends_at' => $newEndDate,
            ]);

            // Create invoice for renewal
            DB::table('invoices')->insert([
                'user_id' => $lease->user_id,
                'lease_id' => $lease->id,
                'type' => 'renewal',
                'amount' => $lease->monthly_price,
                'currency' => 'EUR',
                'status' => 'pending',
                'due_date' => now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Create delisting request
     */
    protected function createDelistingRequest(Subnet $subnet, string $blacklist): void
    {
        DB::table('delisting_requests')->insert([
            'subnet_id' => $subnet->id,
            'blacklist' => $blacklist,
            'status' => 'pending',
            'requested_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Run all scheduled automations
     */
    public function runAll(): array
    {
        return [
            'auto_renewals' => $this->processAutoRenewals(),
            'expiration_reminders' => $this->processExpirationReminders(),
            'expired_leases' => $this->processExpiredLeases(),
            'reputation_checks' => $this->processReputationChecks(),
            'auto_delisting' => $this->processAutoDelisting(),
        ];
    }
}
