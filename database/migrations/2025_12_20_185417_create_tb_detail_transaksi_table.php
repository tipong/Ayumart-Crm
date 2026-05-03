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
        Schema::create('tb_detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail_transaksi');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_produk');
            $table->integer('qty')->default(1);
            $table->decimal('harga_item', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('diskon_item', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('tb_transaksi')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_detail_transaksi');
    }
};
