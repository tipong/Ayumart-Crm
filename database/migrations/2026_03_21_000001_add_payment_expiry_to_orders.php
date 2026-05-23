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
        if (!Schema::hasColumn('tb_transaksi', 'payment_expired_at')) {
            Schema::table('tb_transaksi', function (Blueprint $table) {
                $table->timestamp('payment_expired_at')->nullable()->after('catatan')->comment('Payment expiry time (15 minutes from order creation)');
                $table->timestamp('last_payment_check_at')->nullable()->after('payment_expired_at')->comment('Last time payment status was checked from Midtrans');

                // Add indexes
                $table->index('payment_expired_at');
            });

            try {
                Schema::table('tb_transaksi', function (Blueprint $table) {
                    $table->index('status_pembayaran');
                });
            } catch (\Exception $e) {
                // Index might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropIndex(['payment_expired_at']);
            $table->dropIndex(['status_pembayaran']);
            $table->dropColumn(['payment_expired_at', 'last_payment_check_at']);
        });
    }
};
