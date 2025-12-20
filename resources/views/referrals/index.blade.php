<x-app-layout>
    <x-slot name="header">{{ __('Referral Program') }}</x-slot>
    <x-slot name="title">{{ __('Referral Program') }}</x-slot>

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('Referral Program') }}</h2>
            <p class="text-gray-500 mt-1">{{ __('Earn :rate% commission for every referred customer', ['rate' => $stats['commission_rate']]) }}</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Total Referrals') }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_referrals'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-primary-600">people</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Active') }}</p>
                        <p class="text-3xl font-bold text-success-600">{{ $stats['active_referrals'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-success-600">check_circle</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Pending Earnings') }}</p>
                        <p class="text-3xl font-bold text-warning-600">€{{ number_format($stats['pending_earnings'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-warning-600">schedule</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Total Earnings') }}</p>
                        <p class="text-3xl font-bold text-gray-800">€{{ number_format($stats['total_earnings'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-secondary-600">euro</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Link -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Your Referral Link') }}</h3>
            <div class="flex items-center gap-4">
                <div class="flex-1 bg-gray-50 rounded-lg p-4 font-mono text-sm text-gray-800 overflow-x-auto">
                    {{ $stats['referral_link'] }}
                </div>
                <button onclick="navigator.clipboard.writeText('{{ $stats['referral_link'] }}').then(() => alert('Copied!'))"
                        class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 flex items-center">
                    <span class="material-icons-outlined mr-2">content_copy</span>
                    {{ __('Copy') }}
                </button>
            </div>
            <p class="text-gray-500 text-sm mt-3">
                {{ __('Share this link with friends. When they sign up and make a purchase, you earn :rate% commission!', ['rate' => $stats['commission_rate']]) }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Referred Users -->
            <div class="bg-white rounded-xl shadow-material-1">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Referred Users') }}</h3>
                </div>
                <div class="p-6">
                    @if($referred->isEmpty())
                    <div class="text-center py-8">
                        <span class="material-icons-outlined text-4xl text-gray-300">person_add</span>
                        <p class="mt-2 text-gray-500">{{ __('No referrals yet') }}</p>
                        <p class="text-gray-400 text-sm">{{ __('Share your link to start earning') }}</p>
                    </div>
                    @else
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($referred as $referral)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-primary-600 font-semibold">{{ substr($referral->referred->name ?? '?', 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-800">{{ $referral->referred->name ?? 'Unknown' }}</p>
                                    <p class="text-gray-500 text-sm">{{ $referral->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $referral->status === 'active' ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700' }}">
                                {{ ucfirst($referral->status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Earnings Chart -->
            <div class="bg-white rounded-xl shadow-material-1">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Monthly Earnings') }}</h3>
                </div>
                <div class="p-6">
                    <canvas id="earningsChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Pending Rewards -->
        @if($pendingRewards->isNotEmpty())
        <div class="bg-white rounded-xl shadow-material-1">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Pending Rewards') }}</h3>
                <span class="text-lg font-bold text-success-600">€{{ number_format($pendingRewards->sum('amount'), 2) }}</span>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Date') }}</th>
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Referred User') }}</th>
                                <th class="text-left py-3 px-4 text-gray-500 font-medium">{{ __('Source') }}</th>
                                <th class="text-right py-3 px-4 text-gray-500 font-medium">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRewards as $reward)
                            <tr class="border-b border-gray-50">
                                <td class="py-3 px-4 text-gray-600">{{ $reward->created_at->format('M d, Y') }}</td>
                                <td class="py-3 px-4">{{ $reward->referral->referred->name ?? 'Unknown' }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ ucfirst($reward->source_type) }}</td>
                                <td class="py-3 px-4 text-right font-medium text-success-600">€{{ number_format($reward->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- How It Works -->
        <div class="bg-info-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-info-800 mb-4">{{ __('How It Works') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-info-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-info-700 font-bold">1</span>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-info-800">{{ __('Share Your Link') }}</p>
                        <p class="text-info-600 text-sm">{{ __('Send your unique referral link to friends and colleagues') }}</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-info-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-info-700 font-bold">2</span>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-info-800">{{ __('They Sign Up') }}</p>
                        <p class="text-info-600 text-sm">{{ __('When they register using your link, they become your referral') }}</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-info-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-info-700 font-bold">3</span>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-info-800">{{ __('Earn Commission') }}</p>
                        <p class="text-info-600 text-sm">{{ __('Get :rate% of every purchase they make', ['rate' => $stats['commission_rate']]) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('earningsChart'), {
            type: 'bar',
            data: {
                labels: @json(array_column($monthlyStats, 'month')),
                datasets: [{
                    label: '{{ __("Earnings") }}',
                    data: @json(array_column($monthlyStats, 'earnings')),
                    backgroundColor: '#22c55e'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '€' + value
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
