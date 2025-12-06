<x-app-layout>
    <x-slot name="header">{{ __('Company Settings') }}</x-slot>
    <x-slot name="title">{{ __('Edit Company') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <!-- KYC Status Banner -->
        @if(!$company->kyc_verified_at)
        <div class="bg-warning-50 border border-warning-200 rounded-xl p-4 mb-6 flex items-start">
            <span class="material-icons-outlined text-warning-600 mr-3">info</span>
            <div>
                <h4 class="font-medium text-warning-800">{{ __('KYC Verification Pending') }}</h4>
                <p class="text-sm text-warning-700">{{ __('Your company verification is being reviewed. This usually takes 1-2 business days.') }}</p>
            </div>
        </div>
        @else
        <div class="bg-success-50 border border-success-200 rounded-xl p-4 mb-6 flex items-start">
            <span class="material-icons-outlined text-success-600 mr-3">verified</span>
            <div>
                <h4 class="font-medium text-success-800">{{ __('Verified Company') }}</h4>
                <p class="text-sm text-success-700">{{ __('Your company was verified on') }} {{ $company->kyc_verified_at->format('M d, Y') }}.</p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Company Information') }}</h3>
            </div>

            <form action="{{ route('company.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Type -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Account Type') }}</label>
                        <div class="flex items-center space-x-4">
                            @if($company->isHolder())
                            <span class="inline-flex items-center px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm">
                                <span class="material-icons-outlined text-sm mr-1">inventory_2</span>
                                {{ __('IP Holder') }}
                            </span>
                            @endif
                            @if($company->isLessee())
                            <span class="inline-flex items-center px-3 py-1 bg-secondary-100 text-secondary-700 rounded-full text-sm">
                                <span class="material-icons-outlined text-sm mr-1">shopping_cart</span>
                                {{ __('IP Lessee') }}
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ __('Contact support to change your account type.') }}</p>
                    </div>

                    <!-- Company Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Company Name') }}</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $company->company_name) }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('company_name')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Tax ID / VAT Number') }}</label>
                        <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 {{ $company->kyc_verified_at ? 'bg-gray-50' : '' }}"
                               {{ $company->kyc_verified_at ? 'readonly' : '' }}>
                        @if($company->kyc_verified_at)
                        <p class="text-xs text-gray-500 mt-1">{{ __('Cannot be changed after verification.') }}</p>
                        @endif
                        @error('tax_id')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Country') }}</label>
                        <select name="country" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 {{ $company->kyc_verified_at ? 'bg-gray-50' : '' }}"
                                {{ $company->kyc_verified_at ? 'disabled' : '' }}>
                            <option value="US" {{ $company->country === 'US' ? 'selected' : '' }}>{{ __('United States') }}</option>
                            <option value="GB" {{ $company->country === 'GB' ? 'selected' : '' }}>{{ __('United Kingdom') }}</option>
                            <option value="DE" {{ $company->country === 'DE' ? 'selected' : '' }}>{{ __('Germany') }}</option>
                            <option value="FR" {{ $company->country === 'FR' ? 'selected' : '' }}>{{ __('France') }}</option>
                            <option value="NL" {{ $company->country === 'NL' ? 'selected' : '' }}>{{ __('Netherlands') }}</option>
                            <option value="ES" {{ $company->country === 'ES' ? 'selected' : '' }}>{{ __('Spain') }}</option>
                            <option value="IT" {{ $company->country === 'IT' ? 'selected' : '' }}>{{ __('Italy') }}</option>
                            <option value="CA" {{ $company->country === 'CA' ? 'selected' : '' }}>{{ __('Canada') }}</option>
                            <option value="AU" {{ $company->country === 'AU' ? 'selected' : '' }}>{{ __('Australia') }}</option>
                            <option value="JP" {{ $company->country === 'JP' ? 'selected' : '' }}>{{ __('Japan') }}</option>
                            <option value="BR" {{ $company->country === 'BR' ? 'selected' : '' }}>{{ __('Brazil') }}</option>
                            <option value="MX" {{ $company->country === 'MX' ? 'selected' : '' }}>{{ __('Mexico') }}</option>
                            <option value="AR" {{ $company->country === 'AR' ? 'selected' : '' }}>{{ __('Argentina') }}</option>
                            <option value="CL" {{ $company->country === 'CL' ? 'selected' : '' }}>{{ __('Chile') }}</option>
                            <option value="CO" {{ $company->country === 'CO' ? 'selected' : '' }}>{{ __('Colombia') }}</option>
                        </select>
                        @error('country')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Business Address') }}</label>
                        <textarea name="address" rows="2" required
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Billing Email') }}</label>
                        <input type="email" name="billing_email" value="{{ old('billing_email', $company->billing_email) }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('billing_email')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone Number') }}</label>
                        <input type="tel" name="phone" value="{{ old('phone', $company->phone) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('phone')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Website') }}</label>
                        <input type="url" name="website" value="{{ old('website', $company->website) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        @error('website')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-6 pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden mt-6">
            <div class="p-6 border-b border-danger-100 bg-danger-50">
                <h3 class="text-lg font-semibold text-danger-800">{{ __('Danger Zone') }}</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">{{ __('Once you delete your company profile, there is no going back. All your data will be permanently deleted.') }}</p>
                <button type="button" class="text-danger-600 hover:text-danger-700 font-medium"
                        onclick="confirm('{{ __('Are you sure you want to delete your company profile? This action cannot be undone.') }}')">
                    {{ __('Delete Company Profile') }}
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
