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
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('tb_pembatalan_transaksi', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('status_pembatalan');
            }

            // Also add diproses_oleh if not exists
            if (!Schema::hasColumn('tb_pembatalan_transaksi', 'diproses_oleh')) {
                $table->unsignedBigInteger('diproses_oleh')->nullable()->after('catatan_admin');
                $table->foreign('diproses_oleh')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_pembatalan_transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('tb_pembatalan_transaksi', 'diproses_oleh')) {
                $table->dropForeign(['diproses_oleh']);
                $table->dropColumn('diproses_oleh');
            }

            if (Schema::hasColumn('tb_pembatalan_transaksi', 'catatan_admin')) {
                $table->dropColumn('catatan_admin');
            }
        });
    }
};
