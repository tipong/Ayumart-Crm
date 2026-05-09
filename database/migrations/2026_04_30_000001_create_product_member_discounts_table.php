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
        Schema::create('product_member_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // ID dari tb_produk
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum']);
            $table->decimal('discount_percentage', 5, 2); // Diskon untuk tier ini
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key ke tb_produk dihapus karena tabel tb_produk berada di database berbeda (mysql_integrasi)
            
            // Pastikan 1 produk hanya memiliki 1 diskon per tier
            $table->unique(['product_id', 'tier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_member_discounts');
    }
};
