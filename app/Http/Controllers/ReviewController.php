<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Review;
use App\Models\Order;
use App\Models\Product;
use App\Models\Integrasi\Produk as ProdukIntegrasi;

class ReviewController extends Controller
{
    /**
     * Show form to create review for a product from an order
     */
    public function create($orderId, $productId)
    {
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        // Get order
        $order = Order::where('id_transaksi', $orderId)
                      ->where('id_pelanggan', $pelanggan->id_pelanggan)
                      ->first();

        if (!$order) {
            return redirect()->route('pelanggan.orders')->with('error', 'Pesanan tidak ditemukan');
        }

        // Check if order is delivered
        if ($order->status_pengiriman !== 'terkirim' && $order->status_pengiriman !== 'selesai') {
            return redirect()->route('pelanggan.orders')->with('error', 'Anda hanya bisa memberikan review setelah barang diterima');
        }

        // Get product from order details
        $orderDetail = DB::table('tb_detail_transaksi')
                        ->where('id_transaksi', $orderId)
                        ->where('id_produk', $productId)
                        ->first();

        if (!$orderDetail) {
            return redirect()->route('pelanggan.orders')->with('error', 'Produk tidak ditemukan dalam pesanan ini');
        }

        // Get product from database integrasi (UPDATED)
        $product = ProdukIntegrasi::find($productId);

        // Fallback to CRM database if not found in integrasi
        if (!$product) {
            $product = Product::find($productId);
        }

        if (!$product) {
            return redirect()->route('pelanggan.orders')->with('error', 'Produk tidak ditemukan');
        }

        // Check if already reviewed
        $existingReview = Review::where('id_pelanggan', $pelanggan->id_pelanggan)
                                ->where('id_produk', $productId)
                                ->where('id_transaksi', $orderId)
                                ->first();

        if ($existingReview) {
            return redirect()->route('pelanggan.orders')->with('info', 'Anda sudah memberikan review untuk produk ini');
        }

        return view('pelanggan.review-create', compact('order', 'product'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        // Validasi - UPDATED: id_produk sekarang merujuk ke database integrasi
        $validated = $request->validate([
            'id_transaksi' => 'required|exists:tb_transaksi,id_transaksi',
            'id_produk' => 'required|integer', // Remove exists check to CRM, will validate against integrasi DB below
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:1000',
            'foto_review' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Verify product exists in database integrasi
        $product = ProdukIntegrasi::find($validated['id_produk']);
        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan')->withInput();
        }

        // Verify order belongs to pelanggan and is delivered
        $order = Order::where('id_transaksi', $validated['id_transaksi'])
                      ->where('id_pelanggan', $pelanggan->id_pelanggan)
                      ->first();

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan')->withInput();
        }

        if ($order->status_pengiriman !== 'terkirim' && $order->status_pengiriman !== 'selesai') {
            return back()->with('error', 'Anda hanya bisa memberikan review setelah barang diterima')->withInput();
        }

        // Check if product is in this order
        $orderDetail = DB::table('tb_detail_transaksi')
                        ->where('id_transaksi', $validated['id_transaksi'])
                        ->where('id_produk', $validated['id_produk'])
                        ->first();

        if (!$orderDetail) {
            return back()->with('error', 'Produk tidak ditemukan dalam pesanan ini')->withInput();
        }

        // Check if already reviewed
        $existingReview = Review::where('id_pelanggan', $pelanggan->id_pelanggan)
                                ->where('id_produk', $validated['id_produk'])
                                ->where('id_transaksi', $validated['id_transaksi'])
                                ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan review untuk produk ini');
        }

        try {
            DB::beginTransaction();

            // Handle photo upload if exists
            $fotoPath = null;
            if ($request->hasFile('foto_review')) {
                $file = $request->file('foto_review');
                $filename = 'review_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('reviews', $filename, 'public');
            }

            // Create review
            $review = Review::create([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_produk' => $validated['id_produk'],
                'id_transaksi' => $validated['id_transaksi'],
                'rating' => $validated['rating'],
                'review' => $validated['review'],
                'foto_review' => $fotoPath,
                'is_verified' => true,
            ]);

            DB::commit();

            Log::info('Review created', [
                'id_review' => $review->id_review,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_produk' => $validated['id_produk'],
                'rating' => $validated['rating']
            ]);

            return redirect()->route('pelanggan.orders')
                           ->with('success', 'Terima kasih! Review Anda telah berhasil dikirim');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating review', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_produk' => $validated['id_produk'],
                'id_transaksi' => $validated['id_transaksi']
            ]);

            if (isset($fotoPath) && $fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }

            return back()->with('error', 'Terjadi kesalahan saat mengirim review: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show reviews for a specific product
     */
    public function showProductReviews($productId)
    {
        // Get product from database integrasi (UPDATED)
        $product = ProdukIntegrasi::find($productId);

        // Fallback to CRM database if not found in integrasi
        if (!$product) {
            $product = Product::findOrFail($productId);
        }

        if (!$product) {
            abort(404, 'Produk tidak ditemukan');
        }

        $reviews = Review::where('id_produk', $productId)
                        ->where('is_verified', true)
                        ->with(['pelanggan.user'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        // Calculate average rating and total reviews
        $totalReviews = $reviews->total();
        $averageRating = Review::where('id_produk', $productId)
                              ->where('is_verified', true)
                              ->avg('rating') ?? 0;

        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = Review::where('id_produk', $productId)
                          ->where('is_verified', true)
                          ->where('rating', $i)
                          ->count();
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        return view('pelanggan.product-reviews', compact(
            'product',
            'reviews',
            'averageRating',
            'totalReviews',
            'ratingDistribution'
        ));
    }

    /**
     * Show all reviews by the authenticated customer
     */
    public function myReviews()
    {
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        $reviews = Review::where('id_pelanggan', $pelanggan->id_pelanggan)
                        ->with(['product', 'transaction'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('pelanggan.my-reviews', compact('reviews'));
    }

    /**
     * Show a specific review
     */
    public function show($reviewId)
    {
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        $review = Review::where('id_review', $reviewId)
                       ->where('id_pelanggan', $pelanggan->id_pelanggan)
                       ->with(['product', 'transaction'])
                       ->firstOrFail();

        return view('pelanggan.review-show', compact('review'));
    }

    /**
     * Update a review
     */
    public function update(Request $request, $reviewId)
    {
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        $review = Review::where('id_review', $reviewId)
                       ->where('id_pelanggan', $pelanggan->id_pelanggan)
                       ->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
            'foto_review' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Handle photo upload if exists
            if ($request->hasFile('foto_review')) {
                // Delete old photo
                if ($review->foto_review && Storage::disk('public')->exists($review->foto_review)) {
                    Storage::disk('public')->delete($review->foto_review);
                }

                $file = $request->file('foto_review');
                $filename = 'review_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $validated['foto_review'] = $file->storeAs('reviews', $filename, 'public');
            }

            $review->update($validated);

            DB::commit();

            Log::info('Review updated', [
                'id_review' => $review->id_review,
                'id_pelanggan' => $pelanggan->id_pelanggan,
            ]);

            return redirect()->route('pelanggan.reviews.index')
                           ->with('success', 'Review berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating review: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memperbarui review')->withInput();
        }
    }

    /**
     * Delete a review
     */
    public function destroy($reviewId)
    {
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        $review = Review::where('id_review', $reviewId)
                       ->where('id_pelanggan', $pelanggan->id_pelanggan)
                       ->firstOrFail();

        try {
            DB::beginTransaction();

            // Delete photo if exists
            if ($review->foto_review && Storage::disk('public')->exists($review->foto_review)) {
                Storage::disk('public')->delete($review->foto_review);
            }

            $review->delete();

            DB::commit();

            Log::info('Review deleted', [
                'id_review' => $reviewId,
                'id_pelanggan' => $pelanggan->id_pelanggan,
            ]);

            return redirect()->route('pelanggan.reviews.index')
                           ->with('success', 'Review berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting review: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus review');
        }
    }
}
