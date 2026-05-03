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
        Schema::create('tb_produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('kode_produk', 10)->unique();
            $table->string('nama_produk', 100);
            $table->text('deskripsi_produk')->nullable();
            $table->decimal('harga_produk', 12, 2);
            $table->decimal('diskon_produk', 12, 2)->default(0);
            $table->integer('stok_produk')->default(0);
            $table->decimal('berat_produk', 8, 2)->nullable(); // dalam kg
            $table->string('foto_produk')->nullable();
            $table->enum('status_produk', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_produk');
    }
};
