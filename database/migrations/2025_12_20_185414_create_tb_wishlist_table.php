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
        Schema::create('tb_wishlist', function (Blueprint $table) {
            $table->id('id_wishlist');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_produk');
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('tb_pelanggan')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_wishlist');
    }
};
