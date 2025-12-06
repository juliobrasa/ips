<x-admin-layout>
    <x-slot name="header">{{ __('User Details') }}: {{ $user->name }}</x-slot>
    <x-slot name="title">{{ __('User Details') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- User Information -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-icons-outlined mr-2 text-primary-600">person</span>
                        {{ __('User Information') }}
                    </h3>
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                        <span class="material-icons-outlined align-middle mr-1 text-sm">edit</span>
                        {{ __('Edit') }}
                    </a>
                </div>

                <div class="flex items-start gap-6">
                    <div class="w-20 h-20 rounded-full bg-primary-500 flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-500">{{ __('Name') }}</label>
                            <p class="font-semibold">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">{{ __('Email') }}</label>
                            <p class="font-semibold">{{ $user->email }}</p>
                            @if($user->email_verified_at)
                                <span class="text-green-600 text-xs flex items-center">
                                    <span class="material-icons-outlined text-xs mr-1">verified</span>
                                    {{ __('Verified') }} {{ $user->email_verified_at->format('Y-m-d') }}
                                </span>
                            @else
                                <span class="text-yellow-600 text-xs">{{ __('Not verified') }}</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">{{ __('Phone') }}</label>
                            <p class="font-semibold">{{ $user->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">{{ __('Role') }}</label>
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <span class="material-icons-outlined text-xs mr-1">admin_panel_settings</span>
                                    {{ __('Administrator') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ __('User') }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">{{ __('Status') }}</label>
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-gray-100 text-gray-800',
                                    'suspended' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$user->status ?? 'active'] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500">{{ __('Registered') }}</label>
                            <p class="font-semibold">{{ $user->created_at->format('F d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            @if($user->company)
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-icons-outlined mr-2 text-primary-600">business</span>
                        {{ __('Company Information') }}
                    </h3>
                    <a href="{{ route('admin.kyc.review', $user->company) }}"
                       class="px-4 py-2 bg-warning-600 text-white rounded-lg hover:bg-warning-700 text-sm">
                        <span class="material-icons-outlined align-middle mr-1 text-sm">fact_check</span>
                        {{ __('Review KYC') }}
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Company Name') }}</label>
                        <p class="font-semibold">{{ $user->company->company_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Legal Name') }}</label>
                        <p class="font-semibold">{{ $user->company->legal_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Tax ID') }}</label>
                        <p class="font-mono font-semibold">{{ $user->company->tax_id ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Country') }}</label>
                        <p class="font-semibold">{{ $user->company->country }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Entity Type') }}</label>
                        <p class="font-semibold">{{ $user->company->entity_type === 'individual' ? __('Individual') : __('Company') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Company Type') }}</label>
                        <p class="font-semibold">{{ ucfirst($user->company->company_type) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('Address') }}</label>
                        <p class="font-semibold">{{ $user->company->address ?? '-' }}, {{ $user->company->city ?? '' }} {{ $user->company->postal_code ?? '' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500">{{ __('KYC Status') }}</label>
                        @php
                            $kycColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'in_review' => 'bg-blue-100 text-blue-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kycColors[$user->company->kyc_status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $user->company->kyc_status)) }}
                        </span>
                    </div>
                </div>

                <!-- KYC Documents -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-3">{{ __('KYC Documents') }}</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg {{ $user->company->hasIdentityDocument() ? 'bg-green-50' : 'bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">{{ __('Identity Document') }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if($user->company->identity_document_type)
                                            {{ strtoupper($user->company->identity_document_type) }}: {{ $user->company->identity_document_number }}
                                        @else
                                            {{ __('Not uploaded') }}
                                        @endif
                                    </p>
                                </div>
                                @if($user->company->hasIdentityDocument())
                                    <a href="{{ Storage::url($user->company->identity_document_file) }}" target="_blank"
                                       class="text-primary-600 hover:text-primary-700">
                                        <span class="material-icons-outlined">download</span>
                                    </a>
                                @else
                                    <span class="text-gray-400">
                                        <span class="material-icons-outlined">close</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="p-4 rounded-lg {{ $user->company->hasSignedKyc() ? 'bg-green-50' : 'bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">{{ __('Signed KYC Form') }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if($user->company->kyc_signed_uploaded_at)
                                            {{ __('Uploaded') }}: {{ $user->company->kyc_signed_uploaded_at->format('Y-m-d') }}
                                        @else
                                            {{ __('Not uploaded') }}
                                        @endif
                                    </p>
                                </div>
                                @if($user->company->hasSignedKyc())
                                    <a href="{{ Storage::url($user->company->kyc_signed_document) }}" target="_blank"
                                       class="text-primary-600 hover:text-primary-700">
                                        <span class="material-icons-outlined">download</span>
                                    </a>
                                @else
                                    <span class="text-gray-400">
                                        <span class="material-icons-outlined">close</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Activity Summary -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">analytics</span>
                    {{ __('Activity Summary') }}
                </h3>

                <div class="grid grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900">{{ $user->company?->subnets?->count() ?? 0 }}</p>
                        <p class="text-sm text-gray-500">{{ __('Subnets') }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900">{{ $user->company?->leasesAsHolder?->count() ?? 0 }}</p>
                        <p class="text-sm text-gray-500">{{ __('As Holder') }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900">{{ $user->company?->leasesAsLessee?->count() ?? 0 }}</p>
                        <p class="text-sm text-gray-500">{{ __('As Lessee') }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900">{{ $user->company?->invoices?->count() ?? 0 }}</p>
                        <p class="text-sm text-gray-500">{{ __('Invoices') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Actions') }}</h3>

                <div class="space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center justify-center">
                        <span class="material-icons-outlined mr-2">edit</span>
                        {{ __('Edit User') }}
                    </a>

                    @if(!$user->email_verified_at)
                    <form method="POST" action="{{ route('admin.users.verify-email', $user) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <span class="material-icons-outlined mr-2">mark_email_read</span>
                            {{ __('Verify Email') }}
                        </button>
                    </form>
                    @endif

                    @if($user->id !== auth()->id())
                        @if($user->status !== 'suspended')
                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center"
                                    onclick="return confirm('{{ __('Are you sure?') }}')">
                                <span class="material-icons-outlined mr-2">block</span>
                                {{ __('Suspend User') }}
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                <span class="material-icons-outlined mr-2">check_circle</span>
                                {{ __('Activate User') }}
                            </button>
                        </form>
                        @endif

                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center">
                                <span class="material-icons-outlined mr-2">login</span>
                                {{ __('Impersonate') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                <span class="material-icons-outlined mr-2">delete</span>
                                {{ __('Delete User') }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.users.index') }}"
                       class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <span class="material-icons-outlined mr-2 align-middle">arrow_back</span>
                        {{ __('Back to List') }}
                    </a>
                </div>
            </div>

            <!-- Cart Items -->
            @if($user->cartItems->count() > 0)
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">shopping_cart</span>
                    {{ __('Cart Items') }}
                </h3>
                <p class="text-sm text-gray-500">{{ $user->cartItems->count() }} {{ __('items in cart') }}</p>
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
