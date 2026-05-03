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
        Schema::create('tb_review_produk', function (Blueprint $table) {
            $table->id('id_review_produk');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_transaksi');
            $table->integer('rating'); // 1-5
            $table->text('isi_review')->nullable();
            $table->string('foto_review')->nullable();
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('tb_pelanggan')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
            $table->foreign('id_transaksi')->references('id_transaksi')->on('tb_transaksi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_review_produk');
    }
};
