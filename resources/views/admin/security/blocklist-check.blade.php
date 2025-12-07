<x-admin-layout>
    <x-slot name="header">{{ __('Blocklist Checker') }}</x-slot>
    <x-slot name="title">{{ __('IP Blocklist Checker') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Check Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl p-6 shadow-material-1 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Check IP Address') }}</h2>
                <form id="ip-check-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="ip" class="block text-sm font-medium text-gray-700 mb-1">{{ __('IP Address') }}</label>
                        <input type="text" id="ip" name="ip"
                               placeholder="{{ __('e.g., 192.168.1.1') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                               required>
                    </div>
                    <button type="submit" id="check-btn" class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors flex items-center justify-center">
                        <span class="material-icons-outlined mr-2">search</span>
                        {{ __('Check IP') }}
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-material-1">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Check Subnet') }}</h2>
                <form id="subnet-check-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="subnet_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Select Subnet') }}</label>
                        <select id="subnet_id" name="subnet_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                required>
                            <option value="">{{ __('Select a subnet...') }}</option>
                            @foreach(\App\Models\Subnet::with('company')->orderBy('ip_address')->get() as $subnet)
                                <option value="{{ $subnet->id }}">
                                    {{ $subnet->cidr_notation }} ({{ $subnet->company->name ?? __('Unknown') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" id="check-subnet-btn" class="w-full bg-secondary-600 text-white px-4 py-2 rounded-lg hover:bg-secondary-700 transition-colors flex items-center justify-center">
                        <span class="material-icons-outlined mr-2">lan</span>
                        {{ __('Check Subnet') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Results Panel -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl p-6 shadow-material-1 mb-6" id="results-panel" style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('Check Results') }}</h2>
                    <div id="result-summary" class="flex items-center"></div>
                </div>

                <div id="result-info" class="mb-4 p-4 bg-gray-50 rounded-lg"></div>

                <div class="overflow-x-auto">
                    <table class="w-full" id="results-table">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-3">{{ __('Blocklist') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3">{{ __('Details') }}</th>
                            </tr>
                        </thead>
                        <tbody id="results-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Checks -->
            <div class="bg-white rounded-xl p-6 shadow-material-1">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Recent Checks') }}</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-3">{{ __('IP Address') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3">{{ __('Score') }}</th>
                                <th class="px-4 py-3">{{ __('Checked') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentChecks as $check)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-sm">{{ $check['ip'] }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $summary = $check['results'] ?? [];
                                            $listed = collect($summary)->where('listed', true)->count();
                                        @endphp
                                        @if($listed > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ __(':count Blocklisted', ['count' => $listed]) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('Clean') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $total = count($summary);
                                            $clean = collect($summary)->where('listed', false)->count();
                                            $score = $total > 0 ? round(($clean / $total) * 100) : 0;
                                        @endphp
                                        <span class="text-sm font-medium">{{ $score }}%</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($check['checked_at'])->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        {{ __('No recent checks. Start by checking an IP above.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Blocklist Info -->
    <div class="mt-6 bg-white rounded-xl p-6 shadow-material-1">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Blocklists Checked') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-1">AbuseIPDB</h3>
                <p class="text-sm text-gray-600">{{ __('Crowdsourced IP reputation database with abuse confidence scoring.') }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-1">Spamhaus ZEN</h3>
                <p class="text-sm text-gray-600">{{ __('Combined blocklist (SBL+XBL+PBL) for spam and exploit detection.') }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-1">SpamCop</h3>
                <p class="text-sm text-gray-600">{{ __('User-reported spam sources blocklist.') }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-1">Barracuda</h3>
                <p class="text-sm text-gray-600">{{ __('Barracuda Networks reputation blocklist.') }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-1">SORBS</h3>
                <p class="text-sm text-gray-600">{{ __('Spam and Open Relay Blocking System.') }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-1">SpamRATS</h3>
                <p class="text-sm text-gray-600">{{ __('IP addresses detected sending spam.') }}</p>
            </div>
        </div>
    </div>

    @php
        $jsTranslations = [
            'checking' => __('Checking...'),
            'checkIp' => __('Check IP'),
            'checkSubnet' => __('Check Subnet'),
            'clean' => __('Clean'),
            'blocklisted' => __('Blocklisted'),
            'unknown' => __('Unknown'),
            'listed' => __('Listed'),
            'error' => __('Error'),
            'errorChecking' => __('An error occurred while checking the IP.'),
            'errorCheckingSubnet' => __('An error occurred while checking the subnet.'),
            'score' => __('Score'),
            'cleanCount' => __('clean'),
            'listedCount' => __('listed'),
            'errorsCount' => __('errors'),
            'abuseScore' => __('Abuse Score'),
            'reports' => __('Reports'),
            'response' => __('Response'),
            'goodReputation' => __('Good Reputation'),
            'warning' => __('Warning'),
            'poorReputation' => __('Poor Reputation'),
            'reputationScore' => __('Reputation Score'),
            'sampledIps' => __('Sampled IPs'),
        ];
    @endphp

    @push('scripts')
    <script>
        const translations = @json($jsTranslations);

        document.getElementById('ip-check-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const ip = document.getElementById('ip').value;
            const btn = document.getElementById('check-btn');

            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin material-icons-outlined mr-2">autorenew</span> ' + translations.checking;

            try {
                const response = await fetch('{{ route("admin.security.check-ip") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ip: ip })
                });

                const data = await response.json();
                displayResults(data);
            } catch (error) {
                console.error('Error:', error);
                alert(translations.errorChecking);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-icons-outlined mr-2">search</span> ' + translations.checkIp;
            }
        });

        document.getElementById('subnet-check-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const subnetId = document.getElementById('subnet_id').value;
            const btn = document.getElementById('check-subnet-btn');

            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin material-icons-outlined mr-2">autorenew</span> ' + translations.checking;

            try {
                const response = await fetch('{{ route("admin.security.check-subnet") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ subnet_id: subnetId })
                });

                const data = await response.json();
                displaySubnetResults(data);
            } catch (error) {
                console.error('Error:', error);
                alert(translations.errorCheckingSubnet);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-icons-outlined mr-2">lan</span> ' + translations.checkSubnet;
            }
        });

        function displayResults(data) {
            const panel = document.getElementById('results-panel');
            const body = document.getElementById('results-body');
            const summary = document.getElementById('result-summary');
            const info = document.getElementById('result-info');

            panel.style.display = 'block';
            body.innerHTML = '';

            // Summary badge
            const summaryData = data.summary;
            if (summaryData.status === 'clean') {
                summary.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><span class="material-icons-outlined mr-1 text-sm">check_circle</span>' + translations.clean + '</span>';
            } else if (summaryData.status === 'blocklisted') {
                summary.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><span class="material-icons-outlined mr-1 text-sm">warning</span>' + translations.blocklisted + '</span>';
            } else {
                summary.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"><span class="material-icons-outlined mr-1 text-sm">help</span>' + translations.unknown + '</span>';
            }

            // Info section
            info.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-mono text-lg">${data.ip}</span>
                        <span class="ml-4 text-sm text-gray-500">${translations.score}: ${summaryData.score}%</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        ${summaryData.clean} ${translations.cleanCount} / ${summaryData.listed} ${translations.listedCount} / ${summaryData.errors} ${translations.errorsCount}
                    </div>
                </div>
            `;

            // Results table
            for (const [key, result] of Object.entries(data.results)) {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';

                let statusBadge, details;
                if (result.listed === true) {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">' + translations.listed + '</span>';
                } else if (result.listed === false) {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">' + translations.clean + '</span>';
                } else {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' + translations.error + '</span>';
                }

                if (result.score !== undefined) {
                    details = `${translations.abuseScore}: ${result.score}%, ${translations.reports}: ${result.total_reports || 0}`;
                } else if (result.error) {
                    details = result.error;
                } else if (result.response) {
                    details = `${translations.response}: ${result.response}`;
                } else {
                    details = '-';
                }

                row.innerHTML = `
                    <td class="px-4 py-3 font-medium">${result.name}</td>
                    <td class="px-4 py-3">${statusBadge}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${details}</td>
                `;
                body.appendChild(row);
            }
        }

        function displaySubnetResults(data) {
            const panel = document.getElementById('results-panel');
            const body = document.getElementById('results-body');
            const summary = document.getElementById('result-summary');
            const info = document.getElementById('result-info');

            panel.style.display = 'block';
            body.innerHTML = '';

            const results = data.results;

            // Summary badge
            if (results.score >= 80) {
                summary.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><span class="material-icons-outlined mr-1 text-sm">check_circle</span>' + translations.goodReputation + '</span>';
            } else if (results.score >= 50) {
                summary.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"><span class="material-icons-outlined mr-1 text-sm">warning</span>' + translations.warning + '</span>';
            } else {
                summary.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><span class="material-icons-outlined mr-1 text-sm">error</span>' + translations.poorReputation + '</span>';
            }

            // Info section
            info.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-mono text-lg">${data.subnet}</span>
                        <span class="ml-4 text-sm text-gray-500">${translations.reputationScore}: ${results.score}%</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        ${translations.sampledIps}: ${results.sample_ips.join(', ')}
                    </div>
                </div>
            `;

            // Results by sample IP
            for (const [ip, checks] of Object.entries(results.details)) {
                const headerRow = document.createElement('tr');
                headerRow.className = 'bg-gray-100';
                headerRow.innerHTML = `<td colspan="3" class="px-4 py-2 font-mono font-medium">${ip}</td>`;
                body.appendChild(headerRow);

                for (const [key, result] of Object.entries(checks)) {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';

                    let statusBadge;
                    if (result.listed === true) {
                        statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">' + translations.listed + '</span>';
                    } else if (result.listed === false) {
                        statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">' + translations.clean + '</span>';
                    } else {
                        statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' + translations.error + '</span>';
                    }

                    row.innerHTML = `
                        <td class="px-4 py-2 pl-8">${result.name}</td>
                        <td class="px-4 py-2">${statusBadge}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">${result.error || result.response || '-'}</td>
                    `;
                    body.appendChild(row);
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>
