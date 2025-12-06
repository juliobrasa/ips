<x-admin-layout>
    <x-slot name="header">{{ __('Lease Details') }}</x-slot>
    <x-slot name="title">{{ __('Lease Details') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Lease Information -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">assignment</span>
                    {{ __('Lease Information') }}
                </h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Subnet') }}</label>
                        <p class="font-mono font-semibold text-lg">{{ $lease->subnet->cidr_notation ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $lease->subnet->total_ips ?? 0 }} IPs</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Status') }}</label>
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                'pending_assignment' => 'bg-blue-100 text-blue-800',
                                'expired' => 'bg-gray-100 text-gray-800',
                                'terminated' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$lease->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Start Date') }}</label>
                        <p class="font-semibold">{{ $lease->start_date->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('End Date') }}</label>
                        <p class="font-semibold">{{ $lease->end_date->format('F d, Y') }}</p>
                        @if($lease->status === 'active')
                            @if($lease->end_date->isPast())
                                <span class="text-red-600 text-sm">{{ __('Expired') }}</span>
                            @elseif($lease->end_date->lte(now()->addDays(30)))
                                <span class="text-warning-600 text-sm">{{ $lease->end_date->diffForHumans() }}</span>
                            @else
                                <span class="text-gray-500 text-sm">{{ $lease->end_date->diffForHumans() }}</span>
                            @endif
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Monthly Price') }}</label>
                        <p class="font-semibold text-lg">${{ number_format($lease->monthly_price, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Assigned ASN') }}</label>
                        @if($lease->assigned_asn)
                            <p class="font-mono font-semibold text-primary-600">AS{{ $lease->assigned_asn }}</p>
                        @else
                            <p class="text-gray-500">{{ __('Not assigned') }}</p>
                        @endif
                    </div>
                </div>

                @if($lease->termination_reason)
                <div class="mt-6 p-4 bg-red-50 rounded-lg">
                    <label class="block text-sm font-medium text-red-800 mb-1">{{ __('Termination Reason') }}</label>
                    <p class="text-red-700">{{ $lease->termination_reason }}</p>
                </div>
                @endif
            </div>

            <!-- LOA Information -->
            @if($lease->loa)
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">description</span>
                    {{ __('Letter of Authorization') }}
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('LOA Number') }}</label>
                        <p class="font-mono font-semibold">{{ $lease->loa->loa_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Generated') }}</label>
                        <p class="font-semibold">{{ $lease->loa->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Valid From') }}</label>
                        <p class="font-semibold">{{ $lease->loa->valid_from->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Valid Until') }}</label>
                        <p class="font-semibold">{{ $lease->loa->valid_until->format('F d, Y') }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('loa.download', $lease->loa) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        <span class="material-icons-outlined mr-2">download</span>
                        {{ __('Download LOA PDF') }}
                    </a>
                </div>
            </div>
            @endif

            <!-- Invoices -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">receipt_long</span>
                    {{ __('Invoices') }}
                </h3>

                @if($lease->invoices->count() > 0)
                <div class="space-y-3">
                    @foreach($lease->invoices as $invoice)
                    <div class="flex items-center justify-between py-3 px-4 rounded-lg bg-gray-50">
                        <div>
                            <p class="font-mono font-medium">{{ $invoice->invoice_number }}</p>
                            <p class="text-sm text-gray-500">{{ $invoice->created_at->format('Y-m-d') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">${{ number_format($invoice->total, 2) }}</p>
                            @php
                                $invStatusColors = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'overdue' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $invStatusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500">{{ __('No invoices yet') }}</p>
                @endif
            </div>

            <!-- Abuse Reports -->
            @if($lease->abuseReports && $lease->abuseReports->count() > 0)
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-danger-600">report</span>
                    {{ __('Abuse Reports') }}
                </h3>

                <div class="space-y-3">
                    @foreach($lease->abuseReports as $report)
                    <div class="p-4 rounded-lg bg-red-50 border border-red-200">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-red-800">{{ $report->type }}</p>
                                <p class="text-sm text-red-700">{{ $report->description }}</p>
                                <p class="text-xs text-red-500 mt-1">{{ $report->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Lessee Information -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-blue-600">person</span>
                    {{ __('Lessee') }}
                </h3>

                @if($lease->lesseeCompany)
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Company') }}</label>
                        <p class="font-semibold">{{ $lease->lesseeCompany->company_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Country') }}</label>
                        <p>{{ $lease->lesseeCompany->country }}</p>
                    </div>
                    @if($lease->lesseeCompany->user)
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Contact') }}</label>
                        <p>{{ $lease->lesseeCompany->user->email }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Holder Information -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-green-600">business</span>
                    {{ __('IP Holder') }}
                </h3>

                @if($lease->holderCompany)
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Company') }}</label>
                        <p class="font-semibold">{{ $lease->holderCompany->company_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Country') }}</label>
                        <p>{{ $lease->holderCompany->country }}</p>
                    </div>
                    @if($lease->holderCompany->user)
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Contact') }}</label>
                        <p>{{ $lease->holderCompany->user->email }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Actions') }}</h3>

                <div class="space-y-3">
                    @if($lease->status === 'active')
                    <form method="POST" action="{{ route('admin.leases.extend', $lease) }}" class="space-y-2">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700">{{ __('Extend Lease') }}</label>
                        <div class="flex gap-2">
                            <select name="months" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="1">1 {{ __('month') }}</option>
                                <option value="3">3 {{ __('months') }}</option>
                                <option value="6">6 {{ __('months') }}</option>
                                <option value="12">12 {{ __('months') }}</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                                {{ __('Extend') }}
                            </button>
                        </div>
                    </form>

                    <hr class="border-gray-200">

                    <button type="button" onclick="document.getElementById('terminateForm').classList.toggle('hidden')"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                        <span class="material-icons-outlined mr-2">cancel</span>
                        {{ __('Terminate Lease') }}
                    </button>

                    <form id="terminateForm" method="POST" action="{{ route('admin.leases.terminate', $lease) }}" class="hidden">
                        @csrf
                        <div class="p-4 bg-red-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Termination Reason') }}</label>
                            <textarea name="reason" rows="2" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                            <button type="submit" class="mt-2 w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                {{ __('Confirm Termination') }}
                            </button>
                        </div>
                    </form>
                    @endif

                    <a href="{{ route('admin.leases.index') }}"
                       class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <span class="material-icons-outlined mr-2 align-middle">arrow_back</span>
                        {{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
