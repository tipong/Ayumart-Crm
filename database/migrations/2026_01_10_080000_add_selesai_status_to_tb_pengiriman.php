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
        // Add 'selesai' status to tb_pengiriman status_pengiriman enum
        DB::statement("ALTER TABLE tb_pengiriman MODIFY COLUMN status_pengiriman ENUM('pending', 'dikemas', 'siap_diambil', 'dalam_pengiriman', 'terkirim', 'selesai', 'gagal') DEFAULT 'pending'");

        // Add column for customer confirmation timestamp
        Schema::table('tb_pengiriman', function (Blueprint $table) {
            $table->timestamp('tgl_konfirmasi_pelanggan')->nullable()->after('tgl_sampai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove column
        Schema::table('tb_pengiriman', function (Blueprint $table) {
            $table->dropColumn('tgl_konfirmasi_pelanggan');
        });

        // Revert enum to previous values
        DB::statement("ALTER TABLE tb_pengiriman MODIFY COLUMN status_pengiriman ENUM('pending', 'dikemas', 'siap_diambil', 'dalam_pengiriman', 'terkirim', 'gagal') DEFAULT 'pending'");
    }
};
