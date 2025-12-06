<x-admin-layout>
    <x-slot name="header">Abuse Reports</x-slot>
    <x-slot name="title">Abuse Reports Management</x-slot>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('admin.security.abuse-reports', ['status' => 'open']) }}"
           class="bg-white rounded-xl p-4 shadow-material-1 hover:shadow-material-2 transition-shadow {{ request('status') === 'open' ? 'ring-2 ring-red-500' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Open</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['open'] }}</p>
                </div>
                <span class="material-icons-outlined text-red-400">error_outline</span>
            </div>
        </a>
        <a href="{{ route('admin.security.abuse-reports', ['status' => 'investigating']) }}"
           class="bg-white rounded-xl p-4 shadow-material-1 hover:shadow-material-2 transition-shadow {{ request('status') === 'investigating' ? 'ring-2 ring-yellow-500' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Investigating</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['investigating'] }}</p>
                </div>
                <span class="material-icons-outlined text-yellow-400">search</span>
            </div>
        </a>
        <a href="{{ route('admin.security.abuse-reports', ['status' => 'resolved']) }}"
           class="bg-white rounded-xl p-4 shadow-material-1 hover:shadow-material-2 transition-shadow {{ request('status') === 'resolved' ? 'ring-2 ring-green-500' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Resolved</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['resolved'] }}</p>
                </div>
                <span class="material-icons-outlined text-green-400">check_circle</span>
            </div>
        </a>
        <a href="{{ route('admin.security.abuse-reports', ['status' => 'dismissed']) }}"
           class="bg-white rounded-xl p-4 shadow-material-1 hover:shadow-material-2 transition-shadow {{ request('status') === 'dismissed' ? 'ring-2 ring-gray-500' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Dismissed</p>
                    <p class="text-2xl font-bold text-gray-600">{{ $stats['dismissed'] }}</p>
                </div>
                <span class="material-icons-outlined text-gray-400">cancel</span>
            </div>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 shadow-material-1 mb-6">
        <form method="GET" action="{{ route('admin.security.abuse-reports') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[150px]">
                <label for="status" class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="all">All Status</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label for="severity" class="block text-xs text-gray-500 mb-1">Severity</label>
                <select name="severity" id="severity" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="all">All Severity</option>
                    <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label for="type" class="block text-xs text-gray-500 mb-1">Type</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="all">All Types</option>
                    <option value="spam" {{ request('type') === 'spam' ? 'selected' : '' }}>Spam</option>
                    <option value="phishing" {{ request('type') === 'phishing' ? 'selected' : '' }}>Phishing</option>
                    <option value="malware" {{ request('type') === 'malware' ? 'selected' : '' }}>Malware</option>
                    <option value="ddos" {{ request('type') === 'ddos' ? 'selected' : '' }}>DDoS</option>
                    <option value="scraping" {{ request('type') === 'scraping' ? 'selected' : '' }}>Scraping</option>
                    <option value="fraud" {{ request('type') === 'fraud' ? 'selected' : '' }}>Fraud</option>
                    <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <span class="material-icons-outlined text-sm">filter_list</span>
                    Filter
                </button>
                <a href="{{ route('admin.security.abuse-reports') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Subnet</th>
                        <th class="px-6 py-3">Source</th>
                        <th class="px-6 py-3">Severity</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Reported</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50 {{ $report->severity === 'critical' && $report->status === 'open' ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 text-sm font-medium">
                                #{{ $report->id }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeIcons = [
                                        'spam' => 'email',
                                        'phishing' => 'phishing',
                                        'malware' => 'bug_report',
                                        'ddos' => 'flash_on',
                                        'scraping' => 'travel_explore',
                                        'fraud' => 'gpp_bad',
                                        'other' => 'help',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1 text-sm capitalize">
                                    <span class="material-icons-outlined text-sm text-gray-400">{{ $typeIcons[$report->type] ?? 'help' }}</span>
                                    {{ $report->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm">{{ $report->subnet->cidr_notation ?? 'N/A' }}</span>
                                @if($report->lease)
                                    <span class="block text-xs text-gray-500">Lease #{{ $report->lease_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $report->source ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $severityColors = [
                                        'critical' => 'red',
                                        'high' => 'orange',
                                        'medium' => 'yellow',
                                        'low' => 'green',
                                    ];
                                    $color = $severityColors[$report->severity] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 capitalize">
                                    {{ $report->severity }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'open' => 'red',
                                        'investigating' => 'yellow',
                                        'resolved' => 'green',
                                        'dismissed' => 'gray',
                                    ];
                                    $statusColor = $statusColors[$report->status] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 capitalize">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $report->created_at->format('M d, Y') }}
                                <span class="block text-xs">{{ $report->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.security.abuse-reports.show', $report) }}"
                                       class="text-primary-600 hover:text-primary-800"
                                       title="View Details">
                                        <span class="material-icons-outlined text-xl">visibility</span>
                                    </a>
                                    @if($report->status === 'open' || $report->status === 'investigating')
                                        <button type="button"
                                                onclick="openResolveModal({{ $report->id }})"
                                                class="text-green-600 hover:text-green-800"
                                                title="Resolve">
                                            <span class="material-icons-outlined text-xl">check_circle</span>
                                        </button>
                                        <button type="button"
                                                onclick="openDismissModal({{ $report->id }})"
                                                class="text-gray-600 hover:text-gray-800"
                                                title="Dismiss">
                                            <span class="material-icons-outlined text-xl">cancel</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-icons-outlined text-5xl text-gray-300 mb-4 block">inbox</span>
                                No abuse reports found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    <!-- Resolve Modal -->
    <div id="resolve-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Resolve Abuse Report</h3>
            <form id="resolve-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="resolve-notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Resolution Notes <span class="text-red-500">*</span>
                    </label>
                    <textarea id="resolve-notes" name="resolution_notes" rows="4" required minlength="10"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                              placeholder="Describe what actions were taken to resolve this issue..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeResolveModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <span class="material-icons-outlined text-sm mr-1">check</span>
                        Resolve
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dismiss Modal -->
    <div id="dismiss-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Dismiss Abuse Report</h3>
            <form id="dismiss-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="dismiss-notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Reason for Dismissal <span class="text-red-500">*</span>
                    </label>
                    <textarea id="dismiss-notes" name="resolution_notes" rows="4" required minlength="10"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                              placeholder="Explain why this report is being dismissed..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDismissModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <span class="material-icons-outlined text-sm mr-1">cancel</span>
                        Dismiss
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openResolveModal(reportId) {
            const modal = document.getElementById('resolve-modal');
            const form = document.getElementById('resolve-form');
            form.action = `/admin/security/abuse-reports/${reportId}/resolve`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeResolveModal() {
            const modal = document.getElementById('resolve-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openDismissModal(reportId) {
            const modal = document.getElementById('dismiss-modal');
            const form = document.getElementById('dismiss-form');
            form.action = `/admin/security/abuse-reports/${reportId}/dismiss`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDismissModal() {
            const modal = document.getElementById('dismiss-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modals when clicking outside
        document.getElementById('resolve-modal').addEventListener('click', function(e) {
            if (e.target === this) closeResolveModal();
        });
        document.getElementById('dismiss-modal').addEventListener('click', function(e) {
            if (e.target === this) closeDismissModal();
        });
    </script>
    @endpush
</x-admin-layout>
