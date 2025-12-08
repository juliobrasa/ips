<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_factor_settings_page_loads(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get('/two-factor');

        $response->assertOk();
    }

    public function test_user_can_view_enable_2fa_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get('/two-factor/enable');

        $response->assertOk()
            ->assertViewHas('secret')
            ->assertViewHas('qrCodeUrl');
    }

    public function test_user_with_2fa_cannot_access_enable_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
            'two_factor_secret' => encrypt('test-secret'),
        ]);

        $response = $this->actingAs($user)
            ->get('/two-factor/enable');

        $response->assertRedirect('/two-factor');
    }

    public function test_user_can_generate_recovery_codes(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $codes = $user->generateRecoveryCodes();

        $this->assertCount(8, $codes);
        $this->assertMatchesRegularExpression('/^[A-F0-9]{8}-[A-F0-9]{8}$/', $codes[0]);
    }

    public function test_user_can_use_recovery_code(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => ['CODE1-CODE1', 'CODE2-CODE2'],
        ]);

        $result = $user->useRecoveryCode('CODE1-CODE1');

        $this->assertTrue($result);
        $this->assertCount(1, $user->fresh()->getRecoveryCodes());
    }

    public function test_invalid_recovery_code_fails(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['CODE1-CODE1'],
        ]);

        $result = $user->useRecoveryCode('INVALID-CODE');

        $this->assertFalse($result);
    }

    public function test_user_can_disable_2fa(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => ['CODE1-CODE1'],
        ]);

        $response = $this->actingAs($user)
            ->delete('/two-factor', [
                'password' => 'password',
            ]);

        $response->assertRedirect('/two-factor');

        $user->refresh();
        $this->assertNull($user->two_factor_confirmed_at);
        $this->assertNull($user->two_factor_secret);
    }

    public function test_challenge_page_loads(): void
    {
        $user = User::factory()->create();

        session(['2fa_user_id' => $user->id]);

        $response = $this->get('/two-factor-challenge');

        $response->assertOk();
    }

    public function test_challenge_redirects_without_session(): void
    {
        $response = $this->get('/two-factor-challenge');

        $response->assertRedirect('/login');
    }
}
