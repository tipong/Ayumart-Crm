<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom discount_target ke tb_produk
     * untuk menentukan apakah diskon berlaku untuk semua (general)
     * atau khusus per tier membership (tier).
     */
    public function up(): void
    {
        Schema::connection('mysql_integrasi')->table('tb_produk', function (Blueprint $table) {
            if (!Schema::connection('mysql_integrasi')->hasColumn('tb_produk', 'discount_target')) {
                // 'general' = berlaku untuk semua pelanggan
                // 'tier'    = hanya berlaku untuk tier tertentu (lihat tabel product_member_discounts)
                $table->enum('discount_target', ['general', 'tier'])
                      ->default('general')
                      ->nullable()
                      ->after('is_diskon_active')
                      ->comment('Target diskon: general (semua) atau tier (per tier member)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_integrasi')->table('tb_produk', function (Blueprint $table) {
            $table->dropColumn('discount_target');
        });
    }
};
