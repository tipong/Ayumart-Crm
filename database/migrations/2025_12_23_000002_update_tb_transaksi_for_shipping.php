<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Add shipping method column
            $table->enum('metode_pengiriman', ['kurir', 'ambil_sendiri'])->default('kurir')->after('alamat_pengiriman');

            // Add address ID reference
            $table->unsignedBigInteger('address_id')->nullable()->after('metode_pengiriman');

            // Add tracking/resi number
            $table->string('no_resi', 100)->nullable()->after('address_id');

            // Add Midtrans payment fields
            $table->string('snap_token', 255)->nullable()->after('metode_pembayaran');
            $table->string('payment_type', 50)->nullable()->after('snap_token');
            $table->dateTime('payment_date')->nullable()->after('payment_type');

            // Update status_pengiriman to include pickup option
            $table->dropColumn('status_pengiriman');
        });

        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->enum('status_pengiriman', ['pending', 'dikemas', 'dikirim', 'sampai', 'siap_diambil', 'selesai'])->default('pending')->after('metode_pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropColumn(['metode_pengiriman', 'address_id', 'no_resi', 'snap_token', 'payment_type', 'payment_date']);
            $table->dropColumn('status_pengiriman');
        });

        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->enum('status_pengiriman', ['dikemas', 'dikirim', 'sampai'])->nullable();
        });
    }
};
