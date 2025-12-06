<x-app-layout>
    <x-slot name="header">{{ __('Complete Your Profile') }}</x-slot>
    <x-slot name="title">{{ __('Company Registration') }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
            <!-- Progress Steps -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center font-medium">1</div>
                        <span class="ml-2 text-sm font-medium text-primary-600">{{ __('Company Info') }}</span>
                    </div>
                    <div class="w-16 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-medium">2</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">{{ __('Verification') }}</span>
                    </div>
                    <div class="w-16 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-medium">3</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">{{ __('Complete') }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('company.store') }}" method="POST" class="p-6">
                @csrf

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ __('Company Information') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('Please provide your company details for KYC verification.') }}</p>
                </div>

                <!-- Company Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Account Type') }}</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary-300 transition-colors peer-checked:border-primary-600">
                            <input type="radio" name="company_type" value="lessee" class="peer sr-only" {{ old('company_type') === 'lessee' ? 'checked' : '' }} required>
                            <div class="peer-checked:border-primary-600 w-full">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="material-icons-outlined text-secondary-600">shopping_cart</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ __('IP Lessee') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('I want to lease IP addresses') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 hidden peer-checked:block">
                                <span class="material-icons-outlined text-primary-600">check_circle</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary-300 transition-colors">
                            <input type="radio" name="company_type" value="holder" class="peer sr-only" {{ old('company_type') === 'holder' ? 'checked' : '' }}>
                            <div class="peer-checked:border-primary-600 w-full">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="material-icons-outlined text-primary-600">inventory_2</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ __('IP Holder') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('I want to monetize my IPs') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 hidden peer-checked:block">
                                <span class="material-icons-outlined text-primary-600">check_circle</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary-300 transition-colors md:col-span-2">
                            <input type="radio" name="company_type" value="both" class="peer sr-only" {{ old('company_type') === 'both' ? 'checked' : '' }}>
                            <div class="peer-checked:border-primary-600 w-full">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="material-icons-outlined text-success-600">sync_alt</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ __('Both') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('I want to both lease and monetize IP addresses') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 hidden peer-checked:block">
                                <span class="material-icons-outlined text-primary-600">check_circle</span>
                            </div>
                        </label>
                    </div>
                    @error('company_type')
                    <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Company Name') }}</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="Acme Corporation">
                        @error('company_name')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Tax ID / VAT Number') }}</label>
                        <input type="text" name="tax_id" value="{{ old('tax_id') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="US12-3456789">
                        @error('tax_id')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Country') }}</label>
                        <select name="country" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('Select country...') }}</option>
                            <option value="US" {{ old('country') === 'US' ? 'selected' : '' }}>{{ __('United States') }}</option>
                            <option value="GB" {{ old('country') === 'GB' ? 'selected' : '' }}>{{ __('United Kingdom') }}</option>
                            <option value="DE" {{ old('country') === 'DE' ? 'selected' : '' }}>{{ __('Germany') }}</option>
                            <option value="FR" {{ old('country') === 'FR' ? 'selected' : '' }}>{{ __('France') }}</option>
                            <option value="NL" {{ old('country') === 'NL' ? 'selected' : '' }}>{{ __('Netherlands') }}</option>
                            <option value="ES" {{ old('country') === 'ES' ? 'selected' : '' }}>{{ __('Spain') }}</option>
                            <option value="IT" {{ old('country') === 'IT' ? 'selected' : '' }}>{{ __('Italy') }}</option>
                            <option value="CA" {{ old('country') === 'CA' ? 'selected' : '' }}>{{ __('Canada') }}</option>
                            <option value="AU" {{ old('country') === 'AU' ? 'selected' : '' }}>{{ __('Australia') }}</option>
                            <option value="JP" {{ old('country') === 'JP' ? 'selected' : '' }}>{{ __('Japan') }}</option>
                            <option value="BR" {{ old('country') === 'BR' ? 'selected' : '' }}>{{ __('Brazil') }}</option>
                            <option value="MX" {{ old('country') === 'MX' ? 'selected' : '' }}>{{ __('Mexico') }}</option>
                            <option value="AR" {{ old('country') === 'AR' ? 'selected' : '' }}>{{ __('Argentina') }}</option>
                            <option value="CL" {{ old('country') === 'CL' ? 'selected' : '' }}>{{ __('Chile') }}</option>
                            <option value="CO" {{ old('country') === 'CO' ? 'selected' : '' }}>{{ __('Colombia') }}</option>
                        </select>
                        @error('country')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Business Address') }}</label>
                        <textarea name="address" rows="2" required
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                  placeholder="123 Business Ave, Suite 100, City, State 12345">{{ old('address') }}</textarea>
                        @error('address')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Billing Email') }}</label>
                        <input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()->email) }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="billing@company.com">
                        @error('billing_email')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone Number') }}</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="+1 (555) 123-4567">
                        @error('phone')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Website (Optional)') }}</label>
                        <input type="url" name="website" value="{{ old('website') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                               placeholder="https://www.company.com">
                        @error('website')
                        <p class="text-danger-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Terms -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <label class="flex items-start">
                        <input type="checkbox" name="accept_terms" required
                               class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-600">
                            {{ __('I agree to the') }} <a href="#" class="text-primary-600 hover:text-primary-700">{{ __('Terms of Service') }}</a>
                            {{ __('and') }} <a href="#" class="text-primary-600 hover:text-primary-700">{{ __('Privacy Policy') }}</a>.
                            {{ __('I confirm that all information provided is accurate and I am authorized to represent this company.') }}
                        </span>
                    </label>
                </div>

                <!-- Submit -->
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-primary-600 text-white px-8 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center">
                        {{ __('Continue') }}
                        <span class="material-icons-outlined ml-2">arrow_forward</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
