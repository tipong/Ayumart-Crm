<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restructure users table to only have: id_user, id_role, email, password
     */
    public function up(): void
    {
        // Backup existing data
        $existingUsers = DB::table('users')->get();

        // Drop all foreign key constraints that reference users table
        // Use raw SQL to avoid errors if constraint doesn't exist

        try {
            DB::statement('ALTER TABLE tickets DROP FOREIGN KEY tickets_user_id_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tickets DROP FOREIGN KEY tickets_assigned_to_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE ticket_messages DROP FOREIGN KEY ticket_messages_user_id_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_staff DROP FOREIGN KEY tb_staff_user_id_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE memberships DROP FOREIGN KEY memberships_user_id_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_newsletter DROP FOREIGN KEY tb_newsletter_dibuat_oleh_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pelanggan DROP FOREIGN KEY tb_pelanggan_user_id_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pembatalan_transaksi DROP FOREIGN KEY tb_pembatalan_transaksi_diproses_oleh_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_kurir_foreign');
        } catch (\Exception $e) {}

        // Drop the existing users table
        Schema::dropIfExists('users');

        // Create new users table with simplified structure
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id_user', false, true)->length(11)->primary();
            $table->integer('id_role', false, true)->length(11);
            $table->string('email', 191)->unique();
            $table->string('password', 255);

            // Add index for better performance
            $table->index('id_role');
            $table->index('email');
        });

        // Set auto increment
        DB::statement('ALTER TABLE users MODIFY id_user INT(11) AUTO_INCREMENT');

        // Restore data with new structure (only keep id, role_id, email, password)
        foreach ($existingUsers as $user) {
            DB::table('users')->insert([
                'id_user' => $user->id,
                'id_role' => $user->role_id,
                'email' => $user->email,
                'password' => $user->password,
            ]);
        }

        // Recreate foreign keys with new id_user column
        try {
            DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_assigned_to_foreign FOREIGN KEY (assigned_to) REFERENCES users(id_user) ON DELETE SET NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE ticket_messages ADD CONSTRAINT ticket_messages_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_staff ADD CONSTRAINT tb_staff_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE memberships ADD CONSTRAINT memberships_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_newsletter ADD CONSTRAINT tb_newsletter_dibuat_oleh_foreign FOREIGN KEY (dibuat_oleh) REFERENCES users(id_user) ON DELETE SET NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pelanggan ADD CONSTRAINT tb_pelanggan_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pembatalan_transaksi ADD CONSTRAINT tb_pembatalan_transaksi_diproses_oleh_foreign FOREIGN KEY (diproses_oleh) REFERENCES users(id_user) ON DELETE SET NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman ADD CONSTRAINT tb_pengiriman_id_kurir_foreign FOREIGN KEY (id_kurir) REFERENCES users(id_user) ON DELETE SET NULL');
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backup existing data
        $existingUsers = DB::table('users')->get();

        // Drop the restructured table
        Schema::dropIfExists('users');

        // Restore original structure
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('profile_photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Restore data
        foreach ($existingUsers as $user) {
            DB::table('users')->insert([
                'id' => $user->id_user,
                'role_id' => $user->id_role,
                'email' => $user->email,
                'password' => $user->password,
                'name' => '',
                'is_active' => true,
            ]);
        }
    }
};
