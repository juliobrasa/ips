<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LookingGlassService
{
    /**
     * Available looking glass servers
     */
    protected array $servers = [
        'ripe-ris' => [
            'name' => 'RIPE RIS',
            'url' => 'https://stat.ripe.net/data/looking-glass/data.json',
            'type' => 'ripestat',
        ],
        'he' => [
            'name' => 'Hurricane Electric',
            'url' => 'https://lg.he.net/',
            'type' => 'html',
        ],
        'nlnog' => [
            'name' => 'NLNOG RING',
            'url' => 'https://ring.nlnog.net/api/1.0/',
            'type' => 'api',
        ],
    ];

    /**
     * Query BGP route from RIPE RIS Looking Glass
     */
    public function queryRipeRis(string $resource): array
    {
        $cacheKey = "lg_ripe_ris_{$resource}";

        return Cache::remember($cacheKey, 300, function () use ($resource) {
            try {
                $response = Http::timeout(30)->get('https://stat.ripe.net/data/looking-glass/data.json', [
                    'resource' => $resource,
                    'sourceapp' => config('ripe.stat.source_app', 'soltia-ips'),
                ]);

                if ($response->failed()) {
                    return ['error' => 'Failed to query RIPE RIS'];
                }

                $data = $response->json();

                return [
                    'source' => 'RIPE RIS',
                    'query_time' => $data['data']['query_time'] ?? now()->toIso8601String(),
                    'rrcs' => $this->parseRipeRisResponse($data['data']['rrcs'] ?? []),
                ];
            } catch (\Exception $e) {
                Log::error('RIPE RIS Looking Glass query failed', [
                    'resource' => $resource,
                    'error' => $e->getMessage(),
                ]);
                return ['error' => $e->getMessage()];
            }
        });
    }

    /**
     * Parse RIPE RIS response
     */
    protected function parseRipeRisResponse(array $rrcs): array
    {
        $result = [];

        foreach ($rrcs as $rrc) {
            $rrcId = $rrc['rrc'] ?? 'unknown';
            $location = $rrc['location'] ?? 'Unknown';
            $peers = [];

            foreach ($rrc['peers'] ?? [] as $peer) {
                $peers[] = [
                    'asn' => $peer['asn'] ?? null,
                    'prefix' => $peer['prefix'] ?? null,
                    'as_path' => $peer['as_path'] ?? [],
                    'community' => $peer['community'] ?? [],
                    'next_hop' => $peer['next_hop'] ?? null,
                ];
            }

            $result[] = [
                'rrc' => $rrcId,
                'location' => $location,
                'peers' => $peers,
                'peer_count' => count($peers),
            ];
        }

        return $result;
    }

    /**
     * Get BGP route visibility
     */
    public function getRouteVisibility(string $prefix): array
    {
        $cacheKey = "lg_visibility_{$prefix}";

        return Cache::remember($cacheKey, 300, function () use ($prefix) {
            try {
                $response = Http::timeout(30)->get('https://stat.ripe.net/data/routing-status/data.json', [
                    'resource' => $prefix,
                    'sourceapp' => config('ripe.stat.source_app', 'soltia-ips'),
                ]);

                if ($response->failed()) {
                    return ['error' => 'Failed to query route visibility'];
                }

                $data = $response->json()['data'] ?? [];

                return [
                    'prefix' => $prefix,
                    'visibility' => $data['visibility']['v4']['total_ris_peers'] ?? 0,
                    'first_seen' => $data['first_seen']['time'] ?? null,
                    'last_seen' => $data['last_seen']['time'] ?? null,
                    'announced' => $data['announced'] ?? false,
                    'origins' => $this->extractOrigins($data),
                ];
            } catch (\Exception $e) {
                Log::error('Route visibility query failed', [
                    'prefix' => $prefix,
                    'error' => $e->getMessage(),
                ]);
                return ['error' => $e->getMessage()];
            }
        });
    }

    /**
     * Extract origin ASNs from routing data
     */
    protected function extractOrigins(array $data): array
    {
        $origins = [];

        if (isset($data['first_seen']['origin'])) {
            $origins[] = [
                'asn' => $data['first_seen']['origin'],
                'type' => 'first_seen',
            ];
        }

        if (isset($data['origins'])) {
            foreach ($data['origins'] as $origin) {
                $origins[] = [
                    'asn' => $origin,
                    'type' => 'current',
                ];
            }
        }

        return $origins;
    }

    /**
     * Get AS path analysis
     */
    public function getAsPathAnalysis(string $prefix): array
    {
        $cacheKey = "lg_aspath_{$prefix}";

        return Cache::remember($cacheKey, 300, function () use ($prefix) {
            try {
                $response = Http::timeout(30)->get('https://stat.ripe.net/data/as-path-length/data.json', [
                    'resource' => $prefix,
                    'sourceapp' => config('ripe.stat.source_app', 'soltia-ips'),
                ]);

                if ($response->failed()) {
                    return ['error' => 'Failed to query AS path'];
                }

                $data = $response->json()['data'] ?? [];

                return [
                    'prefix' => $prefix,
                    'avg_path_length' => $data['avg'] ?? 0,
                    'min_path_length' => $data['min'] ?? 0,
                    'max_path_length' => $data['max'] ?? 0,
                    'distribution' => $data['distribution'] ?? [],
                ];
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        });
    }

    /**
     * Get BGP updates for a prefix
     */
    public function getBgpUpdates(string $prefix, ?string $startTime = null, ?string $endTime = null): array
    {
        $startTime = $startTime ?? now()->subHours(24)->toIso8601String();
        $endTime = $endTime ?? now()->toIso8601String();

        $cacheKey = "lg_updates_{$prefix}_" . md5($startTime . $endTime);

        return Cache::remember($cacheKey, 300, function () use ($prefix, $startTime, $endTime) {
            try {
                $response = Http::timeout(30)->get('https://stat.ripe.net/data/bgp-updates/data.json', [
                    'resource' => $prefix,
                    'starttime' => $startTime,
                    'endtime' => $endTime,
                    'sourceapp' => config('ripe.stat.source_app', 'soltia-ips'),
                ]);

                if ($response->failed()) {
                    return ['error' => 'Failed to query BGP updates'];
                }

                $data = $response->json()['data'] ?? [];

                return [
                    'prefix' => $prefix,
                    'period' => [
                        'start' => $startTime,
                        'end' => $endTime,
                    ],
                    'updates' => array_slice($data['updates'] ?? [], 0, 100),
                    'total_updates' => count($data['updates'] ?? []),
                    'withdrawals' => $this->countUpdateType($data['updates'] ?? [], 'W'),
                    'announcements' => $this->countUpdateType($data['updates'] ?? [], 'A'),
                ];
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        });
    }

    /**
     * Count BGP update types
     */
    protected function countUpdateType(array $updates, string $type): int
    {
        return count(array_filter($updates, fn($u) => ($u['type'] ?? '') === $type));
    }

    /**
     * Get prefix routing consistency
     */
    public function getRoutingConsistency(string $prefix): array
    {
        $cacheKey = "lg_consistency_{$prefix}";

        return Cache::remember($cacheKey, 600, function () use ($prefix) {
            try {
                $response = Http::timeout(30)->get('https://stat.ripe.net/data/prefix-routing-consistency/data.json', [
                    'resource' => $prefix,
                    'sourceapp' => config('ripe.stat.source_app', 'soltia-ips'),
                ]);

                if ($response->failed()) {
                    return ['error' => 'Failed to query routing consistency'];
                }

                $data = $response->json()['data'] ?? [];

                return [
                    'prefix' => $prefix,
                    'routes' => $data['routes'] ?? [],
                    'in_bgp' => $data['in_bgp'] ?? false,
                    'in_whois' => $data['in_whois'] ?? false,
                    'irr_sources' => $data['irr_sources'] ?? [],
                ];
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        });
    }

    /**
     * Query multiple sources for comprehensive view
     */
    public function getComprehensiveBgpView(string $prefix): array
    {
        return [
            'prefix' => $prefix,
            'timestamp' => now()->toIso8601String(),
            'visibility' => $this->getRouteVisibility($prefix),
            'looking_glass' => $this->queryRipeRis($prefix),
            'as_path' => $this->getAsPathAnalysis($prefix),
            'consistency' => $this->getRoutingConsistency($prefix),
        ];
    }

    /**
     * Get available looking glass servers
     */
    public function getAvailableServers(): array
    {
        return $this->servers;
    }

    /**
     * Check if prefix is being announced
     */
    public function isPrefixAnnounced(string $prefix): bool
    {
        $visibility = $this->getRouteVisibility($prefix);
        return ($visibility['visibility'] ?? 0) > 0;
    }

    /**
     * Get route origin validation status
     */
    public function getRpkiStatus(string $prefix): array
    {
        $cacheKey = "lg_rpki_{$prefix}";

        return Cache::remember($cacheKey, 600, function () use ($prefix) {
            try {
                $response = Http::timeout(30)->get('https://stat.ripe.net/data/rpki-validation/data.json', [
                    'resource' => $prefix,
                    'sourceapp' => config('ripe.stat.source_app', 'soltia-ips'),
                ]);

                if ($response->failed()) {
                    return ['error' => 'Failed to query RPKI status'];
                }

                $data = $response->json()['data'] ?? [];

                $validatedRoutes = $data['validating_roas'] ?? [];

                return [
                    'prefix' => $prefix,
                    'status' => $data['status'] ?? 'unknown',
                    'roas' => array_map(fn($roa) => [
                        'prefix' => $roa['prefix'] ?? null,
                        'asn' => $roa['origin'] ?? null,
                        'max_length' => $roa['max_length'] ?? null,
                        'validity' => $roa['validity'] ?? null,
                    ], $validatedRoutes),
                    'is_valid' => ($data['status'] ?? '') === 'valid',
                    'is_invalid' => ($data['status'] ?? '') === 'invalid',
                    'not_found' => ($data['status'] ?? '') === 'unknown',
                ];
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        });
    }
}
