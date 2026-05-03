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
            // Add column to store the original product ID from integrasi database
            $table->unsignedBigInteger('id_produk_integrasi')->nullable()->after('id_produk');
            $table->index('id_produk_integrasi'); // Add index for faster lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_produk', function (Blueprint $table) {
            $table->dropIndex(['id_produk_integrasi']);
            $table->dropColumn('id_produk_integrasi');
        });
    }
};
