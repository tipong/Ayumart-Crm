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
        Schema::table('tb_pembatalan_transaksi', function (Blueprint $table) {
            // Drop tgl_pembatalan column as we're using created_at instead
            $table->dropColumn('tgl_pembatalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_pembatalan_transaksi', function (Blueprint $table) {
            // Restore tgl_pembatalan column if needed
            $table->dateTime('tgl_pembatalan')->after('alasan_pembatalan');
        });
    }
};
