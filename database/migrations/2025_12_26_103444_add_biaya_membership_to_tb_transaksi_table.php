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
            $table->decimal('biaya_membership', 12, 2)->default(0)->after('ongkir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropColumn('biaya_membership');
        });
    }
};
