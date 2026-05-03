<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cabang;

class NearestBranchService
{
    /**
     * Mencari cabang terdekat berdasarkan koordinat user
     *
     * @param float $userLat Latitude user
     * @param float $userLng Longitude user
     * @return object|null Cabang terdekat
     */
    public function findNearestBranch($userLat, $userLng)
    {
        try {
            // Ambil semua cabang aktif dari database CRM
            $branches = Cabang::where('is_active', 1)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();

            if ($branches->isEmpty()) {
                Log::warning('Tidak ada cabang aktif dengan koordinat');
                return null;
            }

            $nearestBranch = null;
            $minDistance = PHP_FLOAT_MAX;

            // Hitung jarak ke setiap cabang
            foreach ($branches as $branch) {
                $distance = $this->calculateDistance(
                    $userLat,
                    $userLng,
                    $branch->latitude,
                    $branch->longitude
                );

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestBranch = $branch;
                }
            }

            // Tambahkan informasi jarak
            if ($nearestBranch) {
                $nearestBranch->distance_km = round($minDistance, 2);
            }

            Log::info('Cabang terdekat ditemukan', [
                'branch' => $nearestBranch->nama_cabang ?? 'N/A',
                'distance_km' => $nearestBranch->distance_km ?? 'N/A'
            ]);

            return $nearestBranch;
        } catch (\Exception $e) {
            Log::error('Error finding nearest branch: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Hitung jarak antara 2 koordinat menggunakan Haversine formula
     *
     * @param float $lat1 Latitude point 1
     * @param float $lng1 Longitude point 1
     * @param float $lat2 Latitude point 2
     * @param float $lng2 Longitude point 2
     * @return float Jarak dalam kilometer
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $latDelta = $lat2 - $lat1;
        $lngDelta = $lng2 - $lng1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($lat1) * cos($lat2) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }

    /**
     * Simpan cabang terdekat ke session
     *
     * @param int $branchId ID cabang
     * @param string $branchName Nama cabang
     * @param float $distance Jarak dalam km
     * @return void
     */
    public function saveToSession($branchId, $branchName, $distance = null)
    {
        // Save with individual keys for backward compatibility
        session([
            'nearest_branch_id' => $branchId,
            'nearest_branch_name' => $branchName,
            'nearest_branch_distance' => $distance,
            'nearest_branch_updated_at' => now()->toDateTimeString()
        ]);

        // Also save as array for easier access in views
        session([
            'nearest_branch' => [
                'id_cabang' => $branchId,
                'nama_cabang' => $branchName,
                'distance' => $distance,
                'updated_at' => now()->toDateTimeString()
            ]
        ]);

        Log::info('Cabang terdekat disimpan ke session', [
            'branch_id' => $branchId,
            'branch_name' => $branchName,
            'distance_km' => $distance
        ]);
    }

    /**
     * Dapatkan cabang terdekat dari session
     *
     * @return array|null
     */
    public function getFromSession()
    {
        if (!session()->has('nearest_branch_id')) {
            return null;
        }

        return [
            'id' => session('nearest_branch_id'),
            'name' => session('nearest_branch_name'),
            'distance' => session('nearest_branch_distance'),
            'updated_at' => session('nearest_branch_updated_at')
        ];
    }

    /**
     * Get current branch information from session
     * Returns array with branch info or null
     *
     * @return array|null
     */
    public function getCurrentBranch()
    {
        if (!session()->has('nearest_branch_id')) {
            return null;
        }

        return [
            'id_cabang' => session('nearest_branch_id'),
            'nama_cabang' => session('nearest_branch_name'),
            'distance' => session('nearest_branch_distance'),
            'updated_at' => session('nearest_branch_updated_at')
        ];
    }

    /**
     * Hapus cabang terdekat dari session
     *
     * @return void
     */
    public function clearSession()
    {
        session()->forget([
            'nearest_branch_id',
            'nearest_branch_name',
            'nearest_branch_distance',
            'nearest_branch_updated_at',
            'nearest_branch' // clear array version too
        ]);
    }

    /**
     * Dapatkan ID cabang terdekat (dari session atau default)
     *
     * @return int|null
     */
    public function getNearestBranchId()
    {
        // Cek dari session dulu
        if (session()->has('nearest_branch_id')) {
            return session('nearest_branch_id');
        }

        // Fallback ke cabang pertama yang aktif
        $defaultBranch = Cabang::where('is_active', 1)->first();

        if ($defaultBranch) {
            return $defaultBranch->id_cabang;
        }

        return null;
    }

    /**
     * Dapatkan semua cabang aktif
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllActiveBranches()
    {
        return Cabang::where('is_active', 1)
            ->orderBy('nama_cabang')
            ->get();
    }
}
