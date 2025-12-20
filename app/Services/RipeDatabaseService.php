<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;

class RipeDatabaseService
{
    protected string $baseUrl;
    protected string $source;
    protected int $timeout;

    public function __construct()
    {
        $useTestDb = config('ripe.database.use_test_db', false);
        $this->baseUrl = $useTestDb
            ? config('ripe.database.test_url')
            : config('ripe.database.base_url');
        $this->source = config('ripe.database.source', 'ripe');
        $this->timeout = config('ripe.database.timeout', 30);
    }

    /**
     * Get an object from the RIPE database
     */
    public function getObject(string $objectType, string $key, ?string $apiKey = null): array
    {
        $cacheKey = "ripe_object_{$objectType}_{$key}";

        return Cache::remember($cacheKey, 300, function () use ($objectType, $key, $apiKey) {
            $url = "{$this->baseUrl}/{$this->source}/{$objectType}/{$key}";

            $response = $this->makeRequest('GET', $url, [], $apiKey);

            return $this->parseResponse($response);
        });
    }

    /**
     * Search for objects in the RIPE database
     */
    public function search(string $query, array $options = [], ?string $apiKey = null): array
    {
        $url = "{$this->baseUrl}/search";

        $params = array_merge([
            'query-string' => $query,
            'source' => $this->source,
        ], $options);

        $response = $this->makeRequest('GET', $url, $params, $apiKey);

        return $this->parseResponse($response);
    }

    /**
     * Create a new object in the RIPE database
     */
    public function createObject(string $objectType, array $attributes, string $apiKey, ?string $password = null): array
    {
        $url = "{$this->baseUrl}/{$this->source}/{$objectType}";

        $body = $this->buildObjectPayload($objectType, $attributes);

        $queryParams = [];
        if ($password) {
            $queryParams['password'] = $password;
        }

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $response = $this->makeRequest('POST', $url, [], $apiKey, $body);

        // Clear cache for this object type
        $this->clearObjectCache($objectType, $attributes);

        return $this->parseResponse($response);
    }

    /**
     * Update an existing object in the RIPE database
     */
    public function updateObject(string $objectType, string $key, array $attributes, string $apiKey, ?string $password = null): array
    {
        $url = "{$this->baseUrl}/{$this->source}/{$objectType}/{$key}";

        $body = $this->buildObjectPayload($objectType, $attributes);

        $queryParams = [];
        if ($password) {
            $queryParams['password'] = $password;
        }

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $response = $this->makeRequest('PUT', $url, [], $apiKey, $body);

        // Clear cache
        Cache::forget("ripe_object_{$objectType}_{$key}");

        return $this->parseResponse($response);
    }

