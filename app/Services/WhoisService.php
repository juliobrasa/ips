<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhoisService
{
    protected array $rirWhoisServers = [
        'RIPE' => 'whois.ripe.net',
        'ARIN' => 'whois.arin.net',
        'APNIC' => 'whois.apnic.net',
        'LACNIC' => 'whois.lacnic.net',
        'AFRINIC' => 'whois.afrinic.net',
    ];

    /**
     * Query WHOIS for an IP address
     */
    public function query(string $ip, ?string $rir = null): ?array
    {
        $cacheKey = "whois_{$ip}";

        return Cache::remember($cacheKey, 3600, function () use ($ip, $rir) {
            // Try RIR-specific server first if provided
            if ($rir && isset($this->rirWhoisServers[$rir])) {
                $result = $this->queryWhoisServer($this->rirWhoisServers[$rir], $ip);
                if ($result) {
                    return $this->parseWhoisResponse($result, $rir);
                }
            }

            // Fallback to automatic detection via RDAP
            return $this->queryRdap($ip);
        });
    }

    /**
     * Get the abuse contact email for an IP
     */
    public function getAbuseContact(string $ip, ?string $rir = null): ?string
    {
        $whois = $this->query($ip, $rir);
        return $whois['abuse_email'] ?? null;
    }

    /**
     * Get the organization/company that owns the IP
     */
    public function getOrganization(string $ip, ?string $rir = null): ?string
    {
        $whois = $this->query($ip, $rir);
        return $whois['organization'] ?? null;
    }

    /**
     * Verify ownership by checking if email domain matches WHOIS
     */
    public function verifyOwnership(string $ip, string $email, ?string $rir = null): array
    {
        $whois = $this->query($ip, $rir);

        if (!$whois) {
            return [
                'verified' => false,
                'reason' => 'Could not retrieve WHOIS information',
                'whois_data' => null,
            ];
        }

        $emailDomain = substr(strrchr($email, "@"), 1);
        $abuseEmail = $whois['abuse_email'] ?? null;
        $techEmail = $whois['tech_email'] ?? null;
        $adminEmail = $whois['admin_email'] ?? null;

        // Check if any contact email matches
        $contacts = array_filter([$abuseEmail, $techEmail, $adminEmail]);

        foreach ($contacts as $contact) {
            if (strtolower($contact) === strtolower($email)) {
                return [
                    'verified' => true,
                    'reason' => 'Email matches WHOIS contact',
                    'whois_data' => $whois,
                ];
            }

            $contactDomain = substr(strrchr($contact, "@"), 1);
            if (strtolower($contactDomain) === strtolower($emailDomain)) {
                return [
                    'verified' => true,
                    'reason' => 'Email domain matches WHOIS contact domain',
                    'whois_data' => $whois,
                ];
            }
        }

        return [
            'verified' => false,
            'reason' => 'Email does not match any WHOIS contact',
            'whois_data' => $whois,
            'suggested_contacts' => $contacts,
        ];
    }

    /**
     * Query WHOIS server directly via socket
     */
    protected function queryWhoisServer(string $server, string $query): ?string
    {
        try {
            $socket = @fsockopen($server, 43, $errno, $errstr, 10);

            if (!$socket) {
                Log::warning("WHOIS connection failed: {$server} - {$errstr}");
                return null;
            }

            fwrite($socket, $query . "\r\n");

            $response = '';
            while (!feof($socket)) {
                $response .= fgets($socket, 128);
            }

            fclose($socket);

            return $response;
        } catch (\Exception $e) {
            Log::error("WHOIS query error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Query RDAP (modern WHOIS) via HTTP
     */
    protected function queryRdap(string $ip): ?array
    {
        try {
            // Try ARIN RDAP first (redirects to appropriate RIR)
            $response = Http::timeout(10)->get("https://rdap.arin.net/registry/ip/{$ip}");

            if ($response->successful()) {
                return $this->parseRdapResponse($response->json());
            }

            // Fallback to RIPE
            $response = Http::timeout(10)->get("https://rdap.db.ripe.net/ip/{$ip}");

            if ($response->successful()) {
                return $this->parseRdapResponse($response->json());
            }

            return null;
        } catch (\Exception $e) {
            Log::error("RDAP query error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse WHOIS text response
     */
    protected function parseWhoisResponse(string $response, string $rir): array
    {
        $data = [
            'raw' => $response,
            'rir' => $rir,
        ];

        // Parse based on RIR format
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || str_starts_with($line, '%') || str_starts_with($line, '#')) {
                continue;
            }

            if (preg_match('/^([^:]+):\s*(.+)$/', $line, $matches)) {
                $key = strtolower(trim($matches[1]));
                $value = trim($matches[2]);

                switch ($key) {
                    case 'netname':
                    case 'network-name':
                        $data['netname'] = $value;
                        break;
                    case 'descr':
                    case 'description':
                    case 'orgname':
                    case 'org-name':
                        $data['organization'] = $value;
                        break;
                    case 'abuse-mailbox':
                    case 'abuse-email':
                    case 'orgabuseemail':
                        $data['abuse_email'] = $value;
                        break;
                    case 'tech-c':
                    case 'techcemail':
                        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $data['tech_email'] = $value;
                        }
                        break;
                    case 'admin-c':
                    case 'adminemail':
                        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $data['admin_email'] = $value;
                        }
                        break;
                    case 'country':
                        $data['country'] = $value;
                        break;
                    case 'inetnum':
                    case 'netrange':
                        $data['range'] = $value;
                        break;
                }
            }
        }

        return $data;
    }

    /**
     * Parse RDAP JSON response
     */
    protected function parseRdapResponse(array $response): array
    {
        $data = [
            'raw' => $response,
        ];

        // Get network name
        $data['netname'] = $response['name'] ?? null;
        $data['range'] = $response['startAddress'] ?? null;

        // Get organization from entities
        if (isset($response['entities'])) {
            foreach ($response['entities'] as $entity) {
                $roles = $entity['roles'] ?? [];

                // Get organization name
                if (in_array('registrant', $roles)) {
                    $data['organization'] = $this->extractVcardValue($entity, 'fn');
                }

                // Get abuse contact
                if (in_array('abuse', $roles)) {
                    $data['abuse_email'] = $this->extractVcardValue($entity, 'email');
                }

                // Get technical contact
                if (in_array('technical', $roles)) {
                    $data['tech_email'] = $this->extractVcardValue($entity, 'email');
                }

                // Get administrative contact
                if (in_array('administrative', $roles)) {
                    $data['admin_email'] = $this->extractVcardValue($entity, 'email');
                }
            }
        }

        // Get country
        if (isset($response['country'])) {
            $data['country'] = $response['country'];
        }

        return $data;
    }

    /**
     * Extract value from RDAP vCard format
     */
    protected function extractVcardValue(array $entity, string $type): ?string
    {
        if (!isset($entity['vcardArray'][1])) {
            return null;
        }

        foreach ($entity['vcardArray'][1] as $vcard) {
            if (is_array($vcard) && isset($vcard[0]) && $vcard[0] === $type) {
                return $vcard[3] ?? null;
            }
        }

        return null;
    }

    /**
     * Detect which RIR manages an IP address
     */
    public function detectRir(string $ip): ?string
    {
        $whois = $this->queryRdap($ip);

        if (!$whois || !isset($whois['raw']['port43'])) {
            return null;
        }

        $port43 = $whois['raw']['port43'];

        foreach ($this->rirWhoisServers as $rir => $server) {
            if (stripos($port43, $server) !== false) {
                return $rir;
            }
        }

        return null;
    }
}
