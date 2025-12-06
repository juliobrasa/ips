<x-admin-layout>
    <x-slot name="header">{{ __('Create User') }}</x-slot>
    <x-slot name="title">{{ __('Create User') }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }} *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }} *</label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Role') }} *</label>
                        <select name="role" id="role" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('Administrator') }}</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }} *</label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                        </select>
                    </div>

                    <!-- Verify Email -->
                    <div class="flex items-center">
                        <input type="checkbox" name="verify_email" id="verify_email" value="1"
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('verify_email') ? 'checked' : '' }}>
                        <label for="verify_email" class="ml-2 text-sm text-gray-700">{{ __('Mark email as verified') }}</label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <span class="material-icons-outlined align-middle mr-1">person_add</span>
                        {{ __('Create User') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
