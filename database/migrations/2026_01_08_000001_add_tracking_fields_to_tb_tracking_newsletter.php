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
        Schema::table('tb_tracking_newsletter', function (Blueprint $table) {
            // Add fields for tracking email opens and clicks
            $table->dateTime('waktu_dibuka')->nullable()->after('status_kirim');
            $table->dateTime('waktu_klik')->nullable()->after('waktu_dibuka');
            $table->integer('jumlah_dibuka')->default(0)->after('waktu_klik');
            $table->integer('jumlah_klik')->default(0)->after('jumlah_dibuka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_tracking_newsletter', function (Blueprint $table) {
            $table->dropColumn(['waktu_dibuka', 'waktu_klik', 'jumlah_dibuka', 'jumlah_klik']);
        });
    }
};