    /**
     * Delete an object from the RIPE database
     */
    public function deleteObject(string $objectType, string $key, string $apiKey, ?string $password = null): bool
    {
        $url = "{$this->baseUrl}/{$this->source}/{$objectType}/{$key}";

        $queryParams = [];
        if ($password) {
            $queryParams['password'] = $password;
        }

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        try {
            $this->makeRequest('DELETE', $url, [], $apiKey);

            // Clear cache
            Cache::forget("ripe_object_{$objectType}_{$key}");

            return true;
        } catch (\Exception $e) {
            Log::error('RIPE delete failed', [
                'object_type' => $objectType,
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get inetnum object for an IP range
     */
    public function getInetnum(string $range, ?string $apiKey = null): array
    {
        return $this->getObject('inetnum', $range, $apiKey);
    }

    /**
     * Get route object
     */
    public function getRoute(string $prefix, string $origin, ?string $apiKey = null): array
    {
        // Route key format: prefix + origin (e.g., "193.0.22.0/23AS3333")
        $key = str_replace('/', '', $prefix) . $origin;
        return $this->getObject('route', $key, $apiKey);
    }

    /**
     * Create or update an inetnum object
     */
    public function upsertInetnum(array $data, string $apiKey, ?string $password = null): array
    {
        $attributes = $this->buildInetnumAttributes($data);
        $key = $data['inetnum'];

        try {
            // Try to get existing object
            $existing = $this->getObject('inetnum', $key, $apiKey);

            if (!empty($existing['objects'])) {
                return $this->updateObject('inetnum', $key, $attributes, $apiKey, $password);
            }
        } catch (\Exception $e) {
            // Object doesn't exist, create it
        }

        return $this->createObject('inetnum', $attributes, $apiKey, $password);
    }

    /**
     * Create or update a route object
     */
    public function upsertRoute(array $data, string $apiKey, ?string $password = null): array
    {
        $attributes = $this->buildRouteAttributes($data);
        $prefix = $data['route'];
        $origin = $data['origin'];

        try {
            $existing = $this->getRoute($prefix, $origin, $apiKey);

            if (!empty($existing['objects'])) {
                $key = str_replace('/', '', $prefix) . $origin;
                return $this->updateObject('route', $key, $attributes, $apiKey, $password);
            }
        } catch (\Exception $e) {
            // Object doesn't exist, create it
        }

        return $this->createObject('route', $attributes, $apiKey, $password);
    }

    /**
     * Get all route objects for a prefix
     */
    public function getRoutesForPrefix(string $prefix, ?string $apiKey = null): array
    {
        return $this->search($prefix, [
            'type-filter' => 'route,route6',
            'flags' => 'no-filtering',
        ], $apiKey);
    }

    /**
     * Get person/role contact
     */
    public function getContact(string $nicHdl, ?string $apiKey = null): array
    {
        try {
            return $this->getObject('person', $nicHdl, $apiKey);
        } catch (\Exception $e) {
            // Try role if person not found
            return $this->getObject('role', $nicHdl, $apiKey);
        }
    }

    /**
     * Get organisation
     */
    public function getOrganisation(string $orgId, ?string $apiKey = null): array
    {
        return $this->getObject('organisation', $orgId, $apiKey);
    }

    /**
     * Get maintainer
     */
    public function getMaintainer(string $mntner, ?string $apiKey = null): array
    {
        return $this->getObject('mntner', $mntner, $apiKey);
    }

    /**
     * Validate API key by attempting to fetch a known object
     */
    public function validateApiKey(string $apiKey): bool
    {
        try {
            // Try to fetch RIPE's own allocation as a test
            $response = Http::timeout($this->timeout)
                ->withBasicAuth($apiKey, '')
                ->accept('application/json')
                ->get("{$this->baseUrl}/{$this->source}/inetnum/193.0.0.0 - 193.0.7.255");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Build object payload for RIPE API
     */
    protected function buildObjectPayload(string $objectType, array $attributes): array
    {
        $attributeList = [];

        foreach ($attributes as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $attributeList[] = ['name' => $name, 'value' => $v];
                }
            } else {
                $attributeList[] = ['name' => $name, 'value' => $value];
            }
        }

        return [
            'objects' => [
                'object' => [
                    [
                        'type' => $objectType,
                        'source' => ['id' => $this->source],
                        'attributes' => [
                            'attribute' => $attributeList,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Build inetnum attributes from data
     */
    protected function buildInetnumAttributes(array $data): array
    {
        $attrs = [
            'inetnum' => $data['inetnum'],
            'netname' => $data['netname'],
            'country' => $data['country'],
            'admin-c' => $data['admin_c'],
            'tech-c' => $data['tech_c'],
            'status' => $data['status'] ?? 'ASSIGNED PA',
            'mnt-by' => $data['mnt_by'],
            'source' => strtoupper($this->source),
        ];

        // Optional attributes
        if (!empty($data['descr'])) {
            $attrs['descr'] = $data['descr'];
        }
        if (!empty($data['org'])) {
            $attrs['org'] = $data['org'];
        }
        if (!empty($data['remarks'])) {
            $attrs['remarks'] = $data['remarks'];
        }
        if (!empty($data['notify'])) {
            $attrs['notify'] = $data['notify'];
        }
        if (!empty($data['mnt_lower'])) {
            $attrs['mnt-lower'] = $data['mnt_lower'];
        }
        if (!empty($data['mnt_routes'])) {
            $attrs['mnt-routes'] = $data['mnt_routes'];
        }
        if (!empty($data['geoloc'])) {
            $attrs['geoloc'] = $data['geoloc'];
        }
        if (!empty($data['language'])) {
            $attrs['language'] = $data['language'];
        }

        return $attrs;
    }

    /**
     * Build route attributes from data
     */
    protected function buildRouteAttributes(array $data): array
    {
        $attrs = [
            'route' => $data['route'],
            'origin' => $data['origin'],
            'mnt-by' => $data['mnt_by'],
            'source' => strtoupper($this->source),
        ];

        // Optional attributes
        if (!empty($data['descr'])) {
            $attrs['descr'] = $data['descr'];
        }
        if (!empty($data['remarks'])) {
            $attrs['remarks'] = $data['remarks'];
        }
        if (!empty($data['notify'])) {
            $attrs['notify'] = $data['notify'];
        }
        if (!empty($data['member_of'])) {
            $attrs['member-of'] = $data['member_of'];
        }
        if (!empty($data['holes'])) {
            $attrs['holes'] = $data['holes'];
        }
        if (!empty($data['pingable'])) {
            $attrs['pingable'] = $data['pingable'];
        }

        return $attrs;
    }

    /**
     * Make HTTP request to RIPE API
     */
    protected function makeRequest(string $method, string $url, array $params = [], ?string $apiKey = null, ?array $body = null): array
    {
        $request = Http::timeout($this->timeout)
            ->accept('application/json');

        if ($apiKey) {
            $request->withBasicAuth($apiKey, '');
        }

        if ($body) {
            $request->contentType('application/json');
        }

        try {
            $response = match (strtoupper($method)) {
                'GET' => $request->get($url, $params),
                'POST' => $request->post($url, $body),
                'PUT' => $request->put($url, $body),
                'DELETE' => $request->delete($url),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };

            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMessage = $this->extractErrorMessage($errorBody);

                Log::error('RIPE API request failed', [
                    'method' => $method,
                    'url' => $url,
                    'status' => $response->status(),
                    'error' => $errorMessage,
                ]);

                throw new \Exception("RIPE API Error: {$errorMessage}");
            }

            return $response->json() ?? [];
        } catch (RequestException $e) {
            Log::error('RIPE API request exception', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Parse RIPE API response
     */
    protected function parseResponse(array $response): array
    {
        if (isset($response['objects']['object'])) {
            $objects = [];
            foreach ($response['objects']['object'] as $obj) {
                $parsed = [
                    'type' => $obj['type'] ?? null,
                    'attributes' => [],
                ];

                if (isset($obj['attributes']['attribute'])) {
                    foreach ($obj['attributes']['attribute'] as $attr) {
                        $name = $attr['name'];
                        $value = $attr['value'] ?? null;

                        if (isset($parsed['attributes'][$name])) {
                            if (!is_array($parsed['attributes'][$name])) {
                                $parsed['attributes'][$name] = [$parsed['attributes'][$name]];
                            }
                            $parsed['attributes'][$name][] = $value;
                        } else {
                            $parsed['attributes'][$name] = $value;
                        }
                    }
                }

                $objects[] = $parsed;
            }

            return ['objects' => $objects, 'raw' => $response];
        }

        return ['objects' => [], 'raw' => $response];
    }

    /**
     * Extract error message from RIPE API response
     */
    protected function extractErrorMessage(array $response): string
    {
        if (isset($response['errormessages']['errormessage'])) {
            $messages = [];
            foreach ($response['errormessages']['errormessage'] as $error) {
                $messages[] = $error['text'] ?? 'Unknown error';
            }
            return implode('; ', $messages);
        }

        return 'Unknown RIPE API error';
    }

    /**
     * Clear cache for object
     */
    protected function clearObjectCache(string $objectType, array $attributes): void
    {
        $keyAttribute = match ($objectType) {
            'inetnum' => 'inetnum',
            'inet6num' => 'inet6num',
            'route' => 'route',
            'route6' => 'route6',
            'person', 'role' => 'nic-hdl',
            'organisation' => 'organisation',
            'mntner' => 'mntner',
            'aut-num' => 'aut-num',
            default => null,
        };

        if ($keyAttribute && isset($attributes[$keyAttribute])) {
            Cache::forget("ripe_object_{$objectType}_{$attributes[$keyAttribute]}");
        }
    }
}
