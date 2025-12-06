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
                    <h2>Understanding IPv4 Leasing Prices</h2>
                    <p>IPv4 address leasing prices have evolved significantly since IPv4 exhaustion was declared. Understanding the factors that influence pricing helps both IP holders set competitive rates and lessees make informed decisions.</p>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 my-6">
                        <p class="font-semibold text-green-800 mb-2">Current Market Overview</p>
                        <p class="text-green-700">As of 2024, IPv4 leasing prices typically range from $0.30 to $0.60 per IP per month, depending on various factors discussed below.</p>
                    </div>

                    <h2>Factors Affecting IPv4 Lease Prices</h2>

                    <h3>1. Block Size</h3>
                    <p>The size of the IP block significantly impacts per-IP pricing:</p>
                    <div class="overflow-x-auto my-6">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Block Size</th>
                                    <th class="px-4 py-2 text-left">IP Count</th>
                                    <th class="px-4 py-2 text-left">Typical Price Range</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr>
                                    <td class="px-4 py-2">/24</td>
                                    <td class="px-4 py-2">256</td>
                                    <td class="px-4 py-2">$0.40 - $0.60/IP/month</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">/23</td>
                                    <td class="px-4 py-2">512</td>
                                    <td class="px-4 py-2">$0.38 - $0.55/IP/month</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">/22</td>
                                    <td class="px-4 py-2">1,024</td>
                                    <td class="px-4 py-2">$0.35 - $0.50/IP/month</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">/21</td>
                                    <td class="px-4 py-2">2,048</td>
                                    <td class="px-4 py-2">$0.32 - $0.48/IP/month</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">/20 or larger</td>
                                    <td class="px-4 py-2">4,096+</td>
                                    <td class="px-4 py-2">$0.30 - $0.45/IP/month</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3>2. Regional Internet Registry (RIR)</h3>
                    <p>IP addresses from different RIRs may have different market values:</p>
                    <ul>
                        <li><strong>RIPE NCC (Europe):</strong> Generally highest demand and prices</li>
                        <li><strong>ARIN (North America):</strong> Strong demand, competitive pricing</li>
                        <li><strong>APNIC (Asia-Pacific):</strong> Growing demand, moderate prices</li>
                        <li><strong>LACNIC (Latin America):</strong> Lower demand, competitive prices</li>
                        <li><strong>AFRINIC (Africa):</strong> Emerging market, variable pricing</li>
                    </ul>

                    <h3>3. IP Reputation</h3>
                    <p>Clean IP addresses command premium prices:</p>
                    <ul>
                        <li><strong>Clean (90-100% score):</strong> Full market price or premium</li>
                        <li><strong>Good (70-89% score):</strong> Standard market price</li>
                        <li><strong>Warning (50-69% score):</strong> 10-20% discount typically</li>
                        <li><strong>Poor (below 50%):</strong> May be difficult to lease</li>
                    </ul>

                    <h3>4. Geolocation</h3>
                    <p>IP geolocation affects pricing for specific use cases:</p>
                    <ul>
                        <li>IPs geolocated to major business hubs often command higher prices</li>
                        <li>Specific country geolocation may be required for content delivery or compliance</li>
                        <li>Geolocation changes can often be requested for an additional fee</li>
                    </ul>

                    <h3>5. Lease Duration</h3>
                    <p>Longer commitments often result in better rates:</p>
                    <ul>
                        <li><strong>Month-to-month:</strong> Full price</li>
                        <li><strong>6 months:</strong> 5-10% discount</li>
                        <li><strong>12 months:</strong> 10-15% discount</li>
                        <li><strong>24+ months:</strong> 15-25% discount (negotiable)</li>
                    </ul>

                    <h2>Pricing Strategies for IP Holders</h2>

                    <div class="bg-gray-50 rounded-lg p-6 my-6">
                        <h3 class="text-lg font-semibold mb-4">Tips for Setting Competitive Prices</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-primary-600 mr-2 text-sm">tips_and_updates</span>
                                Research current market rates for similar blocks
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-primary-600 mr-2 text-sm">tips_and_updates</span>
                                Consider offering volume discounts for larger leases
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-primary-600 mr-2 text-sm">tips_and_updates</span>
                                Factor in your reputation score - clean IPs justify higher prices
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-primary-600 mr-2 text-sm">tips_and_updates</span>
                                Consider the desirability of your RIR and geolocation
                            </li>
                            <li class="flex items-start">
                                <span class="material-icons-outlined text-primary-600 mr-2 text-sm">tips_and_updates</span>
                                Be responsive to offers - flexibility can lead to long-term relationships
                            </li>
                        </ul>
                    </div>

                    <h2>Platform Fees</h2>
                    <p>Soltia IPS Marketplace charges a 5% platform fee on all transactions:</p>
                    <ul>
                        <li>The fee covers LOA generation, payment processing, and platform services</li>
                        <li>No listing fees - you only pay when you earn</li>
                        <li>No hidden costs or setup fees</li>
                        <li>Bank transfer fees apply for payouts</li>
                    </ul>

                    <h2>Payment Methods</h2>
                    <p>We support multiple payment options:</p>
                    <ul>
                        <li><strong>Credit/Debit Cards:</strong> Instant processing</li>
                        <li><strong>Bank Transfer:</strong> For larger transactions</li>
                        <li><strong>PayPal:</strong> Available in supported regions</li>
                    </ul>

                    <h2>Cost Comparison: Leasing vs. Buying</h2>
                    <div class="bg-blue-50 rounded-lg p-6 my-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4">When to Lease vs. Buy</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-blue-700 mb-2">Leasing is Better When:</h4>
                                <ul class="text-sm space-y-1 text-blue-800">
                                    <li>You need IPs for short/medium term projects</li>
                                    <li>Capital preservation is important</li>
                                    <li>You want flexibility to scale up/down</li>
                                    <li>You prefer operational expense vs. capital expense</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-blue-700 mb-2">Buying is Better When:</h4>
                                <ul class="text-sm space-y-1 text-blue-800">
                                    <li>You need IPs permanently (5+ years)</li>
                                    <li>You can afford upfront investment</li>
                                    <li>You want to build IP portfolio value</li>
                                    <li>You plan to lease excess capacity</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h2>Market Trends</h2>
                    <p>Key trends affecting IPv4 leasing prices:</p>
                    <ul>
                        <li>Prices have stabilized after years of increases</li>
                        <li>IPv6 adoption is growing but IPv4 demand remains strong</li>
                        <li>Cloud providers and CDNs are major lessees</li>
                        <li>Reputation-based pricing is becoming more important</li>
                    </ul>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Price Calculator CTA -->
                <div class="bg-gradient-to-br from-primary-500 to-secondary-500 rounded-xl shadow-material-1 p-6 mb-6 text-white">
                    <h3 class="font-semibold mb-2">Ready to Explore?</h3>
                    <p class="text-sm opacity-90 mb-4">Browse our marketplace to see current pricing and available IP ranges.</p>
                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-primary-600 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium">
                        <span class="material-icons-outlined mr-2 text-sm">store</span>
                        View Marketplace
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Quick Reference</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Platform Fee:</span>
                            <span class="font-medium">5%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Min Lease:</span>
                            <span class="font-medium">1 month</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Avg Price /24:</span>
                            <span class="font-medium">~$120/month</span>
                        </div>
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
