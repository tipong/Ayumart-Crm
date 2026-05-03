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
        Schema::create('fonte_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('group')->nullable();
            $table->json('variable')->nullable();
            $table->string('fonte_id')->nullable()->unique();
            $table->timestamps();
            $table->index(['phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fonte_contacts');
    }
};
