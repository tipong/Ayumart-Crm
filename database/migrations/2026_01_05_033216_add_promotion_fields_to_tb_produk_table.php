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
        Schema::table('tb_produk', function (Blueprint $table) {
            // Kolom untuk diskon/promosi
            $table->decimal('harga_diskon', 12, 2)->nullable()->after('harga_produk');
            $table->decimal('persentase_diskon', 5, 2)->nullable()->after('harga_diskon');
            $table->date('tanggal_mulai_diskon')->nullable()->after('persentase_diskon');
            $table->date('tanggal_akhir_diskon')->nullable()->after('tanggal_mulai_diskon');
            $table->boolean('is_diskon_active')->default(false)->after('tanggal_akhir_diskon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_produk', function (Blueprint $table) {
            $table->dropColumn([
                'harga_diskon',
                'persentase_diskon',
                'tanggal_mulai_diskon',
                'tanggal_akhir_diskon',
                'is_diskon_active'
            ]);
        });
    }
};
