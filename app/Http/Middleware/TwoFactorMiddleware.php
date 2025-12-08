<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     * Ensure user has completed 2FA verification if enabled.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if user is not authenticated or doesn't have 2FA enabled
        if (!$user || !$user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // Check if 2FA was verified in this session
        if (!session()->has('2fa_verified_at')) {
            // Store intended URL and redirect to 2FA challenge
            session([
                '2fa_user_id' => $user->id,
                '2fa_remember' => $request->boolean('remember'),
            ]);

            auth()->logout();

            return redirect()->route('two-factor.challenge');
        }

        // Optionally re-verify after certain time (e.g., 12 hours)
        $verifiedAt = session('2fa_verified_at');
        if ($verifiedAt && now()->diffInHours($verifiedAt) > 12) {
            session()->forget('2fa_verified_at');

            session([
                '2fa_user_id' => $user->id,
            ]);

            auth()->logout();

            return redirect()->route('two-factor.challenge')
                ->with('info', __('Please verify your identity again for security.'));
        }

        return $next($request);
    }
}
