<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RIPE Database API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the RIPE Database REST API used for managing
    | WHOIS objects like inetnum, route, person, organisation, etc.
    |
    */

    'database' => [
        'base_url' => env('RIPE_DATABASE_URL', 'https://rest.db.ripe.net'),
        'test_url' => env('RIPE_DATABASE_TEST_URL', 'https://rest-test.db.ripe.net'),
        'source' => env('RIPE_DATABASE_SOURCE', 'ripe'),
        'use_test_db' => env('RIPE_USE_TEST_DB', false),
        'timeout' => env('RIPE_DATABASE_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | RIPEstat Data API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the RIPEstat Data API used for querying
    | geolocation, abuse contacts, routing information, etc.
    |
    */

    'stat' => [
        'base_url' => env('RIPESTAT_URL', 'https://stat.ripe.net/data'),
        'source_app' => env('RIPESTAT_SOURCE_APP', 'soltia-ips-marketplace'),
        'timeout' => env('RIPESTAT_TIMEOUT', 30),
        'max_concurrent' => 8, // RIPE limit
    ],

    /*
    |--------------------------------------------------------------------------
    | RIPE Object Types
    |--------------------------------------------------------------------------
    |
    | Supported RIPE database object types and their configurations.
    |
    */

    'object_types' => [
        'inetnum' => [
            'label' => 'IPv4 Address Block',
            'description' => 'IPv4 address space allocation/assignment',
            'required_attrs' => ['inetnum', 'netname', 'country', 'admin-c', 'tech-c', 'status', 'mnt-by', 'source'],
        ],
        'inet6num' => [
            'label' => 'IPv6 Address Block',
            'description' => 'IPv6 address space allocation/assignment',
            'required_attrs' => ['inet6num', 'netname', 'country', 'admin-c', 'tech-c', 'status', 'mnt-by', 'source'],
        ],
        'route' => [
            'label' => 'IPv4 Route Object',
            'description' => 'Route for IPv4 prefix announcement',
            'required_attrs' => ['route', 'origin', 'mnt-by', 'source'],
        ],
        'route6' => [
            'label' => 'IPv6 Route Object',
            'description' => 'Route for IPv6 prefix announcement',
            'required_attrs' => ['route6', 'origin', 'mnt-by', 'source'],
        ],
        'aut-num' => [
            'label' => 'Autonomous System Number',
            'description' => 'AS Number registration',
            'required_attrs' => ['aut-num', 'as-name', 'admin-c', 'tech-c', 'mnt-by', 'source'],
        ],
        'person' => [
            'label' => 'Person Contact',
            'description' => 'Person contact information',
            'required_attrs' => ['person', 'address', 'phone', 'nic-hdl', 'mnt-by', 'source'],
        ],
        'role' => [
            'label' => 'Role Contact',
            'description' => 'Role/team contact information',
            'required_attrs' => ['role', 'address', 'nic-hdl', 'mnt-by', 'source'],
        ],
        'organisation' => [
            'label' => 'Organisation',
            'description' => 'Organisation registration',
            'required_attrs' => ['organisation', 'org-name', 'org-type', 'address', 'mnt-ref', 'mnt-by', 'source'],
        ],
        'mntner' => [
            'label' => 'Maintainer',
            'description' => 'Object maintainer for authentication',
            'required_attrs' => ['mntner', 'admin-c', 'upd-to', 'auth', 'mnt-by', 'source'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Inetnum Status Values
    |--------------------------------------------------------------------------
    |
    | Valid status values for inetnum objects.
    |
    */

    'inetnum_statuses' => [
        'ALLOCATED PA' => 'Provider Aggregatable allocation from RIPE NCC',
        'ALLOCATED PI' => 'Provider Independent allocation from RIPE NCC',
        'ALLOCATED UNSPECIFIED' => 'Allocation type not specified',
        'LIR-PARTITIONED PA' => 'Sub-allocation by LIR',
        'LIR-PARTITIONED PI' => 'Sub-allocation by LIR (PI)',
        'SUB-ALLOCATED PA' => 'Sub-allocated to customer',
        'ASSIGNED PA' => 'Assigned to end-user (PA)',
        'ASSIGNED PI' => 'Assigned to end-user (PI)',
        'ASSIGNED ANYCAST' => 'Assigned for anycast use',
        'LEGACY' => 'Legacy address space',
    ],

    /*
    |--------------------------------------------------------------------------
    | RIPEstat Endpoints
    |--------------------------------------------------------------------------
    |
    | Available RIPEstat Data API endpoints.
    |
    */

    'stat_endpoints' => [
        'network-info' => 'Get containing prefix and announcing ASN',
        'prefix-overview' => 'Check if prefix is seen in routing table',
        'abuse-contact-finder' => 'Find abuse contact information',
        'whois' => 'WHOIS information for resource',
        'geoloc' => 'Geolocation data',
        'maxmind-geo-lite' => 'MaxMind GeoLite geolocation',
        'routing-status' => 'Current routing status',
        'bgp-state' => 'BGP state information',
        'announced-prefixes' => 'Prefixes announced by ASN',
        'as-overview' => 'AS Number overview',
        'address-space-hierarchy' => 'Address space hierarchy',
        'rir-stats-country' => 'RIR statistics by country',
        'historical-whois' => 'Historical WHOIS data',
        'dns-chain' => 'DNS resolution chain',
        'reverse-dns' => 'Reverse DNS information',
        'as-path-length' => 'AS path length statistics',
        'asn-neighbours' => 'ASN neighbours/peers',
        'ris-peerings' => 'RIS peering information',
    ],
];
