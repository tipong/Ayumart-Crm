<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restructure tb_pengiriman table to have: id_pengiriman, id_address, id_transaksi, id_staff, no_resi, nama_penerima, alamat_penerima, status_pengiriman, tgl_kirim, tgl_sampai
     */
    public function up(): void
    {
        // Backup existing data
        $existingShipments = DB::table('tb_pengiriman')->get();

        // Drop foreign key constraints
        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_transaksi_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_kurir_foreign');
        } catch (\Exception $e) {}

        // Drop the existing tb_pengiriman table
        Schema::dropIfExists('tb_pengiriman');

        // Create new tb_pengiriman table with simplified structure
        Schema::create('tb_pengiriman', function (Blueprint $table) {
            $table->integer('id_pengiriman', false, true)->length(11)->primary();
            $table->integer('id_address')->length(11)->nullable();
            $table->unsignedBigInteger('id_transaksi'); // bigint unsigned to match tb_transaksi
            $table->integer('id_staff')->length(11)->nullable(); // int to match tb_staff
            $table->string('no_resi', 50)->unique();
            $table->string('nama_penerima', 100);
            $table->text('alamat_penerima');
            $table->enum('status_pengiriman', [
                'pending',
                'dikemas',
                'siap_diambil',
                'dalam_pengiriman',
                'terkirim',
                'selesai',
                'gagal'
            ])->default('pending');
            $table->dateTime('tgl_kirim')->nullable();
            $table->dateTime('tgl_sampai')->nullable();

            // Add indexes for better performance
            $table->index('id_address');
            $table->index('id_transaksi');
            $table->index('id_staff');
            $table->index('status_pengiriman');
        });

        // Set auto increment
        DB::statement('ALTER TABLE tb_pengiriman MODIFY id_pengiriman INT(11) AUTO_INCREMENT');

        // Restore data with new structure
        foreach ($existingShipments as $shipment) {
            DB::table('tb_pengiriman')->insert([
                'id_pengiriman' => $shipment->id_pengiriman,
                'id_address' => null, // Will be populated later if needed
                'id_transaksi' => $shipment->id_transaksi,
                'id_staff' => $shipment->id_kurir ?? null, // Map id_kurir to id_staff
                'no_resi' => $shipment->no_resi,
                'nama_penerima' => $shipment->nama_penerima,
                'alamat_penerima' => $shipment->alamat_penerima,
                'status_pengiriman' => $shipment->status_pengiriman ?? 'pending',
                'tgl_kirim' => $shipment->tgl_kirim,
                'tgl_sampai' => $shipment->tgl_sampai,
            ]);
        }

        // Add foreign key constraints
        try {
            DB::statement('ALTER TABLE tb_pengiriman ADD CONSTRAINT tb_pengiriman_id_address_foreign FOREIGN KEY (id_address) REFERENCES customer_addresses(id) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // If customer_addresses doesn't have this structure, skip for now
        }

        try {
            DB::statement('ALTER TABLE tb_pengiriman ADD CONSTRAINT tb_pengiriman_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman ADD CONSTRAINT tb_pengiriman_id_staff_foreign FOREIGN KEY (id_staff) REFERENCES tb_staff(id_staff) ON DELETE SET NULL');
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints
        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_address_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_transaksi_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_staff_foreign');
        } catch (\Exception $e) {}

        // Backup data
        $existingShipments = DB::table('tb_pengiriman')->get();

        // Drop table
        Schema::dropIfExists('tb_pengiriman');

        // Recreate old structure
        Schema::create('tb_pengiriman', function (Blueprint $table) {
            $table->id('id_pengiriman');
            $table->unsignedBigInteger('id_pelanggan')->nullable();
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_kurir')->nullable();
            $table->string('no_resi', 50)->unique();
            $table->string('nama_penerima', 100);
            $table->text('alamat_penerima');
            $table->string('kota', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('no_tlp_penerima', 20);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status_pengiriman', ['pending', 'dikemas', 'siap_diambil', 'dalam_pengiriman', 'terkirim', 'selesai', 'gagal'])->default('pending');
            $table->dateTime('tgl_kirim')->nullable();
            $table->dateTime('tgl_sampai')->nullable();
            $table->timestamp('tgl_konfirmasi_pelanggan')->nullable();
            $table->text('catatan_pengiriman')->nullable();
            $table->timestamps();
        });

        // Restore data
        foreach ($existingShipments as $shipment) {
            DB::table('tb_pengiriman')->insert([
                'id_pengiriman' => $shipment->id_pengiriman,
                'id_transaksi' => $shipment->id_transaksi,
                'id_kurir' => $shipment->id_staff,
                'no_resi' => $shipment->no_resi,
                'nama_penerima' => $shipment->nama_penerima,
                'alamat_penerima' => $shipment->alamat_penerima,
                'status_pengiriman' => $shipment->status_pengiriman,
                'tgl_kirim' => $shipment->tgl_kirim,
                'tgl_sampai' => $shipment->tgl_sampai,
            ]);
        }

        // Recreate foreign keys
        try {
            DB::statement('ALTER TABLE tb_pengiriman ADD CONSTRAINT tb_pengiriman_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}
    }
};
