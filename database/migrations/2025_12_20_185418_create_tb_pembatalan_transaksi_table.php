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
        Schema::create('tb_pembatalan_transaksi', function (Blueprint $table) {
            $table->id('id_pembatalan_transaksi');
            $table->unsignedBigInteger('id_transaksi');
            $table->text('alasan_pembatalan');
            $table->dateTime('tgl_pembatalan');
            $table->enum('status_pembatalan', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('tb_transaksi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pembatalan_transaksi');
    }
};
