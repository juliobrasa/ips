<x-app-layout>
    <x-slot name="header">{{ __('Legal Documentation & KYC') }}</x-slot>
    <x-slot name="title">{{ __('Legal Documentation') }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- KYC Status Overview -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary-600">verified_user</span>
                    {{ __('KYC Verification Status') }}
                </h2>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                        'in_review' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'approved' => 'bg-green-100 text-green-800 border-green-300',
                        'rejected' => 'bg-red-100 text-red-800 border-red-300',
                    ];
                    $statusIcons = [
                        'pending' => 'schedule',
                        'in_review' => 'hourglass_top',
                        'approved' => 'check_circle',
                        'rejected' => 'cancel',
                    ];
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border {{ $statusColors[$company->kyc_status] ?? 'bg-gray-100 text-gray-800' }}">
                    <span class="material-icons-outlined text-sm mr-1">{{ $statusIcons[$company->kyc_status] ?? 'help' }}</span>
                    {{ ucfirst(str_replace('_', ' ', $company->kyc_status)) }}
                </span>
            </div>

            @if($company->kyc_status === 'pending')
                <div class="p-4 bg-yellow-50 rounded-lg">
                    <p class="text-yellow-800">
                        <strong>{{ __('Action Required:') }}</strong>
                        {{ __('Please upload the required documents below to complete your KYC verification.') }}
                    </p>
                </div>
            @elseif($company->kyc_status === 'in_review')
                <div class="p-4 bg-blue-50 rounded-lg">
                    <p class="text-blue-800">
                        <strong>{{ __('Under Review:') }}</strong>
                        {{ __('Your documents are being reviewed by our team. This usually takes 1-2 business days.') }}
                    </p>
                </div>
            @elseif($company->kyc_status === 'approved')
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-green-800">
                        <strong>{{ __('Verified:') }}</strong>
                        {{ __('Your KYC verification is complete. You have full access to all platform features.') }}
                    </p>
                </div>
            @elseif($company->kyc_status === 'rejected')
                <div class="p-4 bg-red-50 rounded-lg">
                    <p class="text-red-800">
                        <strong>{{ __('Rejected:') }}</strong>
                        {{ $company->kyc_notes ?? __('Please contact support for more information.') }}
                    </p>
                </div>
            @endif

            <!-- Progress Steps -->
            <div class="mt-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $company->company_name ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                            @if($company->company_name)
                                <span class="material-icons-outlined">check</span>
                            @else
                                <span>1</span>
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium">{{ __('Company Profile') }}</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 {{ $company->hasIdentityDocument() ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $company->hasIdentityDocument() ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                            @if($company->hasIdentityDocument())
                                <span class="material-icons-outlined">check</span>
                            @else
                                <span>2</span>
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium">{{ __('Identity Document') }}</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 {{ $company->hasSignedKyc() ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $company->hasSignedKyc() ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                            @if($company->hasSignedKyc())
                                <span class="material-icons-outlined">check</span>
                            @else
                                <span>3</span>
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium">{{ __('Signed KYC') }}</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 {{ $company->kyc_status === 'approved' ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $company->kyc_status === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                            @if($company->kyc_status === 'approved')
                                <span class="material-icons-outlined">check</span>
                            @else
                                <span>4</span>
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium">{{ __('Verified') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Download KYC Form -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3 text-sm font-bold">1</span>
                {{ __('Download and Complete KYC Form') }}
            </h3>

            <p class="text-gray-600 mb-4">
                {{ __('Download the KYC form pre-filled with your company information. Print it, sign it, and upload the signed copy in step 3.') }}
            </p>

            <div class="flex gap-4">
                <a href="{{ route('kyc.download-form') }}"
                   class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <span class="material-icons-outlined mr-2">download</span>
                    {{ __('Download KYC Form (PDF)') }}
                </a>
                <a href="{{ route('kyc.view-form') }}" target="_blank"
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <span class="material-icons-outlined mr-2">visibility</span>
                    {{ __('Preview Form') }}
                </a>
            </div>
        </div>

        <!-- Step 2: Upload Identity Document -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3 text-sm font-bold">2</span>
                {{ __('Upload Identity Document') }}
                @if($company->hasIdentityDocument())
                    <span class="ml-2 text-green-500">
                        <span class="material-icons-outlined">check_circle</span>
                    </span>
                @endif
            </h3>

            <p class="text-gray-600 mb-4">
                @if($company->entity_type === 'individual')
                    {{ __('Upload a copy of your personal identity document (DNI, NIE, or Passport).') }}
                @else
                    {{ __('Upload a copy of your company\'s tax identification document (NIF/CIF) and the identity document of the legal representative.') }}
                @endif
            </p>

            @if($company->hasIdentityDocument())
                <div class="mb-4 p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-green-800">{{ __('Document Uploaded') }}</p>
                            <p class="text-sm text-green-600">
                                {{ strtoupper($company->identity_document_type) }}: {{ $company->identity_document_number }}
                                <br>
                                {{ __('Uploaded') }}: {{ $company->identity_document_uploaded_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                        <a href="{{ Storage::url($company->identity_document_file) }}" target="_blank"
                           class="text-green-600 hover:text-green-700">
                            <span class="material-icons-outlined">visibility</span>
                        </a>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('kyc.upload-identity') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="identity_document_type" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Document Type') }} *
                        </label>
                        <select name="identity_document_type" id="identity_document_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            @if($company->entity_type === 'individual')
                                <option value="dni" {{ old('identity_document_type', $company->identity_document_type) == 'dni' ? 'selected' : '' }}>DNI ({{ __('Spain') }})</option>
                                <option value="nie" {{ old('identity_document_type', $company->identity_document_type) == 'nie' ? 'selected' : '' }}>NIE ({{ __('Spain') }})</option>
                                <option value="passport" {{ old('identity_document_type', $company->identity_document_type) == 'passport' ? 'selected' : '' }}>{{ __('Passport') }}</option>
                            @else
                                <option value="nif" {{ old('identity_document_type', $company->identity_document_type) == 'nif' ? 'selected' : '' }}>NIF ({{ __('Spain') }})</option>
                                <option value="cif" {{ old('identity_document_type', $company->identity_document_type) == 'cif' ? 'selected' : '' }}>CIF ({{ __('Spain') }})</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="identity_document_number" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Document Number') }} *
                        </label>
                        <input type="text" name="identity_document_number" id="identity_document_number"
                               value="{{ old('identity_document_number', $company->identity_document_number) }}"
                               required placeholder="12345678A"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label for="identity_document_file" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('Document File') }} * <span class="text-gray-500 font-normal">(PDF, JPG, PNG - max 10MB)</span>
                    </label>
                    <input type="file" name="identity_document_file" id="identity_document_file"
                           accept=".pdf,.jpg,.jpeg,.png" {{ $company->hasIdentityDocument() ? '' : 'required' }}
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <span class="material-icons-outlined mr-2">upload</span>
                    {{ $company->hasIdentityDocument() ? __('Update Document') : __('Upload Document') }}
                </button>
            </form>
        </div>

        <!-- Step 3: Upload Signed KYC -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3 text-sm font-bold">3</span>
                {{ __('Upload Signed KYC Form') }}
                @if($company->hasSignedKyc())
                    <span class="ml-2 text-green-500">
                        <span class="material-icons-outlined">check_circle</span>
                    </span>
                @endif
            </h3>

            <p class="text-gray-600 mb-4">
                {{ __('Print the KYC form downloaded in step 1, sign it, scan or photograph it, and upload here.') }}
            </p>

            @if($company->hasSignedKyc())
                <div class="mb-4 p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-green-800">{{ __('Signed KYC Uploaded') }}</p>
                            <p class="text-sm text-green-600">
                                {{ __('Uploaded') }}: {{ $company->kyc_signed_uploaded_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                        <a href="{{ Storage::url($company->kyc_signed_document) }}" target="_blank"
                           class="text-green-600 hover:text-green-700">
                            <span class="material-icons-outlined">visibility</span>
                        </a>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('kyc.upload-signed') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="kyc_signed_document" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('Signed KYC Form') }} * <span class="text-gray-500 font-normal">(PDF - max 10MB)</span>
                    </label>
                    <input type="file" name="kyc_signed_document" id="kyc_signed_document"
                           accept=".pdf" {{ $company->hasSignedKyc() ? '' : 'required' }}
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <span class="material-icons-outlined mr-2">upload</span>
                    {{ $company->hasSignedKyc() ? __('Update Document') : __('Upload Signed KYC') }}
                </button>
            </form>
        </div>

        <!-- Submit for Review -->
        @if($company->hasAllDocuments() && !in_array($company->kyc_status, ['in_review', 'approved']))
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Ready to Submit?') }}</h3>
            <p class="text-gray-600 mb-4">
                {{ __('All required documents have been uploaded. Click the button below to submit your KYC for review.') }}
            </p>
            <form method="POST" action="{{ route('kyc.submit-review') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-lg">
                    <span class="material-icons-outlined mr-2">send</span>
                    {{ __('Submit for Review') }}
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
