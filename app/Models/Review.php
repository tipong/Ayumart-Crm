<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Integrasi\Produk as ProdukIntegrasi;

class Review extends Model
{
    protected $table = 'tb_review';
    protected $primaryKey = 'id_review';

    protected $fillable = [
        'id_pelanggan',
        'id_produk',
        'id_transaksi',
        'rating',
        'review',
        'foto_review',
        'is_verified',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
    ];

    /**
     * Relasi ke Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Relasi ke Produk dari database integrasi (PRIMARY)
     * tb_review.id_produk sekarang merujuk ke db_integrasi_ayu_mart.tb_produk
     */
    public function product()
    {
        return $this->belongsTo(ProdukIntegrasi::class, 'id_produk', 'id_produk');
    }

    /**
     * Alias untuk relasi product
     */
    public function produkIntegrasi()
    {
        return $this->product();
    }

    /**
     * Legacy: Relasi ke produk CRM (DEPRECATED - JANGAN DIGUNAKAN)
     */
    public function productCrm()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }

    /**
     * Relasi ke Transaksi
     */
    public function transaction()
    {
        return $this->belongsTo(Order::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Alias untuk transaction (untuk backward compatibility)
     */
    public function order()
    {
        return $this->transaction();
    }

    /**
     * Get star rating as HTML
     */
    public function getStarsHtmlAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $stars .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        return $stars;
    }

    /**
     * Scope untuk mendapatkan review produk tertentu
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('id_produk', $productId)
                     ->where('is_verified', true)
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Scope untuk mendapatkan review pelanggan
     */
    public function scopeForPelanggan($query, $pelangganId)
    {
        return $query->where('id_pelanggan', $pelangganId)
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Get average rating for a product
     */
    public static function averageRatingForProduct($productId)
    {
        return static::where('id_produk', $productId)
                     ->where('is_verified', true)
                     ->avg('rating') ?? 0;
    }

    /**
     * Get review count for a product
     */
    public static function countForProduct($productId)
    {
        return static::where('id_produk', $productId)
                     ->where('is_verified', true)
                     ->count();
    }
}
