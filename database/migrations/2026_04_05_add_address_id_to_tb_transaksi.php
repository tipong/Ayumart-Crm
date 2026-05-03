<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migration.
     * Add address_id to tb_transaksi to store customer's delivery address for kurir method
     */
    public function up(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Add address_id column to store reference to customer's delivery address
            // This enables reliable shipping data creation after payment success
            if (!Schema::hasColumn('tb_transaksi', 'address_id')) {
                $table->unsignedBigInteger('address_id')->nullable()->after('id_cabang');

                // Add foreign key constraint
                $table->foreign('address_id')
                    ->references('id')
                    ->on('customer_addresses')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Drop foreign key if exists
            try {
                $table->dropForeign(['address_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }

            // Drop column if exists
            if (Schema::hasColumn('tb_transaksi', 'address_id')) {
                $table->dropColumn('address_id');
            }
        });
    }
};
