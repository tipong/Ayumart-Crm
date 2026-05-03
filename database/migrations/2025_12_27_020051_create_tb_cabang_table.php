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
        Schema::create('tb_cabang', function (Blueprint $table) {
            $table->id('id_cabang');
            $table->string('nama_cabang');
            $table->string('kode_cabang', 10)->unique();
            $table->text('alamat');
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kota');
            $table->string('provinsi')->default('Bali');
            $table->string('kode_pos', 10)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('google_maps_url')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->time('jam_buka')->default('08:00:00');
            $table->time('jam_tutup')->default('21:00:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_cabang');
    }
};
