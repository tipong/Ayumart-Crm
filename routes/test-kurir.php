<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/test-kurir-data', function () {
    $userId = 39; // kurir@crm.com
    $user = \App\Models\User::find($userId);

    echo "<h2>Testing Kurir Dashboard Queries</h2>";
    echo "<p>User: " . $user->email . " (ID: " . $user->id_user . ", Role: " . $user->id_role . ")</p>";

    // Test 1: Pending Orders
    echo "<h3>1. Pending Orders (status=dikemas)</h3>";
    $pendingOrders = DB::table('tb_pengiriman')
        ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
        ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
        ->leftJoin('tb_cabang', 'tb_transaksi.id_cabang', '=', 'tb_cabang.id_cabang')
        ->leftJoin('tb_staff', 'tb_pengiriman.id_staff', '=', 'tb_staff.id_staff')
        ->leftJoin('users as staff_user', 'tb_staff.id_user', '=', 'staff_user.id_user')
        ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
        ->where('tb_pengiriman.status_pengiriman', 'dikemas')
        ->where(function($query) use ($user) {
            $query->whereNull('tb_pengiriman.id_staff')
                  ->orWhere('staff_user.id_user', $user->id_user);
        })
        ->select('tb_pengiriman.*', 'tb_transaksi.kode_transaksi')
        ->get();

    echo "<p>Count: " . $pendingOrders->count() . "</p>";
    if ($pendingOrders->count() > 0) {
        echo "<pre>";
        foreach ($pendingOrders as $order) {
            echo "ID: {$order->id_pengiriman}, Resi: {$order->no_resi}, Status: {$order->status_pengiriman}, ID Staff: {$order->id_staff}\n";
        }
        echo "</pre>";
    }

    // Test 2: All pengiriman with status dikemas (regardless of staff)
    echo "<h3>2. All Pengiriman (status=dikemas, sudah_bayar)</h3>";
    $allPending = DB::table('tb_pengiriman')
        ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
        ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
        ->where('tb_pengiriman.status_pengiriman', 'dikemas')
        ->select('tb_pengiriman.*', 'tb_transaksi.kode_transaksi')
        ->get();

    echo "<p>Count: " . $allPending->count() . "</p>";
    echo "<pre>";
    foreach ($allPending as $order) {
        echo "ID: {$order->id_pengiriman}, Resi: {$order->no_resi}, Status: {$order->status_pengiriman}, ID Staff: {$order->id_staff}\n";
    }
    echo "</pre>";

    // Test 3: Completed Orders
    echo "<h3>3. Completed Orders (status=selesai)</h3>";
    $completedOrders = DB::table('tb_pengiriman')
        ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
        ->leftJoin('tb_staff', 'tb_pengiriman.id_staff', '=', 'tb_staff.id_staff')
        ->leftJoin('users as staff_user', 'tb_staff.id_user', '=', 'staff_user.id_user')
        ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
        ->where('tb_pengiriman.status_pengiriman', 'selesai')
        ->where(function($query) use ($user) {
            $query->where('staff_user.id_user', $user->id_user)
                  ->orWhereNull('tb_pengiriman.id_staff');
        })
        ->select('tb_pengiriman.*', 'tb_transaksi.kode_transaksi')
        ->get();

    echo "<p>Count: " . $completedOrders->count() . "</p>";
    echo "<pre>";
    foreach ($completedOrders as $order) {
        echo "ID: {$order->id_pengiriman}, Resi: {$order->no_resi}, Status: {$order->status_pengiriman}, ID Staff: {$order->id_staff}\n";
    }
    echo "</pre>";
});
