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
        Schema::create('tb_tracking_newsletter', function (Blueprint $table) {
            $table->id('id_tracking_newsletter');
            $table->unsignedBigInteger('id_pelanggan');
            $table->string('email_tujuan', 100);
            $table->text('konten_email');
            $table->string('subjek_email', 200);
            $table->dateTime('tanggal_kirim');
            $table->enum('status_kirim', ['terkirim', 'gagal', 'pending'])->default('pending');
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('tb_pelanggan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tracking_newsletter');
    }
};
