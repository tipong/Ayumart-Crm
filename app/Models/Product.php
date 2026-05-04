<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $table = 'tb_produk';
    protected $primaryKey = 'id_produk';
    protected $connection = 'mysql_integrasi';

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id_produk';
    }

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'deskripsi_produk',
        'id_jenis',
        'id_produk_integrasi', // ID produk dari database integrasi
        'harga_produk',
        'harga_diskon',
        'persentase_diskon',
        'tanggal_mulai_diskon',
        'tanggal_akhir_diskon',
        'is_diskon_active',
        'diskon_produk',
        'stok_produk',
        'berat_produk',
        'foto_produk',
        'status_produk',
    ];

    protected $casts = [
        'harga_produk' => 'decimal:2',
        'harga_diskon' => 'decimal:2',
        'persentase_diskon' => 'decimal:2',
        'diskon_produk' => 'decimal:2',
        'berat_produk' => 'decimal:2',
        'tanggal_mulai_diskon' => 'date',
        'tanggal_akhir_diskon' => 'date',
        'is_diskon_active' => 'boolean',
    ];

    // Accessor attributes untuk kompatibilitas dengan view
    protected $appends = ['id', 'name', 'description', 'price', 'stock', 'image', 'category'];

    /**
     * Get the product ID (alias for id_produk)
     */
    public function getIdAttribute()
    {
        return $this->id_produk;
    }

    /**
     * Get the product name (alias for nama_produk)
     */
    public function getNameAttribute()
    {
        return $this->nama_produk;
    }

    /**
     * Get the product description (alias for deskripsi_produk)
     */
    public function getDescriptionAttribute()
    {
        return $this->deskripsi_produk;
    }

    /**
     * Get the product price (alias for harga_produk)
     */
    public function getPriceAttribute()
    {
        return $this->harga_produk;
    }

    /**
     * Get the product stock (alias for stok_produk)
     */
    public function getStockAttribute()
    {
        return $this->stok_produk;
    }

    /**
     * Get the product image (alias for foto_produk)
     */
    public function getImageAttribute()
    {
        return $this->foto_produk;
    }

    /**
     * Get the category name
     */
    public function getCategoryAttribute()
    {
        return $this->jenis ? $this->jenis->nama_jenis : 'Tidak ada kategori';
    }

    public function jenis()
    {
        return $this->belongsTo(\App\Models\Jenis::class, 'id_jenis', 'id_jenis');
    }

    public function category()
    {
        return $this->jenis();
    }

    public function cartItems()
    {
        return $this->hasMany(\App\Models\Cart::class, 'id_produk', 'id_produk');
    }

    public function wishlistItems()
    {
        return $this->hasMany(\App\Models\Wishlist::class, 'id_produk', 'id_produk');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'id_produk', 'id_produk');
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
        return $this->memberDiscounts()
            ->where('tier', $tier)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get price with member tier discount
     */
    public function getPriceWithMemberDiscount($tier)
    {
        $basePrice = $this->getCurrentPrice();
        $discount = $this->getDiscountForTier($tier);

        if (!$discount) {
            return $basePrice;
        }

        return $basePrice - ($basePrice * ($discount->discount_percentage / 100));
    }

    /**
     * Get all member discounts with details
     * Method ini aman dan return empty collection jika tabel tidak ada
     */
    public function getAllMemberDiscounts()
    {
        try {
            // Check if table exists first
            $tableName = 'product_member_discounts';
            $schemaBuilder = \Illuminate\Support\Facades\DB::connection('mysql_integrasi')
                ->getSchemaBuilder();
            
            // Jika tabel tidak ada, return empty collection
            if (!$schemaBuilder->hasTable($tableName)) {
                Log::warning('Table ' . $tableName . ' does not exist in database');
                return collect();
            }
            
            $discounts = $this->memberDiscounts()
                ->where('is_active', true)
                ->get();
            
            if ($discounts->isEmpty()) {
                return collect();
            }
            
            return $discounts->map(function ($discount) {
                $basePrice = $this->getCurrentPrice();
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

    public function hasActiveDiscount()
    {
        if (!$this->is_diskon_active || !$this->harga_diskon) {
            return false;
        }
        $today = now()->startOfDay();
        if ($this->tanggal_mulai_diskon && $this->tanggal_akhir_diskon) {
            return $today->between(
                $this->tanggal_mulai_diskon->startOfDay(),
                $this->tanggal_akhir_diskon->endOfDay()
            );
        }
        return true;
    }

    public function getCurrentPrice()
    {
        if ($this->hasActiveDiscount()) {
            return $this->harga_diskon;
        }
        return $this->harga_produk;
    }

    public function getDiscountPercentage()
    {
        if (!$this->hasActiveDiscount()) {
            return 0;
        }
        if ($this->persentase_diskon) {
            return $this->persentase_diskon;
        }
        return round((($this->harga_produk - $this->harga_diskon) / $this->harga_produk) * 100, 2);
    }

    public function getSavings()
    {
        if (!$this->hasActiveDiscount()) {
            return 0;
        }
        return $this->harga_produk - $this->harga_diskon;
    }

    public function isAvailable()
    {
        return $this->status_produk === 'aktif' && $this->stok_produk > 0;
    }

    public function getImageUrl()
    {
        if ($this->foto_produk) {
            return ImageHelper::getProductImage($this->foto_produk);
        }
        return asset('images/no-image.png');
    }

    public function scopeActive($query)
    {
        return $query->where('status_produk', 'aktif');
    }

    public function scopeInStock($query)
    {
        return $query->where('stok_produk', '>', 0);
    }

    public function scopeWithDiscount($query)
    {
        return $query->where('is_diskon_active', true)
                     ->whereNotNull('harga_diskon')
                     ->where('harga_diskon', '<', 'harga_produk');
    }

    public function scopeByCategory($query, $jenisId)
    {
        return $query->where('id_jenis', $jenisId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_produk', 'LIKE', "%{$search}%")
              ->orWhere('deskripsi_produk', 'LIKE', "%{$search}%")
              ->orWhere('kode_produk', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get average rating for this product
     */
    public function getAverageRating()
    {
        return Review::averageRatingForProduct($this->id_produk);
    }

    /**
     * Get total review count for this product
     */
    public function getReviewCount()
    {
        return Review::countForProduct($this->id_produk);
    }

    /**
     * Get verified reviews for this product
     */
    public function getVerifiedReviews()
    {
        return $this->reviews()
                    ->where('is_verified', true)
                    ->with(['pelanggan.user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get rating stars HTML
     */
    public function getRatingStarsHtml()
    {
        $avgRating = $this->getAverageRating();
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= round($avgRating)) {
                $stars .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $stars .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        return $stars;
    }
}
