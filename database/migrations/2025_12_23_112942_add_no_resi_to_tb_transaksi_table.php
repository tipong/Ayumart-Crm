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
        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Add tracking/resi number column if not exists
            if (!Schema::hasColumn('tb_transaksi', 'no_resi')) {
                $table->string('no_resi', 100)->nullable()->after('address_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('tb_transaksi', 'no_resi')) {
                $table->dropColumn('no_resi');
            }
        });
    }
};
