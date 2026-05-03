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
        Schema::create('tb_kategori_produk', function (Blueprint $table) {
            $table->id('id_kategori_produk');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_jenis');
            $table->timestamps();

            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
            $table->foreign('id_jenis')->references('id_jenis')->on('tb_jenis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kategori_produk');
    }
};
