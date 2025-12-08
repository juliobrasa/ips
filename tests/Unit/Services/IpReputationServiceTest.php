<?php

namespace Tests\Unit\Services;

use App\Services\IpReputationService;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class IpReputationServiceTest extends TestCase
{
    protected IpReputationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new IpReputationService();
        Cache::flush();
    }

    public function test_check_reputation_returns_expected_structure(): void
    {
        $result = $this->service->checkReputation('8.8.8.8');

        $this->assertArrayHasKey('ip', $result);
        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('blocked_count', $result);
        $this->assertArrayHasKey('total_checked', $result);
        $this->assertArrayHasKey('blocklists', $result);
        $this->assertArrayHasKey('can_list', $result);

        $this->assertEquals('8.8.8.8', $result['ip']);
        $this->assertIsInt($result['score']);
        $this->assertGreaterThanOrEqual(0, $result['score']);
        $this->assertLessThanOrEqual(100, $result['score']);
    }

    public function test_score_calculation_is_correct(): void
    {
        // Test with Google DNS which should be clean
        $result = $this->service->checkReputation('8.8.8.8');

        // A clean IP should have high score
        $this->assertGreaterThanOrEqual(70, $result['score']);
    }

    public function test_results_are_cached(): void
    {
        $ip = '1.1.1.1';

        // First call
        $result1 = $this->service->checkReputation($ip);

        // Second call should use cache
        $result2 = $this->service->checkReputation($ip);

        $this->assertEquals($result1, $result2);
    }

    public function test_clear_cache_works(): void
    {
        $ip = '1.1.1.1';

        $this->service->checkReputation($ip);
        $this->assertTrue(Cache::has("ip_reputation_{$ip}"));

        $this->service->clearCache($ip);
        $this->assertFalse(Cache::has("ip_reputation_{$ip}"));
    }

    public function test_get_blocklist_details_returns_array(): void
    {
        $result = $this->service->getBlocklistDetails('8.8.8.8');

        $this->assertIsArray($result);
    }

    public function test_summary_report_contains_all_data(): void
    {
        $result = $this->service->getSummaryReport('8.8.8.8');

        $this->assertArrayHasKey('ip', $result);
        $this->assertArrayHasKey('overall_score', $result);
        $this->assertArrayHasKey('overall_status', $result);
        $this->assertArrayHasKey('blocklist_count', $result);
        $this->assertArrayHasKey('blocklist_details', $result);
        $this->assertArrayHasKey('recommendation', $result);
        $this->assertArrayHasKey('can_be_listed', $result);
    }

    public function test_subnet_reputation_check(): void
    {
        $result = $this->service->checkSubnetReputation('8.8.8.0', 24);

        $this->assertArrayHasKey('subnet', $result);
        $this->assertArrayHasKey('average_score', $result);
        $this->assertArrayHasKey('samples_checked', $result);
        $this->assertArrayHasKey('total_ips', $result);
        $this->assertArrayHasKey('is_clean', $result);
        $this->assertArrayHasKey('can_list', $result);

        $this->assertEquals('8.8.8.0/24', $result['subnet']);
        $this->assertEquals(256, $result['total_ips']);
    }
}
