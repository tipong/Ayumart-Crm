<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix memberships.user_id type to match users.id_user (int instead of bigint unsigned)
     */
    public function up(): void
    {
        // Drop existing foreign key
        try {
            DB::statement('ALTER TABLE memberships DROP FOREIGN KEY memberships_user_id_foreign');
        } catch (\Exception $e) {
            // FK might not exist
        }

        // Change user_id type from bigint unsigned to int
        Schema::table('memberships', function (Blueprint $table) {
            $table->integer('user_id')->change();
        });

        // Re-create foreign key with correct reference
        try {
            DB::statement('ALTER TABLE memberships ADD CONSTRAINT memberships_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Constraint might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to bigint unsigned
        try {
            DB::statement('ALTER TABLE memberships DROP FOREIGN KEY memberships_user_id_foreign');
        } catch (\Exception $e) {}

        Schema::table('memberships', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->change();
        });
    }
};

