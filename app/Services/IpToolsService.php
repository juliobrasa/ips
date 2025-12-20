<?php

namespace App\Services;

class IpToolsService
{
    /**
     * Calculate subnet details from CIDR notation
     */
    public function calculateSubnet(string $cidr): array
    {
        [$ip, $prefix] = explode('/', $cidr);
        $prefix = (int) $prefix;

        $ipLong = ip2long($ip);
        $mask = -1 << (32 - $prefix);
        $network = $ipLong & $mask;
        $broadcast = $network | ~$mask;

        $totalHosts = pow(2, 32 - $prefix);
        $usableHosts = max(0, $totalHosts - 2);

        return [
            'cidr' => $cidr,
            'ip_address' => $ip,
            'prefix_length' => $prefix,
            'network_address' => long2ip($network),
            'broadcast_address' => long2ip($broadcast),
            'first_usable' => $prefix < 31 ? long2ip($network + 1) : long2ip($network),
            'last_usable' => $prefix < 31 ? long2ip($broadcast - 1) : long2ip($broadcast),
            'subnet_mask' => long2ip($mask),
            'wildcard_mask' => long2ip(~$mask),
            'total_hosts' => $totalHosts,
            'usable_hosts' => $usableHosts,
            'ip_class' => $this->getIpClass($ip),
            'is_private' => $this->isPrivateIp($ip),
            'binary_mask' => str_pad(decbin($mask & 0xFFFFFFFF), 32, '0', STR_PAD_LEFT),
        ];
    }

    /**
     * Split a subnet into smaller subnets
     */
    public function splitSubnet(string $cidr, int $newPrefix): array
    {
        [$ip, $currentPrefix] = explode('/', $cidr);
        $currentPrefix = (int) $currentPrefix;

        if ($newPrefix <= $currentPrefix || $newPrefix > 32) {
            throw new \InvalidArgumentException('New prefix must be larger than current prefix and <= 32');
        }

        $subnets = [];
        $numSubnets = pow(2, $newPrefix - $currentPrefix);
        $subnetSize = pow(2, 32 - $newPrefix);

        $network = ip2long($ip) & (-1 << (32 - $currentPrefix));

        for ($i = 0; $i < $numSubnets; $i++) {
            $subnetStart = $network + ($i * $subnetSize);
            $subnets[] = long2ip($subnetStart) . '/' . $newPrefix;
        }

        return $subnets;
    }

    /**
     * Merge contiguous subnets into larger subnet
     */
    public function mergeSubnets(array $cidrs): ?string
    {
        if (count($cidrs) < 2) {
            return $cidrs[0] ?? null;
        }

        // Convert to network addresses and sort
        $networks = [];
        $prefix = null;

        foreach ($cidrs as $cidr) {
            [$ip, $p] = explode('/', $cidr);
            if ($prefix === null) {
                $prefix = (int) $p;
            } elseif ((int) $p !== $prefix) {
                throw new \InvalidArgumentException('All subnets must have the same prefix length');
            }
            $networks[] = ip2long($ip) & (-1 << (32 - $prefix));
        }

        sort($networks);

        // Check if contiguous
        $expectedSubnetSize = pow(2, 32 - $prefix);
        for ($i = 1; $i < count($networks); $i++) {
            if ($networks[$i] - $networks[$i - 1] !== $expectedSubnetSize) {
                return null; // Not contiguous
            }
        }

        // Check if can form a valid larger subnet
        $count = count($networks);
        $newPrefix = $prefix - (int) log($count, 2);

        if (pow(2, $prefix - $newPrefix) !== $count) {
            return null; // Count must be power of 2
        }

        $newNetwork = $networks[0] & (-1 << (32 - $newPrefix));
        if ($newNetwork !== $networks[0]) {
            return null; // Not aligned to new prefix
        }

        return long2ip($newNetwork) . '/' . $newPrefix;
    }

