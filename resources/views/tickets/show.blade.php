<x-app-layout>
    <x-slot name="header">{{ __('Ticket') }} #{{ $ticket->ticket_number }}</x-slot>
    <x-slot name="title">{{ $ticket->subject }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="{{ route('tickets.index') }}" class="hover:text-primary-600">{{ __('Tickets') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">#{{ $ticket->ticket_number }}</span>
        </div>

        <!-- Ticket Header -->
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 text-sm rounded-full
                            @if($ticket->status === 'open') bg-info-100 text-info-700
                            @elseif($ticket->status === 'in_progress') bg-primary-100 text-primary-700
                            @elseif($ticket->status === 'waiting_customer') bg-warning-100 text-warning-700
                            @elseif($ticket->status === 'resolved') bg-success-100 text-success-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ __(ucfirst(str_replace('_', ' ', $ticket->status))) }}
                        </span>
                        <span class="px-3 py-1 text-sm rounded-full
                            @if($ticket->priority === 'urgent') bg-danger-100 text-danger-700
                            @elseif($ticket->priority === 'high') bg-warning-100 text-warning-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($ticket->priority) }} {{ __('Priority') }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $ticket->subject }}</h1>
                    <p class="text-gray-500 mt-1">
                        {{ __('Created') }} {{ $ticket->created_at->format('M d, Y H:i') }}
                        @if($ticket->assignedTo)
                        · {{ __('Assigned to') }} {{ $ticket->assignedTo->name }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if(in_array($ticket->status, ['closed', 'resolved']))
                    <form action="{{ route('tickets.reopen', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50">
                            {{ __('Reopen') }}
                        </button>
                    </form>
                    @else
                    <form action="{{ route('tickets.close', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            {{ __('Close Ticket') }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            @if($ticket->relatedLease || $ticket->relatedSubnet)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-500 mb-2">{{ __('Related to') }}:</p>
                <div class="flex items-center gap-4">
                    @if($ticket->relatedLease)
                    <a href="{{ route('leases.show', $ticket->relatedLease) }}" class="text-primary-600 hover:underline">
                        <span class="material-icons-outlined text-sm mr-1">assignment</span>
                        {{ __('Lease') }}: {{ $ticket->relatedLease->subnet->cidr ?? 'N/A' }}
                    </a>
                    @endif
                    @if($ticket->relatedSubnet)
                    <a href="{{ route('subnets.show', $ticket->relatedSubnet) }}" class="text-primary-600 hover:underline">
                        <span class="material-icons-outlined text-sm mr-1">lan</span>
                        {{ __('Subnet') }}: {{ $ticket->relatedSubnet->cidr }}
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Messages -->
        <div class="space-y-4">
            @foreach($ticket->messages as $message)
            <div class="bg-white rounded-xl shadow-material-1 p-6 {{ $message->user_id !== $ticket->user_id ? 'border-l-4 border-primary-500' : '' }}">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $message->user_id !== $ticket->user_id ? 'bg-primary-100' : 'bg-gray-100' }}">
                            <span class="font-semibold {{ $message->user_id !== $ticket->user_id ? 'text-primary-600' : 'text-gray-600' }}">
                                {{ substr($message->user->name ?? '?', 0, 1) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-800">
                                {{ $message->user->name ?? 'Unknown' }}
                                @if($message->user_id !== $ticket->user_id)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-primary-100 text-primary-700 rounded">{{ __('Support') }}</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-500">{{ $message->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                <div class="prose prose-sm max-w-none text-gray-700">
                    {!! nl2br(e($message->message)) !!}
                </div>
            </div>
            @endforeach
        </div>

        <!-- Reply Form -->
        @if(!in_array($ticket->status, ['closed']))
        <div class="bg-white rounded-xl shadow-material-1 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Add Reply') }}</h3>
            <form action="{{ route('tickets.reply', $ticket) }}" method="POST">
                @csrf
                <textarea name="message" rows="5" required
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                          placeholder="{{ __('Type your reply...') }}"></textarea>
                @error('message')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 flex items-center">
                        <span class="material-icons-outlined mr-2">send</span>
                        {{ __('Send Reply') }}
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-gray-50 rounded-xl p-6 text-center">
            <span class="material-icons-outlined text-4xl text-gray-400">lock</span>
            <p class="mt-2 text-gray-600">{{ __('This ticket is closed. Reopen it to add a reply.') }}</p>
        </div>
        @endif
    </div>
</x-app-layout>
