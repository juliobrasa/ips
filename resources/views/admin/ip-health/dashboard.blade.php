@extends('layouts.admin')

@section('title', __('IP Health Dashboard'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('IP Health Dashboard') }}</h1>
            <p class="text-gray-600">{{ __('Overview of IP reputation and abuse status') }}</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Average Score -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full {{ $stats['average_score'] >= 85 ? 'bg-green-100' : ($stats['average_score'] >= 70 ? 'bg-yellow-100' : 'bg-red-100') }}">
                        <svg class="w-8 h-8 {{ $stats['average_score'] >= 85 ? 'text-green-600' : ($stats['average_score'] >= 70 ? 'text-yellow-600' : 'text-red-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('Average Score') }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['average_score'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Clean Percentage -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('Clean IPs') }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['clean_percentage'] }}%</p>
                        <p class="text-xs text-gray-400">{{ $stats['clean'] }}/{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Open Abuse -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full {{ $stats['open_abuse'] > 0 ? 'bg-red-100' : 'bg-gray-100' }}">
                        <svg class="w-8 h-8 {{ $stats['open_abuse'] > 0 ? 'text-red-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('Open Abuse Reports') }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['open_abuse'] }}</p>
                        @if($stats['critical_abuse'] > 0)
                        <p class="text-xs text-red-600">{{ $stats['critical_abuse'] }} {{ __('critical') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stale Checks -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full {{ $stats['stale'] > 0 ? 'bg-yellow-100' : 'bg-gray-100' }}">
                        <svg class="w-8 h-8 {{ $stats['stale'] > 0 ? 'text-yellow-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('Needing Check') }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['stale'] + $stats['unchecked'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Critical Subnets -->
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">{{ __('Critical Subnets') }}</h3>
                    <a href="{{ route('admin.ip-health.at-risk') }}" class="text-sm text-rose-600 hover:text-rose-800">{{ __('View All') }}</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($criticalSubnets->take(5) as $subnet)
                    <div class="px-6 py-4 flex justify-between items-center">
                        <div>
                            <div class="font-mono font-medium">{{ $subnet->cidr_notation }}</div>
                            <div class="text-sm text-gray-500">{{ $subnet->company->name ?? '-' }}</div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                {{ $subnet->reputation_score ?? 0 }}/100
                            </span>
                            <a href="{{ route('admin.subnets.show', $subnet) }}" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        {{ __('No critical subnets!') }}
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Blocklist Distribution -->
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-900">{{ __('Top Blocklists') }}</h3>
                </div>
                <div class="p-6">
                    @if(count($blocklistDistribution) > 0)
                    <div class="space-y-4">
                        @foreach(array_slice($blocklistDistribution, 0, 8) as $blocklist => $count)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $blocklist }}</span>
                                <span class="text-gray-500">{{ $count }} {{ __('IPs') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-rose-500 h-2 rounded-full" style="width: {{ min(100, ($count / max(1, $stats['total'])) * 100 * 5) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center text-gray-500 py-4">
                        {{ __('No blocklist data available') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Abuse Reports -->
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">{{ __('Recent Abuse Reports') }}</h3>
                <a href="{{ route('admin.security.abuse-reports') }}" class="text-sm text-rose-600 hover:text-rose-800">{{ __('View All') }}</a>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Subnet') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Severity') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentAbuse as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-sm">{{ $report->subnet->cidr_notation ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm capitalize">{{ $report->type }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $report->severity_color }}-100 text-{{ $report->severity_color }}-800">
                                {{ ucfirst($report->severity) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $report->status_color }}-100 text-{{ $report->status_color }}-800">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $report->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('No recent abuse reports') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
