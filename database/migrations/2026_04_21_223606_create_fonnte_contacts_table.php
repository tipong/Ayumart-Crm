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
        Schema::create('fonnte_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable()->comment('Nama kontak');
            $table->string('name')->nullable()->comment('Nama kontak (alias)');
            $table->string('phone')->unique()->comment('Nomor telepon');
            $table->string('email')->nullable()->unique()->comment('Email');
            $table->json('variable')->nullable()->comment('Custom fields');
            $table->string('status')->default('active')->comment('Status kontak');
            $table->timestamps();

            // Indexes untuk performa query
            $table->index('phone');
            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fonnte_contacts');
    }
};
