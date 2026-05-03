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
        Schema::create('tb_review', function (Blueprint $table) {
            $table->id('id_review');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_transaksi');
            $table->integer('rating')->comment('Rating 1-5 bintang');
            $table->text('review')->nullable();
            $table->string('foto_review', 255)->nullable()->comment('Path foto review (optional)');
            $table->boolean('is_verified')->default(true)->comment('True jika sudah menerima barang');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('tb_pelanggan')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('tb_produk')->onDelete('cascade');
            $table->foreign('id_transaksi')->references('id_transaksi')->on('tb_transaksi')->onDelete('cascade');

            // Indexes
            $table->index('id_pelanggan');
            $table->index('id_produk');
            $table->index('id_transaksi');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_review');
    }
};
