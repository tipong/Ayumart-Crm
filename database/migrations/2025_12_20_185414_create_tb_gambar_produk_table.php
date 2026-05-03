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
        Schema::create('tb_gambar_produk', function (Blueprint $table) {
            $table->id('id_gambar_produk');
            $table->unsignedBigInteger('id_produk');
            $table->string('nama_gambar', 100);
            $table->string('url_gambar', 255);
            $table->enum('urutan', ['1', '2', '3', '4', '5'])->default('1');
            $table->timestamps();

            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_gambar_produk');
    }
};
