<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistApiController extends Controller
{
    /**
     * Get wishlist item count for logged-in user
     */
    public function count(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $user = Auth::user();

        // Get pelanggan data
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return response()->json(['count' => 0]);
        }

        // Count wishlist items
        $count = DB::table('tb_wishlist')
                   ->where('id_pelanggan', $pelanggan->id_pelanggan)
                   ->count();

        return response()->json(['count' => (int) $count]);
    }
}
