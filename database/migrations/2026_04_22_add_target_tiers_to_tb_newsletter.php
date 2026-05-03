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
            // Add target_tiers column to store membership tier filters as JSON
            if (!Schema::hasColumn('tb_newsletter', 'target_tiers')) {
                $table->json('target_tiers')->nullable()->after('mailchimp_campaign_id')->comment('JSON array of target membership tiers (bronze, silver, gold, platinum)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_newsletter', function (Blueprint $table) {
            if (Schema::hasColumn('tb_newsletter', 'target_tiers')) {
                $table->dropColumn('target_tiers');
            }
        });
    }
};
