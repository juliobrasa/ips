<x-admin-layout>
    <x-slot name="header">KYC Management</x-slot>
    <x-slot name="title">KYC Management</x-slot>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-material-1 p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="all">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Type</label>
                <select name="type" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="all">All Types</option>
                    <option value="holder" {{ request('type') === 'holder' ? 'selected' : '' }}>Holder</option>
                    <option value="lessee" {{ request('type') === 'lessee' ? 'selected' : '' }}>Lessee</option>
                    <option value="both" {{ request('type') === 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Company, email, tax ID..."
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors">
                    <span class="material-icons-outlined text-sm mr-1">search</span>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Companies Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($companies as $company)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="material-icons-outlined text-primary-600 text-sm">business</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $company->company_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $company->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs rounded-full
                                {{ $company->company_type === 'holder' ? 'bg-primary-100 text-primary-700' : '' }}
                                {{ $company->company_type === 'lessee' ? 'bg-secondary-100 text-secondary-700' : '' }}
                                {{ $company->company_type === 'both' ? 'bg-gray-100 text-gray-700' : '' }}
                            ">
                                {{ ucfirst($company->company_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $company->country }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs rounded-full
                                {{ $company->kyc_status === 'pending' ? 'bg-warning-100 text-warning-700' : '' }}
                                {{ $company->kyc_status === 'in_review' ? 'bg-primary-100 text-primary-700' : '' }}
                                {{ $company->kyc_status === 'approved' ? 'bg-success-100 text-success-700' : '' }}
                                {{ $company->kyc_status === 'rejected' ? 'bg-danger-100 text-danger-700' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $company->kyc_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $company->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.kyc.show', $company) }}" class="text-gray-600 hover:text-primary-600">
                                    <span class="material-icons-outlined">visibility</span>
                                </a>
                                @if(in_array($company->kyc_status, ['pending', 'in_review']))
                                <a href="{{ route('admin.kyc.review', $company) }}" class="text-primary-600 hover:text-primary-700">
                                    <span class="material-icons-outlined">rate_review</span>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <span class="material-icons-outlined text-4xl mb-2">search_off</span>
                            <p>No companies found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $companies->withQueryString()->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
