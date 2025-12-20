<x-app-layout>
    <x-slot name="header">{{ __('Support Tickets') }}</x-slot>
    <x-slot name="title">{{ __('My Tickets') }}</x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ __('Support Tickets') }}</h2>
                <p class="text-gray-500 mt-1">{{ __('Manage your support requests') }}</p>
            </div>
            <a href="{{ route('tickets.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 flex items-center">
                <span class="material-icons-outlined mr-2">add</span>
                {{ __('New Ticket') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-material-1 p-4">
            <form method="GET" class="flex items-center gap-4">
                <label class="text-sm font-medium text-gray-700">{{ __('Status') }}:</label>
                <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="all">{{ __('All') }}</option>
                    @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ __($label) }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Tickets List -->
        <div class="bg-white rounded-xl shadow-material-1 overflow-hidden">
            @if($tickets->isEmpty())
            <div class="p-12 text-center">
                <span class="material-icons-outlined text-6xl text-gray-300">confirmation_number</span>
                <p class="mt-4 text-gray-500">{{ __('No tickets found') }}</p>
                <a href="{{ route('tickets.create') }}" class="mt-4 inline-flex items-center text-primary-600 hover:text-primary-700">
                    <span class="material-icons-outlined mr-1">add</span>
                    {{ __('Create your first ticket') }}
                </a>
            </div>
            @else
            <div class="divide-y divide-gray-100">
                @foreach($tickets as $ticket)
                <a href="{{ route('tickets.show', $ticket) }}" class="block p-6 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-mono text-gray-500">#{{ $ticket->ticket_number }}</span>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($ticket->status === 'open') bg-info-100 text-info-700
                                    @elseif($ticket->status === 'in_progress') bg-primary-100 text-primary-700
                                    @elseif($ticket->status === 'waiting_customer') bg-warning-100 text-warning-700
                                    @elseif($ticket->status === 'resolved') bg-success-100 text-success-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ __($statuses[$ticket->status] ?? $ticket->status) }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($ticket->priority === 'urgent') bg-danger-100 text-danger-700
                                    @elseif($ticket->priority === 'high') bg-warning-100 text-warning-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </div>
                            <h3 class="mt-2 text-lg font-medium text-gray-800">{{ $ticket->subject }}</h3>
                            @if($ticket->messages->first())
                            <p class="mt-1 text-gray-500 text-sm line-clamp-2">{{ Str::limit($ticket->messages->first()->message, 120) }}</p>
                            @endif
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            <p>{{ $ticket->created_at->format('M d, Y') }}</p>
                            <p class="text-xs">{{ $ticket->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
