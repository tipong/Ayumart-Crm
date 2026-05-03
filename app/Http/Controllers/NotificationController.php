<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class NotificationController extends Controller
{
    /**
     * Get unread tickets count for badge
     */
    public function getUnreadCount()
    {
        if (!auth()->check()) {
            return response()->json(['unread_count' => 0]);
        }

        $userId = auth()->user()->id_user;
        $userRole = auth()->user()->id_role;

        if ($userRole == 3) { // CS Staff
            // For CS, count tickets where is_read = false (assigned to them)
            $unreadCount = Ticket::where('is_read', false)
                ->where(function($query) use ($userId) {
                    $query->where('assigned_to', $userId)
                          ->orWhere('assigned_to', null);
                })
                ->count();
        } else { // Customer (role 5)
            // For customer, count their tickets where is_read = false
            $unreadCount = Ticket::where('user_id', $userId)
                ->where('is_read', false)
                ->count();
        }

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get unread tickets list for notification dropdown
     */
    public function getUnreadTickets()
    {
        if (!auth()->check()) {
            return response()->json(['tickets' => []]);
        }

        $userId = auth()->user()->id_user;
        $userRole = auth()->user()->id_role;

        if ($userRole == 3) { // CS Staff
            $tickets = Ticket::where('is_read', false)
                ->where(function($query) use ($userId) {
                    $query->where('assigned_to', $userId)
                          ->orWhere('assigned_to', null);
                })
                ->with(['user', 'user.pelanggan'])
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
        } else { // Customer
            $tickets = Ticket::where('user_id', $userId)
                ->where('is_read', false)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
        }

        return response()->json([
            'tickets' => $tickets->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'customer_name' => $ticket->user->pelanggan->nama_pelanggan ?? $ticket->user->email,
                    'status' => $ticket->status,
                    'updated_at' => $ticket->updated_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Mark ticket as read
     */
    public function markTicketAsRead(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ticketId = $request->input('ticket_id');
        $ticket = Ticket::findOrFail($ticketId);

        $userId = auth()->user()->id_user;

        // Verify user owns or is assigned to the ticket
        if ($ticket->user_id != $userId && $ticket->assigned_to != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ticket->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket marked as read'
        ]);
    }

    /**
     * Get new notifications since last check
     */
    public function getNewNotifications(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'new_notifications' => false,
                'unread_count' => 0,
                'tickets' => []
            ]);
        }

        $userId = auth()->user()->id_user;
        $userRole = auth()->user()->id_role;
        $sinceTimestamp = $request->input('since', now()->subMinutes(5)->timestamp);
        $sinceDate = \Carbon\Carbon::createFromTimestamp($sinceTimestamp);

        if ($userRole == 3) { // CS Staff
            $query = Ticket::where('is_read', false)
                ->where(function($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhere('assigned_to', null);
                });
        } else { // Customer
            $query = Ticket::where('user_id', $userId)
                ->where('is_read', false);
        }

        // Get all unread tickets (not just since timestamp)
        // This ensures we catch all unread tickets even if system time changes
        $unreadTickets = $query->orderBy('updated_at', 'desc')->get();
        $unreadCount = $unreadTickets->count();

        // Check if any updated since timestamp (for actual new notifications)
        // Give a buffer of 5 seconds to account for timing issues
        $fiveSecondsAgo = $sinceDate->subSeconds(5);
        $newTickets = $query->where('updated_at', '>=', $fiveSecondsAgo)->get();
        $hasNewNotifications = $newTickets->count() > 0;

        return response()->json([
            'new_notifications' => $hasNewNotifications,
            'unread_count' => $unreadCount,
            'tickets' => $unreadTickets->map(fn($ticket) => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'is_read' => $ticket->is_read,
                'updated_at' => $ticket->updated_at
            ])->toArray()
        ]);
    }
}
