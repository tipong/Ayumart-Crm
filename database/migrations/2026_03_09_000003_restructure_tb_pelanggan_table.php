<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restructure tb_pelanggan table to have: id_pelanggan, id_user, nama_pelanggan, no_tlp_pelanggan, alamat, status_pelanggan
     */
    public function up(): void
    {
        // Backup existing data
        $existingPelanggan = DB::table('tb_pelanggan')->get();

        // Drop all foreign key constraints that reference tb_pelanggan
        try {
            DB::statement('ALTER TABLE tb_pelanggan DROP FOREIGN KEY tb_pelanggan_user_id_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE customer_addresses DROP FOREIGN KEY customer_addresses_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_detail_cart DROP FOREIGN KEY tb_detail_cart_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_review DROP FOREIGN KEY tb_review_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_review_produk DROP FOREIGN KEY tb_review_produk_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_tracking_newsletter DROP FOREIGN KEY tb_tracking_newsletter_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_transaksi DROP FOREIGN KEY tb_transaksi_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_wishlist DROP FOREIGN KEY tb_wishlist_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        // Drop the existing tb_pelanggan table
        Schema::dropIfExists('tb_pelanggan');

        // Create new tb_pelanggan table with simplified structure
        Schema::create('tb_pelanggan', function (Blueprint $table) {
            $table->integer('id_pelanggan', false, true)->length(11)->primary();
            $table->integer('id_user')->length(11)->nullable(); // Changed to match users.id_user type (non-unsigned)
            $table->string('nama_pelanggan', 100);
            $table->string('no_tlp_pelanggan', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status_pelanggan', ['aktif', 'nonaktif'])->default('aktif');

            // Add indexes for better performance
            $table->index('id_user');
            $table->index('status_pelanggan');
        });

        // Set auto increment
        DB::statement('ALTER TABLE tb_pelanggan MODIFY id_pelanggan INT(11) AUTO_INCREMENT');

        // Restore data with new structure
        foreach ($existingPelanggan as $pelanggan) {
            DB::table('tb_pelanggan')->insert([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_user' => $pelanggan->user_id ?? null,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
                'no_tlp_pelanggan' => $pelanggan->no_tlp_pelanggan ?? null,
                'alamat' => $pelanggan->alamat ?? null,
                'status_pelanggan' => $pelanggan->status_pelanggan ?? 'aktif',
            ]);
        }

        // Recreate foreign keys
        try {
            DB::statement('ALTER TABLE tb_pelanggan ADD CONSTRAINT tb_pelanggan_id_user_foreign FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE customer_addresses ADD CONSTRAINT customer_addresses_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_detail_cart ADD CONSTRAINT tb_detail_cart_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman ADD CONSTRAINT tb_pengiriman_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_review ADD CONSTRAINT tb_review_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_review_produk ADD CONSTRAINT tb_review_produk_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_tracking_newsletter ADD CONSTRAINT tb_tracking_newsletter_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE SET NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_transaksi ADD CONSTRAINT tb_transaksi_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_wishlist ADD CONSTRAINT tb_wishlist_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backup existing data
        $existingPelanggan = DB::table('tb_pelanggan')->get();

        // Drop foreign key
        try {
            DB::statement('ALTER TABLE tb_pelanggan DROP FOREIGN KEY tb_pelanggan_id_user_foreign');
        } catch (\Exception $e) {}

        // Drop the restructured table
        Schema::dropIfExists('tb_pelanggan');

        // Restore original structure
        Schema::create('tb_pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_pelanggan', 100);
            $table->text('profil_pelanggan')->nullable();
            $table->string('no_tlp_pelanggan', 20)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->enum('status_pelanggan', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // Restore data
        foreach ($existingPelanggan as $pelanggan) {
            DB::table('tb_pelanggan')->insert([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'user_id' => $pelanggan->id_user,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
                'no_tlp_pelanggan' => $pelanggan->no_tlp_pelanggan,
                'alamat' => $pelanggan->alamat,
                'status_pelanggan' => $pelanggan->status_pelanggan,
            ]);
        }

        // Recreate foreign key
        try {
            DB::statement('ALTER TABLE tb_pelanggan ADD CONSTRAINT tb_pelanggan_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE');
        } catch (\Exception $e) {}
    }
};
