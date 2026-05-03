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
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cabang')->nullable()->after('id_pelanggan');
            $table->foreign('id_cabang')->references('id_cabang')->on('tb_cabang')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropForeign(['id_cabang']);
            $table->dropColumn('id_cabang');
        });
    }
};
