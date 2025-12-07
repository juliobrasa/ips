<x-admin-layout>
    <x-slot name="header">{{ __('Security & IP Reputation') }}</x-slot>
    <x-slot name="title">{{ __('Security Dashboard') }}</x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Open Reports -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Open Reports') }}</p>
                    <p class="text-3xl font-bold {{ $stats['open_reports'] > 0 ? 'text-red-600' : 'text-gray-800' }}">
                        {{ number_format($stats['open_reports']) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-red-600">report_problem</span>
                </div>
            </div>
            @if($stats['critical_reports'] > 0)
                <div class="mt-2 text-xs text-red-600 font-medium">
                    {{ __(':count critical requiring immediate action', ['count' => $stats['critical_reports']]) }}
                </div>
            @endif
        </div>

        <!-- Investigating -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Investigating') }}</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['investigating_reports']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-yellow-600">search</span>
                </div>
            </div>
        </div>

        <!-- Resolved This Month -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Resolved This Month') }}</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['resolved_this_month']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <!-- IP Reputation Overview -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Clean Subnets') }}</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['clean_subnets']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600">verified</span>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                {{ __(':warning warning, :blocklisted blocklisted', ['warning' => $stats['warning_subnets'], 'blocklisted' => $stats['blocklisted_subnets']]) }}
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl p-6 shadow-material-1 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Quick Actions') }}</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.security.blocklist-check') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <span class="material-icons-outlined mr-2">manage_search</span>
                {{ __('Blocklist Checker') }}
            </a>
            <a href="{{ route('admin.security.abuse-reports') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <span class="material-icons-outlined mr-2">flag</span>
                {{ __('View All Reports') }}
            </a>
            <a href="{{ route('admin.subnets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <span class="material-icons-outlined mr-2">lan</span>
                {{ __('Manage Subnets') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Reports by Type -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Reports by Type') }}</h2>
            <div class="space-y-3">
                @php
                    $typeIcons = [
                        'spam' => ['icon' => 'email', 'color' => 'yellow'],
                        'phishing' => ['icon' => 'phishing', 'color' => 'orange'],
                        'malware' => ['icon' => 'bug_report', 'color' => 'red'],
                        'ddos' => ['icon' => 'flash_on', 'color' => 'purple'],
                        'scraping' => ['icon' => 'travel_explore', 'color' => 'blue'],
                        'fraud' => ['icon' => 'gpp_bad', 'color' => 'red'],
                        'other' => ['icon' => 'help', 'color' => 'gray'],
                    ];
                    $typeTranslations = [
                        'spam' => __('Spam'),
                        'phishing' => __('Phishing'),
                        'malware' => __('Malware'),
                        'ddos' => __('DDoS'),
                        'scraping' => __('Scraping'),
                        'fraud' => __('Fraud'),
                        'other' => __('Other'),
                    ];
                @endphp
                @forelse($reportsByType as $type => $count)
                    @php $config = $typeIcons[$type] ?? $typeIcons['other']; @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-{{ $config['color'] }}-600 mr-3">{{ $config['icon'] }}</span>
                            <span class="font-medium">{{ $typeTranslations[$type] ?? ucfirst($type) }}</span>
                        </div>
                        <span class="bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $count }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">{{ __('No abuse reports recorded yet.') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Subnets Needing Attention -->
        <div class="bg-white rounded-xl p-6 shadow-material-1">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Subnets Needing Attention') }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                            <th class="pb-3">{{ __('Subnet') }}</th>
                            <th class="pb-3">{{ __('Holder') }}</th>
                            <th class="pb-3">{{ __('Score') }}</th>
                            <th class="pb-3">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($subnetsNeedingAttention as $subnet)
                            <tr>
                                <td class="py-3">
                                    <span class="font-mono text-sm">{{ $subnet->cidr_notation }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="text-sm text-gray-600">{{ $subnet->company->name ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3">
                                    @php
                                        $scoreColor = $subnet->reputation_score < 50 ? 'red' : 'yellow';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $scoreColor }}-100 text-{{ $scoreColor }}-800">
                                        {{ $subnet->reputation_score ?? 'N/A' }}%
                                    </span>
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('admin.subnets.show', $subnet) }}" class="text-primary-600 hover:text-primary-800 text-sm">
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">
                                    {{ __('All subnets have good reputation scores.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Abuse Reports -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">{{ __('Recent Abuse Reports') }}</h2>
                <a href="{{ route('admin.security.abuse-reports') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                    {{ __('View All') }} &rarr;
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">{{ __('ID') }}</th>
                        <th class="px-6 py-3">{{ __('Type') }}</th>
                        <th class="px-6 py-3">{{ __('Subnet') }}</th>
                        <th class="px-6 py-3">{{ __('Severity') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3">{{ __('Reported') }}</th>
                        <th class="px-6 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentReports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium">#{{ $report->id }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $typeTranslations[$report->type] ?? ucfirst($report->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm">
                                {{ $report->subnet->cidr_notation ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $severityColors = [
                                        'critical' => 'red',
                                        'high' => 'orange',
                                        'medium' => 'yellow',
                                        'low' => 'green',
                                    ];
                                    $severityTranslations = [
                                        'critical' => __('Critical'),
                                        'high' => __('High'),
                                        'medium' => __('Medium'),
                                        'low' => __('Low'),
                                    ];
                                    $color = $severityColors[$report->severity] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ $severityTranslations[$report->severity] ?? ucfirst($report->severity) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'open' => 'red',
                                        'investigating' => 'yellow',
                                        'resolved' => 'green',
                                        'dismissed' => 'gray',
                                    ];
                                    $statusTranslations = [
                                        'open' => __('Open'),
                                        'investigating' => __('Investigating'),
                                        'resolved' => __('Resolved'),
                                        'dismissed' => __('Dismissed'),
                                    ];
                                    $statusColor = $statusColors[$report->status] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                    {{ $statusTranslations[$report->status] ?? ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $report->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.security.abuse-reports.show', $report) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                {{ __('No abuse reports found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
