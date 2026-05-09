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
        Schema::table('tb_newsletter', function (Blueprint $table) {
            // Add metode_pengiriman column if it doesn't exist
            if (!Schema::hasColumn('tb_newsletter', 'metode_pengiriman')) {
                $table->enum('metode_pengiriman', ['mailchimp', 'fonnte', 'keduanya'])
                    ->default('mailchimp')
                    ->after('konten_html')
                    ->comment('Metode pengiriman newsletter: mailchimp, fonnte, atau keduanya');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_newsletter', function (Blueprint $table) {
            if (Schema::hasColumn('tb_newsletter', 'metode_pengiriman')) {
                $table->dropColumn('metode_pengiriman');
            }
        });
    }
};
