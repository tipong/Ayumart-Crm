<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Integrasi\Produk as ProdukIntegrasi;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Transaction;
use App\Models\Membership;
use App\Models\CustomerAddress;
use App\Services\IntegrasiProdukService;
use App\Services\NearestBranchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PelangganController extends Controller
{
    protected $integrasiProdukService;
    protected $nearestBranchService;

    public function __construct(IntegrasiProdukService $integrasiProdukService, NearestBranchService $nearestBranchService)
    {
        $this->integrasiProdukService = $integrasiProdukService;
        $this->nearestBranchService = $nearestBranchService;
    }

    /**
     * Get or create pelanggan record for authenticated user
     * UPDATED: Use id_user (not user_id) after restructure
     */
    private function getOrCreatePelanggan($user)
    {
        // Try to get pelanggan by id_user
        $pelanggan = DB::table('tb_pelanggan')->where('id_user', $user->id_user)->first();

        // If pelanggan doesn't exist, create one automatically
        if (!$pelanggan) {
            $pelangganId = DB::table('tb_pelanggan')->insertGetId([
                'id_user' => $user->id_user,
                'nama_pelanggan' => $user->nama_lengkap,
                'no_tlp_pelanggan' => $user->no_telp ?? '',
                'alamat' => '',
                'status_pelanggan' => 'aktif',
            ]);

            $pelanggan = DB::table('tb_pelanggan')->where('id_pelanggan', $pelangganId)->first();
        }

        return $pelanggan;
    }

    /**
     * Display shopping cart
     */
    public function cart()
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        $cartItems = Cart::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->with('product')
            ->get();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            if ($item->product) {
                $subtotal += $item->getSubtotal();
            }
        }

        // Get membership discount - using user->membership relationship
        $membership = $user->membership;
        $discount = 0;
        if ($membership && $membership->is_active) {
            $discount = $subtotal * ($membership->discount_percentage / 100);
        }

        $total = $subtotal - $discount;

        return view('pelanggan.cart', compact('cartItems', 'subtotal', 'discount', 'total', 'membership'));
    }

    /**
     * Add product to cart
     * UPDATED: Langsung gunakan ID dari integrasi DB, TANPA sync ke CRM
     * ID produk yang disimpan di cart adalah ID dari db_integrasi_ayu_mart
     */
    public function addToCart(Request $request, $productId)
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        // Get product dari integrasi database
        $product = ProdukIntegrasi::where('id_produk', $productId)
            ->where('status_produk', 'aktif')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Get current branch dari session
        $branchId = $this->nearestBranchService->getNearestBranchId();

        // Check stock availability di cabang terpilih (gunakan integrasi product ID)
        $stok = $this->integrasiProdukService->getStokProdukCabang($productId, $branchId);
        $requestedQty = $request->input('qty', 1);

        if ($stok < $requestedQty) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Tersedia: {$stok}"
            ], 400);
        }

        // Check if product sudah ada di cart (gunakan ID INTEGRASI LANGSUNG)
        $cartItem = Cart::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('id_produk', $productId) // ID dari integrasi DB
            ->first();

        if ($cartItem) {
            // Check if new total quantity exceeds stock
            $newQty = $cartItem->qty + $requestedQty;
            if ($newQty > $stok) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi. Tersedia: {$stok}, di keranjang: {$cartItem->qty}"
                ], 400);
            }

            // Update quantity
            $cartItem->qty = $newQty;
            $cartItem->save();
        } else {
            // Create cart item baru (LANGSUNG PAKAI ID INTEGRASI, TIDAK PERLU SYNC!)
            Cart::create([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_produk' => $productId, // ID dari integrasi DB (BUKAN CRM!)
                'qty' => $requestedQty,
            ]);
        }

        $cartCount = Cart::where('id_pelanggan', $pelanggan->id_pelanggan)->count();

        Log::info('Product added to cart (Direct Integrasi - No Sync)', [
            'id_produk_integrasi' => $productId,
            'qty' => $requestedQty,
            'branch_id' => $branchId,
            'stock_available' => $stok,
            'no_sync_to_crm' => true // Tidak ada sync ke CRM lagi!
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Sync jenis (category) from integrasi database to CRM database
     *
     * @param int $idJenis ID jenis dari integrasi database
     * @return bool True jika jenis tersedia (sudah ada atau berhasil di-sync)
     */
    private function syncJenisToCRM($idJenis)
    {
        // Check if jenis already exists in CRM database
        $existingJenis = \App\Models\Jenis::where('id_jenis', $idJenis)->first();

        if ($existingJenis) {
            Log::debug('Jenis already exists in CRM database', [
                'id_jenis' => $idJenis,
                'nama_jenis' => $existingJenis->nama_jenis
            ]);
            return true;
        }

        // Get jenis from integrasi database
        $jenisIntegrasi = DB::connection('mysql_integrasi')
            ->table('tb_jenis')
            ->where('id_jenis', $idJenis)
            ->first();

        if (!$jenisIntegrasi) {
            Log::error('Jenis not found in integrasi database', [
                'id_jenis' => $idJenis
            ]);
            return false;
        }

        try {
            // Create jenis in CRM database with explicit ID
            DB::table('tb_jenis')->insert([
                'id_jenis' => $jenisIntegrasi->id_jenis,
                'nama_jenis' => $jenisIntegrasi->nama_jenis,
                'deskripsi_jenis' => $jenisIntegrasi->deskripsi_jenis ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Jenis synced to CRM database', [
                'id_jenis' => $jenisIntegrasi->id_jenis,
                'nama_jenis' => $jenisIntegrasi->nama_jenis
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to sync jenis to CRM database', [
                'id_jenis' => $idJenis,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Sync product from integrasi database to CRM database
     * This ensures foreign key constraint is satisfied for tb_detail_cart
     *
     * @param object $produkIntegrasi Product from integrasi database
     * @return int|null ID produk di CRM database, or null if sync fails
     * @throws \Exception If jenis sync fails or product cannot be created
     */
    private function syncProductToCRM($produkIntegrasi)
    {
        // First, sync the jenis (category) to ensure foreign key constraint is satisfied
        if ($produkIntegrasi->id_jenis) {
            $jenisSync = $this->syncJenisToCRM($produkIntegrasi->id_jenis);

            if (!$jenisSync) {
                $errorMsg = "Gagal sinkronisasi kategori produk (id_jenis: {$produkIntegrasi->id_jenis})";
                Log::error($errorMsg, [
                    'id_produk' => $produkIntegrasi->id_produk,
                    'nama_produk' => $produkIntegrasi->nama_produk,
                    'id_jenis' => $produkIntegrasi->id_jenis
                ]);
                throw new \Exception($errorMsg);
            }
        }

        // Check if product already exists in CRM database by:
        // 1. id_produk_integrasi (preferred - direct mapping)
        // 2. id_produk (if product was synced with same ID)
        // 3. kode_produk (fallback - for products created before integrasi mapping)
        $existingProduct = Product::where('id_produk_integrasi', $produkIntegrasi->id_produk)
            ->orWhere('id_produk', $produkIntegrasi->id_produk)
            ->orWhere('kode_produk', $produkIntegrasi->kode_produk)
            ->first();

        // Convert berat_produk to decimal (handle string like "500g", "1kg", etc)
        try {
            $beratProduk = $this->convertBeratToDecimal($produkIntegrasi->berat_produk);

            // Ensure it's a valid numeric value
            if (!is_numeric($beratProduk) || $beratProduk < 0) {
                Log::warning('Invalid berat_produk value', [
                    'id_produk' => $produkIntegrasi->id_produk,
                    'original_berat' => $produkIntegrasi->berat_produk,
                    'converted_berat' => $beratProduk
                ]);
                $beratProduk = 0.0;
            }
        } catch (\Exception $e) {
            Log::error('Error converting berat_produk', [
                'id_produk' => $produkIntegrasi->id_produk,
                'berat_produk' => $produkIntegrasi->berat_produk,
                'error' => $e->getMessage()
            ]);
            $beratProduk = 0.0;
        }

        if (!$existingProduct) {
            // Use kode_produk from integrasi
            $kodeProduk = $produkIntegrasi->kode_produk;

            // Create product in CRM database
            $newProduct = Product::create([
                'id_produk' => $produkIntegrasi->id_produk,
                'id_produk_integrasi' => $produkIntegrasi->id_produk, // Store integrasi ID
                'kode_produk' => $kodeProduk,
                'nama_produk' => $produkIntegrasi->nama_produk,
                'harga_produk' => $produkIntegrasi->harga_produk ?? 0,
                'harga_diskon' => $produkIntegrasi->harga_diskon ?? null,
                'persentase_diskon' => $produkIntegrasi->persentase_diskon ?? null,
                'tanggal_mulai_diskon' => $produkIntegrasi->tanggal_mulai_diskon ?? null,
                'tanggal_akhir_diskon' => $produkIntegrasi->tanggal_akhir_diskon ?? null,
                'is_diskon_active' => $produkIntegrasi->is_diskon_active ?? 0,
                'stok_produk' => 0, // We use stock from tb_stok_cabang, not this field
                'berat_produk' => $beratProduk,
                'deskripsi_produk' => $produkIntegrasi->deskripsi_produk ?? '',
                'foto_produk' => $produkIntegrasi->foto_produk ?? '',
                'id_jenis' => $produkIntegrasi->id_jenis,
                'status_produk' => $produkIntegrasi->status_produk ?? 'aktif',
                'created_at' => $produkIntegrasi->created_at ?? now(),
                'updated_at' => now(),
            ]);

            Log::info('Product synced to CRM database', [
                'id_produk' => $newProduct->id_produk,
                'kode_produk' => $kodeProduk,
                'nama_produk' => $produkIntegrasi->nama_produk,
                'berat_produk' => $beratProduk
            ]);

            // Return the CRM product ID
            return $newProduct->id_produk;
        } else {
            // Update existing product data
            // PENTING: JANGAN ubah id_produk yang sudah ada di CRM!
            // Tapi simpan id_produk_integrasi untuk mapping
            $existingProduct->update([
                // 'id_produk' => JANGAN DIUBAH - akan menyebabkan foreign key error
                'id_produk_integrasi' => $produkIntegrasi->id_produk, // Save integrasi ID mapping
                'kode_produk' => $produkIntegrasi->kode_produk,
                'nama_produk' => $produkIntegrasi->nama_produk,
                'harga_produk' => $produkIntegrasi->harga_produk ?? 0,
                'harga_diskon' => $produkIntegrasi->harga_diskon ?? null,
                'persentase_diskon' => $produkIntegrasi->persentase_diskon ?? null,
                'tanggal_mulai_diskon' => $produkIntegrasi->tanggal_mulai_diskon ?? null,
                'tanggal_akhir_diskon' => $produkIntegrasi->tanggal_akhir_diskon ?? null,
                'is_diskon_active' => $produkIntegrasi->is_diskon_active ?? 0,
                'berat_produk' => $beratProduk,
                'deskripsi_produk' => $produkIntegrasi->deskripsi_produk ?? '',
                'foto_produk' => $produkIntegrasi->foto_produk ?? '',
                'id_jenis' => $produkIntegrasi->id_jenis,
                'status_produk' => $produkIntegrasi->status_produk ?? 'aktif',
                'updated_at' => now(),
            ]);

            Log::info('Product updated in CRM database', [
                'crm_product_id' => $existingProduct->id_produk,
                'integrasi_product_id' => $produkIntegrasi->id_produk,
                'kode_produk' => $produkIntegrasi->kode_produk,
                'nama_produk' => $produkIntegrasi->nama_produk
            ]);

            // Return the existing CRM product ID (tidak berubah)
            return $existingProduct->id_produk;
        }
    }

    /**
     * Convert berat_produk string to decimal
     * Handles formats like: "500g", "1kg", "1.5", "500", "23 gram", "600 ml", etc
     */
    private function convertBeratToDecimal($berat)
    {
        if (empty($berat) || $berat === null) {
            return 0.0; // Return float instead of int
        }

        // If already numeric, return as float
        if (is_numeric($berat)) {
            return (float) $berat;
        }

        // Convert string to lowercase and trim
        $berat = strtolower(trim($berat));

        // Extract numeric value using regex (including decimals)
        preg_match('/([0-9.]+)/', $berat, $matches);

        if (empty($matches)) {
            return 0.0; // Return float instead of int
        }

        $numericValue = (float) $matches[0];

        // Convert kg to grams for consistency
        if (strpos($berat, 'kg') !== false) {
            $numericValue = $numericValue * 1000; // Convert kg to grams
        }

        // Note: We store everything in grams
        // "gram", "g", "ml" are already in base unit

        return $numericValue;
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request, $cartItemId)
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        $cartItem = Cart::where('id_detail_cart', $cartItemId)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        $cartItem->qty = $request->input('qty', 1);
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diupdate'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($cartItemId)
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        $cartItem = Cart::where('id_detail_cart', $cartItemId)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang'
        ]);
    }

    /**
     * Display wishlist
     */
    public function wishlist()
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        $wishlistItems = Wishlist::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->with(['produkIntegrasi', 'product'])
            ->get();

        return view('pelanggan.wishlist', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist
     * UPDATED: Support products from db_integrasi_ayu_mart
     */
    public function addToWishlist($productId)
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        // Get product from integrasi database
        $product = ProdukIntegrasi::where('id_produk', $productId)
            ->where('status_produk', 'aktif')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Check if already in wishlist
        $exists = Wishlist::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('id_produk', $product->id_produk)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah ada di wishlist'
            ]);
        }

        Wishlist::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_produk' => $product->id_produk,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke wishlist'
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist($wishlistId)
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        $wishlistItem = Wishlist::where('id_wishlist', $wishlistId)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        if (!$wishlistItem) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari wishlist'
        ]);
    }

    /**
     * Display customer profile
     */
    public function profile()
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        // Get all addresses for the customer
        $addresses = CustomerAddress::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelanggan.profile', compact('addresses', 'pelanggan'));
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // Update email in users table (only email field exists in users table)
            DB::table('users')
                ->where('id_user', $user->id_user)
                ->update([
                    'email' => $validatedData['email']
                ]);

            // Update profile in tb_pelanggan table
            // Note: Only nama_pelanggan and no_tlp_pelanggan are updated here
            // Address management is handled via "Daftar Alamat Tersimpan"
            DB::table('tb_pelanggan')
                ->where('id_user', $user->id_user)
                ->update([
                    'nama_pelanggan' => $validatedData['name'],
                    'no_tlp_pelanggan' => $validatedData['phone'],
                ]);

            return redirect()->route('pelanggan.profile')
                           ->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui profil')->withInput();
        }
    }

    /**
     * Update password only
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai'])->withInput();
            }

            // Update password (only password field exists in users table)
            DB::table('users')
                ->where('id_user', $user->id_user)
                ->update([
                    'password' => Hash::make($request->password)
                ]);

            return redirect()->route('pelanggan.profile')
                           ->with('success', 'Password berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui password')->withInput();
        }
    }

    /**
     * Display membership info
     */
    public function membership()
    {
        $user = Auth::user();
        $pelanggan = $this->getOrCreatePelanggan($user);

        // Get membership from user relationship
        $membership = $user->membership;

        // Calculate total spent from paid orders (sum of total_harga from all paid transactions)
        $totalSpent = DB::table('tb_transaksi')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('total_harga');

        $totalSpent = $totalSpent ?? 0;

        // Calculate next tier
        $nextTier = null;
        $progressToNext = 0;

        if ($membership) {
            if ($membership->tier === 'bronze') {
                $nextTier = 'silver';
                $required = 101; // Need 101 points to reach Silver
                $progressToNext = min(100, ($membership->points / $required) * 100);
            } elseif ($membership->tier === 'silver') {
                $nextTier = 'gold';
                $required = 251; // Need 251 points to reach Gold
                $progressToNext = min(100, ($membership->points / $required) * 100);
            } elseif ($membership->tier === 'gold') {
                $nextTier = 'platinum';
                $required = 401; // Need 401 points to reach Platinum
                $progressToNext = min(100, ($membership->points / $required) * 100);
            }
        }

        return view('pelanggan.membership', compact('membership', 'nextTier', 'progressToNext', 'totalSpent'));
    }
}
