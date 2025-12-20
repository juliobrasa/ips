<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ReferralController extends Controller
{
    public function __construct(protected ReferralService $referrals)
    {
    }

    /**
     * Show referral dashboard
     */
    public function index(): View
    {
        $user = auth()->user();

        $stats = $this->referrals->getReferrerStats($user);
        $referred = $this->referrals->getReferredUsers($user);
        $pendingRewards = $this->referrals->getPendingRewards($user);
        $monthlyStats = $this->referrals->getMonthlyStats($user, 6);

        return view('referrals.index', [
            'stats' => $stats,
            'referred' => $referred,
            'pendingRewards' => $pendingRewards,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    /**
     * Get referral link
     */
    public function getLink(): JsonResponse
    {
        $user = auth()->user();
        $link = $this->referrals->getReferralLink($user);

        return response()->json([
            'link' => $link,
            'code' => $user->referral_code,
        ]);
    }

    /**
     * Validate referral code
     */
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        return response()->json(
            $this->referrals->validateCode($request->code)
        );
    }

    /**
     * Get leaderboard
     */
    public function leaderboard(): JsonResponse
    {
        return response()->json([
            'leaderboard' => $this->referrals->getLeaderboard(10),
        ]);
    }

    /**
     * Request payout
     */
    public function requestPayout(Request $request): JsonResponse
    {
        $request->validate([
            'reward_ids' => 'required|array',
            'reward_ids.*' => 'integer|exists:referral_rewards,id',
        ]);

        $result = $this->referrals->processRewardPayout(auth()->user(), $request->reward_ids);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Track referral link visit
     */
    public function track(string $code): \Illuminate\Http\RedirectResponse
    {
        // Store referral code in session
        session(['referral_code' => $code]);

        // Redirect to registration page
        return redirect()->route('register');
    }

    /**
     * Show earnings history
     */
    public function earnings(): View
    {
        $user = auth()->user();
        $rewards = $this->referrals->getAllRewards($user);
        $stats = $this->referrals->getReferrerStats($user);

        return view('referrals.earnings', [
            'rewards' => $rewards,
            'stats' => $stats,
        ]);
    }
}
