<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartApiController extends Controller
{
    /**
     * Get cart item count for logged-in user
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

        // Count cart items
        $count = DB::table('tb_detail_cart')
                   ->where('id_pelanggan', $pelanggan->id_pelanggan)
                   ->sum('qty');

        return response()->json(['count' => (int) $count]);
    }
}
