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
        Schema::create('tb_staff', function (Blueprint $table) {
            $table->id('id_staff');
            $table->string('nama_staff', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->string('posisi_staff', 100); // admin, cs, kurir, owner
            $table->text('profil_staff')->nullable();
            $table->string('no_tlp_staff', 20)->nullable();
            $table->enum('status_akun', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_staff');
    }
};
