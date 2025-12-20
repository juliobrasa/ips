<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(protected NotificationService $notifications)
    {
    }

    /**
     * List user's tickets
     */
    public function index(Request $request): View
    {
        $query = SupportTicket::where('user_id', auth()->id())
            ->with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(15);

        return view('tickets.index', [
            'tickets' => $tickets,
            'statuses' => SupportTicket::STATUSES,
        ]);
    }

    /**
     * Show create ticket form
     */
    public function create(): View
    {
        return view('tickets.create', [
            'categories' => SupportTicket::CATEGORIES,
            'priorities' => SupportTicket::PRIORITIES,
        ]);
    }

    /**
     * Store new ticket
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(SupportTicket::CATEGORIES)),
            'priority' => 'required|string|in:' . implode(',', array_keys(SupportTicket::PRIORITIES)),
            'message' => 'required|string|max:5000',
            'related_lease_id' => 'nullable|exists:leases,id',
            'related_subnet_id' => 'nullable|exists:subnets,id',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'related_lease_id' => $validated['related_lease_id'] ?? null,
            'related_subnet_id' => $validated['related_subnet_id'] ?? null,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', __('Ticket created successfully. Ticket number: :number', ['number' => $ticket->ticket_number]));
    }

    /**
     * Show ticket
     */
    public function show(SupportTicket $ticket): View
    {
        if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $ticket->load(['messages.user', 'relatedLease.subnet', 'relatedSubnet', 'assignedTo']);

        return view('tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Add reply to ticket
     */
    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        // Update ticket status
        if ($ticket->status === 'waiting_customer' && $ticket->user_id === auth()->id()) {
            $ticket->update(['status' => 'open']);
        }

        // If admin is replying and no first response, record it
        if (auth()->user()->role === 'admin' && !$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', __('Reply added successfully.'));
    }

    /**
     * Close ticket
     */
    public function close(SupportTicket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $ticket->update([
            'status' => 'closed',
            'resolved_at' => now(),
        ]);

        return redirect()->route('tickets.index')
            ->with('success', __('Ticket closed successfully.'));
    }

    /**
     * Reopen ticket
     */
    public function reopen(SupportTicket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($ticket->status, ['closed', 'resolved'])) {
            return back()->with('error', __('Only closed or resolved tickets can be reopened.'));
        }

        $ticket->update([
            'status' => 'open',
            'resolved_at' => null,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', __('Ticket reopened successfully.'));
    }
}
