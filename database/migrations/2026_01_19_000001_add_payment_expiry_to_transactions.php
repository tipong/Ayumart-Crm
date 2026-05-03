<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Add payment expiry timestamp
            $table->timestamp('payment_expired_at')->nullable()->after('tgl_transaksi');

            // Add index for faster queries
            $table->index(['status_pembayaran', 'payment_expired_at'], 'idx_payment_status_expiry');
        });

        // Set expiry for existing pending transactions (10 minutes from creation)
        DB::statement("
            UPDATE tb_transaksi
            SET payment_expired_at = DATE_ADD(tgl_transaksi, INTERVAL 10 MINUTE)
            WHERE status_pembayaran = 'belum_bayar'
            AND payment_expired_at IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropIndex('idx_payment_status_expiry');
            $table->dropColumn('payment_expired_at');
        });
    }
};
