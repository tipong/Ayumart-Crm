<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestProfileUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-profile-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test profile update functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id_user = 60;

        $this->info('=== CURRENT USER DATA ===');
        $user = DB::table('users')->where('id_user', $id_user)->first();
        $this->line('Email: ' . $user->email);

        $pelanggan = DB::table('tb_pelanggan')->where('id_user', $id_user)->first();
        $this->line('Nama: ' . $pelanggan->nama_pelanggan);
        $this->line('Telepon: ' . $pelanggan->no_tlp_pelanggan);

        $this->info("\n=== TESTING UPDATE USERS TABLE ===");
        try {
            $result = DB::table('users')
                ->where('id_user', $id_user)
                ->update(['email' => 'test.update@example.com']);
            $this->info('✓ Updated ' . $result . ' row(s) in users table');

            $user = DB::table('users')->where('id_user', $id_user)->first();
            $this->line('New email: ' . $user->email);
        } catch (\Exception $e) {
            $this->error('✗ ERROR: ' . $e->getMessage());
        }

        $this->info("\n=== TESTING UPDATE TB_PELANGGAN TABLE ===");
        try {
            $result = DB::table('tb_pelanggan')
                ->where('id_user', $id_user)
                ->update([
                    'nama_pelanggan' => 'Updated Name Test',
                    'no_tlp_pelanggan' => '089123456789'
                ]);
            $this->info('✓ Updated ' . $result . ' row(s) in tb_pelanggan table');

            $pelanggan = DB::table('tb_pelanggan')->where('id_user', $id_user)->first();
            $this->line('New Nama: ' . $pelanggan->nama_pelanggan);
            $this->line('New Telepon: ' . $pelanggan->no_tlp_pelanggan);
        } catch (\Exception $e) {
            $this->error('✗ ERROR: ' . $e->getMessage());
        }

        $this->info("\n✓ ALL TESTS COMPLETED!");
        return 0;
    }
}
