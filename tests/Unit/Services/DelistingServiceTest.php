<?php

namespace Tests\Unit\Services;

use App\Services\DelistingService;
use Tests\TestCase;

class DelistingServiceTest extends TestCase
{
    protected DelistingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DelistingService();
    }

    public function test_get_all_blocklists_returns_array(): void
    {
        $blocklists = $this->service->getAllBlocklists();

        $this->assertIsArray($blocklists);
        $this->assertNotEmpty($blocklists);
    }

    public function test_get_delisting_url_returns_valid_url(): void
    {
        $url = $this->service->getDelistingUrl('zen.spamhaus.org');

        $this->assertNotNull($url);
        $this->assertStringStartsWith('https://', $url);
    }

    public function test_get_delisting_url_returns_null_for_unknown(): void
    {
        $url = $this->service->getDelistingUrl('unknown.blocklist.org');

        $this->assertNull($url);
    }

    public function test_get_blocklist_info_returns_expected_structure(): void
    {
        $info = $this->service->getBlocklistInfo('zen.spamhaus.org');

        $this->assertNotNull($info);
        $this->assertArrayHasKey('name', $info);
        $this->assertArrayHasKey('delisting_url', $info);
        $this->assertArrayHasKey('delisting_method', $info);
        $this->assertArrayHasKey('instructions', $info);
    }

    public function test_request_delisting_returns_expected_structure(): void
    {
        $result = $this->service->requestDelisting('8.8.8.8', 'zen.spamhaus.org');

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
    }

    public function test_request_delisting_for_unknown_blocklist(): void
    {
        $result = $this->service->requestDelisting('8.8.8.8', 'unknown.blocklist.org');

        $this->assertEquals('unknown', $result['status']);
    }

    public function test_get_delisting_instructions_returns_string(): void
    {
        $instructions = $this->service->getDelistingInstructions('zen.spamhaus.org');

        $this->assertIsString($instructions);
        $this->assertNotEmpty($instructions);
    }

    public function test_check_if_still_listed_returns_boolean(): void
    {
        // Google DNS should not be listed
        $isListed = $this->service->checkIfStillListed('8.8.8.8', 'zen.spamhaus.org');

        $this->assertIsBool($isListed);
    }
}
