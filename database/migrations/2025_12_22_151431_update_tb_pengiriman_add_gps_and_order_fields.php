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
        Schema::table('tb_pengiriman', function (Blueprint $table) {
            // Add order code field
            $table->string('kode_transaksi', 100)->nullable()->after('id_pengiriman');

            // Add pelanggan ID
            $table->unsignedBigInteger('id_pelanggan')->nullable()->after('kode_transaksi');

            // Add GPS coordinates
            $table->decimal('latitude', 10, 8)->nullable()->after('no_tlp_penerima');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');

            // Add city, district, postal code
            $table->string('kota', 100)->nullable()->after('alamat_penerima');
            $table->string('kecamatan', 100)->nullable()->after('kota');
            $table->string('kode_pos', 10)->nullable()->after('kecamatan');

            // Modify status enum to match our system
            DB::statement("ALTER TABLE tb_pengiriman MODIFY COLUMN status_pengiriman ENUM('pending', 'dikemas', 'siap_diambil', 'dalam_pengiriman', 'terkirim', 'gagal') DEFAULT 'pending'");

            // Add kurir ID (rename from id_staff)
            $table->unsignedBigInteger('id_kurir')->nullable()->after('id_staff');

            // Add delivery date
            $table->date('tanggal_pengiriman')->nullable()->after('tgl_sampai');

            // Add foreign keys if not exists
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('tb_pelanggan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_pengiriman', function (Blueprint $table) {
            $table->dropForeign(['id_pelanggan']);
            $table->dropColumn([
                'kode_transaksi',
                'id_pelanggan',
                'latitude',
                'longitude',
                'kota',
                'kecamatan',
                'kode_pos',
                'id_kurir',
                'tanggal_pengiriman'
            ]);
        });
    }
};
