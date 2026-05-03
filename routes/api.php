<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Ticket;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Cart count endpoint
Route::middleware(['auth:web'])->get('/cart/count', function (Request $request) {
    try {
        $user = Auth::guard('web')->user();

        if (!$user || $user->id_role != 5) {
            return response()->json(['count' => 0]);
        }

        // Find pelanggan by email
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return response()->json(['count' => 0]);
        }

        $count = Cart::where('id_pelanggan', $pelanggan->id_pelanggan)->sum('qty');
        return response()->json(['count' => $count ?: 0]);
    } catch (\Exception $e) {
        Log::error('Error loading cart count: ' . $e->getMessage());
        return response()->json(['count' => 0], 500);
    }
});

// Wishlist count endpoint
Route::middleware(['auth:web'])->get('/wishlist/count', function (Request $request) {
    try {
        $user = Auth::guard('web')->user();

        if (!$user || $user->id_role != 5) {
            return response()->json(['count' => 0]);
        }

        // Find pelanggan by email
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return response()->json(['count' => 0]);
        }

        $count = Wishlist::where('id_pelanggan', $pelanggan->id_pelanggan)->count();
        return response()->json(['count' => $count]);
    } catch (\Exception $e) {
        Log::error('Error loading wishlist count: ' . $e->getMessage());
        return response()->json(['count' => 0], 500);
    }
});

// Ticket count endpoint
Route::middleware(['auth:web'])->get('/tickets/count', function (Request $request) {
    try {
        $user = Auth::guard('web')->user();

        if (!$user) {
            Log::info('Ticket count: No authenticated user');
            return response()->json(['count' => 0]);
        }

        Log::info('Ticket count for user: ' . $user->id . ' (' . $user->email . ')');

        // Count unread tickets for this user (tickets with new messages from CS)
        $count = Ticket::where('2', $user->id)
            ->where('is_read', false)
            ->count();

        Log::info('Unread ticket count result: ' . $count);

        return response()->json(['count' => $count]);
    } catch (\Exception $e) {
        Log::error('Error loading ticket count: ' . $e->getMessage());
        return response()->json(['count' => 0], 500);
    }
});

// Unread tickets count endpoint for CS
Route::middleware(['auth:web'])->get('/cs/tickets/unread', function (Request $request) {
    try {
        $user = Auth::guard('web')->user();

        if (!$user || $user->id_role != 3) { // Only CS staff
            return response()->json(['unread_count' => 0]);
        }

        $unreadCount = Ticket::where('is_read', false)->count();

        return response()->json(['unread_count' => $unreadCount]);
    } catch (\Exception $e) {
        Log::error('Error loading unread tickets count: ' . $e->getMessage());
        return response()->json(['unread_count' => 0], 500);
    }
});

// Notification endpoints
Route::middleware(['auth:web'])->group(function () {
    // Get unread tickets count for badge
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount']);

    // Get list of unread tickets
    Route::get('/notifications/unread-tickets', [\App\Http\Controllers\NotificationController::class, 'getUnreadTickets']);

    // Mark ticket as read
    Route::post('/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markTicketAsRead']);

    // Get new notifications since last check
    Route::get('/notifications/new', [\App\Http\Controllers\NotificationController::class, 'getNewNotifications']);
});
