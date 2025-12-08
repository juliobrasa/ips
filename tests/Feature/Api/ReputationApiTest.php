<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\Subnet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReputationApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_can_check_single_ip_reputation(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/reputation/check/8.8.8.8');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'ip',
                    'score',
                    'status',
                    'can_list',
                    'blocklists' => [
                        'count',
                        'total_checked',
                        'details',
                    ],
                    'recommendation',
                    'checked_at',
                ],
            ]);
    }

    public function test_invalid_ip_returns_error(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/reputation/check/invalid-ip');

        $response->assertUnprocessable();
    }

    public function test_can_list_available_blocklists(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/reputation/blocklists');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'domain',
                        'weight',
                        'severity',
                        'description',
                    ],
                ],
                'total',
                'max_score',
                'threshold_clean',
                'threshold_listable',
            ]);
    }

    public function test_can_check_subnet_reputation(): void
    {
        $company = Company::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'holder',
            'kyc_status' => 'approved',
        ]);

        $subnet = Subnet::factory()->create([
            'company_id' => $company->id,
            'ip_address' => '8.8.8.0',
            'cidr' => 24,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/reputation/subnet/{$subnet->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'subnet',
                'average_score',
                'samples_checked',
                'total_ips',
                'is_clean',
                'can_list',
                'sample_results',
            ]);
    }

    public function test_admin_can_run_bulk_check(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $company = Company::factory()->create([
            'user_id' => $admin->id,
            'type' => 'holder',
        ]);

        $subnets = Subnet::factory()->count(3)->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/reputation/bulk-check', [
                'subnet_ids' => $subnets->pluck('id')->toArray(),
            ]);

        $response->assertOk()
            ->assertJson([
                'count' => 3,
            ]);
    }

    public function test_non_admin_cannot_run_bulk_check(): void
    {
        $company = Company::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'holder',
        ]);

        $subnets = Subnet::factory()->count(2)->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/admin/reputation/bulk-check', [
                'subnet_ids' => $subnets->pluck('id')->toArray(),
            ]);

        $response->assertForbidden();
    }
}
