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
                    <h2>What is a Letter of Authorization (LOA)?</h2>
                    <p>A <strong>Letter of Authorization (LOA)</strong> is a legal document that grants permission to a third party to use or announce IP address resources that belong to another entity. In the context of IP address leasing, an LOA is issued by the IP holder (the rightful owner of the IP addresses) to authorize the lessee (the renter) to route and use those IP addresses.</p>

                    <div class="bg-primary-50 border-l-4 border-primary-500 p-4 my-6">
                        <p class="font-semibold text-primary-800 mb-2">Key Point</p>
                        <p class="text-primary-700">Without a valid LOA, network operators and Internet Service Providers (ISPs) will typically reject BGP announcements for IP addresses you don't own.</p>
                    </div>

                    <h2>Why is an LOA Important?</h2>
                    <p>LOAs serve several critical functions in the IP leasing ecosystem:</p>
                    <ul>
                        <li><strong>Legal Authorization:</strong> Provides documented proof that you have permission to use the IP addresses</li>
                        <li><strong>Network Acceptance:</strong> Required by upstream providers and IXPs (Internet Exchange Points) to accept your BGP announcements</li>
                        <li><strong>Fraud Prevention:</strong> Helps prevent unauthorized use of IP addresses and IP hijacking</li>
                        <li><strong>Audit Trail:</strong> Creates a paper trail for compliance and verification purposes</li>
                    </ul>

                    <h2>What Information Does an LOA Contain?</h2>
                    <p>A properly formatted LOA typically includes:</p>
                    <ul>
                        <li>IP holder's company name and contact information</li>
                        <li>IP range(s) being authorized (in CIDR notation)</li>
                        <li>Lessee's company name and ASN (Autonomous System Number)</li>
                        <li>Authorization period (start and end dates)</li>
                        <li>Signature of an authorized representative</li>
                        <li>Unique reference number for verification</li>
                        <li>Contact information for verification requests</li>
                    </ul>

                    <h2>How to Use an LOA</h2>
                    <ol>
                        <li><strong>Obtain the LOA:</strong> When you lease IP addresses through our marketplace, an LOA is automatically generated and can be downloaded from your lease details page.</li>
                        <li><strong>Provide to Your Upstream:</strong> Submit the LOA to your ISP, hosting provider, or IXP where you want to announce the IP addresses.</li>
                        <li><strong>Configure BGP:</strong> Once approved, configure your BGP session to announce the leased IP range using your ASN.</li>
                        <li><strong>Verify RPKI/ROA:</strong> Ensure the IP holder has created an ROA record for your ASN to pass RPKI validation.</li>
                    </ol>

                    <h2>LOA Verification</h2>
                    <p>All LOAs issued through Soltia IPS Marketplace include a unique verification code. Third parties can verify the authenticity of an LOA by:</p>
                    <ul>
                        <li>Using our online verification portal</li>
                        <li>Contacting our verification team directly</li>
                        <li>Checking the embedded QR code (if applicable)</li>
                    </ul>

                    <h2>Common LOA Issues and Solutions</h2>
                    <div class="bg-gray-50 rounded-lg p-6 my-6">
                        <h3 class="text-lg font-semibold mb-4">Troubleshooting</h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="font-medium">LOA rejected by upstream provider</dt>
                                <dd class="text-gray-600 mt-1">Ensure the LOA format matches your provider's requirements. Some providers have specific templates they require.</dd>
                            </div>
                            <div>
                                <dt class="font-medium">LOA expired</dt>
                                <dd class="text-gray-600 mt-1">LOAs are tied to your lease period. Renew your lease to receive an updated LOA.</dd>
                            </div>
                            <div>
                                <dt class="font-medium">ASN mismatch</dt>
                                <dd class="text-gray-600 mt-1">Make sure the ASN on the LOA matches exactly what you're using for BGP announcements.</dd>
                            </div>
                        </dl>
                    </div>

                    <h2>Best Practices</h2>
                    <ul>
                        <li>Keep your LOAs stored securely and backed up</li>
                        <li>Renew leases before expiration to avoid service interruption</li>
                        <li>Provide accurate ASN information when leasing IP addresses</li>
                        <li>Request updated LOAs if your company information changes</li>
                    </ul>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('marketplace.index') }}" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">shopping_cart</span>
                            Browse IP Marketplace
                        </a>
                        <a href="{{ route('leases.index') }}" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">assignment</span>
                            View My Leases
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
