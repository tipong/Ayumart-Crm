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
        // Use integrasi database connection
        Schema::connection('mysql_integrasi')->table('tb_produk', function (Blueprint $table) {
            // Check and add columns if they don't exist
            if (!Schema::connection('mysql_integrasi')->hasColumn('tb_produk', 'persentase_diskon')) {
                $table->decimal('persentase_diskon', 5, 2)->nullable()->after('harga_diskon');
            }

            if (!Schema::connection('mysql_integrasi')->hasColumn('tb_produk', 'tanggal_mulai_diskon')) {
                $table->date('tanggal_mulai_diskon')->nullable()->after('persentase_diskon');
            }

            if (!Schema::connection('mysql_integrasi')->hasColumn('tb_produk', 'tanggal_akhir_diskon')) {
                $table->date('tanggal_akhir_diskon')->nullable()->after('tanggal_mulai_diskon');
            }

            if (!Schema::connection('mysql_integrasi')->hasColumn('tb_produk', 'is_diskon_active')) {
                $table->boolean('is_diskon_active')->default(false)->after('tanggal_akhir_diskon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_integrasi')->table('tb_produk', function (Blueprint $table) {
            $table->dropColumn([
                'persentase_diskon',
                'tanggal_mulai_diskon',
                'tanggal_akhir_diskon',
                'is_diskon_active'
            ]);
        });
    }
};
