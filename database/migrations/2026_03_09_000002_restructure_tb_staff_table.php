<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restructure tb_staff table to have: id_staff, id_user, nama_staff, email_staff, posisi_staff, profil_staff, no_tlp_staff, status_akun
     */
    public function up(): void
    {
        // Backup existing data
        $existingStaff = DB::table('tb_staff')->get();

        // Drop foreign key constraints first
        try {
            DB::statement('ALTER TABLE tb_staff DROP FOREIGN KEY tb_staff_user_id_foreign');
        } catch (\Exception $e) {}

        // Drop the existing tb_staff table
        Schema::dropIfExists('tb_staff');

        // Create new tb_staff table with simplified structure
        Schema::create('tb_staff', function (Blueprint $table) {
            $table->integer('id_staff', false, true)->length(11)->primary();
            $table->integer('id_user', false, true)->length(11);
            $table->string('nama_staff', 100);
            $table->string('email_staff', 100);
            $table->string('posisi_staff', 50);
            $table->text('profil_staff')->nullable();
            $table->string('no_tlp_staff', 20)->nullable();
            $table->enum('status_akun', ['aktif', 'nonaktif'])->default('aktif');

            // Add indexes for better performance
            $table->index('id_user');
            $table->index('email_staff');
            $table->index('status_akun');
        });

        // Set auto increment
        DB::statement('ALTER TABLE tb_staff MODIFY id_staff INT(11) AUTO_INCREMENT');

        // Restore data with new structure
        foreach ($existingStaff as $staff) {
            // Get email from users table
            $user = DB::table('users')->where('id_user', $staff->user_id)->first();
            $email = $user ? $user->email : 'staff' . $staff->id_staff . '@crm.com';

            DB::table('tb_staff')->insert([
                'id_staff' => $staff->id_staff,
                'id_user' => $staff->user_id,
                'nama_staff' => $staff->nama_staff,
                'email_staff' => $email,
                'posisi_staff' => $staff->posisi_staff,
                'profil_staff' => $staff->profil_staff ?? null,
                'no_tlp_staff' => $staff->no_tlp_staff ?? null,
                'status_akun' => $staff->status_akun ?? 'aktif',
            ]);
        }

        // Recreate foreign key
        try {
            DB::statement('ALTER TABLE tb_staff ADD CONSTRAINT tb_staff_id_user_foreign FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backup existing data
        $existingStaff = DB::table('tb_staff')->get();

        // Drop foreign key
        try {
            DB::statement('ALTER TABLE tb_staff DROP FOREIGN KEY tb_staff_id_user_foreign');
        } catch (\Exception $e) {}

        // Drop the restructured table
        Schema::dropIfExists('tb_staff');

        // Restore original structure
        Schema::create('tb_staff', function (Blueprint $table) {
            $table->id('id_staff');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_staff', 100);
            $table->string('posisi_staff', 100);
            $table->text('profil_staff')->nullable();
            $table->string('no_tlp_staff', 20)->nullable();
            $table->enum('status_akun', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // Restore data
        foreach ($existingStaff as $staff) {
            DB::table('tb_staff')->insert([
                'id_staff' => $staff->id_staff,
                'user_id' => $staff->id_user,
                'nama_staff' => $staff->nama_staff,
                'posisi_staff' => $staff->posisi_staff,
                'profil_staff' => $staff->profil_staff,
                'no_tlp_staff' => $staff->no_tlp_staff,
                'status_akun' => $staff->status_akun,
            ]);
        }

        // Recreate foreign key
        try {
            DB::statement('ALTER TABLE tb_staff ADD CONSTRAINT tb_staff_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}
    }
};
