<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * All available guides with SEO metadata
     */
    private function getGuides(): array
    {
        return [
            // Getting Started
            [
                'slug' => 'how-to-add-ip-range',
                'icon' => 'add_circle',
                'color' => 'primary',
                'category' => 'getting-started',
                'title' => __('How to Add an IP Range for Lease'),
                'description' => __('Learn how to register and list your IP address ranges on our marketplace.'),
                'meta_title' => 'How to Add IP Range for Lease | IPv4 Leasing Guide',
                'meta_description' => 'Complete guide on how to add and list your IPv4 address ranges on the Soltia IPS Marketplace. Learn about verification, pricing, and publishing.',
            ],
            [
                'slug' => 'how-to-lease-ip-range',
                'icon' => 'shopping_cart',
                'color' => 'secondary',
                'category' => 'getting-started',
                'title' => __('How to Lease an IP Range'),
                'description' => __('Step-by-step guide to finding and leasing IP addresses for your business.'),
                'meta_title' => 'How to Lease IPv4 Addresses | IP Leasing Guide',
                'meta_description' => 'Step-by-step guide to leasing IPv4 addresses. Learn how to search, compare, and lease IP ranges for your business needs.',
            ],
            // IP Management
            [
                'slug' => 'what-is-loa',
                'icon' => 'assignment',
                'color' => 'success',
                'category' => 'ip-management',
                'title' => __('What is a Letter of Authorization (LOA)?'),
                'description' => __('Understand what LOAs are and how they authorize IP address usage.'),
                'meta_title' => 'What is LOA (Letter of Authorization) | IP Address Documentation',
                'meta_description' => 'Learn about Letters of Authorization (LOA) for IP addresses. Understand how LOAs work, their importance, and how to use them for BGP announcements.',
            ],
            [
                'slug' => 'understanding-rpki-roa',
                'icon' => 'verified_user',
                'color' => 'warning',
                'category' => 'ip-management',
                'title' => __('Understanding RPKI and ROA'),
                'description' => __('Learn about Route Origin Authorization and how it secures BGP routing.'),
                'meta_title' => 'RPKI and ROA Explained | BGP Security Guide',
                'meta_description' => 'Complete guide to RPKI (Resource Public Key Infrastructure) and ROA (Route Origin Authorization). Learn how to secure your BGP announcements.',
            ],
            [
                'slug' => 'ip-reputation-management',
                'icon' => 'security',
                'color' => 'danger',
                'category' => 'ip-management',
                'title' => __('IP Reputation Management'),
                'description' => __('How to maintain clean IP reputation and avoid blocklists.'),
                'meta_title' => 'IP Reputation Management Guide | Avoid Blocklists',
                'meta_description' => 'Learn how to manage IP reputation, avoid blocklists, and maintain clean IPv4 addresses. Best practices for IP address hygiene.',
            ],
            // Business & Compliance
            [
                'slug' => 'kyc-verification-guide',
                'icon' => 'fact_check',
                'color' => 'info',
                'category' => 'compliance',
                'title' => __('KYC Verification Guide'),
                'description' => __('Everything you need to know about our Know Your Customer process.'),
                'meta_title' => 'KYC Verification Process | IP Marketplace Compliance',
                'meta_description' => 'Complete guide to the KYC verification process on Soltia IPS Marketplace. Learn what documents are required and how to complete verification.',
            ],
            [
                'slug' => 'ipv4-leasing-pricing-guide',
                'icon' => 'attach_money',
                'color' => 'success',
                'category' => 'business',
                'title' => __('IPv4 Leasing Pricing Guide'),
                'description' => __('Understand IPv4 pricing factors and how to set competitive rates.'),
                'meta_title' => 'IPv4 Leasing Pricing Guide | IP Address Costs 2024',
                'meta_description' => 'Comprehensive guide to IPv4 leasing prices. Learn about pricing factors, market rates, and how to price your IP addresses competitively.',
            ],
            // Technical
            [
                'slug' => 'bgp-announcement-guide',
                'icon' => 'router',
                'color' => 'primary',
                'category' => 'technical',
                'title' => __('BGP Announcement Guide'),
                'description' => __('How to announce leased IP addresses using BGP.'),
                'meta_title' => 'BGP Announcement Guide for Leased IPs | Technical Setup',
                'meta_description' => 'Technical guide for announcing leased IPv4 addresses via BGP. Learn about ASN assignment, route configuration, and best practices.',
            ],
            [
                'slug' => 'ip-geolocation-explained',
                'icon' => 'location_on',
                'color' => 'secondary',
                'category' => 'technical',
                'title' => __('IP Geolocation Explained'),
                'description' => __('How IP geolocation works and why it matters for leased IPs.'),
                'meta_title' => 'IP Geolocation Guide | Understanding IP Location Data',
                'meta_description' => 'Learn how IP geolocation works, its accuracy, and how to update geolocation for leased IPv4 addresses.',
            ],
        ];
    }

    public function index()
    {
        $guides = $this->getGuides();

        // Group by category
        $categories = [
            'getting-started' => [
                'title' => __('Getting Started'),
                'icon' => 'rocket_launch',
                'guides' => array_filter($guides, fn($g) => $g['category'] === 'getting-started'),
            ],
            'ip-management' => [
                'title' => __('IP Management'),
                'icon' => 'lan',
                'guides' => array_filter($guides, fn($g) => $g['category'] === 'ip-management'),
            ],
            'compliance' => [
                'title' => __('Compliance & KYC'),
                'icon' => 'gavel',
                'guides' => array_filter($guides, fn($g) => $g['category'] === 'compliance'),
            ],
            'business' => [
                'title' => __('Business & Pricing'),
                'icon' => 'business',
                'guides' => array_filter($guides, fn($g) => $g['category'] === 'business'),
            ],
            'technical' => [
                'title' => __('Technical Guides'),
                'icon' => 'code',
                'guides' => array_filter($guides, fn($g) => $g['category'] === 'technical'),
            ],
        ];

        return view('help.index', compact('guides', 'categories'));
    }

    public function show($slug)
    {
        $guides = $this->getGuides();
        $guide = collect($guides)->firstWhere('slug', $slug);

        if (!$guide) {
            abort(404);
        }

        $view = 'help.' . str_replace('-', '_', $slug);

        // Check if specific view exists, otherwise use generic template
        if (!view()->exists($view)) {
            $view = 'help.generic';
        }

        // Get related guides (same category, excluding current)
        $relatedGuides = array_filter($guides, fn($g) =>
            $g['category'] === $guide['category'] && $g['slug'] !== $slug
        );

        return view($view, compact('guide', 'relatedGuides'));
    }
}
