<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA settings page
     */
    public function index(): View
    {
        $user = auth()->user();

        return view('profile.two-factor', [
            'enabled' => $user->hasTwoFactorEnabled(),
            'recoveryCodes' => $user->hasTwoFactorEnabled() ? $user->getRecoveryCodes() : [],
        ]);
    }

    /**
     * Enable 2FA - Step 1: Generate secret
     */
    public function enable(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.index')
                ->with('info', __('Two-factor authentication is already enabled.'));
        }

        // Generate new secret
        $secret = $this->google2fa->generateSecretKey(32);

        // Store temporarily in session
        session(['2fa_secret' => $secret]);

        // Generate QR code URL
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('profile.two-factor-enable', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Confirm 2FA setup with code verification
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $secret = session('2fa_secret');

        if (!$secret) {
            return redirect()->route('two-factor.enable')
                ->with('error', __('Session expired. Please try again.'));
        }

        // Verify the code
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->with('error', __('Invalid verification code. Please try again.'));
        }

        // Enable 2FA
        $user->enableTwoFactor($secret);
        $user->confirmTwoFactor();

        // Clear session
        session()->forget('2fa_secret');

        return redirect()->route('two-factor.index')
            ->with('success', __('Two-factor authentication has been enabled.'));
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $user->disableTwoFactor();

        return redirect()->route('two-factor.index')
            ->with('success', __('Two-factor authentication has been disabled.'));
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.index')
                ->with('error', __('Two-factor authentication is not enabled.'));
        }

        $user->regenerateRecoveryCodes();

        return redirect()->route('two-factor.index')
            ->with('success', __('Recovery codes have been regenerated.'));
    }

    /**
     * Show 2FA challenge during login
     */
    public function challenge(): View|RedirectResponse
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'nullable|string|size:6',
            'recovery_code' => 'nullable|string',
        ]);

        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->with('error', __('Session expired. Please login again.'));
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        // Try verification code
        if ($request->filled('code')) {
            $secret = $user->getTwoFactorSecret();
            $valid = $this->google2fa->verifyKey($secret, $request->code);

            if ($valid) {
                return $this->completeLogin($user);
            }

            return back()->with('error', __('Invalid verification code.'));
        }

        // Try recovery code
        if ($request->filled('recovery_code')) {
            if ($user->useRecoveryCode($request->recovery_code)) {
                return $this->completeLogin($user);
            }

            return back()->with('error', __('Invalid recovery code.'));
        }

        return back()->with('error', __('Please enter a verification code or recovery code.'));
    }

    /**
     * Complete the login process after 2FA verification
     */
    protected function completeLogin($user): RedirectResponse
    {
        auth()->login($user, session('2fa_remember', false));

        session()->forget(['2fa_user_id', '2fa_remember']);

        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'))
            ->with('success', __('Login successful.'));
    }
}
