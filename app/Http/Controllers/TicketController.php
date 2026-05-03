<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets (CS view)
     */
    public function index()
    {
        $query = \App\Models\Ticket::with(['user', 'user.pelanggan']);  // FIXED: Added pelanggan eager loading

        // Apply filters
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('priority')) {
            $query->where('priority', request('priority'));
        }
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      // FIXED: Search by email instead of name (name doesn't exist in users table)
                      $q2->where('email', 'like', "%{$search}%")
                         ->orWhereHas('pelanggan', function($q3) use ($search) {
                             $q3->where('nama_pelanggan', 'like', "%{$search}%");
                         });
                  });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $totalTickets = \App\Models\Ticket::count();
        $openTickets = \App\Models\Ticket::where('status', 'open')->count();
        $inProgressTickets = \App\Models\Ticket::where('status', 'in_progress')->count();
        $resolvedTickets = \App\Models\Ticket::where('status', 'resolved')->count();

        // Get users (customers) for create modal - users with id_role 5 (pelanggan)
        // FIXED: Changed role_id to id_role, id to id_user, removed name (doesn't exist in users table)
        $pelanggan = \App\Models\User::with('pelanggan')
            ->where('id_role', 5)
            ->select('id_user', 'email')
            ->orderBy('email')
            ->get();

        return view('cs.tickets.index', compact(
            'tickets',
            'totalTickets',
            'openTickets',
            'inProgressTickets',
            'resolvedTickets',
            'pelanggan'
        ));
    }

    /**
     * Display the specified ticket (CS view)
     */
    public function show(string $id)
    {
        $ticket = \App\Models\Ticket::with([
            'user',
            'user.pelanggan',
            'messages.user',
            'messages.user.pelanggan',
            'messages.user.staff',
            'assignedTo',
            'assignedTo.staff'
        ])  // FIXED: Added pelanggan and staff eager loading
            ->findOrFail($id);

        // Mark ticket as read when viewing
        if (!$ticket->is_read) {
            $ticket->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return view('cs.tickets.show', compact('ticket'));
    }

    /**
     * Update the specified ticket status
     */
    public function update(Request $request, string $id)
    {
        $ticket = \App\Models\Ticket::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        // Auto-assign to current CS if not assigned
        if (!$ticket->assigned_to) {
            $validated['assigned_to'] = auth()->id();
        }

        // Set resolved_at when status changes to resolved or closed
        if (in_array($validated['status'], ['resolved', 'closed']) && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        return back()->with('success', 'Status tiket berhasil diupdate');
    }

    /**
     * Add a reply to a ticket (CS)
     */
    public function reply(Request $request, string $id)
    {
        $ticket = \App\Models\Ticket::findOrFail($id);

        if ($ticket->status === 'closed') {
            return back()->with('error', 'Tidak dapat mengirim pesan ke tiket yang sudah ditutup');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = \App\Models\TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->user()->id_user,
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        // Prepare update data
        $updateData = [
            'is_read' => false,
            'read_at' => null,
        ];

        // Auto-assign ticket to current CS if not assigned
        if (!$ticket->assigned_to) {
            $updateData['assigned_to'] = auth()->user()->id_user;
        }

        // Update ticket status to in_progress if open
        if ($ticket->status === 'open') {
            $updateData['status'] = 'in_progress';
        }

        // Mark ticket as unread for customer and force update timestamp
        // Using raw DB query to ensure updated_at is actually modified
        \Illuminate\Support\Facades\DB::table('tickets')
            ->where('id', $ticket->id)
            ->update(array_merge($updateData, ['updated_at' => \Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP')]));

        // Refresh ticket instance
        $ticket->refresh();

        // Return success response with notification info
        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim',
            'ticket_id' => $ticket->id,
            'customer_id' => $ticket->user_id,
            'notification' => [
                'title' => 'Balasan Baru',
                'body' => 'Ada balasan dari CS untuk tiket: ' . $ticket->ticket_number
            ]
        ]);
    }

    /**
     * Customer ticket index
     */
    public function customerIndex()
    {
        $tickets = \App\Models\Ticket::where('user_id', auth()->id())
            ->with('messages')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pelanggan.tickets.index', compact('tickets'));
    }

    /**
     * Show ticket creation form (customer)
     */
    public function create()
    {
        return view('pelanggan.tickets.create');
    }

    /**
     * Store new ticket from customer
     */
    public function store(Request $request)
    {
        // Check if request is from CS or Customer
        if (auth()->user()->id_role == 3) { // CS Staff
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id_user',
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:low,medium,high',
                'category' => 'nullable|string|max:100',
            ]);

            $validated['status'] = 'open';
            $validated['assigned_to'] = auth()->user()->id_user;

            $ticket = \App\Models\Ticket::create($validated);

            return redirect()->route('cs.tickets.index')
                ->with('success', 'Ticket #' . $ticket->ticket_number . ' berhasil dibuat');
        } else { // Customer
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:low,medium,high',
                'category' => 'nullable|string|max:100',
            ]);

            $validated['user_id'] = auth()->user()->id_user;
            $validated['status'] = 'open';

            $ticket = \App\Models\Ticket::create($validated);

            return redirect()->route('pelanggan.tickets.index')
                ->with('success', 'Tiket #' . $ticket->ticket_number . ' berhasil dibuat. Tim CS kami akan segera merespon.');
        }
    }

    /**
     * Customer ticket detail
     */
    public function customerShow(string $id)
    {
        $ticket = \App\Models\Ticket::where('user_id', auth()->user()->id_user)
            ->with(['messages.user', 'assignedTo'])
            ->findOrFail($id);

        // Mark ticket as read when viewing
        if (!$ticket->is_read) {
            $ticket->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return view('pelanggan.tickets.show', compact('ticket'));
    }

    /**
     * Customer add reply to ticket
     */
    public function customerReply(Request $request, string $id)
    {
        $ticket = \App\Models\Ticket::where('user_id', auth()->user()->id_user)->findOrFail($id);

        // Prevent reply if ticket is closed or resolved
        if ($ticket->status === 'closed') {
            return back()->with('error', 'Tidak dapat mengirim pesan ke tiket yang sudah ditutup');
        }

        if ($ticket->status === 'resolved') {
            return back()->with('error', 'Tidak dapat mengirim pesan ke tiket yang sudah diselesaikan. Silakan hubungi CS untuk membuka kembali tiket ini.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        \App\Models\TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->user()->id_user,
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        // Mark ticket as unread for CS so they get notified of customer's reply
        // Using raw DB query to ensure updated_at is actually modified
        \Illuminate\Support\Facades\DB::table('tickets')
            ->where('id', $ticket->id)
            ->update([
                'is_read' => false,
                'read_at' => null,
                'updated_at' => \Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP')
            ]);

        // Refresh ticket instance
        $ticket->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim',
            'ticket_id' => $ticket->id
        ]);
    }

    /**
     * Customer close ticket
     */
    public function customerClose(string $id)
    {
        $ticket = \App\Models\Ticket::where('user_id', auth()->id())->findOrFail($id);

        $ticket->update([
            'status' => 'closed',
            'resolved_at' => now(),
        ]);

        return redirect()->route('pelanggan.tickets.index')
            ->with('success', 'Tiket berhasil ditutup');
    }

    /**
     * Delete ticket (CS only)
     */
    public function destroy(string $id)
    {
        try {
            $ticket = \App\Models\Ticket::findOrFail($id);

            // Store ticket number for success message
            $ticketNumber = $ticket->ticket_number;

            // Delete related messages first (if not using cascade delete)
            \App\Models\TicketMessage::where('ticket_id', $ticket->id)->delete();

            // Delete the ticket
            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => "Ticket #{$ticketNumber} berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus ticket: ' . $e->getMessage()
            ], 500);
        }
    }
}
