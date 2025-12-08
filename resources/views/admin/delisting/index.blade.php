@extends('layouts.admin')

@section('title', __('Blacklist Delisting Management'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Blacklist Delisting') }}</h1>
                <p class="text-gray-600">{{ __('Manage IP delisting requests from various blocklists') }}</p>
            </div>
            <a href="{{ route('admin.delisting.pending') }}"
                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                {{ __('Pending') }} ({{ $stats['pending'] + $stats['manual_required'] }})
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <div class="text-sm text-yellow-600">{{ __('Pending') }}</div>
                <div class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="text-sm text-blue-600">{{ __('In Progress') }}</div>
                <div class="text-2xl font-bold text-blue-700">{{ $stats['in_progress'] }}</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="text-sm text-green-600">{{ __('Delisted') }}</div>
                <div class="text-2xl font-bold text-green-700">{{ $stats['delisted'] }}</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <div class="text-sm text-red-600">{{ __('Failed') }}</div>
                <div class="text-2xl font-bold text-red-700">{{ $stats['failed'] }}</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                <div class="text-sm text-orange-600">{{ __('Manual Required') }}</div>
                <div class="text-2xl font-bold text-orange-700">{{ $stats['manual_required'] }}</div>
            </div>
        </div>

        <!-- Blocklist Reference -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">{{ __('Blocklist Reference') }}</h3>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($blocklists as $domain => $info)
                <div class="border rounded-lg p-3">
                    <div class="font-medium text-gray-900">{{ $info['name'] }}</div>
                    <div class="text-xs text-gray-500 font-mono">{{ $domain }}</div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $info['delisting_method'] === 'automatic' ? 'bg-green-100 text-green-700' :
                               ($info['delisting_method'] === 'self_service' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst(str_replace('_', ' ', $info['delisting_method'])) }}
                        </span>
                        @if($info['delisting_url'])
                        <a href="{{ $info['delisting_url'] }}" target="_blank" class="text-xs text-rose-600 hover:underline">
                            {{ __('Delist') }} &rarr;
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Subnet') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Blocklist') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Requested') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Last Check') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-mono font-medium">{{ $request->subnet->cidr_notation }}</div>
                            <div class="text-xs text-gray-500">{{ $request->subnet->company->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $request->blocklist }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $request->status === 'delisted' ? 'bg-green-100 text-green-800' :
                                   ($request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                   ($request->status === 'failed' ? 'bg-red-100 text-red-800' :
                                   ($request->status === 'manual_required' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'))) }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $request->requested_at?->diffForHumans() ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $request->last_checked_at?->diffForHumans() ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.delisting.show', $request) }}"
                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                    {{ __('Details') }}
                                </a>
                                @if(!in_array($request->status, ['delisted']))
                                <form action="{{ route('admin.delisting.check-status', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm">
                                        {{ __('Check') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            {{ __('No delisting requests found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