    /**
     * Check if an IP is within a subnet
     */
    public function isIpInSubnet(string $ip, string $cidr): bool
    {
        [$network, $prefix] = explode('/', $cidr);
        $prefix = (int) $prefix;

        $ipLong = ip2long($ip);
        $networkLong = ip2long($network);
        $mask = -1 << (32 - $prefix);

        return ($ipLong & $mask) === ($networkLong & $mask);
    }

    /**
     * Get all IPs in a subnet
     */
    public function getSubnetIps(string $cidr, int $limit = 1000): array
    {
        [$ip, $prefix] = explode('/', $cidr);
        $prefix = (int) $prefix;

        $network = ip2long($ip) & (-1 << (32 - $prefix));
        $broadcast = $network | ~(-1 << (32 - $prefix));
        $totalHosts = $broadcast - $network + 1;

        if ($totalHosts > $limit) {
            return ['error' => "Subnet has {$totalHosts} IPs, limit is {$limit}"];
        }

        $ips = [];
        for ($i = $network; $i <= $broadcast; $i++) {
            $ips[] = long2ip($i);
        }

        return $ips;
    }

    /**
     * Convert IP range to CIDR notations
     */
    public function rangeToCidr(string $startIp, string $endIp): array
    {
        $start = ip2long($startIp);
        $end = ip2long($endIp);

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $cidrs = [];

        while ($start <= $end) {
            $maxSize = 32;

            while ($maxSize > 0) {
                $mask = -1 << (32 - $maxSize);
                $maskBase = $start & $mask;

                if ($maskBase !== $start) {
                    break;
                }

                $maxSize--;
            }

            $x = log($end - $start + 1, 2);
            $maxDiff = (32 - floor($x));

            if ($maxSize < $maxDiff) {
                $maxSize = $maxDiff;
            }

            $cidrs[] = long2ip($start) . '/' . $maxSize;
            $start += pow(2, 32 - $maxSize);
        }

        return $cidrs;
    }

    /**
     * Convert CIDR to IP range
     */
    public function cidrToRange(string $cidr): array
    {
        [$ip, $prefix] = explode('/', $cidr);
        $prefix = (int) $prefix;

        $network = ip2long($ip) & (-1 << (32 - $prefix));
        $broadcast = $network | ~(-1 << (32 - $prefix));

        return [
            'start' => long2ip($network),
            'end' => long2ip($broadcast),
        ];
    }

    /**
     * Generate RFC 8805 Geofeed CSV
     */
    public function generateGeofeed(array $entries): string
    {
        $lines = ["# Geofeed file generated by Soltia IPS Marketplace"];
        $lines[] = "# Format: prefix,country_code,region_code,city,postal_code";
        $lines[] = "";

        foreach ($entries as $entry) {
            $parts = [
                $entry['prefix'],
                $entry['country'] ?? '',
                $entry['region'] ?? '',
                $entry['city'] ?? '',
                $entry['postal_code'] ?? '',
            ];

            // Remove trailing empty values
            while (count($parts) > 1 && empty($parts[count($parts) - 1])) {
                array_pop($parts);
            }

            $lines[] = implode(',', $parts);
        }

        return implode("\n", $lines);
    }

    /**
     * Parse Geofeed file
     */
    public function parseGeofeed(string $content): array
    {
        $entries = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            $parts = str_getcsv($line);

            if (count($parts) < 2) {
                continue;
            }

            $entries[] = [
                'prefix' => $parts[0] ?? null,
                'country' => $parts[1] ?? null,
                'region' => $parts[2] ?? null,
                'city' => $parts[3] ?? null,
                'postal_code' => $parts[4] ?? null,
            ];
        }

