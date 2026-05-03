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
        Schema::create('tb_pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->string('nama_pelanggan', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->text('profil_pelanggan')->nullable();
            $table->string('no_tlp_pelanggan', 20)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->enum('status_pelanggan', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pelanggan');
    }
};
