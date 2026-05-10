<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductMemberDiscount extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'product_member_discounts';

    protected $fillable = [
        'product_id',
        'tier',
        'discount_percentage',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    const TIER_BRONZE = 'bronze';
    const TIER_SILVER = 'silver';
    const TIER_GOLD = 'gold';
    const TIER_PLATINUM = 'platinum';

    const TIERS = [
        'bronze' => 'Bronze',
        'silver' => 'Silver',
        'gold' => 'Gold',
        'platinum' => 'Platinum',
    ];

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Integrasi\Produk::class, 'product_id', 'id_produk');
    }

    /**
     * Find discount for product and tier
     */
    public static function findByProductAndTier($productId, $tier)
    {
        return static::where('product_id', $productId)
            ->where('tier', $tier)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all discounts for a product
     */
    public static function getProductDiscounts($productId)
    {
        return static::where('product_id', $productId)
            ->where('is_active', true)
            ->get()
            ->keyBy('tier');
    }
}
