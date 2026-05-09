<?php

namespace App\Models\Integrasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Produk extends Model
{
    protected $connection = 'mysql_integrasi';
    protected $table = 'tb_produk';
    protected $primaryKey = 'id_produk';
    public $timestamps = false;

    protected $fillable = [
        'id_produk',
        'kode_produk',
        'nama_produk',
        'deskripsi_produk',
        'id_jenis',
        'harga_produk',
        'harga_diskon',
        'persentase_diskon',
        'tanggal_mulai_diskon',
        'tanggal_akhir_diskon',
        'is_diskon_active',
        'discount_target',
        'berat_produk',
        'foto_produk',
        'status_produk',
        'satuan',
        'harga_beli',
    ];

    protected $casts = [
        'harga_produk' => 'decimal:0',
        'harga_diskon' => 'decimal:0',
        // NOTE: berat_produk is stored as string in the database (e.g., "23 gram", "600 ml")
        // DO NOT cast it to decimal here - conversion will be done when syncing to CRM
        // 'berat_produk' => 'decimal:0',
        'harga_beli' => 'decimal:0',
        'is_diskon_active' => 'boolean',
        'tanggal_mulai_diskon' => 'date',
        'tanggal_akhir_diskon' => 'date',
    ];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis', 'id_jenis');
    }

    public function stokCabang()
    {
        return $this->hasMany(StokCabang::class, 'id_produk', 'id_produk');
    }

    /**
     * Get member tier discounts for this product
     */
    public function memberDiscounts()
    {
        return $this->hasMany(\App\Models\ProductMemberDiscount::class, 'product_id', 'id_produk');
    }

    /**
     * Get discount for specific member tier
     */
    public function getDiscountForTier($tier)
    {
        try {
            // Check if table exists first
            $tableName = 'product_member_discounts';
            $schemaBuilder = DB::connection('mysql_integrasi')->getSchemaBuilder();

            // Jika tabel tidak ada, return null
            if (!$schemaBuilder->hasTable($tableName)) {
                return null;
            }

            return $this->memberDiscounts()
                ->where('tier', $tier)
                ->where('is_active', true)
                ->first();
        } catch (\Exception $e) {
            // Return null jika ada error
            Log::warning('Error getting discount for tier ' . $tier . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all member discounts with details
     */
    public function getAllMemberDiscounts()
    {
        try {
            // Check if table exists first
            $tableName = 'product_member_discounts';
            $schemaBuilder = DB::connection('mysql_integrasi')->getSchemaBuilder();

            // Jika tabel tidak ada, return empty collection
            if (!$schemaBuilder->hasTable($tableName)) {
                Log::warning('Table ' . $tableName . ' does not exist in database');
                return collect();
            }

            return $this->memberDiscounts()
                ->where('is_active', true)
                ->get()
                ->map(function ($discount) {
                    $basePrice = $this->harga_final;
                    return [
                        'tier' => $discount->tier,
                        'tier_name' => \App\Models\ProductMemberDiscount::TIERS[$discount->tier] ?? $discount->tier,
                        'discount_percentage' => $discount->discount_percentage,
                        'price_with_discount' => $basePrice - ($basePrice * ($discount->discount_percentage / 100)),
                    ];
                });
        } catch (\Exception $e) {
            // Return empty collection jika ada error (misalnya tabel tidak ada)
            Log::warning('Error getting member discounts for product ' . $this->id_produk . ': ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get harga final (dengan diskon jika aktif)
     */
    public function getHargaFinalAttribute()
    {
        if ($this->is_diskon_active && $this->harga_diskon) {
            $now = now();
            $start = $this->tanggal_mulai_diskon;
            $end = $this->tanggal_akhir_diskon;

            if ($start && $end && $now->between($start, $end)) {
                return $this->harga_diskon;
            }
        }

        return $this->harga_produk;
    }

    /**
     * Get stok di cabang tertentu
     */
    public function getStokCabang($idCabang)
    {
        $stok = $this->stokCabang()
            ->where('id_detail_cabang', $idCabang)
            ->first();

        return $stok ? $stok->total_stok : 0;
    }

    /**
     * Check if product has active discount
     */
    public function hasActiveDiscount()
    {
        if (!$this->is_diskon_active) {
            return false;
        }

        // Diskon tier: cek tanggal saja (harga_diskon boleh null untuk tier target)
        if ($this->discount_target === 'tier') {
            $now   = now();
            $start = $this->tanggal_mulai_diskon;
            $end   = $this->tanggal_akhir_diskon;
            return $start && $end && $now->between($start, $end);
        }

        // Diskon general: perlu harga_diskon dan dalam range tanggal
        if (!$this->harga_diskon) {
            return false;
        }

        $now   = now();
        $start = $this->tanggal_mulai_diskon;
        $end   = $this->tanggal_akhir_diskon;

        if ($start && $end && $now->between($start, $end)) {
            return true;
        }

        return false;
    }

    /**
     * Get current price (with discount if active)
     * Alias untuk harga_final untuk kompatibilitas
     */
    public function getCurrentPrice()
    {
        return $this->harga_final;
    }

    /**
     * Get discount percentage
     */
    public function getPersentaseDiskonAttribute()
    {
        if (!$this->hasActiveDiscount()) {
            return 0;
        }

        // Jika target tier, tidak ada harga_diskon global
        if ($this->discount_target === 'tier') {
            return 0;
        }

        if (!$this->harga_diskon || $this->harga_produk <= 0) {
            return 0;
        }

        return round((($this->harga_produk - $this->harga_diskon) / $this->harga_produk) * 100);
    }

    /**
     * Get discount percentage (alias method untuk kompatibilitas dengan views)
     */
    public function getDiscountPercentage()
    {
        return $this->persentase_diskon;
    }

    /**
     * Cek apakah diskon ini bertarget tier
     */
    public function isTierDiscount(): bool
    {
        return $this->discount_target === 'tier';
    }

    /**
     * Get savings amount
     */
    public function getSavingsAttribute()
    {
        if (!$this->hasActiveDiscount()) {
            return 0;
        }

        return $this->harga_produk - $this->harga_diskon;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->harga_produk, 0, ',', '.');
    }

    /**
     * Get formatted discount price
     */
    public function getFormattedDiscountPriceAttribute()
    {
        if ($this->harga_diskon) {
            return 'Rp ' . number_format($this->harga_diskon, 0, ',', '.');
        }
        return null;
    }

    /**
     * Get formatted final price
     */
    public function getFormattedFinalPriceAttribute()
    {
        return 'Rp ' . number_format($this->harga_final, 0, ',', '.');
    }

    /**
     * Get product image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->foto_produk) {
            return asset('images/products/' . $this->foto_produk);
        }
        return asset('images/products/default.jpg');
    }

    /**
     * Scope untuk produk aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status_produk', 'aktif');
    }

    /**
     * Scope untuk produk dengan diskon aktif
     */
    public function scopeWithActiveDiscount($query)
    {
        return $query->where('status_produk', 'aktif')
            ->where('is_diskon_active', 1)
            ->whereNotNull('harga_diskon')
            ->whereNotNull('tanggal_akhir_diskon')
            ->where('tanggal_akhir_diskon', '>=', now()->startOfDay());
    }

    /**
     * Check if discount has expired
     */
    public function isDiscountExpired()
    {
        if (!$this->tanggal_akhir_diskon) {
            return false;
        }
        return now()->isAfter($this->tanggal_akhir_diskon->endOfDay());
    }

    /**
     * Auto-deactivate expired discounts
     * Jika tanggal akhir diskon sudah lewat, maka status diskon akan otomatis nonaktif
     */
    public function autoDeactivateIfExpired()
    {
        if ($this->is_diskon_active && $this->isDiscountExpired()) {
            $this->update(['is_diskon_active' => false]);
            return true;
        }
        return false;
    }

    /**
     * Static method untuk auto-deactivate semua diskon yang expired
     */
    public static function autoDeactivateAllExpired()
    {
        $count = 0;
        $expiredDiscounts = self::where('is_diskon_active', true)
            ->whereNotNull('tanggal_akhir_diskon')
            ->where('tanggal_akhir_diskon', '<', now()->startOfDay())
            ->get();

        foreach ($expiredDiscounts as $product) {
            if ($product->autoDeactivateIfExpired()) {
                $count++;
            }
        }

        return $count;
    }
}
