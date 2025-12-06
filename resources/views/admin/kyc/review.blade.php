<x-admin-layout>
    <x-slot name="header">Review KYC: {{ $company->company_name }}</x-slot>
    <x-slot name="title">KYC Review</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Company Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Company Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Company Name</p>
                        <p class="font-medium">{{ $company->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Legal Name</p>
                        <p class="font-medium">{{ $company->legal_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tax ID / VAT</p>
                        <p class="font-medium font-mono">{{ $company->tax_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Company Type</p>
                        <span class="inline-flex px-2 py-1 text-xs rounded-full
                            {{ $company->company_type === 'holder' ? 'bg-primary-100 text-primary-700' : '' }}
                            {{ $company->company_type === 'lessee' ? 'bg-secondary-100 text-secondary-700' : '' }}
                            {{ $company->company_type === 'both' ? 'bg-gray-100 text-gray-700' : '' }}
                        ">
                            {{ ucfirst($company->company_type) }}
                        </span>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Address</p>
                        <p class="font-medium">
                            {{ $company->address }}<br>
                            {{ $company->city }}, {{ $company->postal_code }}<br>
                            {{ $company->country }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Owner</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="font-medium">{{ $company->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $company->user->email }}</p>
                        @if($company->user->email_verified_at)
                        <span class="inline-flex items-center text-xs text-success-600">
                            <span class="material-icons-outlined text-sm mr-1">verified</span>
                            Verified
                        </span>
                        @else
                        <span class="inline-flex items-center text-xs text-warning-600">
                            <span class="material-icons-outlined text-sm mr-1">warning</span>
                            Not verified
                        </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-medium">{{ $company->user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Registered</p>
                        <p class="font-medium">{{ $company->user->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- KYC Documents -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('KYC Documents') }}</h3>

                <div class="space-y-4">
                    <!-- Identity Document -->
                    <div class="p-4 rounded-lg {{ $company->hasIdentityDocument() ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="material-icons-outlined {{ $company->hasIdentityDocument() ? 'text-green-600' : 'text-gray-400' }} mr-3">
                                    {{ $company->hasIdentityDocument() ? 'check_circle' : 'pending' }}
                                </span>
                                <div>
                                    <p class="font-medium {{ $company->hasIdentityDocument() ? 'text-green-800' : 'text-gray-700' }}">
                                        {{ __('Identity Document') }}
                                        @if($company->entity_type === 'individual')
                                            ({{ __('Personal ID') }})
                                        @else
                                            ({{ __('Company Tax ID') }})
                                        @endif
                                    </p>
                                    @if($company->hasIdentityDocument())
                                        <p class="text-sm text-green-600">
                                            {{ strtoupper($company->identity_document_type) }}: {{ $company->identity_document_number }}
                                            <br>
                                            {{ __('Uploaded') }}: {{ $company->identity_document_uploaded_at->format('Y-m-d H:i') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500">{{ __('Not uploaded') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($company->hasIdentityDocument())
                                <a href="{{ Storage::url($company->identity_document_file) }}" target="_blank"
                                   class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                    <span class="material-icons-outlined text-sm mr-1">visibility</span>
                                    {{ __('View') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Signed KYC Form -->
                    <div class="p-4 rounded-lg {{ $company->hasSignedKyc() ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="material-icons-outlined {{ $company->hasSignedKyc() ? 'text-green-600' : 'text-gray-400' }} mr-3">
                                    {{ $company->hasSignedKyc() ? 'check_circle' : 'pending' }}
                                </span>
                                <div>
                                    <p class="font-medium {{ $company->hasSignedKyc() ? 'text-green-800' : 'text-gray-700' }}">
                                        {{ __('Signed KYC Form') }}
                                    </p>
                                    @if($company->hasSignedKyc())
                                        <p class="text-sm text-green-600">
                                            {{ __('Uploaded') }}: {{ $company->kyc_signed_uploaded_at->format('Y-m-d H:i') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500">{{ __('Not uploaded') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($company->hasSignedKyc())
                                <a href="{{ Storage::url($company->kyc_signed_document) }}" target="_blank"
                                   class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                    <span class="material-icons-outlined text-sm mr-1">visibility</span>
                                    {{ __('View') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Document Status Summary -->
                    <div class="p-4 rounded-lg {{ $company->hasAllDocuments() ? 'bg-green-100' : 'bg-yellow-100' }}">
                        <div class="flex items-center">
                            <span class="material-icons-outlined {{ $company->hasAllDocuments() ? 'text-green-700' : 'text-yellow-700' }} mr-2">
                                {{ $company->hasAllDocuments() ? 'task_alt' : 'warning' }}
                            </span>
                            <span class="{{ $company->hasAllDocuments() ? 'text-green-700' : 'text-yellow-700' }} font-medium">
                                @if($company->hasAllDocuments())
                                    {{ __('All required documents have been uploaded') }}
                                @else
                                    {{ __('Some documents are still missing') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Legacy documents (if any) -->
                @if($company->kyc_documents && count($company->kyc_documents) > 0)
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Other Documents') }}</h4>
                    <div class="space-y-2">
                        @foreach($company->kyc_documents as $type => $path)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="material-icons-outlined text-gray-400 mr-3">description</span>
                                <div>
                                    <p class="font-medium text-gray-800">{{ ucwords(str_replace('_', ' ', $type)) }}</p>
                                    <p class="text-sm text-gray-500">{{ basename($path) }}</p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($path) }}" target="_blank" class="text-primary-600 hover:text-primary-700">
                                <span class="material-icons-outlined">open_in_new</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Legal Representative (if company) -->
            @if($company->entity_type === 'company')
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Legal Representative') }}</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Name') }}</p>
                        <p class="font-medium">{{ $company->legal_representative_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('ID Number') }}</p>
                        <p class="font-medium font-mono">{{ $company->legal_representative_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Position') }}</p>
                        <p class="font-medium">{{ $company->legal_representative_position ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Previous Notes -->
            @if($company->kyc_notes)
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Previous Notes</h3>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $company->kyc_notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Panel -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h3>
                <div class="text-center py-4">
                    <span class="inline-flex px-4 py-2 text-lg rounded-full
                        {{ $company->kyc_status === 'pending' ? 'bg-warning-100 text-warning-700' : '' }}
                        {{ $company->kyc_status === 'in_review' ? 'bg-primary-100 text-primary-700' : '' }}
                        {{ $company->kyc_status === 'approved' ? 'bg-success-100 text-success-700' : '' }}
                        {{ $company->kyc_status === 'rejected' ? 'bg-danger-100 text-danger-700' : '' }}
                    ">
                        {{ ucfirst(str_replace('_', ' ', $company->kyc_status)) }}
                    </span>
                </div>
                @if($company->kyc_approved_at)
                <p class="text-center text-sm text-gray-500 mt-2">
                    Approved on {{ $company->kyc_approved_at->format('M d, Y') }}
                </p>
                @endif
            </div>

            <!-- Approve Form -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-success-700 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2">check_circle</span>
                    Approve KYC
                </h3>
                <form action="{{ route('admin.kyc.approve', $company) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-success-500 focus:ring-success-500"
                                  placeholder="Any notes about the approval..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-success-600 text-white py-2 px-4 rounded-lg hover:bg-success-700 transition-colors flex items-center justify-center">
                        <span class="material-icons-outlined mr-2">check</span>
                        Approve KYC
                    </button>
                </form>
            </div>

            <!-- Request Info Form -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-warning-700 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2">help</span>
                    Request Information
                </h3>
                <form action="{{ route('admin.kyc.request-info', $company) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">What information is needed? *</label>
                        <textarea name="notes" rows="3" required class="w-full rounded-lg border-gray-300 focus:border-warning-500 focus:ring-warning-500"
                                  placeholder="Please specify what documents or information is missing..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-warning-600 text-white py-2 px-4 rounded-lg hover:bg-warning-700 transition-colors flex items-center justify-center">
                        <span class="material-icons-outlined mr-2">send</span>
                        Request Info
                    </button>
                </form>
            </div>

            <!-- Reject Form -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-danger-700 mb-4 flex items-center">
                    <span class="material-icons-outlined mr-2">cancel</span>
                    Reject KYC
                </h3>
                <form action="{{ route('admin.kyc.reject', $company) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason *</label>
                        <textarea name="notes" rows="3" required class="w-full rounded-lg border-gray-300 focus:border-danger-500 focus:ring-danger-500"
                                  placeholder="Please explain why the KYC is being rejected..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-danger-600 text-white py-2 px-4 rounded-lg hover:bg-danger-700 transition-colors flex items-center justify-center"
                            onclick="return confirm('Are you sure you want to reject this KYC?')">
                        <span class="material-icons-outlined mr-2">close</span>
                        Reject KYC
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