        return $entries;
    }

    /**
     * Get IP class (A, B, C, D, E)
     */
    public function getIpClass(string $ip): string
    {
        $firstOctet = (int) explode('.', $ip)[0];

        return match (true) {
            $firstOctet >= 1 && $firstOctet <= 126 => 'A',
            $firstOctet >= 128 && $firstOctet <= 191 => 'B',
            $firstOctet >= 192 && $firstOctet <= 223 => 'C',
            $firstOctet >= 224 && $firstOctet <= 239 => 'D (Multicast)',
            $firstOctet >= 240 && $firstOctet <= 255 => 'E (Reserved)',
            default => 'Unknown',
        };
    }

    /**
     * Check if IP is private (RFC 1918)
     */
    public function isPrivateIp(string $ip): bool
    {
        $private = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
        ];

        foreach ($private as $range) {
            if ($this->isIpInSubnet($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is reserved
     */
    public function isReservedIp(string $ip): bool
    {
        $reserved = [
            '0.0.0.0/8',
            '10.0.0.0/8',
            '100.64.0.0/10',
            '127.0.0.0/8',
            '169.254.0.0/16',
            '172.16.0.0/12',
            '192.0.0.0/24',
            '192.0.2.0/24',
            '192.88.99.0/24',
            '192.168.0.0/16',
            '198.18.0.0/15',
            '198.51.100.0/24',
            '203.0.113.0/24',
            '224.0.0.0/4',
            '240.0.0.0/4',
        ];

        foreach ($reserved as $range) {
            if ($this->isIpInSubnet($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get RIR for IP
     */
    public function getRir(string $ip): string
    {
        // Simplified - in production would use delegation files
        $firstOctet = (int) explode('.', $ip)[0];

        return match (true) {
            in_array($firstOctet, range(1, 10)) => 'ARIN',
            in_array($firstOctet, range(11, 30)) => 'ARIN',
            in_array($firstOctet, range(31, 60)) => 'RIPE NCC',
            in_array($firstOctet, range(61, 80)) => 'APNIC',
            in_array($firstOctet, range(81, 100)) => 'RIPE NCC',
            in_array($firstOctet, range(101, 120)) => 'APNIC',
            in_array($firstOctet, range(121, 140)) => 'APNIC',
            in_array($firstOctet, range(141, 160)) => 'ARIN',
            in_array($firstOctet, range(161, 180)) => 'RIPE NCC',
            in_array($firstOctet, range(181, 190)) => 'LACNIC',
            in_array($firstOctet, range(191, 200)) => 'LACNIC',
            in_array($firstOctet, range(201, 210)) => 'LACNIC',
            in_array($firstOctet, range(211, 220)) => 'APNIC',
            default => 'Unknown',
        };
    }

    /**
     * Validate CIDR notation
     */
    public function validateCidr(string $cidr): array
    {
        $errors = [];

        if (!preg_match('/^(\d{1,3}\.){3}\d{1,3}\/\d{1,2}$/', $cidr)) {
            $errors[] = 'Invalid CIDR format';
            return ['valid' => false, 'errors' => $errors];
        }

        [$ip, $prefix] = explode('/', $cidr);
        $prefix = (int) $prefix;

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $errors[] = 'Invalid IPv4 address';
        }

        if ($prefix < 0 || $prefix > 32) {
            $errors[] = 'Prefix must be between 0 and 32';
        }

        // Check if IP is the network address
        $ipLong = ip2long($ip);
        $mask = -1 << (32 - $prefix);
        $network = $ipLong & $mask;

        if ($ipLong !== $network) {
            $errors[] = 'IP is not the network address. Should be: ' . long2ip($network) . '/' . $prefix;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'normalized' => long2ip($network) . '/' . $prefix,
        ];
    }

    /**
     * Get subnets summary
     */
    public function summarizeSubnets(array $cidrs): array
    {
        $totalIps = 0;
        $prefixCounts = [];

        foreach ($cidrs as $cidr) {
            [$ip, $prefix] = explode('/', $cidr);
            $prefix = (int) $prefix;

            $ips = pow(2, 32 - $prefix);
            $totalIps += $ips;

            if (!isset($prefixCounts[$prefix])) {
                $prefixCounts[$prefix] = 0;
            }
            $prefixCounts[$prefix]++;
        }

        ksort($prefixCounts);

        return [
            'total_subnets' => count($cidrs),
            'total_ips' => $totalIps,
            'by_prefix' => $prefixCounts,
            'equivalent_slash24' => $totalIps / 256,
        ];
    }
}
