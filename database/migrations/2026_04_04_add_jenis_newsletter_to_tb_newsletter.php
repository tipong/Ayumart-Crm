<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_newsletter', function (Blueprint $table) {
            // Add jenis_newsletter column if it doesn't exist
            if (!Schema::hasColumn('tb_newsletter', 'jenis_newsletter')) {
                $table->enum('jenis_newsletter', [
                    'mailchimp',
                    'fonte',
                    'keduanya'
                ])->default('mailchimp')->after('subjek_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_newsletter', function (Blueprint $table) {
            if (Schema::hasColumn('tb_newsletter', 'jenis_newsletter')) {
                $table->dropColumn('jenis_newsletter');
            }
        });
    }
};
