<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;

class TicketApiController extends Controller
{
    /**
     * Get ticket count for authenticated user
     */
    public function count()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'count' => 0,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Count tickets for the current user
            $count = Ticket::where('user_id', $user->id)->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Error fetching ticket count: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get open ticket count for authenticated user
     */
    public function openCount()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'count' => 0,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Count open tickets for the current user
            $count = Ticket::where('user_id', $user->id)
                          ->whereIn('status', ['open', 'in_progress'])
                          ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Error fetching open ticket count: ' . $e->getMessage()
            ], 500);
        }
    }
}
