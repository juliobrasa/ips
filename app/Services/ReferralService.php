<?php

namespace App\Services;

use App\Models\User;
use App\Models\Referral;
use App\Models\ReferralReward;
use Illuminate\Support\Str;

class ReferralService
{
    /**
     * Commission rate (percentage)
     */
    protected float $commissionRate;

    /**
     * Cookie lifetime in days
     */
    protected int $cookieLifetime;

    public function __construct()
    {
        $this->commissionRate = config('referral.commission_rate', 10);
        $this->cookieLifetime = config('referral.cookie_lifetime', 30);
    }

    /**
     * Generate referral code for user
     */
    public function generateReferralCode(User $user): string
    {
        if ($user->referral_code) {
            return $user->referral_code;
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        $user->update(['referral_code' => $code]);

        return $code;
    }

    /**
     * Get referral link for user
     */
    public function getReferralLink(User $user): string
    {
        $code = $this->generateReferralCode($user);
        return url('/register?ref=' . $code);
    }

    /**
     * Track referral from code
     */
    public function trackReferral(string $code): ?User
    {
        return User::where('referral_code', $code)->first();
    }

    /**
     * Create referral relationship
     */
    public function createReferral(User $referrer, User $referred): Referral
    {
        return Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $referred->id,
            'status' => 'pending',
            'commission_rate' => $this->commissionRate,
        ]);
    }

    /**
     * Process commission for a payment
     */
    public function processCommission(User $user, float $amount, string $source, ?int $sourceId = null): ?ReferralReward
    {
        $referral = Referral::where('referred_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$referral) {
            return null;
        }

        $commission = $amount * ($referral->commission_rate / 100);

        if ($commission <= 0) {
            return null;
        }

        $reward = ReferralReward::create([
            'referral_id' => $referral->id,
            'referrer_id' => $referral->referrer_id,
            'amount' => $commission,
            'source_type' => $source,
            'source_id' => $sourceId,
            'status' => 'pending',
        ]);

        // Update referral stats
        $referral->increment('total_earnings', $commission);
        $referral->increment('total_orders');

        return $reward;
    }

    /**
     * Activate referral (after referred user makes first purchase)
     */
    public function activateReferral(User $referred): bool
    {
        $referral = Referral::where('referred_id', $referred->id)
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return false;
        }

        $referral->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);

        return true;
    }

    /**
     * Get referrer stats
     */
    public function getReferrerStats(User $user): array
    {
        $referrals = Referral::where('referrer_id', $user->id)->get();

        $pending = $referrals->where('status', 'pending')->count();
        $active = $referrals->where('status', 'active')->count();
        $totalEarnings = $referrals->sum('total_earnings');
        $totalOrders = $referrals->sum('total_orders');

        $pendingRewards = ReferralReward::where('referrer_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $paidRewards = ReferralReward::where('referrer_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        return [
            'referral_code' => $user->referral_code ?? $this->generateReferralCode($user),
            'referral_link' => $this->getReferralLink($user),
            'total_referrals' => $referrals->count(),
            'pending_referrals' => $pending,
            'active_referrals' => $active,
            'total_earnings' => $totalEarnings,
            'pending_earnings' => $pendingRewards,
            'paid_earnings' => $paidRewards,
            'total_orders' => $totalOrders,
            'commission_rate' => $this->commissionRate,
        ];
    }

    /**
     * Get referred users
     */
    public function getReferredUsers(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Referral::where('referrer_id', $user->id)
            ->with('referred:id,name,email,created_at')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get pending rewards for payout
     */
    public function getPendingRewards(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return ReferralReward::where('referrer_id', $user->id)
            ->where('status', 'pending')
            ->with('referral.referred:id,name')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Process payout for rewards
     */
    public function processRewardPayout(User $user, array $rewardIds): array
    {
        $rewards = ReferralReward::where('referrer_id', $user->id)
            ->whereIn('id', $rewardIds)
            ->where('status', 'pending')
            ->get();

        if ($rewards->isEmpty()) {
            return ['success' => false, 'message' => 'No pending rewards found'];
        }

        $totalAmount = $rewards->sum('amount');

        // Mark rewards as paid
        ReferralReward::whereIn('id', $rewards->pluck('id'))
            ->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

        return [
            'success' => true,
            'amount' => $totalAmount,
            'rewards_count' => $rewards->count(),
        ];
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(int $limit = 10): \Illuminate\Support\Collection
    {
        return Referral::select('referrer_id')
            ->selectRaw('COUNT(*) as referral_count')
            ->selectRaw('SUM(total_earnings) as total_earnings')
            ->where('status', 'active')
            ->groupBy('referrer_id')
            ->orderByDesc('total_earnings')
            ->limit($limit)
            ->with('referrer:id,name')
            ->get()
            ->map(fn($row) => [
                'user' => $row->referrer?->name ?? 'Anonymous',
                'referrals' => $row->referral_count,
                'earnings' => $row->total_earnings,
            ]);
    }

    /**
     * Get monthly stats for a referrer
     */
    public function getMonthlyStats(User $user, int $months = 6): array
    {
        $stats = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $rewards = ReferralReward::where('referrer_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->get();

            $stats[] = [
                'month' => $date->format('M Y'),
                'earnings' => $rewards->sum('amount'),
                'orders' => $rewards->count(),
            ];
        }

        return $stats;
    }

    /**
     * Validate referral code
     */
    public function validateCode(string $code): array
    {
        $referrer = User::where('referral_code', $code)->first();

        if (!$referrer) {
            return ['valid' => false, 'message' => 'Invalid referral code'];
        }

        return [
            'valid' => true,
            'referrer_id' => $referrer->id,
            'referrer_name' => $referrer->name,
            'commission_rate' => $this->commissionRate,
        ];
    }
}
