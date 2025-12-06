<x-admin-layout>
    <x-slot name="header">{{ __('Edit User') }}: {{ $user->name }}</x-slot>
    <x-slot name="title">{{ __('Edit User') }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }} *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('New Password') }}</label>
                        <input type="password" name="password" id="password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="{{ __('Leave blank to keep current password') }}">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Role') }} *</label>
                        <select name="role" id="role" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>{{ __('Administrator') }}</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <p class="mt-1 text-sm text-gray-500">{{ __('You cannot change your own role.') }}</p>
                        @endif
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }} *</label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="status" value="{{ $user->status }}">
                            <p class="mt-1 text-sm text-gray-500">{{ __('You cannot change your own status.') }}</p>
                        @endif
                    </div>

                    <!-- Email Verification Status -->
                    <div class="p-4 rounded-lg {{ $user->email_verified_at ? 'bg-green-50' : 'bg-yellow-50' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium {{ $user->email_verified_at ? 'text-green-800' : 'text-yellow-800' }}">
                                    {{ __('Email Verification') }}
                                </p>
                                <p class="text-sm {{ $user->email_verified_at ? 'text-green-600' : 'text-yellow-600' }}">
                                    @if($user->email_verified_at)
                                        {{ __('Verified on') }}: {{ $user->email_verified_at->format('Y-m-d H:i') }}
                                    @else
                                        {{ __('Not verified') }}
                                    @endif
                                </p>
                            </div>
                            @if(!$user->email_verified_at)
                                <label class="flex items-center">
                                    <input type="checkbox" name="verify_email" value="1"
                                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ __('Verify now') }}</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">{{ __('Created') }}:</span>
                                <span class="font-medium">{{ $user->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">{{ __('Last Updated') }}:</span>
                                <span class="font-medium">{{ $user->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end gap-3">
                    <a href="{{ route('admin.users.show', $user) }}"
                       class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <span class="material-icons-outlined align-middle mr-1">save</span>
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
