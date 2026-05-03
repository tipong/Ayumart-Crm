<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove unused id_staff column - replaced by id_kurir
     */
    public function up(): void
    {
        Schema::table('tb_pengiriman', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['id_staff']);

            // Drop the column
            $table->dropColumn('id_staff');
        });
    }

    /**
     * Reverse the migrations.
     * Restore id_staff column if needed for rollback
     */
    public function down(): void
    {
        Schema::table('tb_pengiriman', function (Blueprint $table) {
            // Restore the column
            $table->unsignedBigInteger('id_staff')->nullable()->after('id_transaksi');

            // Restore foreign key
            $table->foreign('id_staff')->references('id_staff')->on('tb_staff')->onDelete('set null');
        });
    }
};
