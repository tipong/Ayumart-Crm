<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncJenisFromIntegrasi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integrasi:sync-jenis
                            {--force : Force sync all jenis even if they exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all jenis (categories) from integrasi database to CRM database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting jenis sync from integrasi database...');

        try {
            // Get all jenis from integrasi database
            $jenisIntegrasi = DB::connection('mysql_integrasi')
                ->table('tb_jenis')
                ->orderBy('id_jenis')
                ->get();

            $this->info("Found {$jenisIntegrasi->count()} jenis in integrasi database");

            $synced = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($jenisIntegrasi as $jenis) {
                // Check if jenis already exists in CRM
                $existingJenis = DB::table('tb_jenis')
                    ->where('id_jenis', $jenis->id_jenis)
                    ->first();

                if ($existingJenis && !$this->option('force')) {
                    $this->line("  Skipping jenis {$jenis->id_jenis} ({$jenis->nama_jenis}) - already exists");
                    $skipped++;
                    continue;
                }

                try {
                    if ($existingJenis && $this->option('force')) {
                        // Update existing jenis
                        DB::table('tb_jenis')
                            ->where('id_jenis', $jenis->id_jenis)
                            ->update([
                                'nama_jenis' => $jenis->nama_jenis,
                                'deskripsi_jenis' => $jenis->deskripsi_jenis ?? '',
                                'updated_at' => now(),
                            ]);

                        $this->info("  Updated jenis {$jenis->id_jenis} ({$jenis->nama_jenis})");
                    } else {
                        // Insert new jenis with explicit ID
                        DB::table('tb_jenis')->insert([
                            'id_jenis' => $jenis->id_jenis,
                            'nama_jenis' => $jenis->nama_jenis,
                            'deskripsi_jenis' => $jenis->deskripsi_jenis ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $this->info("  Synced jenis {$jenis->id_jenis} ({$jenis->nama_jenis})");
                    }

                    $synced++;
                } catch (\Exception $e) {
                    $this->error("  Failed to sync jenis {$jenis->id_jenis}: {$e->getMessage()}");
                    Log::error('Failed to sync jenis', [
                        'id_jenis' => $jenis->id_jenis,
                        'error' => $e->getMessage()
                    ]);
                    $errors++;
                }
            }

            $this->newLine();
            $this->info("Sync completed:");
            $this->table(
                ['Status', 'Count'],
                [
                    ['Synced/Updated', $synced],
                    ['Skipped', $skipped],
                    ['Errors', $errors],
                    ['Total', $jenisIntegrasi->count()],
                ]
            );

            // Show summary of jenis in both databases
            $crmCount = DB::table('tb_jenis')->count();
            $integrasiCount = DB::connection('mysql_integrasi')->table('tb_jenis')->count();

            $this->newLine();
            $this->info("Database summary:");
            $this->table(
                ['Database', 'Jenis Count'],
                [
                    ['Integrasi', $integrasiCount],
                    ['CRM', $crmCount],
                ]
            );

            if ($errors > 0) {
                $this->warn("Completed with {$errors} errors. Check logs for details.");
                return 1;
            }

            $this->info('All jenis synced successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error("Fatal error: {$e->getMessage()}");
            Log::error('Fatal error in sync jenis command', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
