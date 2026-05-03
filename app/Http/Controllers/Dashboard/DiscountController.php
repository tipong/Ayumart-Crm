<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Integrasi\Produk as ProdukIntegrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscountController extends Controller
{
    /**
     * Display a listing of products with discounts from integrasi database
     */
    public function index()
    {
        try {
            // Auto-deactivate expired discounts
            $expiredCount = ProdukIntegrasi::autoDeactivateAllExpired();
            if ($expiredCount > 0) {
                Log::info("Auto-deactivated $expiredCount expired discount(s)");
            }

            // Get all products from integrasi database with their discount information
            $products = ProdukIntegrasi::with('jenis')
                ->where('status_produk', 'aktif')
                ->orderBy('nama_produk')
                ->paginate(15);

            // Get statistics for all products (not just paginated)
            $totalActiveDiscounts = ProdukIntegrasi::where('is_diskon_active', true)
                ->where('status_produk', 'aktif')
                ->count();
            $totalInactiveDiscounts = ProdukIntegrasi::whereNotNull('harga_diskon')
                ->where('is_diskon_active', false)
                ->where('status_produk', 'aktif')
                ->count();
            $totalNoDiscounts = ProdukIntegrasi::whereNull('harga_diskon')
                ->where('status_produk', 'aktif')
                ->count();

            return view('admin.discounts.index', compact(
                'products',
                'totalActiveDiscounts',
                'totalInactiveDiscounts',
                'totalNoDiscounts'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading discounts from integrasi DB: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat data diskon.');
        }
    }

    /**
     * Show form to add discount to a product
     */
    public function create($productId)
    {
        try {
            $product = ProdukIntegrasi::with('jenis', 'stokCabang')->findOrFail($productId);
            return view('admin.discounts.create', compact('product'));
        } catch (\Exception $e) {
            Log::error('Error loading discount create form: ' . $e->getMessage());
            return back()->with('error', 'Produk tidak ditemukan.');
        }
    }

    /**
     * Store discount for a product in integrasi database
     */
    public function store(Request $request, $productId)
    {
        try {
            $validated = $request->validate([
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $product = ProdukIntegrasi::findOrFail($productId);

            // Calculate discount price based on type
            if ($validated['discount_type'] === 'percentage') {
                $discountPercentage = min($validated['discount_value'], 100); // Max 100%
                $discountPrice = $product->harga_produk - ($product->harga_produk * ($discountPercentage / 100));
            } else {
                // Fixed amount discount
                $discountPrice = max($product->harga_produk - $validated['discount_value'], 0);
                $discountPercentage = (($product->harga_produk - $discountPrice) / $product->harga_produk) * 100;
            }

            // Update product with discount in integrasi database
            $product->update([
                'harga_diskon' => $discountPrice,
                'persentase_diskon' => $discountPercentage,
                'tanggal_mulai_diskon' => $validated['start_date'],
                'tanggal_akhir_diskon' => $validated['end_date'],
                'is_diskon_active' => true,
            ]);

            Log::info('Discount added to integrasi product', [
                'product_id' => $productId,
                'discount_percentage' => $discountPercentage,
                'discount_price' => $discountPrice
            ]);

            return redirect()->route('admin.discounts.index')
                ->with('success', 'Diskon berhasil ditambahkan untuk produk ' . $product->nama_produk);
        } catch (\Exception $e) {
            Log::error('Error storing discount in integrasi DB: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan diskon: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit discount
     */
    public function edit($productId)
    {
        try {
            $product = ProdukIntegrasi::with('jenis', 'stokCabang')->findOrFail($productId);

            if (!$product->harga_diskon) {
                return redirect()->route('admin.discounts.index')
                    ->with('error', 'Produk ini tidak memiliki diskon.');
            }

            return view('admin.discounts.edit', compact('product'));
        } catch (\Exception $e) {
            Log::error('Error loading discount edit form from integrasi DB: ' . $e->getMessage());
            return back()->with('error', 'Produk tidak ditemukan.');
        }
    }

    /**
     * Update discount for a product in integrasi database
     */
    public function update(Request $request, $productId)
    {
        try {
            // Log incoming request for debugging
            Log::info('🔄 Discount update request received', [
                'product_id' => $productId,
                'request_data' => $request->all(),
                'has_is_active' => $request->has('is_active')
            ]);

            $validated = $request->validate([
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'is_active' => 'nullable|boolean',
            ]);

            Log::info('✅ Validation passed', ['validated_data' => $validated]);

            $product = ProdukIntegrasi::findOrFail($productId);

            Log::info('📦 Product found', [
                'product_id' => $product->id_produk,
                'product_name' => $product->nama_produk,
                'current_price' => $product->harga_produk,
                'current_discount' => $product->harga_diskon
            ]);

            // Calculate discount price based on type
            if ($validated['discount_type'] === 'percentage') {
                $discountPercentage = min($validated['discount_value'], 100);
                $discountPrice = $product->harga_produk - ($product->harga_produk * ($discountPercentage / 100));
            } else {
                $discountPrice = max($product->harga_produk - $validated['discount_value'], 0);
                $discountPercentage = (($product->harga_produk - $discountPrice) / $product->harga_produk) * 100;
            }

            Log::info('💰 Discount calculated', [
                'discount_type' => $validated['discount_type'],
                'discount_value' => $validated['discount_value'],
                'discount_percentage' => $discountPercentage,
                'discount_price' => $discountPrice
            ]);

            // Update product with discount in integrasi database
            $updateData = [
                'harga_diskon' => $discountPrice,
                'persentase_diskon' => $discountPercentage,
                'tanggal_mulai_diskon' => $validated['start_date'],
                'tanggal_akhir_diskon' => $validated['end_date'],
                'is_diskon_active' => (bool)($request->input('is_active', 0)),
            ];

            Log::info('📝 Attempting to update with data', ['update_data' => $updateData]);

            $result = $product->update($updateData);

            Log::info('✅ Update result', [
                'success' => $result,
                'product_id' => $product->id_produk
            ]);

            // Refresh product to verify update
            $product->refresh();

            Log::info('🔍 Product after update', [
                'harga_diskon' => $product->harga_diskon,
                'persentase_diskon' => $product->persentase_diskon,
                'is_diskon_active' => $product->is_diskon_active
            ]);

            return redirect()->route('admin.discounts.index')
                ->with('success', 'Diskon berhasil diperbarui untuk produk ' . $product->nama_produk);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed', [
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('❌ Error updating discount in integrasi DB', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Gagal memperbarui diskon: ' . $e->getMessage());
        }
    }

    /**
     * Remove discount from a product in integrasi database
     */
    public function destroy($productId)
    {
        try {
            $product = ProdukIntegrasi::findOrFail($productId);

            // Remove discount from integrasi database
            $product->update([
                'harga_diskon' => null,
                'persentase_diskon' => null,
                'tanggal_mulai_diskon' => null,
                'tanggal_akhir_diskon' => null,
                'is_diskon_active' => false,
            ]);

            Log::info('Discount removed from integrasi product', ['product_id' => $productId]);

            return redirect()->route('admin.discounts.index')
                ->with('success', 'Diskon berhasil dihapus dari produk ' . $product->nama_produk);
        } catch (\Exception $e) {
            Log::error('Error deleting discount from integrasi DB: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus diskon: ' . $e->getMessage());
        }
    }

    /**
     * Toggle discount active status in integrasi database
     */
    public function toggleStatus($productId)
    {
        try {
            $product = ProdukIntegrasi::findOrFail($productId);

            $product->update([
                'is_diskon_active' => !$product->is_diskon_active,
            ]);

            $status = $product->is_diskon_active ? 'diaktifkan' : 'dinonaktifkan';

            Log::info('Discount status toggled in integrasi DB', [
                'product_id' => $productId,
                'is_active' => $product->is_diskon_active
            ]);

            return back()->with('success', "Diskon berhasil $status untuk produk " . $product->nama_produk);
        } catch (\Exception $e) {
            Log::error('Error toggling discount status in integrasi DB: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status diskon.');
        }
    }

    /**
     * Show member discount form for a product
     */
    public function editMemberDiscount($productId)
    {
        try {
            $product = ProdukIntegrasi::with('jenis', 'memberDiscounts')->findOrFail($productId);

            $tiers = \App\Models\ProductMemberDiscount::TIERS;

            $discounts = \App\Models\ProductMemberDiscount::where('product_id', $productId)
                ->get()
                ->keyBy('tier');

            return view('admin.discounts.member-discount', compact('product', 'tiers', 'discounts'));
        } catch (\Exception $e) {
            Log::error('Error loading member discount form: ' . $e->getMessage());
            return back()->with('error', 'Produk tidak ditemukan.');
        }
    }

    /**
     * Update member discounts for a product
     */
    public function updateMemberDiscount(Request $request, $productId)
    {
        try {
            $validated = $request->validate([
                'discounts' => 'required|array',
                'discounts.*.tier' => 'required|string|in:bronze,silver,gold,platinum',
                'discounts.*.discount_percentage' => 'required|numeric|min:0|max:100',
                'discounts.*.is_active' => 'boolean',
            ]);

            $product = ProdukIntegrasi::findOrFail($productId);

            // Update or create member discounts for each tier
            foreach ($validated['discounts'] as $discount) {
                \App\Models\ProductMemberDiscount::updateOrCreate(
                    ['product_id' => $productId, 'tier' => $discount['tier']],
                    [
                        'discount_percentage' => $discount['discount_percentage'],
                        'is_active' => $discount['is_active'] ?? true,
                    ]
                );
            }

            return back()->with('success', 'Diskon member untuk produk berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating member discount: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui diskon member.');
        }
    }
}
