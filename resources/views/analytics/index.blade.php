<x-app-layout>
    <x-slot name="header">{{ __('Analytics') }}</x-slot>
    <x-slot name="title">{{ __('Analytics Dashboard') }}</x-slot>

    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Subnets -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Total Subnets') }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['subnets']['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-primary-600">lan</span>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-success-600">{{ $stats['subnets']['available'] }} {{ __('available') }}</span>
                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-info-600">{{ $stats['subnets']['leased'] }} {{ __('leased') }}</span>
                </div>
            </div>

            <!-- Active Leases -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Active Leases') }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['leases']['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-success-600">assignment</span>
                    </div>
                </div>
                @if($stats['leases']['expiring_soon'] > 0)
                <div class="mt-4 flex items-center text-sm text-warning-600">
                    <span class="material-icons-outlined text-sm mr-1">schedule</span>
                    {{ $stats['leases']['expiring_soon'] }} {{ __('expiring within 30 days') }}
                </div>
                @endif
            </div>

            <!-- Revenue This Month -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('Revenue This Month') }}</p>
                        <p class="text-3xl font-bold text-gray-800">€{{ number_format($stats['revenue']['current'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-secondary-600">euro</span>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    @if($stats['revenue']['change_percent'] >= 0)
                    <span class="text-success-600 flex items-center">
                        <span class="material-icons-outlined text-sm mr-1">trending_up</span>
                        +{{ $stats['revenue']['change_percent'] }}%
                    </span>
                    @else
                    <span class="text-danger-600 flex items-center">
                        <span class="material-icons-outlined text-sm mr-1">trending_down</span>
                        {{ $stats['revenue']['change_percent'] }}%
                    </span>
                    @endif
                    <span class="ml-2 text-gray-500">{{ __('vs last month') }}</span>
                </div>
            </div>

            <!-- IP Utilization -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">{{ __('IP Utilization') }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['ips']['utilization'] }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
                        <span class="material-icons-outlined text-info-600">donut_large</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-info-600 h-2 rounded-full" style="width: {{ $stats['ips']['utilization'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($stats['ips']['leased']) }} / {{ number_format($stats['ips']['total']) }} IPs</p>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Revenue Trend') }}</h3>
                    <div class="text-sm text-gray-500">{{ __('Last 12 months') }}</div>
                </div>
                <canvas id="revenueChart" height="250"></canvas>
            </div>

            <!-- Lease Trend Chart -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Lease Activity') }}</h3>
                    <div class="text-sm text-gray-500">{{ __('Last 12 months') }}</div>
                </div>
                <canvas id="leaseChart" height="250"></canvas>
            </div>
        </div>

        <!-- Second Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Subnet Distribution -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">{{ __('Subnet Distribution') }}</h3>
                <canvas id="subnetDistChart" height="250"></canvas>
            </div>

            <!-- IP Reputation -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">{{ __('IP Reputation Summary') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('Clean IPs') }}</span>
                        <span class="px-3 py-1 bg-success-100 text-success-700 rounded-full text-sm font-medium">
                            {{ $reputationSummary['clean'] }} ({{ $reputationSummary['clean_percent'] }}%)
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('Warning') }}</span>
                        <span class="px-3 py-1 bg-warning-100 text-warning-700 rounded-full text-sm font-medium">
                            {{ $reputationSummary['warning'] }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('Critical') }}</span>
                        <span class="px-3 py-1 bg-danger-100 text-danger-700 rounded-full text-sm font-medium">
                            {{ $reputationSummary['critical'] }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('Unchecked') }}</span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">
                            {{ $reputationSummary['unchecked'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">{{ __('Export Data') }}</h3>
                <div class="space-y-3">
                    <a href="{{ route('analytics.export', ['type' => 'revenue', 'format' => 'csv']) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <span class="material-icons-outlined text-primary-600 mr-3">download</span>
                        <span>{{ __('Export Revenue (CSV)') }}</span>
                    </a>
                    <a href="{{ route('analytics.export', ['type' => 'leases', 'format' => 'csv']) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <span class="material-icons-outlined text-primary-600 mr-3">download</span>
                        <span>{{ __('Export Leases (CSV)') }}</span>
                    </a>
                    <a href="{{ route('analytics.export', ['type' => 'subnets', 'format' => 'csv']) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <span class="material-icons-outlined text-primary-600 mr-3">download</span>
                        <span>{{ __('Export Subnets (CSV)') }}</span>
                    </a>
                    <a href="{{ route('analytics.export', ['type' => 'subnets', 'format' => 'json']) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <span class="material-icons-outlined text-secondary-600 mr-3">code</span>
                        <span>{{ __('Export Subnets (JSON)') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($revenueChart['labels']),
                datasets: [{
                    label: '{{ __("Revenue") }}',
                    data: @json($revenueChart['data']),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '€' + value.toLocaleString()
                        }
                    }
                }
            }
        });

        // Lease Chart
        new Chart(document.getElementById('leaseChart'), {
            type: 'bar',
            data: {
                labels: @json($leaseTrend['labels']),
                datasets: [{
                    label: '{{ __("Created") }}',
                    data: @json($leaseTrend['created']),
                    backgroundColor: '#22c55e'
                }, {
                    label: '{{ __("Expired") }}',
                    data: @json($leaseTrend['expired']),
                    backgroundColor: '#ef4444'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Subnet Distribution Chart
        new Chart(document.getElementById('subnetDistChart'), {
            type: 'doughnut',
            data: {
                labels: @json($subnetDistribution['labels']),
                datasets: [{
                    data: @json($subnetDistribution['data']),
                    backgroundColor: ['#4f46e5', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
