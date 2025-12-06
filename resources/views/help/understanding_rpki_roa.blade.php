<x-guest-layout>
    <x-slot name="title">{{ $guide['meta_title'] }}</x-slot>
    <x-slot name="metaDescription">{{ $guide['meta_description'] }}</x-slot>

    <!-- Breadcrumb -->
    <div class="bg-gradient-to-r from-primary-600 to-secondary-600 text-white py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm mb-4">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:underline opacity-80">Home</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li><a href="{{ route('help.index') }}" class="hover:underline opacity-80">Help Center</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li class="opacity-80">{{ $guide['title'] }}</li>
                </ol>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold">{{ $guide['title'] }}</h1>
            <p class="mt-2 text-lg opacity-90">{{ $guide['description'] }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <article class="prose prose-lg max-w-none">
                    <h2>What is RPKI?</h2>
                    <p><strong>Resource Public Key Infrastructure (RPKI)</strong> is a cryptographic framework designed to secure the internet's routing infrastructure. It provides a way to connect Internet number resource information (such as IP addresses and ASNs) to a trust anchor, enabling network operators to verify the legitimacy of BGP route announcements.</p>

                    <div class="bg-warning-50 border-l-4 border-warning-500 p-4 my-6">
                        <p class="font-semibold text-warning-800 mb-2">Why RPKI Matters</p>
                        <p class="text-warning-700">RPKI adoption is growing rapidly. Major networks and IXPs are increasingly filtering routes based on RPKI validation. Without proper ROA records, your IP announcements may be rejected by many networks.</p>
                    </div>

                    <h2>What is a ROA?</h2>
                    <p>A <strong>Route Origin Authorization (ROA)</strong> is a cryptographically signed object that states which Autonomous System (AS) is authorized to originate a particular IP address prefix. ROAs are the core building blocks of RPKI.</p>

                    <p>A ROA contains three key pieces of information:</p>
                    <ul>
                        <li><strong>Prefix:</strong> The IP address block (e.g., 192.0.2.0/24)</li>
                        <li><strong>Maximum Length:</strong> The maximum prefix length that can be announced</li>
                        <li><strong>Origin AS:</strong> The ASN authorized to announce this prefix</li>
                    </ul>

                    <h2>RPKI Validation States</h2>
                    <p>When a network validates a BGP announcement against RPKI, there are three possible outcomes:</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <span class="material-icons-outlined text-3xl text-green-600">check_circle</span>
                            <h4 class="font-semibold text-green-800 mt-2">Valid</h4>
                            <p class="text-sm text-green-700">ROA exists and matches the announcement</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <span class="material-icons-outlined text-3xl text-red-600">cancel</span>
                            <h4 class="font-semibold text-red-800 mt-2">Invalid</h4>
                            <p class="text-sm text-red-700">ROA exists but doesn't match (likely hijack)</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <span class="material-icons-outlined text-3xl text-gray-600">help_outline</span>
                            <h4 class="font-semibold text-gray-800 mt-2">Not Found</h4>
                            <p class="text-sm text-gray-700">No ROA exists for this prefix</p>
                        </div>
                    </div>

                    <h2>How RPKI Works with Leased IPs</h2>
                    <p>When you lease IP addresses through our marketplace, the ROA creation process works as follows:</p>

                    <ol>
                        <li><strong>Lease Agreement:</strong> You lease the IP range and provide your ASN</li>
                        <li><strong>ROA Request:</strong> You request a ROA through your lease management dashboard</li>
                        <li><strong>IP Holder Creates ROA:</strong> The IP holder creates an ROA record in the RIR's RPKI system</li>
                        <li><strong>Propagation:</strong> The ROA propagates through the RPKI infrastructure (usually within hours)</li>
                        <li><strong>Announce:</strong> You can now announce the IP range with RPKI-valid status</li>
                    </ol>

                    <h2>Benefits of RPKI/ROA</h2>
                    <ul>
                        <li><strong>Protection Against Hijacking:</strong> Makes unauthorized route announcements detectable</li>
                        <li><strong>Improved Acceptance:</strong> Your routes are more likely to be accepted by major networks</li>
                        <li><strong>Industry Standard:</strong> Shows you follow best practices for network security</li>
                        <li><strong>Required by Many Providers:</strong> Some networks only accept RPKI-valid routes</li>
                    </ul>

                    <h2>ROA Management Best Practices</h2>

                    <div class="bg-gray-50 rounded-lg p-6 my-6">
                        <h3 class="text-lg font-semibold mb-4">Do's and Don'ts</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-green-700 mb-2">Do:</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Request ROA creation immediately after leasing</li>
                                    <li>Use the correct maxLength for your announcements</li>
                                    <li>Verify ROA status before announcing routes</li>
                                    <li>Update ROAs when your ASN changes</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-red-700 mb-2">Don't:</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Announce routes before ROA is active</li>
                                    <li>Use a different ASN than in the ROA</li>
                                    <li>Announce more-specific prefixes than maxLength allows</li>
                                    <li>Forget to request ROA removal when lease ends</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h2>Checking RPKI Status</h2>
                    <p>You can verify the RPKI status of IP prefixes using various online tools:</p>
                    <ul>
                        <li>RIPE RIPEstat: <code>stat.ripe.net</code></li>
                        <li>Cloudflare RPKI Portal: <code>rpki.cloudflare.com</code></li>
                        <li>Hurricane Electric BGP Toolkit: <code>bgp.he.net</code></li>
                    </ul>

                    <h2>Troubleshooting RPKI Issues</h2>
                    <dl class="space-y-4 my-6">
                        <div class="bg-white border rounded-lg p-4">
                            <dt class="font-medium">Route shows as Invalid</dt>
                            <dd class="text-gray-600 mt-1">Check that your ASN matches the ROA. Verify you're not announcing a more-specific prefix than allowed by maxLength.</dd>
                        </div>
                        <div class="bg-white border rounded-lg p-4">
                            <dt class="font-medium">ROA not propagating</dt>
                            <dd class="text-gray-600 mt-1">ROA propagation can take up to 24 hours. Contact the IP holder if the ROA doesn't appear after this time.</dd>
                        </div>
                        <div class="bg-white border rounded-lg p-4">
                            <dt class="font-medium">Multiple ROAs conflict</dt>
                            <dd class="text-gray-600 mt-1">If there are multiple ROAs for the same prefix, ensure all are valid for your ASN or request removal of outdated ones.</dd>
                        </div>
                    </dl>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">RPKI Resources</h3>
                    <div class="space-y-3 text-sm">
                        <a href="https://rpki.cloudflare.com" target="_blank" rel="noopener" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">open_in_new</span>
                            Cloudflare RPKI Portal
                        </a>
                        <a href="https://stat.ripe.net" target="_blank" rel="noopener" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">open_in_new</span>
                            RIPE RIPEstat
                        </a>
                    </div>
                </div>

                <!-- Related Guides -->
                @if(count($relatedGuides) > 0)
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Related Guides</h3>
                    <ul class="space-y-3">
                        @foreach($relatedGuides as $related)
                        <li>
                            <a href="{{ route('help.show', $related['slug']) }}" class="text-gray-700 hover:text-primary-600 flex items-start">
                                <span class="material-icons-outlined text-sm mr-2 mt-0.5 text-gray-400">article</span>
                                {{ $related['title'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
