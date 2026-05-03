<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Integrasi\Produk as ProdukIntegrasi;
use App\Models\Integrasi\Jenis;
use App\Models\Cabang;
use App\Services\NearestBranchService;
use App\Services\IntegrasiProdukService;

class HomeController extends Controller
{
    protected $nearestBranchService;
    protected $integrasiProdukService;

    public function __construct(NearestBranchService $nearestBranchService, IntegrasiProdukService $integrasiProdukService)
    {
        $this->nearestBranchService = $nearestBranchService;
        $this->integrasiProdukService = $integrasiProdukService;
    }

    /**
     * Display the home page with products (public access)
     * Products are loaded from db_integrasi_ayu_mart
     * Stock is filtered by nearest branch
     */
    public function index(Request $request)
    {
        // Get category filter if exists
        $categoryId = $request->get('category');

        // Get nearest branch ID (from session or default)
        $nearestBranchId = $this->nearestBranchService->getNearestBranchId();

        // Log untuk debugging
        Log::info('Home Index - Branch ID from session', [
            'branch_id' => $nearestBranchId,
            'session_data' => session('nearest_branch')
        ]);

        // Get all active products with stock from nearest branch
        $filters = [];
        if ($categoryId) {
            $filters['id_jenis'] = $categoryId;
        }

        $products = $this->integrasiProdukService->getProdukWithStokByCabang($nearestBranchId, $filters);

        // Limit to 24 products for home page
        $products = $products->take(24);

        // Get product categories from database integrasi
        $categories = Jenis::orderBy('nama_jenis')->get();

        // Get products with active discount for promo section (with category filter and stock)
        $promoProducts = $this->integrasiProdukService->getProdukPromoWithStokByCabang($nearestBranchId, 6);

        // Get all active branches for selection
        $branches = $this->nearestBranchService->getAllActiveBranches();

        return view('home.index', compact('products', 'categories', 'promoProducts', 'categoryId', 'branches'));
    }

    /**
     * Show product details
     * Product loaded from db_integrasi_ayu_mart
     */
    public function show($id)
    {
        // Get product from database integrasi
        $product = ProdukIntegrasi::where('id_produk', $id)
            ->where('status_produk', 'aktif')
            ->with('jenis')
            ->first();

        if (!$product) {
            abort(404);
        }

        // Get current branch from session
        $currentBranch = $this->nearestBranchService->getCurrentBranch();

        // If no branch in session, use first branch as default
        if (!$currentBranch) {
            $firstBranch = Cabang::first();
            if ($firstBranch) {
                $this->nearestBranchService->saveToSession(
                    $firstBranch->id_cabang,
                    $firstBranch->nama_cabang,
                    null
                );
                $currentBranch = $this->nearestBranchService->getCurrentBranch();
            }
        }

        // Get stock for selected branch
        if ($currentBranch) {
            $stok = $this->integrasiProdukService->getStokProdukCabang($id, $currentBranch['id_cabang']);
            $product->stok_produk = $stok;
            $product->stok_cabang = $stok;
        } else {
            $product->stok_produk = 0;
            $product->stok_cabang = 0;
        }

        // Get all branches for dropdown
        $branches = Cabang::all();

        // Get product images (still from CRM database if exists)
        $images = DB::table('tb_gambar_produk')
            ->where('id_produk', $id)
            ->get();

        // Get product reviews (from tb_review, not tb_review_produk)
        $reviews = DB::table('tb_review')
            ->join('tb_pelanggan', 'tb_review.id_pelanggan', '=', 'tb_pelanggan.id_pelanggan')
            ->where('tb_review.id_produk', $id)
            ->where('tb_review.is_verified', true)
            ->select(
                'tb_review.*',
                'tb_pelanggan.nama_pelanggan as customer_name'
            )
            ->orderBy('tb_review.created_at', 'desc')
            ->get();

        // Calculate review statistics
        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->count();

        // Rating distribution
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $reviews->where('rating', $i)->count();
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        return view('home.product', compact('product', 'images', 'reviews', 'averageRating', 'totalReviews', 'ratingDistribution', 'branches'));
    }

    /**
     * Set user location and find nearest branch
     * Called via AJAX when user allows geolocation
     */
    public function setUserLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $userLat = $request->latitude;
        $userLng = $request->longitude;

        // Find nearest branch
        $nearestBranch = $this->nearestBranchService->findNearestBranch($userLat, $userLng);

        if ($nearestBranch) {
            // Save to session
            $this->nearestBranchService->saveToSession(
                $nearestBranch->id_cabang,
                $nearestBranch->nama_cabang,
                $nearestBranch->distance_km
            );

            return response()->json([
                'success' => true,
                'message' => 'Lokasi berhasil disimpan',
                'branch' => [
                    'id_cabang' => $nearestBranch->id_cabang,
                    'nama_cabang' => $nearestBranch->nama_cabang,
                    'distance' => number_format($nearestBranch->distance_km, 1)
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak dapat menemukan cabang terdekat'
        ], 404);
    }

    /**
     * Change selected branch manually
     */
    public function changeBranch(Request $request)
    {
        $request->validate([
            'id_cabang' => 'required|exists:tb_cabang,id_cabang'
        ]);

        $branch = Cabang::find($request->id_cabang);

        if ($branch) {
            // Save to session
            $this->nearestBranchService->saveToSession(
                $branch->id_cabang,
                $branch->nama_cabang,
                null // distance tidak diketahui jika dipilih manual
            );

            // Log untuk debugging
            Log::info('Branch Changed', [
                'new_branch_id' => $branch->id_cabang,
                'new_branch_name' => $branch->nama_cabang,
                'session_after_save' => session('nearest_branch')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cabang berhasil diubah',
                'branch' => [
                    'id_cabang' => $branch->id_cabang,
                    'nama_cabang' => $branch->nama_cabang,
                    'distance' => 'Pilihan Manual'
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cabang tidak ditemukan'
        ], 404);
    }

    /**
     * Get current branch from session
     * Called via AJAX to display selected branch info
     */
    public function getSessionBranch(Request $request)
    {
        $sessionBranch = session('nearest_branch');

        if ($sessionBranch && isset($sessionBranch['id_cabang'])) {
            // Get full branch details
            $branch = Cabang::find($sessionBranch['id_cabang']);

            if ($branch) {
                return response()->json([
                    'success' => true,
                    'branch' => [
                        'id_cabang' => $branch->id_cabang,
                        'nama_cabang' => $branch->nama_cabang,
                        'alamat' => $branch->alamat,
                        'formatted_address' => $branch->formatted_address,
                        'no_telepon' => $branch->no_telepon,
                        'latitude' => (float) $branch->latitude,
                        'longitude' => (float) $branch->longitude,
                        'distance' => $sessionBranch['distance'] ?? null
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada cabang yang dipilih'
        ], 404);
    }
}
