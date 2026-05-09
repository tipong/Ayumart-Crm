<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Integrasi\Produk as ProdukIntegrasi;
use App\Models\ProductMemberDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscountController extends Controller
{
    /**
     * Tampilkan daftar produk beserta info diskon dari database integrasi.
     */
    public function index()
    {
        try {
            // Auto-nonaktifkan diskon yang sudah kedaluwarsa
            $expiredCount = ProdukIntegrasi::autoDeactivateAllExpired();
            if ($expiredCount > 0) {
                Log::info("Auto-nonaktifkan $expiredCount diskon yang kedaluwarsa");
            }

            $products = ProdukIntegrasi::with('jenis')
                ->where('status_produk', 'aktif')
                ->orderBy('nama_produk')
                ->paginate(15);

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
            Log::error('Gagal memuat data diskon dari DB integrasi: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat data diskon.');
        }
    }

    /**
     * Tampilkan form tambah diskon untuk sebuah produk.
     */
    public function create($productId)
    {
        try {
            $product = ProdukIntegrasi::with('jenis', 'stokCabang')->findOrFail($productId);
            return view('admin.discounts.create', compact('product'));
        } catch (\Exception $e) {
            Log::error('Gagal memuat form tambah diskon: ' . $e->getMessage());
            return back()->with('error', 'Produk tidak ditemukan.');
        }
    }

    /**
     * Simpan diskon baru untuk sebuah produk.
     * Mendukung dua target: 'general' (semua pelanggan) dan 'tier' (per tier member).
     */
    public function store(Request $request, $productId)
    {
        try {
            // Validasi dasar
            $validated = $request->validate([
                'discount_target' => 'required|in:general,tier',
                'start_date'      => 'required|date',
                'end_date'        => 'required|date|after_or_equal:start_date',

                // Validasi jika target = general
                'discount_type'   => 'required_if:discount_target,general|nullable|in:percentage,fixed',
                'discount_value'  => 'required_if:discount_target,general|nullable|numeric|min:0',

                // Validasi jika target = tier
                'tier_discounts'                    => 'required_if:discount_target,tier|nullable|array',
                'tier_discounts.*.tier'              => 'required_if:discount_target,tier|string|in:bronze,silver,gold,platinum',
                'tier_discounts.*.discount_percentage' => 'required_if:discount_target,tier|numeric|min:0|max:100',
                'tier_discounts.*.is_active'         => 'boolean',
            ], [
                'discount_target.required'   => 'Target diskon harus dipilih.',
                'discount_target.in'         => 'Target diskon tidak valid.',
                'discount_type.required_if'  => 'Tipe diskon harus dipilih untuk diskon umum.',
                'discount_value.required_if' => 'Nilai diskon harus diisi untuk diskon umum.',
                'start_date.required'        => 'Tanggal mulai harus diisi.',
                'end_date.required'          => 'Tanggal berakhir harus diisi.',
                'end_date.after_or_equal'    => 'Tanggal berakhir tidak boleh lebih awal dari tanggal mulai.',
            ]);

            $product = ProdukIntegrasi::findOrFail($productId);

            if ($validated['discount_target'] === 'general') {
                // ---- DISKON UMUM ----
                $this->applyGeneralDiscount($product, $validated);

                // Hapus diskon tier yang mungkin ada sebelumnya
                ProductMemberDiscount::where('product_id', $productId)->update(['is_active' => false]);

                Log::info('Diskon umum berhasil ditambahkan', [
                    'product_id' => $productId,
                    'target'     => 'general',
                ]);

                return redirect()->route('admin.discounts.index')
                    ->with('success', 'Diskon umum berhasil ditambahkan untuk produk ' . $product->nama_produk);

            } else {
                // ---- DISKON PER TIER ----
                // Untuk diskon tier, simpan placeholder di produk agar terdeteksi sebagai "ada diskon"
                // Nilai harga_diskon dan persentase_diskon = null (tidak digunakan, hanya tier yang berlaku)
                $product->update([
                    'harga_diskon'          => null,
                    'persentase_diskon'     => null,
                    'tanggal_mulai_diskon'  => $validated['start_date'],
                    'tanggal_akhir_diskon'  => $validated['end_date'],
                    'is_diskon_active'      => true,
                    'discount_target'       => 'tier',
                ]);

                // Simpan diskon per tier
                if (!empty($validated['tier_discounts'])) {
                    foreach ($validated['tier_discounts'] as $tierDiscount) {
                        ProductMemberDiscount::updateOrCreate(
                            ['product_id' => $productId, 'tier' => $tierDiscount['tier']],
                            [
                                'discount_percentage' => $tierDiscount['discount_percentage'],
                                'is_active'           => $tierDiscount['is_active'] ?? true,
                            ]
                        );
                    }
                }

                Log::info('Diskon tier berhasil ditambahkan', [
                    'product_id'    => $productId,
                    'target'        => 'tier',
                    'tier_discounts' => $validated['tier_discounts'] ?? [],
                ]);

                return redirect()->route('admin.discounts.index')
                    ->with('success', 'Diskon per tier berhasil ditambahkan untuk produk ' . $product->nama_produk);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan diskon: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Gagal menambahkan diskon: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit diskon.
     */
    public function edit($productId)
    {
        try {
            $product = ProdukIntegrasi::with('jenis', 'stokCabang')->findOrFail($productId);

            // Cek apakah produk memiliki diskon (general atau tier)
            $hasTierDiscount = ProductMemberDiscount::where('product_id', $productId)->exists();
            $hasGeneralDiscount = !is_null($product->harga_diskon);
            $hasTierTarget = $product->discount_target === 'tier' && $product->is_diskon_active;

            if (!$hasGeneralDiscount && !$hasTierDiscount && !$hasTierTarget) {
                return redirect()->route('admin.discounts.index')
                    ->with('error', 'Produk ini belum memiliki diskon. Silakan tambah diskon terlebih dahulu.');
            }

            return view('admin.discounts.edit', compact('product'));
        } catch (\Exception $e) {
            Log::error('Gagal memuat form edit diskon: ' . $e->getMessage());
            return back()->with('error', 'Produk tidak ditemukan.');
        }
    }

    /**
     * Perbarui diskon produk.
     * Mendukung target 'general' dan 'tier'.
     */
    public function update(Request $request, $productId)
    {
        try {
            Log::info('🔄 Permintaan update diskon diterima', [
                'product_id'   => $productId,
                'request_data' => $request->all(),
            ]);

            $validated = $request->validate([
                'discount_target' => 'required|in:general,tier',
                'start_date'      => 'required|date',
                'end_date'        => 'required|date|after_or_equal:start_date',
                'is_active'       => 'nullable|boolean',

                'discount_type'   => 'required_if:discount_target,general|nullable|in:percentage,fixed',
                'discount_value'  => 'required_if:discount_target,general|nullable|numeric|min:0',

                'tier_discounts'                       => 'required_if:discount_target,tier|nullable|array',
                'tier_discounts.*.tier'                => 'required_if:discount_target,tier|string|in:bronze,silver,gold,platinum',
                'tier_discounts.*.discount_percentage' => 'required_if:discount_target,tier|numeric|min:0|max:100',
                'tier_discounts.*.is_active'           => 'boolean',
            ], [
                'discount_target.required'   => 'Target diskon harus dipilih.',
                'discount_type.required_if'  => 'Tipe diskon harus dipilih untuk diskon umum.',
                'discount_value.required_if' => 'Nilai diskon harus diisi untuk diskon umum.',
                'start_date.required'        => 'Tanggal mulai harus diisi.',
                'end_date.required'          => 'Tanggal berakhir harus diisi.',
                'end_date.after_or_equal'    => 'Tanggal berakhir tidak boleh lebih awal dari tanggal mulai.',
            ]);

            $product  = ProdukIntegrasi::findOrFail($productId);
            $isActive = (bool) ($request->input('is_active', 0));

            if ($validated['discount_target'] === 'general') {
                // ---- UPDATE DISKON UMUM ----
                $this->applyGeneralDiscount($product, $validated, $isActive);

                // Nonaktifkan diskon tier
                ProductMemberDiscount::where('product_id', $productId)->update(['is_active' => false]);

                Log::info('✅ Diskon umum berhasil diperbarui', ['product_id' => $productId]);

                return redirect()->route('admin.discounts.index')
                    ->with('success', 'Diskon umum berhasil diperbarui untuk produk ' . $product->nama_produk);

            } else {
                // ---- UPDATE DISKON PER TIER ----
                $product->update([
                    'harga_diskon'         => null,
                    'persentase_diskon'    => null,
                    'tanggal_mulai_diskon' => $validated['start_date'],
                    'tanggal_akhir_diskon' => $validated['end_date'],
                    'is_diskon_active'     => $isActive,
                    'discount_target'      => 'tier',
                ]);

                if (!empty($validated['tier_discounts'])) {
                    foreach ($validated['tier_discounts'] as $tierDiscount) {
                        ProductMemberDiscount::updateOrCreate(
                            ['product_id' => $productId, 'tier' => $tierDiscount['tier']],
                            [
                                'discount_percentage' => $tierDiscount['discount_percentage'],
                                'is_active'           => $tierDiscount['is_active'] ?? true,
                            ]
                        );
                    }
                }

                Log::info('✅ Diskon tier berhasil diperbarui', ['product_id' => $productId]);

                return redirect()->route('admin.discounts.index')
                    ->with('success', 'Diskon per tier berhasil diperbarui untuk produk ' . $product->nama_produk);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validasi gagal', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('❌ Gagal memperbarui diskon', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Gagal memperbarui diskon: ' . $e->getMessage());
        }
    }

    /**
     * Helper: terapkan diskon umum ke produk.
     */
    private function applyGeneralDiscount(ProdukIntegrasi $product, array $validated, bool $isActive = true): void
    {
        if ($validated['discount_type'] === 'percentage') {
            $discountPercentage = min($validated['discount_value'], 100);
            $discountPrice      = $product->harga_produk - ($product->harga_produk * ($discountPercentage / 100));
        } else {
            $discountPrice      = max($product->harga_produk - $validated['discount_value'], 0);
            $discountPercentage = (($product->harga_produk - $discountPrice) / $product->harga_produk) * 100;
        }

        $product->update([
            'harga_diskon'         => $discountPrice,
            'persentase_diskon'    => $discountPercentage,
            'tanggal_mulai_diskon' => $validated['start_date'],
            'tanggal_akhir_diskon' => $validated['end_date'],
            'is_diskon_active'     => $isActive,
            'discount_target'      => 'general',
        ]);
    }

    /**
     * Hapus diskon dari sebuah produk.
     */
    public function destroy($productId)
    {
        try {
            $product = ProdukIntegrasi::findOrFail($productId);

            $product->update([
                'harga_diskon'         => null,
                'persentase_diskon'    => null,
                'tanggal_mulai_diskon' => null,
                'tanggal_akhir_diskon' => null,
                'is_diskon_active'     => false,
                'discount_target'      => null,
            ]);

            // Juga nonaktifkan diskon tier jika ada
            ProductMemberDiscount::where('product_id', $productId)->update(['is_active' => false]);

            Log::info('Diskon berhasil dihapus dari produk', ['product_id' => $productId]);

            return redirect()->route('admin.discounts.index')
                ->with('success', 'Diskon berhasil dihapus dari produk ' . $product->nama_produk);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus diskon: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus diskon: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif diskon.
     */
    public function toggleStatus($productId)
    {
        try {
            $product = ProdukIntegrasi::findOrFail($productId);

            $product->update([
                'is_diskon_active' => !$product->is_diskon_active,
            ]);

            $status = $product->is_diskon_active ? 'diaktifkan' : 'dinonaktifkan';

            Log::info('Status diskon diubah', [
                'product_id' => $productId,
                'is_active'  => $product->is_diskon_active,
            ]);

            return back()->with('success', "Diskon berhasil $status untuk produk " . $product->nama_produk);
        } catch (\Exception $e) {
            Log::error('Gagal mengubah status diskon: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengubah status diskon.');
        }
    }

    /**
     * Tampilkan form edit diskon member tier (standalone page).
     */
    public function editMemberDiscount($productId)
    {
        try {
            $product = ProdukIntegrasi::with('jenis', 'memberDiscounts')->findOrFail($productId);

            $tiers     = ProductMemberDiscount::TIERS;
            $discounts = ProductMemberDiscount::where('product_id', $productId)
                ->get()
                ->keyBy('tier');

            return view('admin.discounts.member-discount', compact('product', 'tiers', 'discounts'));
        } catch (\Exception $e) {
            Log::error('Gagal memuat form edit diskon member: ' . $e->getMessage());
            return back()->with('error', 'Produk tidak ditemukan.');
        }
    }

    /**
     * Perbarui diskon member tier untuk sebuah produk (standalone form).
     */
    public function updateMemberDiscount(Request $request, $productId)
    {
        try {
            $validated = $request->validate([
                'discounts'                       => 'required|array',
                'discounts.*.tier'                => 'required|string|in:bronze,silver,gold,platinum',
                'discounts.*.discount_percentage' => 'required|numeric|min:0|max:100',
                'discounts.*.is_active'           => 'boolean',
            ]);

            $product = ProdukIntegrasi::findOrFail($productId);

            foreach ($validated['discounts'] as $discount) {
                ProductMemberDiscount::updateOrCreate(
                    ['product_id' => $productId, 'tier' => $discount['tier']],
                    [
                        'discount_percentage' => $discount['discount_percentage'],
                        'is_active'           => $discount['is_active'] ?? true,
                    ]
                );
            }

            return back()->with('success', 'Diskon member untuk produk berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui diskon member: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui diskon member.');
        }
    }
}
