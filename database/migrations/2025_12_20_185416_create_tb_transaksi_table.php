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
        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->unsignedBigInteger('id_pelanggan');
            $table->string('kode_transaksi', 50)->unique();
            $table->dateTime('tgl_transaksi');
            $table->decimal('total_harga', 12, 2);
            $table->decimal('total_diskon', 12, 2)->default(0);
            $table->decimal('ongkir', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['belum_bayar', 'sudah_bayar', 'kadaluarsa'])->default('belum_bayar');
            $table->string('metode_pembayaran', 50)->nullable();
            $table->enum('status_pengiriman', ['dikemas', 'dikirim', 'sampai'])->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('tb_pelanggan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_transaksi');
    }
};
