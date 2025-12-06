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
                    <h2>What is IP Reputation?</h2>
                    <p><strong>IP reputation</strong> refers to the trustworthiness score assigned to an IP address based on its historical behavior. This score is maintained by various security organizations, email providers, and threat intelligence platforms. A clean IP reputation is essential for legitimate business operations, especially for email delivery, web hosting, and API services.</p>

                    <div class="bg-red-50 border-l-4 border-red-500 p-4 my-6">
                        <p class="font-semibold text-red-800 mb-2">Important Warning</p>
                        <p class="text-red-700">Poor IP reputation can result in emails being blocked, websites being flagged as malicious, and services being denied access to major platforms. Prevention is much easier than recovery.</p>
                    </div>

                    <h2>Common Blocklists</h2>
                    <p>There are numerous blocklists that track problematic IP addresses:</p>

                    <div class="overflow-x-auto my-6">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Blocklist</th>
                                    <th class="px-4 py-2 text-left">Focus</th>
                                    <th class="px-4 py-2 text-left">Impact</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr>
                                    <td class="px-4 py-2 font-medium">Spamhaus</td>
                                    <td class="px-4 py-2">Spam, malware, botnets</td>
                                    <td class="px-4 py-2 text-red-600">Very High</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">SpamCop</td>
                                    <td class="px-4 py-2">Spam sources</td>
                                    <td class="px-4 py-2 text-orange-600">High</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">Barracuda</td>
                                    <td class="px-4 py-2">General abuse</td>
                                    <td class="px-4 py-2 text-orange-600">High</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">AbuseIPDB</td>
                                    <td class="px-4 py-2">All abuse types</td>
                                    <td class="px-4 py-2 text-yellow-600">Medium</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">SORBS</td>
                                    <td class="px-4 py-2">Spam, open relays</td>
                                    <td class="px-4 py-2 text-yellow-600">Medium</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h2>Causes of Poor IP Reputation</h2>
                    <ul>
                        <li><strong>Spam:</strong> Sending unsolicited bulk email is the most common cause</li>
                        <li><strong>Malware Distribution:</strong> Hosting or spreading malicious software</li>
                        <li><strong>Phishing:</strong> Hosting phishing pages or sending phishing emails</li>
                        <li><strong>Botnet Activity:</strong> IP used as part of a botnet (C2 server or infected host)</li>
                        <li><strong>Port Scanning:</strong> Aggressive network scanning activities</li>
                        <li><strong>Brute Force Attacks:</strong> Password guessing attempts</li>
                        <li><strong>DDoS Participation:</strong> Being source or target of DDoS attacks</li>
                    </ul>

                    <h2>How to Check IP Reputation</h2>
                    <p>Regularly monitor your IP addresses using these tools:</p>

                    <div class="bg-gray-50 rounded-lg p-6 my-6">
                        <h3 class="text-lg font-semibold mb-4">Recommended Tools</h3>
                        <ul class="space-y-2">
                            <li><strong>MXToolbox:</strong> Comprehensive blocklist checker for email servers</li>
                            <li><strong>AbuseIPDB:</strong> Crowdsourced abuse database with confidence scores</li>
                            <li><strong>VirusTotal:</strong> Security vendors' verdicts on IP addresses</li>
                            <li><strong>Talos Intelligence:</strong> Cisco's threat intelligence platform</li>
                            <li><strong>Soltia IPS:</strong> Our built-in reputation checker in your dashboard</li>
                        </ul>
                    </div>

                    <h2>Best Practices for Clean Reputation</h2>

                    <h3>For Email Servers</h3>
                    <ul>
                        <li>Implement SPF, DKIM, and DMARC authentication</li>
                        <li>Use proper opt-in for mailing lists</li>
                        <li>Monitor bounce rates and unsubscribes</li>
                        <li>Never buy email lists</li>
                        <li>Set up feedback loops with major email providers</li>
                    </ul>

                    <h3>For Web Hosting</h3>
                    <ul>
                        <li>Keep all software updated and patched</li>
                        <li>Use web application firewalls (WAF)</li>
                        <li>Monitor for compromised accounts</li>
                        <li>Implement DDoS protection</li>
                        <li>Regularly scan for malware</li>
                    </ul>

                    <h3>For General Use</h3>
                    <ul>
                        <li>Implement rate limiting on all services</li>
                        <li>Use fail2ban or similar intrusion prevention</li>
                        <li>Monitor outbound traffic for anomalies</li>
                        <li>Have clear abuse policies and respond quickly to complaints</li>
                        <li>Keep detailed logs for investigation</li>
                    </ul>

                    <h2>Recovering from Blocklisting</h2>
                    <p>If your IP gets blocklisted, follow these steps:</p>

                    <ol>
                        <li><strong>Identify the Cause:</strong> Determine what activity triggered the listing</li>
                        <li><strong>Stop the Abuse:</strong> Fix the underlying issue immediately</li>
                        <li><strong>Document Actions:</strong> Keep records of remediation steps taken</li>
                        <li><strong>Request Delisting:</strong> Submit removal requests to each blocklist</li>
                        <li><strong>Monitor:</strong> Continue monitoring to prevent recurrence</li>
                    </ol>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 my-6">
                        <p class="font-semibold text-blue-800 mb-2">Delisting Times</p>
                        <p class="text-blue-700">Different blocklists have different delisting processes. Some are automatic after a period, others require manual requests. Spamhaus typically requires manual delisting with explanation of remediation.</p>
                    </div>

                    <h2>IP Reputation and Leased IPs</h2>
                    <p>When leasing IP addresses through our marketplace:</p>
                    <ul>
                        <li>We verify the reputation of all IPs before listing</li>
                        <li>Each subnet has a reputation score visible in the marketplace</li>
                        <li>IP holders are required to maintain clean reputation</li>
                        <li>Lessees are responsible for maintaining reputation during the lease</li>
                        <li>Severe abuse may result in lease termination</li>
                    </ul>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Check Your IPs</h3>
                    <div class="space-y-3 text-sm">
                        <a href="https://mxtoolbox.com/blacklists.aspx" target="_blank" rel="noopener" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">open_in_new</span>
                            MXToolbox Blacklist Check
                        </a>
                        <a href="https://www.abuseipdb.com/check/" target="_blank" rel="noopener" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">open_in_new</span>
                            AbuseIPDB Check
                        </a>
                        <a href="https://talosintelligence.com/reputation_center" target="_blank" rel="noopener" class="flex items-center text-primary-600 hover:text-primary-800">
                            <span class="material-icons-outlined mr-2 text-sm">open_in_new</span>
                            Talos Intelligence
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
