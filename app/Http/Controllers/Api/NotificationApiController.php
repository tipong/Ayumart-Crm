<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Ticket;

class NotificationApiController extends Controller
{
    /**
     * Get notification count for authenticated user
     */
    public function count()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['count' => 0]);
            }

            $pelanggan = $user->pelanggan;

            if (!$pelanggan) {
                return response()->json(['count' => 0]);
            }

            // Count unread notifications:
            // 1. New orders that are processed/shipped
            $orderNotifications = Order::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('status_pembayaran', 'sudah_bayar')
                ->whereIn('status_pengiriman', ['dikemas', 'dikirim', 'sampai'])
                ->count();

            // 2. Tickets with new replies from staff
            $ticketNotifications = Ticket::where('user_id', $user->id)
                ->whereIn('status', ['open', 'in_progress', 'resolved'])
                ->whereHas('messages', function($q) use ($user) {
                    $q->where('user_id', '!=', $user->id)
                      ->where('created_at', '>', DB::raw('(SELECT MAX(created_at) FROM ticket_messages WHERE ticket_id = tickets.id AND user_id = ' . $user->id . ')'));
                })
                ->count();

            $totalCount = $orderNotifications + $ticketNotifications;

            return response()->json([
                'success' => true,
                'count' => $totalCount,
                'breakdown' => [
                    'orders' => $orderNotifications,
                    'tickets' => $ticketNotifications
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Notification Count Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'count' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all notifications for authenticated user
     */
    public function index()
    {
        try {
            $user = Auth::user();

            Log::info('Notification Index Request', [
                'user_id' => $user ? $user->id : null,
                'user_email' => $user ? $user->email : null
            ]);

            if (!$user) {
                Log::warning('Notification Index: No authenticated user');
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'notifications' => []
                ], 401);
            }

            $pelanggan = $user->pelanggan;

            Log::info('Notification Index: Pelanggan data', [
                'has_pelanggan' => $pelanggan ? true : false,
                'pelanggan_id' => $pelanggan ? $pelanggan->id_pelanggan : null
            ]);

            if (!$pelanggan) {
                Log::warning('Notification Index: No customer data found for user ' . $user->id);
                return response()->json([
                    'success' => true,
                    'notifications' => [],
                    'total' => 0,
                    'message' => 'No customer data found'
                ]);
            }

            $notifications = [];

            // Get order notifications
            $orders = Order::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('status_pembayaran', 'sudah_bayar')
                ->whereIn('status_pengiriman', ['dikemas', 'dikirim', 'sampai'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            Log::info('Notification Index: Orders found', ['count' => $orders->count()]);

            foreach ($orders as $order) {
                $statusText = [
                    'dikemas' => 'sedang dikemas',
                    'dikirim' => 'sedang dikirim',
                    'sampai' => 'sudah sampai'
                ];

                $statusColor = [
                    'dikemas' => 'warning',
                    'dikirim' => 'info',
                    'sampai' => 'success'
                ];

                $notifications[] = [
                    'id' => 'order_' . $order->id_transaksi,
                    'type' => 'order',
                    'title' => 'Pesanan #' . $order->kode_transaksi,
                    'message' => 'Pesanan Anda ' . ($statusText[$order->status_pengiriman] ?? $order->status_pengiriman),
                    'time' => $order->updated_at->diffForHumans(),
                    'read' => true, // Always mark as read for now since we don't have is_notified field
                    'url' => route('pelanggan.orders.detail', $order->id_transaksi),
                    'icon' => 'bi-bag-check',
                    'color' => $statusColor[$order->status_pengiriman] ?? 'primary'
                ];
            }

            // Get ticket notifications
            $tickets = Ticket::where('user_id', $user->id)
                ->whereIn('status', ['open', 'in_progress', 'resolved'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            Log::info('Notification Index: Tickets found', ['count' => $tickets->count()]);

            foreach ($tickets as $ticket) {
                // Check if ticket has new replies from staff (not from user)
                $latestStaffMessage = $ticket->messages()
                    ->where('user_id', '!=', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $latestUserMessage = $ticket->messages()
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // If latest staff message is newer than latest user message, consider it unread
                $hasNewReply = $latestStaffMessage &&
                    (!$latestUserMessage || $latestStaffMessage->created_at > $latestUserMessage->created_at);

                if ($hasNewReply || $ticket->status === 'resolved') {
                    $message = $hasNewReply
                        ? 'Ada balasan baru dari Customer Service'
                        : 'Tiket Anda telah diselesaikan';

                    $notifications[] = [
                        'id' => 'ticket_' . $ticket->id,
                        'type' => 'ticket',
                        'title' => 'Tiket #' . $ticket->ticket_number,
                        'message' => $message,
                        'time' => $ticket->updated_at->diffForHumans(),
                        'read' => false,
                        'url' => route('pelanggan.tickets.show', $ticket->id),
                        'icon' => 'bi-headset',
                        'color' => 'info'
                    ];
                }
            }

            // Sort by time (most recent first)
            usort($notifications, function($a, $b) {
                return strcmp($b['time'], $a['time']);
            });

            Log::info('Notification Index: Total notifications', ['count' => count($notifications)]);

            return response()->json([
                'success' => true,
                'notifications' => array_slice($notifications, 0, 15),
                'total' => count($notifications)
            ]);
        } catch (\Exception $e) {
            Log::error('Notification API Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage(),
                'notifications' => []
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // For now, just return success since we don't have is_notified field
            // This can be implemented later when the field is added to the database

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Mark as Read Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $pelanggan = $user->pelanggan;
            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer data not found'
                ], 404);
            }

            // For now, just return success since we don't have is_notified field
            // This can be implemented later when the field is added to the database

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Mark All as Read Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
