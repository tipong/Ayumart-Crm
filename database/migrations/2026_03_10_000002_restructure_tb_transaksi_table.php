<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restructure tb_transaksi table to have: id_transaksi, id_pelanggan, id_cabang, kode_transaksi, midtrans_order_id, snap_token, total_harga, total_diskon, ongkir, biaya_membership, status_pembayaran, status_pengiriman, metode_pengiriman, catatan
     */
    public function up(): void
    {
        // Backup existing data
        $existingTransactions = DB::table('tb_transaksi')->get();

        // Drop foreign key constraints
        try {
            DB::statement('ALTER TABLE tb_transaksi DROP FOREIGN KEY tb_transaksi_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_transaksi DROP FOREIGN KEY tb_transaksi_id_cabang_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_transaksi DROP FOREIGN KEY tb_transaksi_address_id_foreign');
        } catch (\Exception $e) {}

        // Drop foreign keys from other tables that reference tb_transaksi
        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_transaksi_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_detail_transaksi DROP FOREIGN KEY tb_detail_transaksi_id_transaksi_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pembatalan_transaksi DROP FOREIGN KEY tb_pembatalan_transaksi_id_transaksi_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_review DROP FOREIGN KEY tb_review_id_transaksi_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_review_produk DROP FOREIGN KEY tb_review_produk_id_transaksi_foreign');
        } catch (\Exception $e) {}

        // Drop the existing tb_transaksi table
        Schema::dropIfExists('tb_transaksi');

        // Create new tb_transaksi table with simplified structure
        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->integer('id_transaksi', false, true)->length(11)->primary();
            $table->integer('id_pelanggan')->length(11); // int to match tb_pelanggan
            $table->unsignedBigInteger('id_cabang')->nullable(); // bigint unsigned to match tb_cabang
            $table->string('kode_transaksi', 50)->unique();
            $table->string('midtrans_order_id', 100)->nullable();
            $table->string('snap_token', 200)->nullable();
            $table->decimal('total_harga', 12, 2);
            $table->decimal('total_diskon', 12, 2)->default(0);
            $table->string('ongkir', 50)->default('0');
            $table->decimal('biaya_membership', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['belum_bayar', 'sudah_bayar', 'kadaluarsa'])->default('belum_bayar');
            $table->enum('status_pengiriman', ['pending', 'dikemas', 'dikirim', 'sampai', 'siap_diambil', 'selesai'])->default('pending');
            $table->enum('metode_pengiriman', ['kurir', 'ambil_sendiri'])->default('kurir');
            $table->text('catatan')->nullable();

            // Add indexes for better performance
            $table->index('id_pelanggan');
            $table->index('id_cabang');
            $table->index('status_pembayaran');
            $table->index('status_pengiriman');
        });

        // Set auto increment
        DB::statement('ALTER TABLE tb_transaksi MODIFY id_transaksi INT(11) AUTO_INCREMENT');

        // Restore data with new structure
        foreach ($existingTransactions as $transaction) {
            DB::table('tb_transaksi')->insert([
                'id_transaksi' => $transaction->id_transaksi,
                'id_pelanggan' => $transaction->id_pelanggan,
                'id_cabang' => $transaction->id_cabang,
                'kode_transaksi' => $transaction->kode_transaksi,
                'midtrans_order_id' => $transaction->midtrans_order_id ?? null,
                'snap_token' => $transaction->snap_token ?? null,
                'total_harga' => $transaction->total_harga,
                'total_diskon' => $transaction->total_diskon ?? 0,
                'ongkir' => (string)($transaction->ongkir ?? '0'),
                'biaya_membership' => $transaction->biaya_membership ?? 0,
                'status_pembayaran' => $transaction->status_pembayaran ?? 'belum_bayar',
                'status_pengiriman' => $transaction->status_pengiriman ?? 'pending',
                'metode_pengiriman' => $transaction->metode_pengiriman ?? 'kurir',
                'catatan' => $transaction->catatan ?? null,
            ]);
        }

        // Add foreign key constraints
        try {
            DB::statement('ALTER TABLE tb_transaksi ADD CONSTRAINT tb_transaksi_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_transaksi ADD CONSTRAINT tb_transaksi_id_cabang_foreign FOREIGN KEY (id_cabang) REFERENCES tb_cabang(id_cabang) ON DELETE SET NULL');
        } catch (\Exception $e) {}

        // Recreate foreign keys from other tables
        try {
            DB::statement('ALTER TABLE tb_pengiriman ADD CONSTRAINT tb_pengiriman_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_detail_transaksi ADD CONSTRAINT tb_detail_transaksi_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pembatalan_transaksi ADD CONSTRAINT tb_pembatalan_transaksi_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_review ADD CONSTRAINT tb_review_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_review_produk ADD CONSTRAINT tb_review_produk_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints
        try {
            DB::statement('ALTER TABLE tb_transaksi DROP FOREIGN KEY tb_transaksi_id_pelanggan_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_transaksi DROP FOREIGN KEY tb_transaksi_id_cabang_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman DROP FOREIGN KEY tb_pengiriman_id_transaksi_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_detail_transaksi DROP FOREIGN KEY tb_detail_transaksi_id_transaksi_foreign');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pembatalan_transaksi DROP FOREIGN KEY tb_pembatalan_transaksi_id_transaksi_foreign');
        } catch (\Exception $e) {}

        // Backup data
        $existingTransactions = DB::table('tb_transaksi')->get();

        // Drop table
        Schema::dropIfExists('tb_transaksi');

        // Recreate old structure
        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_cabang')->nullable();
            $table->string('kode_transaksi', 50)->unique();
            $table->string('midtrans_order_id', 100)->nullable();
            $table->dateTime('tgl_transaksi');
            $table->timestamp('payment_expired_at')->nullable();
            $table->decimal('total_harga', 12, 2);
            $table->decimal('total_diskon', 12, 2)->default(0);
            $table->decimal('ongkir', 12, 2)->default(0);
            $table->decimal('biaya_membership', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['belum_bayar', 'sudah_bayar', 'kadaluarsa'])->default('belum_bayar');
            $table->string('metode_pembayaran', 50)->nullable();
            $table->string('snap_token', 255)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->enum('metode_pengiriman', ['kurir', 'ambil_sendiri'])->default('kurir');
            $table->enum('status_pengiriman', ['pending', 'dikemas', 'dikirim', 'sampai', 'siap_diambil', 'selesai'])->default('pending');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Restore data
        foreach ($existingTransactions as $transaction) {
            DB::table('tb_transaksi')->insert([
                'id_transaksi' => $transaction->id_transaksi,
                'id_pelanggan' => $transaction->id_pelanggan,
                'id_cabang' => $transaction->id_cabang,
                'kode_transaksi' => $transaction->kode_transaksi,
                'midtrans_order_id' => $transaction->midtrans_order_id,
                'tgl_transaksi' => now(),
                'total_harga' => $transaction->total_harga,
                'total_diskon' => $transaction->total_diskon,
                'ongkir' => $transaction->ongkir,
                'biaya_membership' => $transaction->biaya_membership,
                'status_pembayaran' => $transaction->status_pembayaran,
                'status_pengiriman' => $transaction->status_pengiriman,
                'metode_pengiriman' => $transaction->metode_pengiriman,
                'snap_token' => $transaction->snap_token,
                'catatan' => $transaction->catatan,
            ]);
        }

        // Recreate foreign keys
        try {
            DB::statement('ALTER TABLE tb_transaksi ADD CONSTRAINT tb_transaksi_id_pelanggan_foreign FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_transaksi ADD CONSTRAINT tb_transaksi_id_cabang_foreign FOREIGN KEY (id_cabang) REFERENCES tb_cabang(id_cabang) ON DELETE SET NULL');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pengiriman ADD CONSTRAINT tb_pengiriman_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_detail_transaksi ADD CONSTRAINT tb_detail_transaksi_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE tb_pembatalan_transaksi ADD CONSTRAINT tb_pembatalan_transaksi_id_transaksi_foreign FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE');
        } catch (\Exception $e) {}
    }
};
