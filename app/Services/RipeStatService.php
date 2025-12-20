<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RipeStatService
{
    protected string $baseUrl;
    protected string $sourceApp;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('ripe.stat.base_url', 'https://stat.ripe.net/data');
        $this->sourceApp = config('ripe.stat.source_app', 'soltia-ips');
        $this->timeout = config('ripe.stat.timeout', 30);
    }

    /**
     * Get network information for an IP address
     */
    public function getNetworkInfo(string $resource): array
    {
        return $this->query('network-info', $resource, [], 600);
    }

    /**
     * Get prefix overview (routing status)
     */
    public function getPrefixOverview(string $resource): array
    {
        return $this->query('prefix-overview', $resource, [], 600);
    }

    /**
     * Get abuse contact information
     */
    public function getAbuseContact(string $resource): array
    {
        return $this->query('abuse-contact-finder', $resource, [], 3600);
    }

    /**
     * Get WHOIS information
     */
    public function getWhois(string $resource): array
    {
        return $this->query('whois', $resource, [], 1800);
    }

    /**
     * Get geolocation data (MaxMind GeoLite)
     */
    public function getGeolocation(string $resource): array
    {
        return $this->query('maxmind-geo-lite', $resource, [], 86400);
    }

    /**
     * Get alternative geolocation data
     */
    public function getGeoloc(string $resource): array
    {
        return $this->query('geoloc', $resource, [], 86400);
    }

    /**
     * Get routing status
     */
    public function getRoutingStatus(string $resource): array
    {
        return $this->query('routing-status', $resource, [], 300);
    }

    /**
     * Get BGP state
     */
    public function getBgpState(string $resource): array
    {
        return $this->query('bgp-state', $resource, [], 300);
    }

    /**
     * Get announced prefixes for an ASN
     */
    public function getAnnouncedPrefixes(string $asn): array
    {
        return $this->query('announced-prefixes', $asn, [], 1800);
    }

    /**
     * Get AS overview
     */
    public function getAsOverview(string $asn): array
    {
        return $this->query('as-overview', $asn, [], 3600);
    }

    /**
     * Get address space hierarchy
     */
    public function getAddressHierarchy(string $resource): array
    {
        return $this->query('address-space-hierarchy', $resource, [], 3600);
    }

    /**
     * Get ASN neighbours
     */
    public function getAsnNeighbours(string $asn): array
    {
        return $this->query('asn-neighbours', $asn, [], 3600);
    }

    /**
     * Get historical WHOIS data
     */
    public function getHistoricalWhois(string $resource): array
    {
        return $this->query('historical-whois', $resource, [], 7200);
    }

    /**
     * Get reverse DNS information
     */
    public function getReverseDns(string $resource): array
    {
        return $this->query('reverse-dns', $resource, [], 3600);
    }

    /**
     * Get DNS chain
     */
    public function getDnsChain(string $resource): array
    {
        return $this->query('dns-chain', $resource, [], 1800);
    }

    /**
     * Get RIS peerings
     */
    public function getRisPeerings(string $resource): array
    {
        return $this->query('ris-peerings', $resource, [], 1800);
    }

    /**
     * Get RIR statistics by country
     */
    public function getRirStatsCountry(string $country): array
    {
        return $this->query('rir-stats-country', $country, [], 86400);
    }

    /**
     * Get comprehensive IP information (combines multiple endpoints)
     */
    public function getComprehensiveInfo(string $resource): array
    {
        $cacheKey = "ripestat_comprehensive_{$resource}";

        return Cache::remember($cacheKey, 600, function () use ($resource) {
            $results = [
                'resource' => $resource,
                'timestamp' => now()->toIso8601String(),
            ];

            // Network info
            try {
                $networkInfo = $this->getNetworkInfo($resource);
                $results['network'] = [
                    'prefix' => $networkInfo['data']['prefix'] ?? null,
                    'asn' => $networkInfo['data']['asns'][0] ?? null,
                ];
            } catch (\Exception $e) {
                $results['network'] = ['error' => $e->getMessage()];
            }

            // Geolocation
            try {
                $geoData = $this->getGeolocation($resource);
                if (isset($geoData['data']['located_resources'])) {
                    $location = $geoData['data']['located_resources'][0]['locations'][0] ?? null;
                    $results['geolocation'] = [
                        'country' => $location['country'] ?? null,
                        'city' => $location['city'] ?? null,
                        'latitude' => $location['latitude'] ?? null,
                        'longitude' => $location['longitude'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                $results['geolocation'] = ['error' => $e->getMessage()];
            }

            // Abuse contact
            try {
                $abuseData = $this->getAbuseContact($resource);
                $results['abuse_contact'] = [
                    'email' => $abuseData['data']['abuse_contacts'][0] ?? null,
                ];
            } catch (\Exception $e) {
                $results['abuse_contact'] = ['error' => $e->getMessage()];
            }

            // Routing status
            try {
                $routingData = $this->getRoutingStatus($resource);
                $results['routing'] = [
                    'visibility' => $routingData['data']['visibility']['v4']['total_ris_peers'] ?? 0,
                    'first_seen' => $routingData['data']['first_seen']['prefix'] ?? null,
                    'last_seen' => $routingData['data']['last_seen']['prefix'] ?? null,
                ];
            } catch (\Exception $e) {
                $results['routing'] = ['error' => $e->getMessage()];
            }

            // WHOIS
            try {
                $whoisData = $this->getWhois($resource);
                if (isset($whoisData['data']['records'])) {
                    $records = [];
                    foreach ($whoisData['data']['records'] as $recordSet) {
                        foreach ($recordSet as $record) {
                            $records[$record['key']] = $record['value'];
                        }
                    }
                    $results['whois'] = $records;
                }
            } catch (\Exception $e) {
                $results['whois'] = ['error' => $e->getMessage()];
            }

            return $results;
        });
    }

    /**
     * Get prefix routing consistency
     */
    public function getPrefixRoutingConsistency(string $resource): array
    {
        return $this->query('prefix-routing-consistency', $resource, [], 1800);
    }

    /**
     * Get AS routing consistency
     */
    public function getAsRoutingConsistency(string $asn): array
    {
        return $this->query('as-routing-consistency', $asn, [], 1800);
    }

    /**
     * Get BGP updates for a resource
     */
    public function getBgpUpdates(string $resource, ?string $startTime = null, ?string $endTime = null): array
    {
        $params = [];
        if ($startTime) {
            $params['starttime'] = $startTime;
        }
        if ($endTime) {
            $params['endtime'] = $endTime;
        }

        return $this->query('bgp-updates', $resource, $params, 300);
    }

    /**
     * Get allocation history
     */
    public function getAllocationHistory(string $resource): array
    {
        return $this->query('allocation-history', $resource, [], 86400);
    }

    /**
     * Get address space usage
     */
    public function getAddressSpaceUsage(string $resource): array
    {
        return $this->query('address-space-usage', $resource, [], 3600);
    }

    /**
     * Get IANA registry info
     */
    public function getIanaRegistryInfo(string $resource): array
    {
        return $this->query('iana-registry-info', $resource, [], 86400);
    }

    /**
     * Make a query to RIPEstat API
     */
    protected function query(string $endpoint, string $resource, array $params = [], int $cacheTtl = 300): array
    {
        $cacheKey = "ripestat_{$endpoint}_" . md5($resource . json_encode($params));

        return Cache::remember($cacheKey, $cacheTtl, function () use ($endpoint, $resource, $params) {
            $url = "{$this->baseUrl}/{$endpoint}/data.json";

            $queryParams = array_merge([
                'resource' => $resource,
                'sourceapp' => $this->sourceApp,
            ], $params);

            try {
                $response = Http::timeout($this->timeout)
                    ->get($url, $queryParams);

                if ($response->failed()) {
                    Log::error('RIPEstat API request failed', [
                        'endpoint' => $endpoint,
                        'resource' => $resource,
                        'status' => $response->status(),
                    ]);

                    throw new \Exception("RIPEstat API Error: HTTP {$response->status()}");
                }

                $data = $response->json();

                if (isset($data['status']) && $data['status'] === 'error') {
                    throw new \Exception("RIPEstat API Error: " . ($data['message'] ?? 'Unknown error'));
                }

                return $data;
            } catch (\Exception $e) {
                Log::error('RIPEstat API exception', [
                    'endpoint' => $endpoint,
                    'resource' => $resource,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Clear cache for a resource
     */
    public function clearCache(string $resource): void
    {
        $endpoints = array_keys(config('ripe.stat_endpoints', []));

        foreach ($endpoints as $endpoint) {
            Cache::forget("ripestat_{$endpoint}_" . md5($resource . '[]'));
        }

        Cache::forget("ripestat_comprehensive_{$resource}");
    }

    /**
     * Get available endpoints
     */
    public function getAvailableEndpoints(): array
    {
        return config('ripe.stat_endpoints', []);
    }

    /**
     * Validate a resource format
     */
    public function validateResource(string $resource): bool
    {
        // IP address
        if (filter_var($resource, FILTER_VALIDATE_IP)) {
            return true;
        }

        // IP prefix (CIDR notation)
        if (preg_match('/^(\d{1,3}\.){3}\d{1,3}\/\d{1,2}$/', $resource)) {
            return true;
        }

        // IPv6 prefix
        if (preg_match('/^[0-9a-fA-F:]+\/\d{1,3}$/', $resource)) {
            return true;
        }

        // ASN
        if (preg_match('/^AS?\d+$/i', $resource)) {
            return true;
        }

        // Country code
        if (preg_match('/^[A-Z]{2}$/i', $resource)) {
            return true;
        }

        return false;
    }
}
