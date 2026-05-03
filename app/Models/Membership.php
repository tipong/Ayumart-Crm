<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  // FIXED: Changed from pelanggan_id (was misleading, actually references users table)
        'tier',
        'points',
        'discount_percentage',
        'valid_from',
        'valid_until',
        'is_active'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'points' => 'integer',
        'discount_percentage' => 'decimal:2'
    ];

    // Tier configuration
    const TIER_BRONZE = 'bronze';
    const TIER_SILVER = 'silver';
    const TIER_GOLD = 'gold';
    const TIER_PLATINUM = 'platinum';

    // Points thresholds for each tier
    const TIER_THRESHOLDS = [
        self::TIER_BRONZE => 0,      // 0-100 points
        self::TIER_SILVER => 101,    // 101-250 points
        self::TIER_GOLD => 251,      // 251-400 points
        self::TIER_PLATINUM => 401,  // 401+ points
    ];

    // Discount percentages for each tier
    const TIER_DISCOUNTS = [
        self::TIER_BRONZE => 5,      // 5%
        self::TIER_SILVER => 10,     // 10%
        self::TIER_GOLD => 15,       // 15%
        self::TIER_PLATINUM => 20,   // 20%
    ];

    // Points conversion rate: 1 point per 20,000 IDR
    const POINTS_PER_AMOUNT = 20000;

    /**
     * Relationship to User
     * FIXED: Changed from pelanggan() to user()
     * This membership belongs to a user in the users table
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');  // FIXED: Added 'id_user' as owner key
    }

    /**
     * Backward compatibility alias (deprecated)
     * @deprecated Use user() instead
     */
    public function pelanggan()
    {
        return $this->user();
    }

    /**
     * Calculate tier based on points
     */
    public static function calculateTier($points)
    {
        if ($points >= self::TIER_THRESHOLDS[self::TIER_PLATINUM]) {
            return self::TIER_PLATINUM;
        } elseif ($points >= self::TIER_THRESHOLDS[self::TIER_GOLD]) {
            return self::TIER_GOLD;
        } elseif ($points >= self::TIER_THRESHOLDS[self::TIER_SILVER]) {
            return self::TIER_SILVER;
        } else {
            return self::TIER_BRONZE;
        }
    }

    /**
     * Calculate discount percentage based on tier
     */
    public static function getDiscountForTier($tier)
    {
        return self::TIER_DISCOUNTS[$tier] ?? 0;
    }

    /**
     * Calculate points from transaction amount
     */
    public static function calculatePoints($amount)
    {
        return floor($amount / self::POINTS_PER_AMOUNT);
    }

    /**
     * Add points and update tier automatically
     */
    public function addPoints($points)
    {
        $this->points += $points;
        $this->updateTier();
        $this->save();
    }

    /**
     * Update tier based on current points
     */
    public function updateTier()
    {
        $newTier = self::calculateTier($this->points);

        if ($this->tier !== $newTier) {
            $this->tier = $newTier;
            $this->discount_percentage = self::getDiscountForTier($newTier);
        }
    }

    /**
     * Get tier name (formatted)
     */
    public function getTierNameAttribute()
    {
        $tiers = [
            'bronze' => 'Bronze',
            'silver' => 'Silver',
            'gold' => 'Gold',
            'platinum' => 'Platinum'
        ];
        return $tiers[$this->tier] ?? $this->tier;
    }

    /**
     * Check if membership is valid
     */
    public function isValid()
    {
        return $this->is_active &&
               $this->valid_from <= now() &&
               $this->valid_until >= now();
    }

    /**
     * Get discount percentage for this membership
     */
    public function getDiscount()
    {
        // Return discount_percentage if it's set and membership is valid
        if ($this->isValid() && $this->discount_percentage) {
            return $this->discount_percentage;
        }

        // Otherwise return discount based on tier
        return self::getDiscountForTier($this->tier);
    }

    /**
     * Get next tier information
     */
    public function getNextTierInfo()
    {
        $currentPoints = $this->points;

        switch ($this->tier) {
            case self::TIER_BRONZE:
                return [
                    'next_tier' => 'Silver',
                    'points_needed' => self::TIER_THRESHOLDS[self::TIER_SILVER] - $currentPoints,
                    'total_needed' => self::TIER_THRESHOLDS[self::TIER_SILVER]
                ];
            case self::TIER_SILVER:
                return [
                    'next_tier' => 'Gold',
                    'points_needed' => self::TIER_THRESHOLDS[self::TIER_GOLD] - $currentPoints,
                    'total_needed' => self::TIER_THRESHOLDS[self::TIER_GOLD]
                ];
            case self::TIER_GOLD:
                return [
                    'next_tier' => 'Platinum',
                    'points_needed' => self::TIER_THRESHOLDS[self::TIER_PLATINUM] - $currentPoints,
                    'total_needed' => self::TIER_THRESHOLDS[self::TIER_PLATINUM]
                ];
            default:
                return [
                    'next_tier' => 'Maximum',
                    'points_needed' => 0,
                    'total_needed' => $currentPoints
                ];
        }
    }

    /**
     * Get count of members by tier
     */
    public static function getTierCounts()
    {
        return [
            'bronze' => self::where('tier', self::TIER_BRONZE)->where('is_active', true)->count(),
            'silver' => self::where('tier', self::TIER_SILVER)->where('is_active', true)->count(),
            'gold' => self::where('tier', self::TIER_GOLD)->where('is_active', true)->count(),
            'platinum' => self::where('tier', self::TIER_PLATINUM)->where('is_active', true)->count(),
            'total' => self::where('is_active', true)->count(),
        ];
    }

    /**
     * Boot method to auto-update tier on saving
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate tier and discount when creating new membership
        static::creating(function ($membership) {
            if (!$membership->tier) {
                $membership->tier = self::calculateTier($membership->points ?? 0);
            }
            if (!$membership->discount_percentage) {
                $membership->discount_percentage = self::getDiscountForTier($membership->tier);
            }
        });

        // Auto-update tier and discount when updating membership
        static::updating(function ($membership) {
            // Only update if points changed
            if ($membership->isDirty('points')) {
                $newTier = self::calculateTier($membership->points);
                $membership->tier = $newTier;
                $membership->discount_percentage = self::getDiscountForTier($newTier);
            }
        });
    }
}
