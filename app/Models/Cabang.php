<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'tb_cabang';
    protected $primaryKey = 'id_cabang';

    protected $fillable = [
        'nama_cabang',
        'kode_cabang',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'latitude',
        'longitude',
        'google_maps_url',
        'no_telepon',
        'jam_buka',
        'jam_tutup',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    // Scope: Hanya cabang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute()
    {
        $parts = array_filter([
            $this->alamat,
            $this->kelurahan,
            $this->kecamatan,
            $this->kota,
            $this->provinsi,
            $this->kode_pos
        ]);

        return implode(', ', $parts);
    }

    /**
     * Find nearest branch based on latitude and longitude
     */
    public static function findNearest($latitude, $longitude)
    {
        $branches = self::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($branches->isEmpty()) {
            return null;
        }

        $nearestBranch = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($branches as $branch) {
            $distance = self::calculateDistance(
                $latitude,
                $longitude,
                $branch->latitude,
                $branch->longitude
            );

            $branch->distance = $distance;

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestBranch = $branch;
            }
        }

        return $nearestBranch;
    }

    /**
     * Calculate distance from this branch to given coordinates
     * Returns distance in kilometers
     */
    public function calculateDistanceTo($latitude, $longitude)
    {
        return self::calculateDistance(
            $this->latitude,
            $this->longitude,
            $latitude,
            $longitude
        );
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Calculate shipping cost based on distance
     * Base cost: Rp 15.000
     * Additional: Rp 2.000 per km after first 5km
     */
    public static function calculateShippingCost($distance)
    {
        $baseCost = 15000; // Rp 15.000 for first 5km
        $baseDistance = 5; // km
        $additionalCostPerKm = 2000; // Rp 2.000 per km

        if ($distance <= $baseDistance) {
            return $baseCost;
        }

        $additionalDistance = $distance - $baseDistance;
        $additionalCost = ceil($additionalDistance) * $additionalCostPerKm;

        return $baseCost + $additionalCost;
    }
}
