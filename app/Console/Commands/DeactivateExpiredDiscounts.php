<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Integrasi\Produk as ProdukIntegrasi;
use Illuminate\Support\Facades\Log;

class DeactivateExpiredDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discounts:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menonaktifkan diskon produk yang sudah kadaluarsa dan mengembalikan harga ke harga normal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Mencari diskon produk yang sudah kadaluarsa...');

        // Deactivate expired discounts in CRM database
        $this->info('📊 Memproses database CRM...');
        $crmCount = $this->deactivateCRMDiscounts();

        // Deactivate expired discounts in Integrasi database
        $this->info('📊 Memproses database Integrasi...');
        $integrasiCount = $this->deactivateIntegrationDiscounts();

        $totalCount = $crmCount + $integrasiCount;

        $this->newLine();
        $this->info("✅ Berhasil menonaktifkan diskon untuk {$totalCount} produk.");
        $this->info("   - CRM Database: {$crmCount} produk");
        $this->info("   - Integrasi Database: {$integrasiCount} produk");
        $this->info("💰 Harga produk telah dikembalikan ke harga normal.");

        return 0;
    }

    /**
     * Deactivate expired discounts in CRM database
     */
    private function deactivateCRMDiscounts()
    {
        $expiredProducts = Product::where('is_diskon_active', true)
            ->whereNotNull('tanggal_akhir_diskon')
            ->where('tanggal_akhir_diskon', '<', now()->startOfDay())
            ->get();

        if ($expiredProducts->isEmpty()) {
            return 0;
        }

        $deactivatedCount = 0;
        $bar = $this->output->createProgressBar($expiredProducts->count());
        $bar->start();

        foreach ($expiredProducts as $product) {
            try {
                $product->update([
                    'is_diskon_active' => false,
                ]);

                $deactivatedCount++;

                Log::info('CRM: Discount deactivated for expired product', [
                    'product_id' => $product->id_produk,
                    'product_name' => $product->nama_produk,
                    'expired_date' => $product->tanggal_akhir_diskon,
                ]);

                $bar->advance();
            } catch (\Exception $e) {
                Log::error('CRM: Error deactivating discount: ' . $e->getMessage(), [
                    'product_id' => $product->id_produk ?? null,
                ]);
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();

        return $deactivatedCount;
    }

    /**
     * Deactivate expired discounts in Integrasi database
     */
    private function deactivateIntegrationDiscounts()
    {
        return ProdukIntegrasi::autoDeactivateAllExpired();
    }
}
