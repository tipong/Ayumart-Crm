<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\PembatalanTransaksi;

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

            // 1. Orders in active process states
            $orderNotifications = Order::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where(function ($q) {
                    $q->where(function ($q2) {
                        // Active shipping events
                        $q2->where('status_pembayaran', 'sudah_bayar')
                            ->whereIn('status_pengiriman', ['dikemas', 'dikirim', 'sampai']);
                    })->orWhere(function ($q2) {
                        // Expired payment
                        $q2->where('status_pembayaran', 'kadaluarsa');
                    });
                })
                ->count();

            // 2. Cancellation results (approved or rejected)
            $cancellationNotifications = PembatalanTransaksi::whereIn('status_pembatalan', ['approved', 'rejected'])
                ->whereHas('transaksi', function ($q) use ($pelanggan) {
                    $q->where('id_pelanggan', $pelanggan->id_pelanggan);
                })
                ->count();

            // 3. Tickets with new replies or resolved
            $ticketNotifications = Ticket::where('user_id', $user->id)
                ->where(function ($q) use ($user) {
                    $q->where('status', 'resolved')
                      ->orWhereHas('messages', function ($q2) use ($user) {
                          $q2->where('user_id', '!=', $user->id)
                            ->where('created_at', '>', DB::raw(
                                '(SELECT COALESCE(MAX(created_at), \'1970-01-01\') FROM ticket_messages WHERE ticket_id = tickets.id AND user_id = ' . (int)$user->id . ')'
                            ));
                      });
                })
                ->count();

            $totalCount = $orderNotifications + $cancellationNotifications + $ticketNotifications;

            return response()->json([
                'success' => true,
                'count' => $totalCount,
                'breakdown' => [
                    'orders' => $orderNotifications,
                    'cancellations' => $cancellationNotifications,
                    'tickets' => $ticketNotifications,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Notification Count Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'count' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all notifications for authenticated user
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized', 'notifications' => []], 401);
            }

            $pelanggan = $user->pelanggan;

            if (!$pelanggan) {
                return response()->json(['success' => true, 'notifications' => [], 'total' => 0]);
            }

            $notifications = [];

            // ── ORDER NOTIFICATIONS ─────────────────────────────────────────
            $orders = Order::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get();

            foreach ($orders as $order) {
                $statusMap = [
                    'dikemas'        => ['text' => 'Pesanan sedang dikemas', 'icon' => 'bi-box-seam', 'color' => 'warning', 'label' => 'Dikemas'],
                    'dikirim'        => ['text' => 'Pesanan sedang dalam pengiriman', 'icon' => 'bi-truck', 'color' => 'info', 'label' => 'Dikirim'],
                    'sampai'         => ['text' => 'Pesanan sudah tiba di tujuan', 'icon' => 'bi-house-check', 'color' => 'success', 'label' => 'Tiba'],
                    'siap_diambil'   => ['text' => 'Pesanan siap diambil di toko', 'icon' => 'bi-bag-check', 'color' => 'primary', 'label' => 'Siap Diambil'],
                    'selesai'        => ['text' => 'Pesanan telah selesai', 'icon' => 'bi-check-circle', 'color' => 'success', 'label' => 'Selesai'],
                ];

                // Paid + active shipping events
                if ($order->status_pembayaran === 'sudah_bayar' && isset($statusMap[$order->status_pengiriman])) {
                    $info = $statusMap[$order->status_pengiriman];
                    $notifications[] = [
                        'id'      => 'order_' . $order->id_transaksi . '_shipping',
                        'type'    => 'shipping',
                        'title'   => 'Pesanan #' . $order->kode_transaksi,
                        'message' => $info['text'],
                        'label'   => $info['label'],
                        'time'    => $order->updated_at->diffForHumans(),
                        'timestamp' => $order->updated_at->timestamp,
                        'read'    => false,
                        'url'     => route('pelanggan.orders.detail', $order->id_transaksi),
                        'icon'    => $info['icon'],
                        'color'   => $info['color'],
                    ];
                }

                // Payment confirmed
                if ($order->status_pembayaran === 'sudah_bayar' && $order->status_pengiriman === 'menunggu') {
                    $notifications[] = [
                        'id'      => 'order_' . $order->id_transaksi . '_paid',
                        'type'    => 'payment',
                        'title'   => 'Pembayaran Dikonfirmasi',
                        'message' => 'Pembayaran pesanan #' . $order->kode_transaksi . ' berhasil dikonfirmasi',
                        'label'   => 'Pembayaran',
                        'time'    => $order->updated_at->diffForHumans(),
                        'timestamp' => $order->updated_at->timestamp,
                        'read'    => false,
                        'url'     => route('pelanggan.orders.detail', $order->id_transaksi),
                        'icon'    => 'bi-credit-card-2-front',
                        'color'   => 'success',
                    ];
                }

                // Expired payment
                if ($order->status_pembayaran === 'kadaluarsa') {
                    $notifications[] = [
                        'id'      => 'order_' . $order->id_transaksi . '_expired',
                        'type'    => 'expired',
                        'title'   => 'Pembayaran Kadaluarsa',
                        'message' => 'Pesanan #' . $order->kode_transaksi . ' dibatalkan karena pembayaran melewati batas waktu',
                        'label'   => 'Kadaluarsa',
                        'time'    => $order->updated_at->diffForHumans(),
                        'timestamp' => $order->updated_at->timestamp,
                        'read'    => false,
                        'url'     => route('pelanggan.orders.detail', $order->id_transaksi),
                        'icon'    => 'bi-clock-history',
                        'color'   => 'danger',
                    ];
                }
            }

            // ── CANCELLATION NOTIFICATIONS ──────────────────────────────────
            $cancellations = PembatalanTransaksi::whereIn('status_pembatalan', ['approved', 'rejected'])
                ->whereHas('transaksi', function ($q) use ($pelanggan) {
                    $q->where('id_pelanggan', $pelanggan->id_pelanggan);
                })
                ->with('transaksi')
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($cancellations as $cancel) {
                $isApproved = $cancel->status_pembatalan === 'approved';
                $notifications[] = [
                    'id'      => 'cancel_' . $cancel->id_pembatalan_transaksi,
                    'type'    => 'cancellation',
                    'title'   => $isApproved ? 'Pembatalan Disetujui' : 'Pembatalan Ditolak',
                    'message' => $isApproved
                        ? 'Permintaan pembatalan pesanan #' . ($cancel->transaksi->kode_transaksi ?? '-') . ' telah disetujui'
                        : 'Permintaan pembatalan pesanan #' . ($cancel->transaksi->kode_transaksi ?? '-') . ' ditolak oleh admin',
                    'label'   => $isApproved ? 'Dibatalkan' : 'Ditolak',
                    'time'    => $cancel->updated_at->diffForHumans(),
                    'timestamp' => $cancel->updated_at->timestamp,
                    'read'    => false,
                    'url'     => $cancel->transaksi ? route('pelanggan.orders.detail', $cancel->transaksi->id_transaksi) : '#',
                    'icon'    => $isApproved ? 'bi-x-circle' : 'bi-slash-circle',
                    'color'   => $isApproved ? 'warning' : 'danger',
                ];
            }

            // ── TICKET NOTIFICATIONS ────────────────────────────────────────
            $tickets = Ticket::where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($tickets as $ticket) {
                $latestStaffMessage = $ticket->messages()
                    ->where('user_id', '!=', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $latestUserMessage = $ticket->messages()
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $hasNewReply = $latestStaffMessage &&
                    (!$latestUserMessage || $latestStaffMessage->created_at > $latestUserMessage->created_at);

                if ($hasNewReply) {
                    $notifications[] = [
                        'id'      => 'ticket_' . $ticket->id . '_reply',
                        'type'    => 'ticket',
                        'title'   => 'Tiket #' . $ticket->ticket_number,
                        'message' => 'Ada balasan baru dari Customer Service untuk tiket Anda',
                        'label'   => 'Balasan CS',
                        'time'    => $latestStaffMessage->created_at->diffForHumans(),
                        'timestamp' => $latestStaffMessage->created_at->timestamp,
                        'read'    => false,
                        'url'     => route('pelanggan.tickets.show', $ticket->id),
                        'icon'    => 'bi-chat-left-dots',
                        'color'   => 'info',
                    ];
                }

                if ($ticket->status === 'resolved') {
                    $notifications[] = [
                        'id'      => 'ticket_' . $ticket->id . '_resolved',
                        'type'    => 'ticket',
                        'title'   => 'Tiket #' . $ticket->ticket_number,
                        'message' => 'Tiket Anda telah diselesaikan oleh Customer Service',
                        'label'   => 'Selesai',
                        'time'    => $ticket->updated_at->diffForHumans(),
                        'timestamp' => $ticket->updated_at->timestamp,
                        'read'    => false,
                        'url'     => route('pelanggan.tickets.show', $ticket->id),
                        'icon'    => 'bi-headset',
                        'color'   => 'success',
                    ];
                }
            }

            // Sort by timestamp descending (most recent first)
            usort($notifications, fn($a, $b) => $b['timestamp'] - $a['timestamp']);

            $total = count($notifications);

            return response()->json([
                'success'       => true,
                'notifications' => array_slice($notifications, 0, 20),
                'total'         => $total,
                'unread_count'  => $total, // all are unread for now
            ]);
        } catch (\Exception $e) {
            Log::error('Notification API Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success'       => false,
                'message'       => 'Failed to fetch notifications',
                'error'         => $e->getMessage(),
                'notifications' => [],
            ], 500);
        }
    }

    /**
     * Mark notification as read (stub – extend with DB field when ready)
     */
    public function markAsRead($id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read (stub)
     */
    public function markAllAsRead()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }
}
