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
        Schema::create('tb_pengiriman', function (Blueprint $table) {
            $table->id('id_pengiriman');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_staff')->nullable(); // kurir
            $table->string('no_resi', 50)->unique();
            $table->string('nama_penerima', 100);
            $table->text('alamat_penerima');
            $table->string('no_tlp_penerima', 20);
            $table->enum('status_pengiriman', ['pending', 'diambil', 'dikirim', 'sampai', 'gagal'])->default('pending');
            $table->dateTime('tgl_kirim')->nullable();
            $table->dateTime('tgl_sampai')->nullable();
            $table->text('catatan_pengiriman')->nullable();
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('tb_transaksi')->onDelete('cascade');
            $table->foreign('id_staff')->references('id_staff')->on('tb_staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pengiriman');
    }
};
