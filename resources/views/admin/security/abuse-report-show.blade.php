<x-admin-layout>
    <x-slot name="header">Abuse Report #{{ $report->id }}</x-slot>
    <x-slot name="title">Abuse Report Details</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.security.abuse-reports') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <span class="material-icons-outlined mr-1">arrow_back</span>
            Back to Reports
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Report Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Report Summary Card -->
            <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">
                                {{ ucfirst($report->type) }} Report
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Reported {{ $report->created_at->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @php
                                $severityColors = [
                                    'critical' => 'red',
                                    'high' => 'orange',
                                    'medium' => 'yellow',
                                    'low' => 'green',
                                ];
                                $statusColors = [
                                    'open' => 'red',
                                    'investigating' => 'yellow',
                                    'resolved' => 'green',
                                    'dismissed' => 'gray',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $severityColors[$report->severity] ?? 'gray' }}-100 text-{{ $severityColors[$report->severity] ?? 'gray' }}-800 capitalize">
                                {{ $report->severity }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $statusColors[$report->status] ?? 'gray' }}-100 text-{{ $statusColors[$report->status] ?? 'gray' }}-800 capitalize">
                                {{ $report->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Description</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {{ $report->description }}
                    </div>
                </div>

                @if($report->evidence)
                    <div class="p-6 border-t border-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Evidence</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($report->evidence, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif

                @if($report->resolved_at)
                    <div class="p-6 border-t border-gray-100 bg-{{ $report->status === 'resolved' ? 'green' : 'gray' }}-50">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">
                            {{ $report->status === 'resolved' ? 'Resolution' : 'Dismissal' }} Notes
                        </h3>
                        <p class="text-gray-700 mb-4">{{ $report->resolution_notes }}</p>
                        <p class="text-sm text-gray-500">
                            {{ ucfirst($report->status) }} by {{ $report->resolvedByUser->name ?? 'Unknown' }}
                            on {{ $report->resolved_at->format('F j, Y \a\t g:i A') }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Affected IP Information -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Affected IP Range</h3>

                @if($report->subnet)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Subnet</p>
                            <p class="font-mono text-lg">{{ $report->subnet->cidr_notation }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">IP Count</p>
                            <p class="font-medium">{{ number_format($report->subnet->ip_count) }} IPs</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">RIR</p>
                            <p class="font-medium">{{ strtoupper($report->subnet->rir ?? 'Unknown') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Reputation Score</p>
                            @php
                                $score = $report->subnet->reputation_score ?? 0;
                                $scoreColor = $score >= 80 ? 'green' : ($score >= 50 ? 'yellow' : 'red');
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-{{ $scoreColor }}-100 text-{{ $scoreColor }}-800">
                                {{ $score }}%
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Holder</p>
                            <p class="font-medium">{{ $report->subnet->company->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-medium capitalize">{{ $report->subnet->status }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.subnets.show', $report->subnet) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                            <span class="material-icons-outlined text-sm mr-1">visibility</span>
                            View Subnet
                        </a>
                        <form action="{{ route('admin.subnets.check-reputation', $report->subnet) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-primary-100 text-primary-700 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                                <span class="material-icons-outlined text-sm mr-1">refresh</span>
                                Check Reputation
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-gray-500">Subnet information not available.</p>
                @endif
            </div>

            <!-- Lease Information -->
            @if($report->lease)
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Associated Lease</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Lease ID</p>
                            <p class="font-medium">#{{ $report->lease->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lessee</p>
                            <p class="font-medium">{{ $report->lease->lesseeCompany->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Start Date</p>
                            <p class="font-medium">{{ $report->lease->start_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">End Date</p>
                            <p class="font-medium">{{ $report->lease->end_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-medium capitalize">{{ $report->lease->status }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Monthly Price</p>
                            <p class="font-medium">${{ number_format($report->lease->monthly_price, 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.leases.show', $report->lease) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                            <span class="material-icons-outlined text-sm mr-1">visibility</span>
                            View Lease
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions Card -->
            @if($report->status === 'open' || $report->status === 'investigating')
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>

                    <div class="space-y-3">
                        <!-- Resolve Form -->
                        <form action="{{ route('admin.security.abuse-reports.resolve', $report) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="resolve-notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Resolution Notes
                                </label>
                                <textarea id="resolve-notes" name="resolution_notes" rows="3" required minlength="10"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500"
                                          placeholder="Describe actions taken..."></textarea>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                <span class="material-icons-outlined mr-2 text-sm">check_circle</span>
                                Mark as Resolved
                            </button>
                        </form>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">or</span>
                            </div>
                        </div>

                        <!-- Dismiss Form -->
                        <form action="{{ route('admin.security.abuse-reports.dismiss', $report) }}" method="POST">
                            @csrf
                            <input type="hidden" name="resolution_notes" id="dismiss-reason" value="">
                            <button type="button" onclick="promptDismiss(this.form)" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center">
                                <span class="material-icons-outlined mr-2 text-sm">cancel</span>
                                Dismiss Report
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Report Source -->
            <div class="bg-white rounded-xl shadow-material-1 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Report Source</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Source</p>
                        <p class="font-medium">{{ $report->source ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Report Type</p>
                        <p class="font-medium capitalize">{{ $report->type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Severity Level</p>
                        <p class="font-medium capitalize">{{ $report->severity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Reported At</p>
                        <p class="font-medium">{{ $report->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Related Reports -->
            @if($relatedReports->count() > 0)
                <div class="bg-white rounded-xl shadow-material-1 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Related Reports</h3>
                    <div class="space-y-3">
                        @foreach($relatedReports as $related)
                            <a href="{{ route('admin.security.abuse-reports.show', $related) }}"
                               class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-sm">#{{ $related->id }} - {{ ucfirst($related->type) }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColors[$related->status] ?? 'gray' }}-100 text-{{ $statusColors[$related->status] ?? 'gray' }}-800 capitalize">
                                        {{ $related->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $related->created_at->diffForHumans() }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function promptDismiss(form) {
            const reason = prompt('Please provide a reason for dismissing this report (min 10 characters):');
            if (reason && reason.length >= 10) {
                form.querySelector('#dismiss-reason').value = reason;
                form.submit();
            } else if (reason !== null) {
                alert('Please provide a reason with at least 10 characters.');
            }
        }
    </script>
    @endpush
</x-admin-layout>
