<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/test-kurir-data', function () {
    // Check kurir users
    $kurirUsers = DB::table('users')
        ->where('id_role', 4)
        ->get();

    echo "=== Kurir Users ===\n";
    echo "Count: " . count($kurirUsers) . "\n";
    foreach ($kurirUsers as $user) {
        echo "ID: {$user->id_user}, Name: {$user->name}, Email: {$user->email}\n";
    }

    echo "\n=== Delivery Summary ===\n";

    // Get all transactions
    $allTransactions = DB::table('tb_transaksi')
        ->count();
    echo "Total Transactions: $allTransactions\n";

    // Get paid transactions
    $paidTransactions = DB::table('tb_transaksi')
        ->where('status_pembayaran', 'sudah_bayar')
        ->count();
    echo "Paid Transactions: $paidTransactions\n";

    // Get all deliveries
    $allDeliveries = DB::table('tb_pengiriman')
        ->count();
    echo "Total Deliveries: $allDeliveries\n";

    echo "\n=== Deliveries by Status (Paid Transactions) ===\n";

    $statuses = DB::table('tb_pengiriman')
        ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
        ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
        ->select('tb_pengiriman.status_pengiriman')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('tb_pengiriman.status_pengiriman')
        ->get();

    foreach ($statuses as $s) {
        echo "  {$s->status_pengiriman}: {$s->count}\n";
    }

    echo "\n=== Sample Delivery Data (Paid + Pending) ===\n";

    $samples = DB::table('tb_pengiriman')
        ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
        ->leftJoin('tb_pelanggan', 'tb_transaksi.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
        ->where('tb_transaksi.status_pembayaran', 'sudah_bayar')
        ->where('tb_pengiriman.status_pengiriman', 'dikemas')
        ->select(
            'tb_pengiriman.id_pengiriman',
            'tb_pengiriman.id_staff',
            'tb_pengiriman.status_pengiriman',
            'tb_transaksi.kode_transaksi',
            'tb_transaksi.status_pembayaran',
            'tb_pelanggan.nama_pelanggan'
        )
        ->limit(5)
        ->get();

    foreach ($samples as $sample) {
        echo "ID: {$sample->id_pengiriman}, Staff: {$sample->id_staff}, Order: {$sample->kode_transaksi}, Customer: {$sample->nama_pelanggan}\n";
    }

    echo "\n=== Done ===\n";
});
