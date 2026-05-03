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
            // Drop the existing unique constraint
            $table->dropUnique('tb_produk_kode_produk_unique');
        });

        Schema::table('tb_produk', function (Blueprint $table) {
            // Increase kode_produk length from varchar(10) to varchar(50) to accommodate longer product codes
            $table->string('kode_produk', 50)->change();
        });

        Schema::table('tb_produk', function (Blueprint $table) {
            // Add the unique constraint back
            $table->unique('kode_produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_produk', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('tb_produk_kode_produk_unique');
        });

        Schema::table('tb_produk', function (Blueprint $table) {
            // Revert back to varchar(10)
            $table->string('kode_produk', 10)->change();
        });

        Schema::table('tb_produk', function (Blueprint $table) {
            // Add the unique constraint back
            $table->unique('kode_produk');
        });
    }
};
