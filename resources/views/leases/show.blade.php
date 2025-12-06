<x-app-layout>
    <x-slot name="header">{{ __('Lease Details') }}</x-slot>
    <x-slot name="title">{{ $lease->subnet->cidr_notation }}</x-slot>

    <div class="max-w-5xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('leases.index') }}" class="inline-flex items-center text-gray-600 hover:text-primary-600 mb-6">
            <span class="material-icons-outlined mr-1">arrow_back</span>
            {{ __('Back to My Leases') }}
        </a>

        <!-- Status Banner -->
        @if($lease->status === 'pending_payment')
        <div class="bg-warning-50 border border-warning-200 rounded-xl p-4 mb-6 flex items-start">
            <span class="material-icons-outlined text-warning-600 mr-3">payment</span>
            <div class="flex-1">
                <h4 class="font-medium text-warning-800">{{ __('Payment Required') }}</h4>
                <p class="text-sm text-warning-700">{{ __('Complete your payment to activate this lease.') }}</p>
            </div>
            <a href="{{ route('invoices.show', $lease->invoices->first()) }}" class="bg-warning-600 text-white px-4 py-2 rounded-lg hover:bg-warning-700 transition-colors font-medium">
                {{ __('Pay Now') }}
            </a>
        </div>
        @elseif($lease->status === 'pending_assignment')
        <div class="bg-primary-50 border border-primary-200 rounded-xl p-4 mb-6 flex items-start">
            <span class="material-icons-outlined text-primary-600 mr-3">settings</span>
            <div>
                <h4 class="font-medium text-primary-800">{{ __('Assign Your ASN') }}</h4>
                <p class="text-sm text-primary-700">{{ __('Configure your ASN to start using these IP addresses.') }}</p>
            </div>
        </div>
        @elseif($lease->status === 'active')
        <div class="bg-success-50 border border-success-200 rounded-xl p-4 mb-6 flex items-start">
            <span class="material-icons-outlined text-success-600 mr-3">check_circle</span>
            <div>
                <h4 class="font-medium text-success-800">{{ __('Lease Active') }}</h4>
                <p class="text-sm text-success-700">{{ __('Your IP addresses are ready to use. Next billing:') }} {{ $lease->end_date->format('M d, Y') }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Subnet Card -->
                <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-6 text-white">
                        <div class="flex items-center justify-between mb-2">
                            <h1 class="text-2xl font-bold">{{ $lease->subnet->cidr_notation }}</h1>
                            <span class="bg-white/20 px-3 py-1 rounded-lg text-sm">{{ $lease->subnet->rir }}</span>
                        </div>
                        <div class="flex items-center text-primary-100">
                            <span class="material-icons-outlined mr-2">location_on</span>
                            <span>{{ $lease->subnet->geolocation_country ?? __('Unknown') }}</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('IP Count') }}</p>
                                <p class="text-xl font-bold text-gray-800">{{ number_format($lease->subnet->ip_count) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Start IP') }}</p>
                                <p class="font-mono text-gray-800">{{ $lease->subnet->start_ip }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('End IP') }}</p>
                                <p class="font-mono text-gray-800">{{ $lease->subnet->end_ip }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Monthly Price') }}</p>
                                <p class="text-xl font-bold text-secondary-600">${{ number_format($lease->monthly_price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ASN Assignment -->
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('ASN Assignment') }}</h3>

                    @if($lease->assigned_asn)
                    <div class="bg-success-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-success-600">{{ __('Assigned ASN') }}</p>
                                <p class="text-2xl font-bold text-success-700">AS{{ $lease->assigned_asn }}</p>
                            </div>
                            <span class="material-icons-outlined text-success-500 text-3xl">verified</span>
                        </div>
                    </div>
                    @else
                    <form action="{{ route('leases.assignAsn', $lease) }}" method="POST">
                        @csrf
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Your ASN Number') }}</label>
                                <input type="text" name="asn" required
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                       placeholder="{{ __('e.g.') }}, 12345">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                                    {{ __('Assign ASN') }}
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ __('Enter the ASN number you want to use for these IP addresses.') }}
                        </p>
                    </form>
                    @endif
                </div>

                <!-- LOA Section -->
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Letter of Authorization (LOA)') }}</h3>

                    @if($lease->loa)
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <span class="material-icons-outlined text-primary-600 text-3xl mr-3">description</span>
                            <div>
                                <p class="font-medium text-gray-800">{{ $lease->loa->loa_number }}</p>
                                <p class="text-sm text-gray-500">{{ __('Generated') }} {{ $lease->loa->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('loa.download', $lease->loa) }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center">
                            <span class="material-icons-outlined mr-2">download</span>
                            {{ __('Download') }}
                        </a>
                    </div>
                    @elseif($lease->assigned_asn)
                    <div class="text-center py-4">
                        <p class="text-gray-500 mb-4">{{ __('Generate your LOA to authorize the use of these IP addresses.') }}</p>
                        <a href="{{ route('loa.generate', $lease) }}" class="inline-flex items-center bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                            <span class="material-icons-outlined mr-2">add</span>
                            {{ __('Generate LOA') }}
                        </a>
                    </div>
                    @else
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <span class="material-icons-outlined text-gray-400 text-3xl mb-2">description</span>
                        <p class="text-gray-500">{{ __('Assign an ASN first to generate your LOA.') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Holder Information -->
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('IP Holder') }}</h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                            <span class="material-icons-outlined text-primary-600">business</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $lease->holderCompany->company_name }}</p>
                            <p class="text-sm text-gray-500">{{ $lease->holderCompany->country }}</p>
                        </div>
                        @if($lease->holderCompany->kyc_verified_at)
                        <span class="ml-auto inline-flex items-center px-3 py-1 bg-success-100 text-success-700 rounded-full text-sm">
                            <span class="material-icons-outlined text-sm mr-1">verified</span>
                            {{ __('Verified') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Lease Summary -->
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Lease Summary') }}</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">{{ __('Status') }}</span>
                            @if($lease->status === 'active')
                            <span class="text-success-600 font-medium">{{ __('Active') }}</span>
                            @elseif($lease->status === 'pending_payment')
                            <span class="text-warning-600 font-medium">{{ __('Pending Payment') }}</span>
                            @elseif($lease->status === 'pending_assignment')
                            <span class="text-primary-600 font-medium">{{ __('Pending Assignment') }}</span>
                            @else
                            <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $lease->status)) }}</span>
                            @endif
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">{{ __('Start Date') }}</span>
                            <span class="text-gray-800">{{ $lease->start_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">{{ __('End Date') }}</span>
                            <span class="text-gray-800">{{ $lease->end_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">{{ __('Duration') }}</span>
                            <span class="text-gray-800">{{ $lease->start_date->diffInMonths($lease->end_date) }} {{ __('months') }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500">{{ __('Monthly Price') }}</span>
                            <span class="text-secondary-600 font-bold">${{ number_format($lease->monthly_price, 2) }}</span>
                        </div>
                    </div>

                    @if($lease->auto_renew)
                    <div class="mt-4 p-3 bg-primary-50 rounded-lg">
                        <div class="flex items-center text-primary-700 text-sm">
                            <span class="material-icons-outlined text-sm mr-2">autorenew</span>
                            {{ __('Auto-renewal enabled') }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Recent Invoices -->
                @if($lease->invoices->count() > 0)
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Recent Invoices') }}</h3>

                    <div class="space-y-3">
                        @foreach($lease->invoices->take(3) as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <p class="font-medium text-gray-800">{{ $invoice->invoice_number }}</p>
                                <p class="text-sm text-gray-500">{{ $invoice->due_date->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-800">${{ number_format($invoice->total, 2) }}</p>
                                @if($invoice->status === 'paid')
                                <span class="text-xs text-success-600">{{ __('Paid') }}</span>
                                @elseif($invoice->status === 'pending')
                                <span class="text-xs text-warning-600">{{ __('Pending') }}</span>
                                @else
                                <span class="text-xs text-danger-600">{{ __('Overdue') }}</span>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
