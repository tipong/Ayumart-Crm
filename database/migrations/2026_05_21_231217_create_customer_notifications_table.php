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
        Schema::create('customer_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');           // FK ke users.id_user
            $table->string('type', 50);                      // order_placed|payment_confirmed|payment_expired|dikemas|dikirim|sampai|selesai|siap_diambil|cancelled|cancellation_approved|cancellation_rejected|ticket_reply|ticket_resolved
            $table->string('title', 255);
            $table->text('message');
            $table->string('icon', 60)->default('bi-bell');
            $table->string('color', 30)->default('primary'); // success|warning|danger|info|primary
            $table->string('url')->nullable();
            $table->string('reference_type', 50)->nullable(); // order|ticket|cancellation
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_notifications');
    }
};
