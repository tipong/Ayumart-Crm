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
        Schema::create('tb_newsletter', function (Blueprint $table) {
            $table->id('id_newsletter');
            $table->string('judul', 200);
            $table->string('subjek_email', 200);
            $table->text('konten_email'); // Plain text version
            $table->longText('konten_html')->nullable(); // HTML version
            $table->enum('status', ['draft', 'mengirim', 'terkirim', 'gagal'])->default('draft');
            $table->dateTime('tanggal_kirim')->nullable();
            $table->integer('total_penerima')->default(0);
            $table->integer('total_terkirim')->default(0);
            $table->integer('total_gagal')->default(0);
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->string('mailchimp_campaign_id')->nullable();
            $table->timestamps();

            $table->foreign('dibuat_oleh')->references('id')->on('users')->onDelete('set null');
        });

        // Update tb_tracking_newsletter to add newsletter reference
        Schema::table('tb_tracking_newsletter', function (Blueprint $table) {
            $table->unsignedBigInteger('id_newsletter')->nullable()->after('id_tracking_newsletter');
            $table->string('mailchimp_member_id')->nullable()->after('status_kirim');

            $table->foreign('id_newsletter')->references('id_newsletter')->on('tb_newsletter')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_tracking_newsletter', function (Blueprint $table) {
            $table->dropForeign(['id_newsletter']);
            $table->dropColumn(['id_newsletter', 'mailchimp_member_id']);
        });

        Schema::dropIfExists('tb_newsletter');
    }
};
