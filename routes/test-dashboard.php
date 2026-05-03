<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Test kurir dashboard data - no auth needed
Route::get('/test/kurir-dashboard-sql', function () {
    echo "=== KURIR DASHBOARD DATA TEST ===\n\n";

    // Test for kurir user ID 39
    $userId = 39;

    echo "Testing with User ID: $userId\n\n";

    // 1. Pending orders
    echo "1. PENDING ORDERS (dikemas):\n";
    $pending = DB::table('tb_pengiriman')
        ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
        ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
        ->leftJoin('tb_cabang', 'tb_transaksi.id_cabang', '=', 'tb_cabang.id_cabang')
        ->leftJoin('tb_staff', 'tb_pengiriman.id_staff', '=', 'tb_staff.id_staff')
        ->leftJoin('users as staff_user', 'tb_staff.id_user', '=', 'staff_user.id_user')
        ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
        ->where('tb_pengiriman.status_pengiriman', 'dikemas')
        ->where(function($query) use ($userId) {
            $query->whereNull('tb_pengiriman.id_staff')
                  ->orWhere('staff_user.id_user', $userId);
        })
        ->select(
            'tb_pengiriman.id_pengiriman',
            'tb_pengiriman.id_staff',
            'tb_transaksi.kode_transaksi',
            'tb_pelanggan.nama_pelanggan',
            'tb_cabang.nama_cabang'
        )
        ->get();

    echo "Count: " . count($pending) . "\n";
    foreach ($pending as $p) {
        echo "  - ID: {$p->id_pengiriman}, Order: {$p->kode_transaksi}, Customer: {$p->nama_pelanggan}\n";
    }

    // 2. Ongoing orders
    echo "\n2. ONGOING ORDERS (dalam_pengiriman):\n";
    $ongoing = DB::table('tb_pengiriman')
        ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
        ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
        ->leftJoin('tb_cabang', 'tb_transaksi.id_cabang', '=', 'tb_cabang.id_cabang')
        ->leftJoin('tb_staff', 'tb_pengiriman.id_staff', '=', 'tb_staff.id_staff')
        ->leftJoin('users as staff_user', 'tb_staff.id_user', '=', 'staff_user.id_user')
        ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
        ->where('tb_pengiriman.status_pengiriman', 'dalam_pengiriman')
        ->where('staff_user.id_user', $userId)
        ->select(
            'tb_pengiriman.id_pengiriman',
            'tb_transaksi.kode_transaksi',
            'tb_pelanggan.nama_pelanggan'
        )
        ->get();

    echo "Count: " . count($ongoing) . "\n";
    foreach ($ongoing as $o) {
        echo "  - ID: {$o->id_pengiriman}, Order: {$o->kode_transaksi}\n";
    }

    // 3. Completed orders
    echo "\n3. COMPLETED ORDERS (selesai, last 30 days):\n";
    $completed = DB::table('tb_pengiriman')
        ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
        ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
        ->leftJoin('tb_cabang', 'tb_transaksi.id_cabang', '=', 'tb_cabang.id_cabang')
        ->leftJoin('tb_staff', 'tb_pengiriman.id_staff', '=', 'tb_staff.id_staff')
        ->leftJoin('users as staff_user', 'tb_staff.id_user', '=', 'staff_user.id_user')
        ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
        ->where('tb_pengiriman.status_pengiriman', 'selesai')
        ->where(function($query) use ($userId) {
            $query->where('staff_user.id_user', $userId)
                  ->orWhereNull('tb_pengiriman.id_staff');
        })
        ->where('tb_pengiriman.tgl_sampai', '>=', now()->subDays(30))
        ->select(
            'tb_pengiriman.id_pengiriman',
            'tb_pengiriman.tgl_sampai',
            'tb_transaksi.kode_transaksi',
            'tb_pelanggan.nama_pelanggan'
        )
        ->get();

    echo "Count: " . count($completed) . "\n";
    foreach ($completed as $c) {
        echo "  - ID: {$c->id_pengiriman}, Order: {$c->kode_transaksi}, Tgl: {$c->tgl_sampai}\n";
    }

    echo "\n=== END TEST ===\n";
});
