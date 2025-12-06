<x-admin-layout>
    <x-slot name="header">Document Templates</x-slot>
    <x-slot name="title">Document Templates</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Legal Document Templates</h2>
                    <p class="text-gray-600 mt-1">Manage and customize document templates for KYC, LOA, contracts, and agreements.</p>
                </div>
                <span class="material-icons-outlined text-5xl text-primary-200">description</span>
            </div>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($templates as $key => $template)
            <div class="bg-white rounded-xl shadow-material-1 overflow-hidden hover:shadow-material-2 transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-lg bg-primary-100 flex items-center justify-center">
                            @switch($key)
                                @case('kyc')
                                    <span class="material-icons-outlined text-primary-600">verified_user</span>
                                    @break
                                @case('loa')
                                    <span class="material-icons-outlined text-primary-600">assignment</span>
                                    @break
                                @case('lease_agreement')
                                    <span class="material-icons-outlined text-primary-600">handshake</span>
                                    @break
                                @case('holder_agreement')
                                    <span class="material-icons-outlined text-primary-600">business</span>
                                    @break
                                @case('aup')
                                    <span class="material-icons-outlined text-primary-600">policy</span>
                                    @break
                                @case('nda')
                                    <span class="material-icons-outlined text-primary-600">lock</span>
                                    @break
                                @default
                                    <span class="material-icons-outlined text-primary-600">description</span>
                            @endswitch
                        </div>
                        <span class="text-xs text-gray-500">{{ $template['last_updated'] }}</span>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mt-4">{{ $template['name'] }}</h3>
                    <p class="text-sm text-gray-600 mt-2">{{ $template['description'] }}</p>

                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.documents.preview', $key) }}" target="_blank"
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            <span class="material-icons-outlined text-sm mr-1">visibility</span>
                            Preview
                        </a>
                        <a href="{{ route('admin.documents.download', $key) }}"
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-sm">
                            <span class="material-icons-outlined text-sm mr-1">download</span>
                            Download
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Template Variables Reference -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Template Variables Reference</h3>
            <p class="text-gray-600 mb-4">These variables are automatically replaced when generating documents:</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Company Information</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code class="bg-gray-200 px-1 rounded">@{{company_name}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{legal_name}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{tax_id}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{address}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{country}}</code></li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Contact Information</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code class="bg-gray-200 px-1 rounded">@{{contact_name}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{contact_email}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{contact_phone}}</code></li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Subnet Information</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code class="bg-gray-200 px-1 rounded">@{{subnet}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{ip_count}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{lessee_asn}}</code></li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Lease Information</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code class="bg-gray-200 px-1 rounded">@{{start_date}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{end_date}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{price_per_ip}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{monthly_total}}</code></li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Document References</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code class="bg-gray-200 px-1 rounded">@{{loa_number}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{contract_number}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{verification_code}}</code></li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Platform Settings</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code class="bg-gray-200 px-1 rounded">@{{platform_fee}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{minimum_payout}}</code></li>
                        <li><code class="bg-gray-200 px-1 rounded">@{{date}}</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
