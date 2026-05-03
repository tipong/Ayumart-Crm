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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pelanggan');
            $table->string('label', 50); // Home, Office, etc
            $table->text('alamat_lengkap');
            $table->string('kota', 100);
            $table->string('kecamatan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('nama_penerima', 100);
            $table->string('no_telp_penerima', 20);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('tb_pelanggan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
