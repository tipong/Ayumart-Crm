<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\Membership;
use App\Models\Product;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Cabang;
use App\Services\IntegrasiProdukService;
use App\Services\PaymentUpdateService;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    /**
     * Display checkout page
     */
    public function checkout()
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        // Load cart items dengan eager loading relasi product (dari integrasi DB)
        $cartItems = Cart::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->with('product') // Eager load relasi ke ProdukIntegrasi
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('pelanggan.cart')->with('error', 'Keranjang belanja Anda kosong');
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            // Gunakan relasi product yang sudah di-load
            if ($item->product) {
                $subtotal += $item->getSubtotal();
            }
        }

        // Get membership discount
        $membership = $user->membership;
        $discount = 0;
        if ($membership && $membership->is_active && $membership->isValid()) {
            $discount = $subtotal * ($membership->discount_percentage / 100);
        }

        // Check if user needs to pay membership fee
        // User needs to pay membership fee ONLY if:
        // 1. This is their first transaction AND
        // 2. They don't have a membership record yet
        $membershipFee = 0;
        $isFirstTransaction = !Order::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('status_pembayaran', 'sudah_bayar')
            ->exists();

        // Check membership using user_id (not id_pelanggan)
        $hasMembership = Membership::where('user_id', $user->id)->exists();

        // Apply membership fee if BOTH conditions are true
        if ($isFirstTransaction && !$hasMembership) {
            $membershipFee = 10000; // Biaya pembuatan member Rp 10.000
        }

        $total = $subtotal - $discount + $membershipFee;

        // Calculate points that will be earned (1 point per 20k)
        $pointsToEarn = Membership::calculatePoints($total);

        // Get customer addresses
        $addresses = CustomerAddress::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelanggan.checkout', compact(
            'cartItems',
            'subtotal',
            'discount',
            'total',
            'membership',
            'pointsToEarn',
            'pelanggan',
            'addresses',
            'membershipFee',
            'isFirstTransaction',
            'hasMembership'
        ));
    }

    /**
     * Place order and award membership points
     */
    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        try {
            DB::beginTransaction();

            // Validate cart is not empty - EAGER LOAD PRODUCT RELATION
            $cartItems = Cart::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->with('product') // Eager load produk dari integrasi DB
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('pelanggan.cart')->with('error', 'Keranjang belanja Anda kosong');
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                // Gunakan relasi product yang sudah di-load
                if ($item->product) {
                    $subtotal += $item->getSubtotal();
                }
            }

            // Get membership discount
            $membership = $user->membership;
            $discount = 0;
            if ($membership && $membership->is_active && $membership->isValid()) {
                $discount = $subtotal * ($membership->discount_percentage / 100);

                Log::info('💎 Membership Discount Applied', [
                    'user_id' => $user->id,
                    'membership_tier' => $membership->tier,
                    'discount_percentage' => $membership->discount_percentage,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount
                ]);
            } else {
                Log::info('No membership discount', [
                    'user_id' => $user->id,
                    'has_membership' => $membership ? 'Yes' : 'No',
                    'is_active' => $membership ? ($membership->is_active ? 'Yes' : 'No') : 'N/A',
                    'is_valid' => $membership ? ($membership->isValid() ? 'Yes' : 'No') : 'N/A'
                ]);
            }

            // Validate request
            $validationRules = [
                'metode_pengiriman' => 'required|string|in:kurir,ambil_sendiri',
                'catatan' => 'nullable|string|max:500',
                'shipping_cost' => 'nullable|numeric|min:0', // ✅ NEW: Accept shipping cost from frontend
            ];

            // Add address validation only if shipping method is kurir
            if ($request->metode_pengiriman === 'kurir') {
                $validationRules['address_id'] = 'required|exists:customer_addresses,id';
            }

            // Validate id_cabang if provided (for ambil_sendiri method)
            if ($request->has('id_cabang') && $request->id_cabang) {
                $validationRules['id_cabang'] = 'exists:tb_cabang,id_cabang';
            }

            $validated = $request->validate($validationRules);

            // Calculate shipping cost
            $ongkir = 0;
            $addressId = null;
            $alamatPengiriman = null;
            $shippingData = null;
            $nearestBranch = null;
            $idCabang = null;

            if ($validated['metode_pengiriman'] === 'kurir') {
                if (!$request->address_id) {
                    return back()->with('error', 'Silakan pilih alamat pengiriman terlebih dahulu')->withInput();
                }

                $address = CustomerAddress::find($request->address_id);
                if (!$address || $address->id_pelanggan != $pelanggan->id_pelanggan) {
                    return back()->with('error', 'Alamat pengiriman tidak valid')->withInput();
                }

                // ✅ PRIORITAS 1: Use shipping_cost from frontend form (sent by checkout.blade.php)
                if ($request->has('shipping_cost') && $request->shipping_cost > 0) {
                    $ongkir = (int) $request->shipping_cost;
                    Log::info("✅ KURIR: Using shipping_cost from frontend form", [
                        'ongkir' => $ongkir,
                        'address_id' => $address->id
                    ]);
                } else {
                    // FALLBACK: Calculate shipping cost if not provided from frontend
                    Log::warning("⚠️ KURIR: shipping_cost not from frontend, calculating from backend", [
                        'address_id' => $address->id
                    ]);
                    $ongkir = 15000;
                }

                // ✅ Get id_cabang from form/session (user's selected branch)
                if ($request->has('id_cabang') && $request->id_cabang) {
                    $nearestBranch = Cabang::find($request->id_cabang);
                    if ($nearestBranch && $nearestBranch->is_active) {
                        $idCabang = $nearestBranch->id_cabang;
                        Log::info("✅ KURIR: Using branch from form/session", [
                            'id_cabang' => $idCabang,
                            'nama_cabang' => $nearestBranch->nama_cabang
                        ]);
                    } else {
                        Log::warning("⚠️ KURIR: Branch from form not found or inactive", [
                            'requested_id_cabang' => $request->id_cabang
                        ]);
                    }
                }

                // ✅ FALLBACK: If no valid branch from form, try to get from session
                if ((!isset($idCabang) || !$idCabang)) {
                    $sessionBranch = session('nearest_branch');
                    if ($sessionBranch && isset($sessionBranch['id_cabang'])) {
                        $nearestBranch = Cabang::find($sessionBranch['id_cabang']);
                        if ($nearestBranch && $nearestBranch->is_active) {
                            $idCabang = $nearestBranch->id_cabang;
                            Log::info("✅ KURIR: Using branch from session", [
                                'id_cabang' => $idCabang,
                                'nama_cabang' => $nearestBranch->nama_cabang
                            ]);
                        }
                    }
                }

                // ⚠️ LAST RESORT: Use first active branch as fallback
                if ((!isset($idCabang) || !$idCabang)) {
                    $nearestBranch = Cabang::where('is_active', true)->first();
                    if ($nearestBranch) {
                        $idCabang = $nearestBranch->id_cabang;
                        Log::warning("⚠️ KURIR: Using default/first branch (no form/session)", [
                            'id_cabang' => $idCabang,
                            'nama_cabang' => $nearestBranch->nama_cabang
                        ]);
                    } else {
                        DB::rollBack();
                        return back()->with('error', 'Tidak ada cabang aktif tersedia')->withInput();
                    }
                }

                $addressId = $address->id;
                $alamatPengiriman = $address->formatted_address;

                // Prepare shipping data for courier system
                $shippingData = [
                    'order_code' => null, // Will be set after order creation
                    'customer_name' => $address->nama_penerima,
                    'customer_phone' => $address->no_telp_penerima,
                    'address' => $address->alamat_lengkap,
                    'city' => $address->kota,
                    'district' => $address->kecamatan,
                    'postal_code' => $address->kode_pos,
                    'latitude' => $address->latitude,
                    'longitude' => $address->longitude,
                    'notes' => $validated['catatan'] ?? null,
                ];
            } else {
                // Pickup at store
                // PRIORITAS 1: Gunakan id_cabang yang dikirim dari form (pilihan user di checkout)
                if ($request->has('id_cabang') && $request->id_cabang) {
                    $nearestBranch = Cabang::find($request->id_cabang);
                    if ($nearestBranch && $nearestBranch->is_active) {
                        $idCabang = $nearestBranch->id_cabang;
                        Log::info("✅ PICKUP: Using branch from form input", [
                            'form_id_cabang' => $request->id_cabang,
                            'id_cabang' => $idCabang,
                            'nama_cabang' => $nearestBranch->nama_cabang
                        ]);
                    } else {
                        Log::warning("⚠️ PICKUP: Branch from form not found or inactive", [
                            'form_id_cabang' => $request->id_cabang
                        ]);
                    }
                }

                // PRIORITAS 2: Gunakan cabang dari SESSION (pilihan user di home page)
                if ((!isset($idCabang) || !$idCabang)) {
                    $sessionBranch = session('nearest_branch');

                    if ($sessionBranch && isset($sessionBranch['id_cabang'])) {
                        $nearestBranch = Cabang::find($sessionBranch['id_cabang']);
                        if ($nearestBranch && $nearestBranch->is_active) {
                            $idCabang = $nearestBranch->id_cabang;
                            Log::info("✅ PICKUP: Using branch from session", [
                                'session_branch' => $sessionBranch,
                                'id_cabang' => $idCabang,
                                'nama_cabang' => $nearestBranch->nama_cabang
                            ]);
                        }
                    }
                }

                // PRIORITAS 3: Gunakan koordinat pickup (jika user override di checkout)
                if ((!isset($idCabang) || !$idCabang) &&
                    $request->has('pickup_latitude') &&
                    $request->has('pickup_longitude')) {
                    $nearestBranch = Cabang::findNearest(
                        $request->pickup_latitude,
                        $request->pickup_longitude
                    );
                    if ($nearestBranch) {
                        $idCabang = $nearestBranch->id_cabang;
                        Log::info("✅ PICKUP: Using branch from coordinates", [
                            'id_cabang' => $idCabang,
                            'nama_cabang' => $nearestBranch->nama_cabang,
                            'distance' => $nearestBranch->distance
                        ]);
                    }
                }

                // PRIORITAS 4: Fallback ke cabang aktif pertama
                if (!isset($idCabang) || !$idCabang) {
                    $nearestBranch = Cabang::where('is_active', true)->first();
                    if ($nearestBranch) {
                        $idCabang = $nearestBranch->id_cabang;
                        Log::warning("⚠️ PICKUP: Using fallback branch (no form/session/coordinates)", [
                            'id_cabang' => $idCabang,
                            'nama_cabang' => $nearestBranch->nama_cabang
                        ]);
                    } else {
                        Log::error("❌ PICKUP: No active branch found!");
                        DB::rollBack();
                        return back()->with('error', 'Tidak ada cabang aktif tersedia untuk pickup')->withInput();
                    }
                }

                $ongkir = 0;
                $alamatPengiriman = $nearestBranch ? $nearestBranch->formatted_address : 'Ambil sendiri di toko';
            }

            // Check if user needs to pay membership fee
            // User needs to pay membership fee ONLY if:
            // 1. This is their first transaction AND
            // 2. They don't have a membership record yet
            $membershipFee = 0;
            $isFirstTransaction = !Order::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('status_pembayaran', 'sudah_bayar')
                ->exists();

            // Check membership using user_id (not id_pelanggan)
            $hasMembership = Membership::where('user_id', $user->id)->exists();

            // Apply membership fee if BOTH conditions are true
            if ($isFirstTransaction && !$hasMembership) {
                $membershipFee = 10000; // Biaya pembuatan member Rp 10.000
                Log::info("Membership fee applied: Rp {$membershipFee} (First transaction: Yes, Has membership: No)");
            } else {
                Log::info("No membership fee (First transaction: " .
                         ($isFirstTransaction ? 'Yes' : 'No') . ", Has membership: " .
                         ($hasMembership ? 'Yes' : 'No') . ")");
            }

            $total = $subtotal - $discount + $ongkir + $membershipFee;

            // Generate unique transaction code
            $transactionCode = 'TRX-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));

            // Create main order
            $order = Order::create([
                'kode_transaksi' => $transactionCode,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_cabang' => $idCabang,
                'address_id' => $addressId ?? null,  // ✅ FIXED: Store address_id for kurir method to enable shipping data creation
                'tanggal_transaksi' => now(),  // ✅ ADDED: Set transaction date to current datetime
                'total_harga' => $subtotal,
                'total_diskon' => $discount,
                'ongkir' => (string)$ongkir,
                'biaya_membership' => $membershipFee,
                'status_pembayaran' => 'belum_bayar',
                'status_pengiriman' => 'pending',
                'metode_pengiriman' => $validated['metode_pengiriman'],
                'catatan' => $validated['catatan'] ?? null,
                'payment_expired_at' => now()->addMinutes(15), // ✅ SET: Payment must be completed within 15 minutes
            ]);

            Log::info('📦 Order Created', [
                'order_code' => $transactionCode,
                'id_cabang' => $idCabang,
                'nama_cabang' => $nearestBranch ? $nearestBranch->nama_cabang : 'N/A',
                'metode_pengiriman' => $validated['metode_pengiriman'],
                'created_at' => now()->toIso8601String(),
            ]);

            // Initialize service untuk cek stok dari database integrasi
            $integrasiService = app(IntegrasiProdukService::class);

            // Check stock and create order items
            foreach ($cartItems as $item) {
                // Gunakan relasi product yang sudah di-eager load
                $produk = $item->product;

                if (!$produk) {
                    DB::rollBack();
                    Log::error('Product not found for cart item', [
                        'cart_id' => $item->id_detail_cart,
                        'product_id' => $item->id_produk
                    ]);
                    return back()->with('error', 'Produk tidak ditemukan di database integrasi. Silakan hapus dari keranjang dan tambahkan lagi.');
                }

                // Check stock availability dari database integrasi
                if ($idCabang) {
                    // id_produk di cart sudah langsung merujuk ke ID produk di database integrasi
                    $stokTersedia = $integrasiService->getStokProdukCabang($item->id_produk, $idCabang);

                    // id_produk di cart sudah langsung merujuk ke ID produk di database integrasi
                    $stokTersedia = $integrasiService->getStokProdukCabang($item->id_produk, $idCabang);

                    if ($stokTersedia < $item->qty) {
                        DB::rollBack();
                        Log::warning('Insufficient stock for product', [
                            'product_id' => $item->id_produk,
                            'product_name' => $produk->nama_produk,
                            'requested_qty' => $item->qty,
                            'available_stock' => $stokTersedia,
                            'branch_id' => $idCabang
                        ]);
                        return back()->with('error', "Stok produk {$produk->nama_produk} tidak mencukupi di cabang ini. Tersedia: {$stokTersedia}");
                    }
                } else {
                    DB::rollBack();
                    return back()->with('error', "Silakan pilih metode pengiriman terlebih dahulu");
                }

                // Create order detail record
                // PENTING: Gunakan getCurrentPrice() untuk harga_item (termasuk diskon jika aktif)
                $finalPrice = $produk->getCurrentPrice();
                $subtotalItem = $finalPrice * $item->qty;

                // Hitung diskon item jika ada
                $diskonItem = 0;
                if ($produk->hasActiveDiscount()) {
                    $diskonItem = ($produk->harga_produk - $finalPrice) * $item->qty;
                    Log::info('💰 Saving discount info to order detail', [
                        'product_id' => $item->id_produk,
                        'product_name' => $produk->nama_produk,
                        'original_price' => $produk->harga_produk,
                        'discount_price' => $finalPrice,
                        'qty' => $item->qty,
                        'discount_per_item' => $produk->harga_produk - $finalPrice,
                        'total_discount' => $diskonItem,
                        'subtotal_after_discount' => $subtotalItem
                    ]);
                }

                DB::table('tb_detail_transaksi')->insert([
                    'id_transaksi' => $order->id_transaksi,
                    'id_produk' => $item->id_produk,
                    'qty' => $item->qty,
                    'harga_item' => $finalPrice, // Harga final (sudah termasuk diskon jika ada)
                    'subtotal' => $subtotalItem,
                    'diskon_item' => $diskonItem, // Total diskon untuk item ini
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // PENTING: Stok akan dikurangi SETELAH pembayaran sukses
                // Simpan data untuk pengurangan stok nanti
                // Untuk sekarang, hanya tandai di order bahwa stok perlu dikurangi
            }

            // Configure Midtrans
            try {
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = config('services.midtrans.is_production', false);
                Config::$isSanitized = true;
                Config::$is3ds = true;
            } catch (\Exception $e) {
                Log::error('Midtrans Config Error: ' . $e->getMessage());
                // Continue without Midtrans config
            }

            // Prepare order items for Midtrans
            $midtransItems = [];
            foreach ($cartItems as $item) {
                // Gunakan relasi product yang sudah di-eager load
                $produk = $item->product;

                if ($produk) {
                    // PENTING: Gunakan getCurrentPrice() untuk mendapatkan harga dengan diskon jika aktif
                    $finalPrice = $produk->getCurrentPrice();

                    $midtransItems[] = [
                        'id' => $item->id_produk,
                        'price' => (int) $finalPrice,
                        'quantity' => $item->qty,
                        'name' => $produk->nama_produk,
                    ];

                    // Log untuk debugging harga yang digunakan
                    if ($produk->hasActiveDiscount()) {
                        Log::info('💰 Product with active discount in Midtrans items', [
                            'product_id' => $item->id_produk,
                            'product_name' => $produk->nama_produk,
                            'original_price' => $produk->harga_produk,
                            'discount_price' => $produk->harga_diskon,
                            'final_price_used' => $finalPrice,
                            'quantity' => $item->qty,
                            'subtotal' => $finalPrice * $item->qty
                        ]);
                    }
                }
            }

            // Add discount as item if exists
            if ($discount > 0) {
                $discountItem = [
                    'id' => 'DISCOUNT',
                    'price' => -(int) $discount,
                    'quantity' => 1,
                    'name' => 'Membership Discount',
                ];
                $midtransItems[] = $discountItem;

                Log::info('Adding discount to Midtrans items', [
                    'discount_amount' => $discount,
                    'discount_item' => $discountItem
                ]);
            }

            // Add shipping cost if exists
            if ($ongkir > 0) {
                $midtransItems[] = [
                    'id' => 'SHIPPING',
                    'price' => (int) $ongkir,
                    'quantity' => 1,
                    'name' => 'Biaya Pengiriman',
                ];
            }

            // Add membership registration fee if first transaction
            if ($membershipFee > 0) {
                $midtransItems[] = [
                    'id' => 'MEMBERSHIP_FEE',
                    'price' => (int) $membershipFee,
                    'quantity' => 1,
                    'name' => 'Biaya Pembuatan Member',
                ];
            }

            // Verify item_details sum equals gross_amount
            $itemDetailsSum = 0;
            foreach ($midtransItems as $item) {
                $itemDetailsSum += $item['price'] * $item['quantity'];
            }

            // Log untuk debugging
            Log::info('💰 Midtrans Payment Details', [
                'order_code' => $transactionCode,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'ongkir' => $ongkir,
                'membership_fee' => $membershipFee,
                'calculated_total' => $total,
                'item_details_sum' => $itemDetailsSum,
                'items_count' => count($midtransItems),
                'match' => $itemDetailsSum === (int)$total ? '✅ MATCH' : '❌ MISMATCH'
            ]);

            // Jika ada mismatch, gunakan itemDetailsSum sebagai gross_amount
            $grossAmount = ($itemDetailsSum === (int)$total) ? (int)$total : $itemDetailsSum;

            if ($itemDetailsSum !== (int)$total) {
                Log::warning('⚠️ Midtrans amount mismatch detected, using item_details_sum', [
                    'calculated_total' => $total,
                    'item_details_sum' => $itemDetailsSum,
                    'difference' => $itemDetailsSum - (int)$total
                ]);
            }

            // Prepare transaction details for Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $transactionCode,
                    'gross_amount' => $grossAmount,
                ],
                'item_details' => $midtransItems,
                'customer_details' => [
                    'first_name' => $pelanggan->nama_pelanggan,
                    'email' => $user->email,
                    'phone' => $pelanggan->no_tlp_pelanggan ?? '',
                ],
                'callbacks' => [
                    'finish' => url('/pelanggan/orders'),
                ],
            ];

            // Get Snap token from Midtrans
            $snapToken = null;
            try {
                $serverKey = config('services.midtrans.server_key');
                $clientKey = config('services.midtrans.client_key');

                Log::info('🔧 Midtrans Configuration Check', [
                    'server_key_present' => !empty($serverKey),
                    'server_key_length' => strlen($serverKey ?? ''),
                    'client_key_present' => !empty($clientKey),
                    'client_key_length' => strlen($clientKey ?? ''),
                    'is_production' => config('services.midtrans.is_production', false)
                ]);

                if ($serverKey && $clientKey) {
                    Log::info('📤 Requesting Snap Token from Midtrans', [
                        'order_code' => $transactionCode,
                        'gross_amount' => $grossAmount,
                        'items_count' => count($midtransItems)
                    ]);

                    $snapToken = Snap::getSnapToken($params);

                    if ($snapToken && strlen($snapToken) > 10) {
                        Log::info('✅ Snap Token Generated Successfully', [
                            'order_code' => $transactionCode,
                            'snap_token_length' => strlen($snapToken),
                            'snap_token_preview' => substr($snapToken, 0, 20) . '...'
                        ]);
                        $order->update(['snap_token' => $snapToken]);
                    } else {
                        Log::warning('❌ Invalid Snap Token Received', [
                            'order_code' => $transactionCode,
                            'snap_token_length' => strlen($snapToken ?? ''),
                            'snap_token' => $snapToken
                        ]);
                    }
                } else {
                    Log::warning('⚠️ Midtrans not fully configured, skipping Snap token generation', [
                        'server_key_present' => !empty($serverKey),
                        'client_key_present' => !empty($clientKey)
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❌ Midtrans Snap Token Error', [
                    'order_code' => $transactionCode,
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'error_trace' => $e->getTraceAsString()
                ]);
                // Continue without Snap token - will show manual payment instruction
            }

            // Clear cart
            Cart::where('id_pelanggan', $pelanggan->id_pelanggan)->delete();

            // PENTING: Data pengiriman TIDAK dibuat di sini
            // Data pengiriman akan dibuat HANYA SETELAH pembayaran sukses (di payment callback)
            // Simpan shipping data di session untuk digunakan nanti setelah pembayaran
            if ($validated['metode_pengiriman'] === 'kurir' && $shippingData) {
                session()->put('pending_shipping_' . $order->id_transaksi, $shippingData);
            }

            DB::commit();

            Log::info('✅ Order Created and Ready for Payment', [
                'order_code' => $transactionCode,
                'order_id' => $order->id_transaksi,
                'has_snap_token' => !empty($snapToken),
                'total' => $total,
                'pelanggan_id' => $pelanggan->id_pelanggan,
                'id_cabang' => $idCabang
            ]);

            // Redirect to payment page
            // ✅ IMPORTANT: Calculate total from saved order data to ensure consistency
            $calculatedTotal = $order->total_harga - $order->total_diskon + $order->ongkir + ($order->biaya_membership ?? 0);

            Log::info('✅ Sending to Payment Page', [
                'order_id' => $order->id_transaksi,
                'calculated_total' => $calculatedTotal,
                'order_total_harga' => $order->total_harga,
                'order_total_diskon' => $order->total_diskon,
                'order_ongkir' => $order->ongkir,
                'order_biaya_membership' => $order->biaya_membership,
                'match_original' => $calculatedTotal === (int)$total ? '✅ MATCH' : '⚠️ MISMATCH'
            ]);

            return view('pelanggan.payment', compact('order', 'snapToken', 'calculatedTotal', 'pelanggan'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error placing order: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Handle Midtrans payment callback
     * ✅ IMPROVED: More robust status handling with comprehensive logging
     */
    public function paymentCallback(Request $request)
    {
        Log::info('🔔 ========== PAYMENT CALLBACK START ==========', [
            'timestamp' => now()->toIso8601String(),
            'request_ip' => $request->ip(),
            'request_method' => $request->method(),
            'request_data_keys' => array_keys($request->all())
        ]);

        try {
            // Configure Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production', false);

            if (!Config::$serverKey) {
                Log::error('❌ CRITICAL: Midtrans server_key not configured!');
                return response()->json(['message' => 'Midtrans not configured'], 500);
            }

            $notif = new \Midtrans\Notification();

            $transactionStatus = $notif->transaction_status ?? null;
            $paymentType = $notif->payment_type ?? null;
            $orderId = $notif->order_id ?? null;
            $fraudStatus = $notif->fraud_status ?? null;
            $transactionId = $notif->transaction_id ?? null;

            Log::info('✅ Midtrans Callback Received', [
                'order_id' => $orderId,
                'transaction_id' => $transactionId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType,
                'gross_amount' => $notif->gross_amount ?? null,
                'settlement_time' => $notif->settlement_time ?? null
            ]);

            if (!$orderId) {
                Log::error('❌ No order_id in callback notification');
                return response()->json(['message' => 'Invalid notification'], 400);
            }

            // Find order by kode_transaksi or midtrans_order_id
            // Support both original order_id and continue payment order_id (with -R suffix)
            $order = Order::where('kode_transaksi', $orderId)
                ->orWhere('midtrans_order_id', $orderId)
                ->first();

            // If not found, try to extract original order_id from continue payment format (TRX-xxx-Rtimestamp)
            if (!$order && strpos($orderId, '-R') !== false) {
                $originalOrderId = substr($orderId, 0, strrpos($orderId, '-R'));
                $order = Order::where('kode_transaksi', $originalOrderId)->first();
            }

            if (!$order) {
                Log::error('❌ Order not found in callback', [
                    'order_id' => $orderId,
                    'tried_formats' => ['kode_transaksi', 'midtrans_order_id', 'extracted_original']
                ]);
                return response()->json(['message' => 'Order not found'], 404);
            }

            Log::info('📦 Order found for callback', [
                'order_id' => $order->id_transaksi,
                'order_code' => $order->kode_transaksi,
                'current_status' => $order->status_pembayaran,
                'payment_expired_at' => $order->payment_expired_at
            ]);

            // Get pelanggan and user via relation
            $pelanggan = \App\Models\Pelanggan::find($order->id_pelanggan);
            $user = $pelanggan ? $pelanggan->user : null;

            // Update last payment check time
            $order->last_payment_check_at = now();

            // ✅ IMPROVED: More robust payment status handling
            $paymentProcessed = false;

            // Handle payment status based on transaction_status
            if ($transactionStatus === 'capture') {
                // Credit card successful capture
                if ($paymentType === 'credit_card') {
                    if ($fraudStatus === 'challenge') {
                        $order->status_pembayaran = 'belum_bayar';
                        Log::warning('⚠️ Payment challenged (fraud suspected)', [
                            'order_id' => $order->id_transaksi,
                            'order_code' => $order->kode_transaksi
                        ]);
                    } else if ($fraudStatus === 'accept') {
                        // Fraud status accepted - payment is successful
                        $order->status_pembayaran = 'sudah_bayar';
                        $paymentProcessed = true;

                        Log::info('✅ Payment successful (credit card capture accepted)', [
                            'order_id' => $order->id_transaksi,
                            'order_code' => $order->kode_transaksi,
                            'fraud_status' => $fraudStatus
                        ]);
                    } else {
                        // No fraud status or not challenged - treat as success
                        $order->status_pembayaran = 'sudah_bayar';
                        $paymentProcessed = true;

                        Log::info('✅ Payment successful (credit card capture)', [
                            'order_id' => $order->id_transaksi,
                            'order_code' => $order->kode_transaksi
                        ]);
                    }
                }
            } elseif ($transactionStatus === 'settlement') {
                // Bank transfer, e-wallet, etc. - successful settlement
                $order->status_pembayaran = 'sudah_bayar';
                $paymentProcessed = true;

                Log::info('✅ Payment successful (settlement)', [
                    'order_id' => $order->id_transaksi,
                    'order_code' => $order->kode_transaksi,
                    'transaction_id' => $transactionId,
                    'payment_type' => $paymentType
                ]);
            } elseif ($transactionStatus === 'pending') {
                // Payment still pending (waiting for customer action)
                $order->status_pembayaran = 'belum_bayar';

                Log::info('⏳ Payment pending (waiting for settlement)', [
                    'order_id' => $order->id_transaksi,
                    'order_code' => $order->kode_transaksi,
                    'payment_type' => $paymentType
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel', 'refund'])) {
                // Payment failed or was cancelled
                if ($order->status_pembayaran !== 'sudah_bayar') {
                    // Only update if not already paid
                    $order->status_pembayaran = 'kadaluarsa';

                    Log::warning('❌ Payment failed/cancelled', [
                        'order_id' => $order->id_transaksi,
                        'order_code' => $order->kode_transaksi,
                        'transaction_status' => $transactionStatus,
                        'previous_status' => $order->getOriginal('status_pembayaran')
                    ]);

                    // Restore stock if not already paid
                    $this->kembalikanStok($order);
                }
            } else {
                // Unknown status - log it
                Log::warning('⚠️ Unknown transaction status', [
                    'order_id' => $order->id_transaksi,
                    'order_code' => $order->kode_transaksi,
                    'transaction_status' => $transactionStatus
                ]);
            }

            // ✅ CRITICAL: Use PaymentUpdateService for ATOMIC, VERIFIED update
            if ($paymentProcessed && $order->status_pembayaran === 'sudah_bayar') {
                Log::info('🎉 Processing successful payment with PaymentUpdateService', [
                    'order_id' => $order->id_transaksi,
                    'order_code' => $order->kode_transaksi
                ]);

                // Prepare additional data
                $additionalData = [
                    'status_pengiriman' => $order->metode_pengiriman === 'ambil_sendiri' ? 'siap_diambil' : 'dikemas'
                ];

                // Use PaymentUpdateService for guaranteed update
                $updateResult = PaymentUpdateService::updatePaymentStatus(
                    $order->id_transaksi,
                    'sudah_bayar',
                    $additionalData
                );

                if ($updateResult['success']) {
                    $order = $updateResult['data']; // Reload updated order
                    $saved = true;

                    Log::info('✅ Callback: PaymentUpdateService succeeded', [
                        'order_id' => $order->id_transaksi,
                        'order_code' => $order->kode_transaksi,
                        'saved_status' => $order->status_pembayaran
                    ]);

                    // Award membership points
                    if ($user) {
                        $this->awardMembershipPoints($user, $order);
                    }

                    // Reduce stock from integration database
                    $this->kurangiStokSetelahPembayaran($order);

                    // Create shipping record
                    $this->createShippingAfterPayment($order);

                    Log::info('✅ All payment post-processing completed', [
                        'order_id' => $order->id_transaksi,
                        'order_code' => $order->kode_transaksi
                    ]);

                } else {
                    Log::error('❌ Callback: PaymentUpdateService FAILED', [
                        'order_id' => $order->id_transaksi,
                        'order_code' => $order->kode_transaksi,
                        'error' => $updateResult['message']
                    ]);

                    // IMPORTANT: Still return success to Midtrans to acknowledge receipt
                    // But log this critical error for admin to investigate
                    return response()->json(['message' => 'OK', 'status' => 'success'], 200);
                }

            } else if (!$paymentProcessed) {
                // Payment not successful - still save status changes (e.g., kadaluarsa)
                Log::info('Callback: Attempting to save non-payment-processed status', [
                    'order_id' => $order->id_transaksi,
                    'order_code' => $order->kode_transaksi,
                    'new_status' => $order->status_pembayaran
                ]);

                // For non-success cases, use direct update
                $saveAttempt = 0;
                $maxAttempts = 5;
                $saved = false;

                while ($saveAttempt < $maxAttempts && !$saved) {
                    try {
                        $saveAttempt++;

                        // Use database transaction for atomicity
                        DB::beginTransaction();

                        $order->save();

                        // Verify save was successful by re-fetching from DB BEFORE commit
                        $verifyOrder = Order::find($order->id_transaksi);

                        if ($verifyOrder && $verifyOrder->status_pembayaran === $order->status_pembayaran) {
                            $saved = true;

                            Log::info('✅ Callback: Non-payment status saved and verified', [
                                'order_id' => $order->id_transaksi,
                                'order_code' => $order->kode_transaksi,
                                'saved_status_pembayaran' => $order->status_pembayaran,
                                'attempt' => $saveAttempt
                            ]);

                            DB::commit();

                        } else {
                            DB::rollBack();

                            Log::warning('⚠️ Callback: Verify failed in transaction', [
                                'order_id' => $order->id_transaksi,
                                'order_code' => $order->kode_transaksi,
                                'expected_status' => $order->status_pembayaran,
                                'actual_status' => $verifyOrder ? $verifyOrder->status_pembayaran : 'NULL',
                                'attempt' => $saveAttempt
                            ]);

                            if ($saveAttempt < $maxAttempts) {
                                sleep(1); // Wait 1 second before retry
                            }
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();

                        Log::error('❌ Callback save attempt ' . $saveAttempt . ' failed: ' . $e->getMessage(), [
                            'order_id' => $order->id_transaksi,
                            'order_code' => $order->kode_transaksi,
                            'error' => $e->getMessage()
                        ]);

                        if ($saveAttempt < $maxAttempts) {
                            sleep(1); // Wait 1 second before retry
                        }
                    }
                }
            }

            if (!$saved) {
                Log::error('❌ CRITICAL: Failed to save order in callback', [
                    'order_id' => $order->id_transaksi,
                    'order_code' => $order->kode_transaksi,
                    'transaction_status' => $transactionStatus
                ]);

                // IMPORTANT: Still return success to Midtrans to acknowledge receipt
                // But log this critical error for admin to investigate
                return response()->json(['message' => 'OK', 'status' => 'success'], 200);
            }

            Log::info('✅ Callback processed successfully', [
                'order_id' => $order->id_transaksi,
                'order_code' => $order->kode_transaksi,
                'new_status' => $order->status_pembayaran,
                'final_save_attempt' => $saveAttempt
            ]);

            Log::info('🔔 ========== PAYMENT CALLBACK END (SUCCESS) ==========', [
                'timestamp' => now()->toIso8601String(),
                'order_code' => $order->kode_transaksi ?? 'unknown',
                'final_status' => $order->status_pembayaran ?? 'unknown'
            ]);

            return response()->json(['message' => 'OK', 'status' => 'success']);

        } catch (\Exception $e) {
            Log::error('❌ ========== PAYMENT CALLBACK ERROR ==========', [
                'timestamp' => now()->toIso8601String(),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Award membership points after successful payment
     */
    private function awardMembershipPoints($user, $order)
    {
        if (!$user) {
            Log::warning('⚠️ awardMembershipPoints: User is null, skipping points award', [
                'order_id' => $order->id_transaksi,
                'order_code' => $order->kode_transaksi
            ]);
            return;
        }

        Log::info('🎯 Starting awardMembershipPoints', [
            'user_id' => $user->id,
            'user_id_user' => $user->id_user,
            'order_id' => $order->id_transaksi,
            'order_code' => $order->kode_transaksi
        ]);

        try {
            $total = $order->total_harga - $order->total_diskon + $order->ongkir + ($order->biaya_membership ?? 0);
            $pointsEarned = Membership::calculatePoints($total);

            Log::info('📊 Points calculation', [
                'total_harga' => $order->total_harga,
                'total_diskon' => $order->total_diskon,
                'ongkir' => $order->ongkir,
                'biaya_membership' => $order->biaya_membership ?? 0,
                'final_total' => $total,
                'points_earned' => $pointsEarned
            ]);

            $membership = $user->membership;

            if ($membership) {
                Log::info('💎 Existing membership found, adding points', [
                    'membership_id' => $membership->id,
                    'current_points' => $membership->points,
                    'points_to_add' => $pointsEarned
                ]);

                $membership->addPoints($pointsEarned);

                Log::info("✅ Membership points awarded", [
                    'user_id' => $user->id,
                    'membership_id' => $membership->id,
                    'points_earned' => $pointsEarned,
                    'total_points' => $membership->points,
                    'new_tier' => $membership->tier,
                    'transaction_total' => $total
                ]);
            } else {
                Log::info('👤 No membership found, creating new one', [
                    'user_id' => $user->id,
                    'user_id_user' => $user->id_user,
                    'points_to_create' => $pointsEarned
                ]);

                $newMembership = Membership::create([
                    'user_id' => $user->id,  // FIXED: Changed from pelanggan_id to user_id
                    'points' => $pointsEarned,
                    'tier' => Membership::TIER_BRONZE,
                    'discount_percentage' => Membership::TIER_DISCOUNTS[Membership::TIER_BRONZE],
                    'valid_from' => now(),
                    'valid_until' => now()->addYear(),
                    'is_active' => true,
                ]);

                Log::info("✅ New membership created", [
                    'membership_id' => $newMembership->id,
                    'user_id' => $user->id,
                    'points_earned' => $pointsEarned,
                    'transaction_total' => $total
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Error awarding membership points: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'order_id' => $order->id_transaksi,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Check payment status (for frontend polling)
     * ✅ IMPROVED: Direct Midtrans query with database write verification
     */
    public function checkPaymentStatus($orderId, Request $request)
    {
        try {
            // Try multiple ways to find the order
            // Frontend sends id_transaksi (numeric) but could also be kode_transaksi (string)
            $order = null;

            // First try by id_transaksi (numeric ID - primary method)
            if (is_numeric($orderId)) {
                $order = Order::find($orderId);
            }

            // If not found, try by kode_transaksi (transaction code)
            if (!$order) {
                $order = Order::where('kode_transaksi', $orderId)->first();
            }

            if (!$order) {
                Log::warning('❌ Order not found for payment check', [
                    'order_id_param' => $orderId,
                    'tried_formats' => ['id_transaksi', 'kode_transaksi']
                ]);
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            $forceUpdate = $request->query('force') === '1';

            Log::info('🔍 Payment Status Check Started', [
                'order_code' => $order->kode_transaksi,
                'current_status' => $order->status_pembayaran,
                'is_already_paid' => $order->isPaid(),
                'force_update' => $forceUpdate
            ]);

            // If already paid and not forcing update, return immediately
            if ($order->isPaid() && !$forceUpdate) {
                Log::info('✅ Order already marked as paid (no force)', ['order_code' => $order->kode_transaksi]);
                return response()->json([
                    'success' => true,
                    'is_paid' => true,
                    'status_pembayaran' => $order->status_pembayaran,
                    'status_pengiriman' => $order->status_pengiriman,
                    'verified_from_midtrans' => true,
                ]);
            }

            // Check from Midtrans API directly
            $midtransPaymentVerified = false;
            $paymentProcessed = false;

            try {
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = config('services.midtrans.is_production', false);

                if (!Config::$serverKey) {
                    Log::warning('⚠️ Midtrans not configured, cannot verify from Midtrans', [
                        'order_code' => $order->kode_transaksi,
                    ]);
                } else {
                    // CRITICAL FIX: Use midtrans_order_id if set (for retry payments),
                    // otherwise use kode_transaksi
                    $midtransOrderId = $order->midtrans_order_id ?? $order->kode_transaksi;

                    Log::info('🔍 Querying Midtrans API', [
                        'order_code' => $order->kode_transaksi,
                        'midtrans_order_id' => $midtransOrderId,
                        'is_retry_payment' => !empty($order->midtrans_order_id) && $order->midtrans_order_id !== $order->kode_transaksi,
                    ]);

                    $status = \Midtrans\Transaction::status($midtransOrderId);

                    Log::info('📊 Midtrans Query Response', [
                        'order_code' => $order->kode_transaksi,
                        'transaction_status' => $status->transaction_status ?? 'N/A',
                        'fraud_status' => $status->fraud_status ?? 'N/A',
                        'payment_type' => $status->payment_type ?? 'N/A',
                    ]);

                    $transactionStatus = $status->transaction_status ?? null;

                    // ========== Check if payment was successful in Midtrans ==========
                    if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                        // Double-check fraud status for credit cards
                        if ($transactionStatus === 'capture') {
                            $fraudStatus = $status->fraud_status ?? null;
                            if ($fraudStatus === 'challenge') {
                                Log::warning('⚠️ Payment challenged (fraud suspected)', [
                                    'order_code' => $order->kode_transaksi,
                                    'fraud_status' => $fraudStatus
                                ]);
                                // Payment is still uncertain, don't process yet
                                return response()->json([
                                    'success' => true,
                                    'is_paid' => false,
                                    'status_pembayaran' => $order->status_pembayaran,
                                    'status_pengiriman' => $order->status_pengiriman,
                                    'verified_from_midtrans' => false,
                                    'note' => 'Payment challenged - waiting for verification'
                                ]);
                            }
                        }

                        $midtransPaymentVerified = true;

                        // ========== CRITICAL: Process payment if not already processed ==========
                        if ($order->status_pembayaran !== 'sudah_bayar') {
                            Log::info('🎉 Payment confirmed in Midtrans, updating database', [
                                'order_code' => $order->kode_transaksi,
                                'midtrans_status' => $transactionStatus
                            ]);

                            // Get pelanggan and user - dengan eager loading relasi
                            $pelanggan = \App\Models\Pelanggan::with('user')->find($order->id_pelanggan);
                            $user = $pelanggan ? $pelanggan->user : null;

                            // FALLBACK: Jika user tidak ditemukan via pelanggan, cari langsung dari order
                            if (!$user && $pelanggan) {
                                $user = \App\Models\User::find($pelanggan->id_user);
                                Log::warning('⚠️ User loaded via direct query (not via relation)', [
                                    'pelanggan_id' => $pelanggan->id_pelanggan,
                                    'id_user' => $pelanggan->id_user
                                ]);
                            }

                            Log::info('📋 User data for payment processing', [
                                'pelanggan_id' => $pelanggan?->id_pelanggan,
                                'user_id' => $user?->id_user,
                                'user_email' => $user?->email,
                                'has_user' => !!$user
                            ]);

                            // Prepare additional data for update
                            $additionalData = [
                                'status_pengiriman' => $order->metode_pengiriman === 'ambil_sendiri' ? 'siap_diambil' : 'dikemas'
                            ];

                            // ========== Use PaymentUpdateService for ATOMIC, VERIFIED update ==========
                            $updateResult = PaymentUpdateService::updatePaymentStatus(
                                $order->id_transaksi,
                                'sudah_bayar',
                                $additionalData
                            );

                            if ($updateResult['success']) {
                                $paymentProcessed = true;
                                $order = $updateResult['data']; // Reload updated order

                                Log::info('✅ checkPaymentStatus: PaymentUpdateService succeeded', [
                                    'order_code' => $order->kode_transaksi,
                                    'saved_status' => $order->status_pembayaran
                                ]);

                                // Award membership points
                                $this->awardMembershipPoints($user, $order);

                                // KURANGI STOK DI DATABASE INTEGRASI
                                $this->kurangiStokSetelahPembayaran($order);

                                // BUAT DATA PENGIRIMAN SETELAH PEMBAYARAN SUKSES
                                $this->createShippingAfterPayment($order);

                            } else {
                                Log::error('❌ checkPaymentStatus: PaymentUpdateService FAILED', [
                                    'order_code' => $order->kode_transaksi,
                                    'error' => $updateResult['message']
                                ]);

                                // Still return success but with warning
                                return response()->json([
                                    'success' => true,
                                    'is_paid' => false, // Don't mark as paid until confirmed in DB
                                    'status_pembayaran' => $order->status_pembayaran,
                                    'verified_from_midtrans' => true,
                                    'note' => 'Payment verified from Midtrans but DB update pending - please retry'
                                ]);
                            }

                            Log::info('✅ Payment fully processed and saved', [
                                'order_code' => $order->kode_transaksi,
                                'payment_processed' => $paymentProcessed
                            ]);
                        }
                    } elseif ($transactionStatus === 'pending') {
                        Log::info('⏳ Payment still pending', [
                            'order_code' => $order->kode_transaksi,
                        ]);
                    } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                        Log::warning('❌ Payment failed/expired in Midtrans', [
                            'order_code' => $order->kode_transaksi,
                            'midtrans_status' => $transactionStatus
                        ]);

                        if ($order->status_pembayaran !== 'kadaluarsa') {
                            $order->status_pembayaran = 'kadaluarsa';
                            $order->last_payment_check_at = now();
                            $order->save();

                            // Restore stock
                            $this->kembalikanStok($order);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('⚠️ Failed to query Midtrans with primary ID: ' . $e->getMessage(), [
                    'order_code' => $order->kode_transaksi,
                    'midtrans_order_id' => $midtransOrderId,
                    'error_code' => $e->getCode(),
                ]);

                // FALLBACK: If retry payment (midtrans_order_id has -R suffix) and query failed,
                // try with original kode_transaksi
                if (!empty($order->midtrans_order_id) && $order->midtrans_order_id !== $order->kode_transaksi) {
                    try {
                        Log::info('🔄 Fallback: Retrying Midtrans query with original kode_transaksi', [
                            'order_code' => $order->kode_transaksi,
                            'original_query' => $midtransOrderId,
                        ]);

                        $status = \Midtrans\Transaction::status($order->kode_transaksi);

                        Log::info('📊 Midtrans Fallback Query Response', [
                            'order_code' => $order->kode_transaksi,
                            'transaction_status' => $status->transaction_status ?? 'N/A',
                        ]);

                        $transactionStatus = $status->transaction_status ?? null;

                        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                            $midtransPaymentVerified = true;

                            if ($order->status_pembayaran !== 'sudah_bayar') {
                                Log::info('🎉 Fallback query confirmed payment in Midtrans', [
                                    'order_code' => $order->kode_transaksi,
                                ]);

                                $pelanggan = \App\Models\Pelanggan::with('user')->find($order->id_pelanggan);
                                $user = $pelanggan ? $pelanggan->user : null;

                                // FALLBACK: Jika user tidak ditemukan via pelanggan, cari langsung
                                if (!$user && $pelanggan) {
                                    $user = \App\Models\User::find($pelanggan->id_user);
                                }

                                $additionalData = [
                                    'status_pengiriman' => $order->metode_pengiriman === 'ambil_sendiri' ? 'siap_diambil' : 'dikemas'
                                ];

                                $updateResult = PaymentUpdateService::updatePaymentStatus(
                                    $order->id_transaksi,
                                    'sudah_bayar',
                                    $additionalData
                                );

                                if ($updateResult['success']) {
                                    $paymentProcessed = true;
                                    $order = $updateResult['data'];

                                    Log::info('✅ Fallback query update succeeded', [
                                        'order_code' => $order->kode_transaksi,
                                    ]);

                                    $this->awardMembershipPoints($user, $order);
                                    $this->kurangiStokSetelahPembayaran($order);
                                    $this->createShippingAfterPayment($order);
                                }
                            }
                        }
                    } catch (\Exception $fallbackError) {
                        Log::warning('⚠️ Fallback Midtrans query also failed: ' . $fallbackError->getMessage(), [
                            'order_code' => $order->kode_transaksi,
                        ]);
                    }
                }
            }

            // ========== CRITICAL: Reload order from database to get latest state ==========
            $freshOrder = Order::find($order->id_transaksi);

            return response()->json([
                'success' => true,
                'is_paid' => $freshOrder ? $freshOrder->isPaid() : $order->isPaid(),
                'status_pembayaran' => $freshOrder ? $freshOrder->status_pembayaran : $order->status_pembayaran,
                'status_pengiriman' => $freshOrder ? $freshOrder->status_pengiriman : $order->status_pengiriman,
                'verified_from_midtrans' => $midtransPaymentVerified,
                'payment_processed' => $paymentProcessed,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error in checkPaymentStatus: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error checking status'], 500);
        }
    }

    /**
     * Display user's orders
     */
    public function orders()
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        // Get orders with shipping info and cancellation if available
        $orders = Order::where('tb_transaksi.id_pelanggan', $pelanggan->id_pelanggan)
            ->with(['address', 'cancellation'])
            ->leftJoin('tb_pengiriman', 'tb_transaksi.id_transaksi', '=', 'tb_pengiriman.id_transaksi')
            ->select('tb_transaksi.*', 'tb_pengiriman.no_resi', 'tb_pengiriman.status_pengiriman as shipping_status')
            ->orderBy('tb_transaksi.id_transaksi', 'desc')
            ->paginate(10);

        $membership = $user->membership;

        return view('pelanggan.orders', compact('orders', 'membership'));
    }

    /**
     * Display order detail
     */
    public function orderDetail($id)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        $order = Order::where('id_transaksi', $id)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->with(['address', 'shipment.staff.user'])
            ->firstOrFail();

        $orderItems = DB::table('tb_detail_transaksi')
            ->join('db_integrasi_ayu_mart.tb_produk', 'tb_detail_transaksi.id_produk', '=', 'db_integrasi_ayu_mart.tb_produk.id_produk')
            ->leftJoin('db_integrasi_ayu_mart.tb_jenis', 'db_integrasi_ayu_mart.tb_produk.id_jenis', '=', 'db_integrasi_ayu_mart.tb_jenis.id_jenis')
            ->where('tb_detail_transaksi.id_transaksi', $order->id_transaksi)
            ->select(
                'tb_detail_transaksi.*',
                'db_integrasi_ayu_mart.tb_produk.nama_produk',
                'db_integrasi_ayu_mart.tb_produk.harga_produk',
                'db_integrasi_ayu_mart.tb_produk.foto_produk',
                'db_integrasi_ayu_mart.tb_jenis.nama_jenis as kategori_produk'
            )
            ->get();

        return view('pelanggan.order-detail', compact('order', 'orderItems'));
    }

    /**
     * Continue payment for unpaid order
     */
    public function continuePayment($orderId)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        // Find the order
        $order = Order::where('id_transaksi', $orderId)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        if (!$order) {
            return redirect()->route('pelanggan.orders')->with('error', 'Pesanan tidak ditemukan');
        }

        // Check if payment is already completed
        if ($order->status_pembayaran === 'sudah_bayar') {
            return redirect()->route('pelanggan.orders')->with('info', 'Pesanan ini sudah dibayar');
        }

        // Check if payment is expired (optional: 24 hours)
        if ($order->status_pembayaran === 'kadaluarsa') {
            return redirect()->route('pelanggan.orders')->with('error', 'Pesanan ini sudah kadaluarsa');
        }

        try {
            // Configure Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Try to cancel old transaction if snap_token exists
            if ($order->snap_token) {
                try {
                    \Midtrans\Transaction::cancel($order->kode_transaksi);
                    Log::info('Old transaction cancelled for continue payment', [
                        'order_id' => $order->kode_transaksi
                    ]);
                } catch (\Exception $e) {
                    // Transaction might be expired or not found, it's okay to continue
                    Log::warning('Failed to cancel old transaction (may be expired)', [
                        'order_id' => $order->kode_transaksi,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Get order items from detail transaksi dengan JOIN ke database integrasi
            $orderItems = DB::table('tb_detail_transaksi')
                ->join('db_integrasi_ayu_mart.tb_produk', 'tb_detail_transaksi.id_produk', '=', 'db_integrasi_ayu_mart.tb_produk.id_produk')
                ->where('tb_detail_transaksi.id_transaksi', $order->id_transaksi)
                ->select(
                    'tb_detail_transaksi.*',
                    'db_integrasi_ayu_mart.tb_produk.nama_produk',
                    'db_integrasi_ayu_mart.tb_produk.harga_produk'
                )
                ->get();

            // Prepare items for Midtrans
            $midtransItems = [];
            foreach ($orderItems as $item) {
                $midtransItems[] = [
                    'id' => $item->id_produk,
                    'price' => (int) $item->harga_item,
                    'quantity' => $item->qty,
                    'name' => $item->nama_produk,
                ];
            }

            // Add discount if exists
            if ($order->total_diskon > 0) {
                $midtransItems[] = [
                    'id' => 'DISCOUNT',
                    'price' => -(int) $order->total_diskon,
                    'quantity' => 1,
                    'name' => 'Membership Discount',
                ];
            }

            // Add shipping cost if exists
            if ($order->ongkir > 0) {
                $midtransItems[] = [
                    'id' => 'SHIPPING',
                    'price' => (int) $order->ongkir,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ];
            }

            // Add membership registration fee if exists
            if (isset($order->biaya_membership) && $order->biaya_membership > 0) {
                $midtransItems[] = [
                    'id' => 'MEMBERSHIP_FEE',
                    'price' => (int) $order->biaya_membership,
                    'quantity' => 1,
                    'name' => 'Biaya Pembuatan Member',
                ];
            }

            // Calculate total
            $totalAmount = $order->total_harga - $order->total_diskon + $order->ongkir + ($order->biaya_membership ?? 0);

            // Create unique order_id for continue payment to avoid duplicate
            // Use original code + timestamp suffix
            $uniqueOrderId = $order->kode_transaksi . '-R' . time();

            // Prepare transaction params
            $params = [
                'transaction_details' => [
                    'order_id' => $uniqueOrderId,
                    'gross_amount' => (int) $totalAmount,
                ],
                'item_details' => $midtransItems,
                'customer_details' => [
                    'first_name' => $pelanggan->nama_pelanggan,
                    'email' => $user->email,
                    'phone' => $pelanggan->no_tlp_pelanggan ?? '',
                ],
                'callbacks' => [
                    'finish' => url('/pelanggan/orders'),
                ],
            ];

            // Generate new Snap token
            $snapToken = Snap::getSnapToken($params);

            // Validate snap token
            if (!$snapToken || strlen($snapToken) < 10) {
                throw new \Exception('Invalid Snap Token generated from Midtrans');
            }

            Log::info('🔄 Continue Payment - New Snap Token Generated', [
                'original_order_id' => $order->kode_transaksi,
                'midtrans_order_id' => $uniqueOrderId,
                'snap_token_length' => strlen($snapToken),
                'total_amount' => $totalAmount,
            ]);

            // Update snap token and midtrans order id
            $order->snap_token = $snapToken;
            $order->midtrans_order_id = $uniqueOrderId;
            $order->status_pembayaran = 'belum_bayar'; // Reset status
            $order->save();

            // CRITICAL: Reload from database to ensure fresh data for view
            $order = $order->fresh();

            if (!$order || !$order->snap_token) {
                throw new \Exception('Failed to save snap token to database');
            }

            Log::info('✅ Continue Payment - Snap Token Ready for Display', [
                'order_id' => $order->id_transaksi,
                'snap_token_set' => !empty($order->snap_token),
                'midtrans_order_id' => $order->midtrans_order_id,
            ]);

            // Return view with fresh snap token
            return view('pelanggan.payment', [
                'snapToken' => $order->snap_token,
                'order' => $order,
                'total' => $totalAmount,
            ]);

        } catch (\Exception $e) {
            Log::error('Continue Payment Error: ' . $e->getMessage());
            return redirect()->route('pelanggan.orders')
                ->with('error', 'Gagal membuat pembayaran. Silakan coba lagi atau hubungi customer service.');
        }
    }

    /**
     * Update courier system status
     */
    private function updateCourierSystemStatus($orderCode, $status)
    {
        try {
            // Update shipping status based on transaction relation
            DB::table('tb_pengiriman')
                ->join('tb_transaksi', 'tb_pengiriman.id_transaksi', '=', 'tb_transaksi.id_transaksi')
                ->where('tb_transaksi.kode_transaksi', $orderCode)
                ->update([
                    'tb_pengiriman.status_pengiriman' => $status,
                    'tb_pengiriman.updated_at' => now(),
                ]);

            Log::info('Courier system status updated', [
                'order_code' => $orderCode,
                'new_status' => $status
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error updating courier system status: ' . $e->getMessage(), [
                'order_code' => $orderCode,
                'status' => $status
            ]);
            return false;
        }
    }

    /**
     * Create shipping data ONLY after payment is successful
     * This ensures courier dashboard only shows paid orders
     */
    private function createShippingAfterPayment($order)
    {
        try {
            Log::info('=== START createShippingAfterPayment ===', [
                'order_code' => $order->kode_transaksi,
                'id_transaksi' => $order->id_transaksi,
                'metode_pengiriman' => $order->metode_pengiriman,
                'address_id' => $order->address_id,
                'status_pembayaran' => $order->status_pembayaran
            ]);

            // Check if shipping already exists
            $existingShipping = DB::table('tb_pengiriman')
                ->where('id_transaksi', $order->id_transaksi)
                ->first();

            if ($existingShipping) {
                Log::info('Shipping record already exists', [
                    'order_code' => $order->kode_transaksi,
                    'id_pengiriman' => $existingShipping->id_pengiriman,
                    'no_resi' => $existingShipping->no_resi
                ]);
                return true;
            }

            // Only create shipping for kurir method
            if ($order->metode_pengiriman !== 'kurir') {
                Log::info('Skipping shipping creation - pickup at store', [
                    'order_code' => $order->kode_transaksi,
                    'metode_pengiriman' => $order->metode_pengiriman
                ]);
                return true;
            }

            // PERBAIKAN: Prioritas data shipping
            // 1. Coba dari address_id di order (PALING RELIABLE)
            // 2. Fallback ke session (bisa hilang)
            // 3. Fallback ke data pelanggan default

            $shippingData = null;

            // Priority 1: Get from order address_id (most reliable)
            if ($order->address_id) {
                $address = CustomerAddress::find($order->address_id);
                if ($address) {
                    $shippingData = [
                        'order_code' => $order->kode_transaksi,
                        'customer_name' => $address->nama_penerima,
                        'customer_phone' => $address->no_telp_penerima,
                        'address' => $address->alamat_lengkap,
                        'city' => $address->kota,
                        'district' => $address->kecamatan,
                        'postal_code' => $address->kode_pos,
                        'latitude' => $address->latitude,
                        'longitude' => $address->longitude,
                        'notes' => $order->catatan,
                    ];

                    Log::info('✅ Shipping data loaded from customer_address', [
                        'order_code' => $order->kode_transaksi,
                        'address_id' => $order->address_id,
                        'customer_name' => $address->nama_penerima,
                        'city' => $address->kota
                    ]);
                } else {
                    Log::warning('⚠️ Address ID exists but address not found', [
                        'order_code' => $order->kode_transaksi,
                        'address_id' => $order->address_id
                    ]);
                }
            } else {
                Log::warning('⚠️ No address_id in order', [
                    'order_code' => $order->kode_transaksi
                ]);
            }

            // Priority 2: Try session (legacy support)
            if (!$shippingData) {
                $shippingData = session()->get('pending_shipping_' . $order->id_transaksi);
                if ($shippingData) {
                    Log::info('✅ Shipping data loaded from session', [
                        'order_code' => $order->kode_transaksi
                    ]);
                } else {
                    Log::warning('⚠️ No shipping data in session', [
                        'order_code' => $order->kode_transaksi,
                        'session_key' => 'pending_shipping_' . $order->id_transaksi
                    ]);
                }
            }

            // Priority 3: Get from pelanggan table as fallback
            if (!$shippingData) {
                $pelanggan = \App\Models\Pelanggan::find($order->id_pelanggan);
                if ($pelanggan) {
                    $shippingData = [
                        'order_code' => $order->kode_transaksi,
                        'customer_name' => $pelanggan->nama_pelanggan,
                        'customer_phone' => $pelanggan->no_tlp_pelanggan ?? '-',
                        'address' => $pelanggan->alamat ?? 'Alamat tidak tersedia',
                        'city' => 'Jakarta', // Default
                        'district' => null,
                        'postal_code' => null,
                        'latitude' => null,
                        'longitude' => null,
                        'notes' => $order->catatan ?? 'Harap hubungi pelanggan untuk konfirmasi alamat',
                    ];

                    Log::warning('⚠️ Shipping data created from pelanggan fallback', [
                        'order_code' => $order->kode_transaksi,
                        'id_pelanggan' => $order->id_pelanggan,
                        'customer_name' => $pelanggan->nama_pelanggan
                    ]);
                }
            }

            if (!$shippingData) {
                Log::error('❌ CRITICAL: Cannot create shipping - no data source available', [
                    'order_code' => $order->kode_transaksi,
                    'id_transaksi' => $order->id_transaksi,
                    'address_id' => $order->address_id,
                    'id_pelanggan' => $order->id_pelanggan,
                    'metode_pengiriman' => $order->metode_pengiriman
                ]);
                return false;
            }

            // Generate no_resi (receipt number) for courier
            $noResi = 'RESI-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));

            // Prepare shipping record for courier
            // ✅ FIXED: Only include fields that exist in tb_pengiriman schema
            $courierData = [
                'id_address' => $order->address_id,  // ✅ ADDED: Store reference to customer address
                'id_transaksi' => $order->id_transaksi,
                'no_resi' => $noResi,
                'nama_penerima' => $shippingData['customer_name'],
                'alamat_penerima' => $shippingData['address'],
                'status_pengiriman' => 'dikemas', // Order is paid and ready to pack
                'id_staff' => null, // Will be assigned by admin/system
                'tgl_kirim' => null,
                'tgl_sampai' => null,
            ];

            Log::info('📦 Preparing to insert shipping data', [
                'order_code' => $order->kode_transaksi,
                'no_resi' => $noResi,
                'nama_penerima' => $courierData['nama_penerima'],
                'alamat_penerima' => $courierData['alamat_penerima'],
                'id_address' => $courierData['id_address']
            ]);

            // Insert shipping data into courier system table
            $insertedId = DB::table('tb_pengiriman')->insertGetId($courierData);

            // REMOVED: no_resi update from tb_transaksi (column dropped, now only in tb_pengiriman)
            // No resi is stored in tb_pengiriman table only for single source of truth

            // Clear session data
            session()->forget('pending_shipping_' . $order->id_transaksi);

            Log::info('✅ SUCCESS: Shipping data created after payment', [
                'order_code' => $order->kode_transaksi,
                'id_pengiriman' => $insertedId,
                'no_resi' => $noResi,
                'customer' => $shippingData['customer_name'],
                'address' => $shippingData['address'],
                'city' => $shippingData['city'],
                'status_pembayaran' => $order->status_pembayaran,
                'status_pengiriman' => $courierData['status_pengiriman']
            ]);

            Log::info('=== END createShippingAfterPayment SUCCESS ===');

            return true;

        } catch (\Exception $e) {
            Log::error('❌ ERROR createShippingAfterPayment: ' . $e->getMessage(), [
                'order_code' => $order->kode_transaksi ?? null,
                'id_transaksi' => $order->id_transaksi ?? null,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            Log::info('=== END createShippingAfterPayment ERROR ===');

            return false;
        }
    }

    /**
     * Send shipping data to courier system
     */
    private function sendToCourierSystem($order, $shippingData)
    {
        try {
            // Generate no_resi (receipt number) for courier
            $noResi = 'RESI-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));

            // Refresh order to ensure id_transaksi is loaded
            $order->refresh();

            // Prepare shipping record for courier
            $courierData = [
                'kode_transaksi' => $shippingData['order_code'],
                'id_pelanggan' => $order->id_pelanggan,
                'id_transaksi' => $order->id_transaksi,
                'no_resi' => $noResi,
                'nama_penerima' => $shippingData['customer_name'],
                'no_tlp_penerima' => $shippingData['customer_phone'],
                'alamat_penerima' => $shippingData['address'],
                'kota' => $shippingData['city'],
                'kecamatan' => $shippingData['district'] ?? null,
                'kode_pos' => $shippingData['postal_code'] ?? null,
                'latitude' => $shippingData['latitude'] ?? null,
                'longitude' => $shippingData['longitude'] ?? null,
                'catatan_pengiriman' => $shippingData['notes'] ?? null,
                'status_pengiriman' => 'pending', // waiting for payment
                'tanggal_pengiriman' => null,
                'id_kurir' => null, // Will be assigned by admin/system
                'tgl_kirim' => null,
                'tgl_sampai' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert shipping data into courier system table
            DB::table('tb_pengiriman')->insert($courierData);

            // REMOVED: no_resi update from tb_transaksi (column dropped)
            // No resi is now only stored in tb_pengiriman for single source of truth

            Log::info('Shipping data sent to courier system', [
                'order_code' => $shippingData['order_code'],
                'no_resi' => $noResi,
                'customer' => $shippingData['customer_name'],
                'address' => $shippingData['address'],
                'gps' => [
                    'lat' => $shippingData['latitude'],
                    'long' => $shippingData['longitude']
                ]
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending data to courier system: ' . $e->getMessage(), [
                'order_code' => $shippingData['order_code'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Don't throw exception - order is already created
            // Courier data can be added manually if needed
            return false;
        }
    }

    /**
     * API endpoint to get nearest branch based on coordinates
     */
    public function getNearestBranch(Request $request)
    {
        try {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            if (!$latitude || !$longitude) {
                return response()->json([
                    'success' => false,
                    'message' => 'Latitude dan longitude harus disediakan'
                ], 400);
            }

            $nearestBranch = Cabang::findNearest($latitude, $longitude);

            if (!$nearestBranch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada cabang aktif yang ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'branch' => [
                    'id_cabang' => $nearestBranch->id_cabang,
                    'nama_cabang' => $nearestBranch->nama_cabang,
                    'kode_cabang' => $nearestBranch->kode_cabang,
                    'alamat' => $nearestBranch->alamat,
                    'formatted_address' => $nearestBranch->formatted_address,
                    'no_telepon' => $nearestBranch->no_telepon,
                    'jam_buka' => $nearestBranch->jam_buka,
                    'jam_tutup' => $nearestBranch->jam_tutup,
                    'google_maps_url' => $nearestBranch->google_maps_url,
                    'latitude' => $nearestBranch->latitude,
                    'longitude' => $nearestBranch->longitude,
                    'distance' => $nearestBranch->distance, // in kilometers
                    'shipping_cost' => Cabang::calculateShippingCost($nearestBranch->distance), // calculated based on distance
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting nearest branch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari cabang terdekat'
            ], 500);
        }
    }

    /**
     * Customer confirms order has been received
     */
    public function confirmOrderReceived(Request $request, $orderId)
    {
        try {
            $user = Auth::user();
            $pelanggan = $user->getOrCreatePelanggan();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pelanggan tidak ditemukan'
                ], 403);
            }

            // Get the order
            $order = Order::where('id_transaksi', $orderId)
                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                ->with('pengiriman')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Check if order is paid
            if ($order->status_pembayaran !== 'sudah_bayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan belum dibayar'
                ], 400);
            }

            // Check if there's shipping data
            if (!$order->pengiriman) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pengiriman tidak ditemukan'
                ], 404);
            }

            // Check if status is terkirim (delivered by courier)
            if (!in_array($order->pengiriman->status_pengiriman, ['terkirim', 'sampai'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan belum dalam status terkirim. Status saat ini: ' . $order->pengiriman->status_pengiriman
                ], 400);
            }

            // Check if already confirmed
            if ($order->pengiriman->status_pengiriman === 'selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan sudah dikonfirmasi sebelumnya'
                ], 400);
            }

            DB::beginTransaction();

            // Update shipping status to selesai
            $order->pengiriman->update([
                'status_pengiriman' => 'selesai',
                'tgl_konfirmasi_pelanggan' => now(),
            ]);

            // Also update order status if needed
            $order->update([
                'status_pengiriman' => 'selesai',
            ]);

            DB::commit();

            Log::info('Order confirmed as received by customer', [
                'order_code' => $order->kode_transaksi,
                'id_transaksi' => $order->id_transaksi,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'confirmed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih! Pesanan telah dikonfirmasi sebagai diterima. Anda sekarang dapat memberikan review untuk produk ini.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming order received: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi pesanan'
            ], 500);
        }
    }

    /**
     * Confirm order picked up (for ambil_sendiri method)
     */
    public function confirmPickup(Request $request, $orderId)
    {
        try {
            $user = Auth::user();
            $pelanggan = $user->getOrCreatePelanggan();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pelanggan tidak ditemukan'
                ], 403);
            }

            // Get the order
            $order = Order::where('id_transaksi', $orderId)
                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Check if order is paid
            if ($order->status_pembayaran !== 'sudah_bayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan belum dibayar'
                ], 400);
            }

            // Check if this is a pickup order
            if ($order->metode_pengiriman !== 'ambil_sendiri') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode pengiriman bukan ambil sendiri'
                ], 400);
            }

            // Check if status is siap_diambil
            if ($order->status_pengiriman !== 'siap_diambil') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan belum siap diambil. Status saat ini: ' . $order->status_pengiriman
                ], 400);
            }

            // Check if already confirmed
            if ($order->status_pengiriman === 'selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan sudah dikonfirmasi sebelumnya'
                ], 400);
            }

            DB::beginTransaction();

            // Update order status to selesai
            $order->update([
                'status_pengiriman' => 'selesai',
                'tgl_konfirmasi_pelanggan' => now(),
            ]);

            DB::commit();

            Log::info('Order confirmed as picked up by customer', [
                'order_code' => $order->kode_transaksi,
                'id_transaksi' => $order->id_transaksi,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'confirmed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih! Pesanan telah dikonfirmasi sebagai diambil. Anda sekarang dapat memberikan review untuk produk ini.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming order pickup: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi pengambilan pesanan'
            ], 500);
        }
    }

    /**
     * Kurangi stok di database integrasi setelah pembayaran sukses
     */
    private function kurangiStokSetelahPembayaran($order)
    {
        if (!$order->id_cabang) {
            Log::error("❌ Order {$order->kode_transaksi} tidak memiliki id_cabang, TIDAK BISA KURANGI STOK", [
                'order_id' => $order->id_transaksi,
                'metode_pengiriman' => $order->metode_pengiriman
            ]);
            return;
        }

        // Log cabang yang akan digunakan untuk pengurangan stok
        $cabang = Cabang::find($order->id_cabang);
        Log::info("🔄 START: Mengurangi stok untuk order {$order->kode_transaksi}", [
            'id_cabang' => $order->id_cabang,
            'nama_cabang' => $cabang ? $cabang->nama_cabang : 'Unknown',
            'metode_pengiriman' => $order->metode_pengiriman
        ]);

        try {
            $integrasiService = app(IntegrasiProdukService::class);

            // Get order details
            $orderDetails = DB::table('tb_detail_transaksi')
                ->where('id_transaksi', $order->id_transaksi)
                ->get();

            foreach ($orderDetails as $detail) {
                try {
                    // PENTING: id_produk di tb_detail_transaksi sekarang langsung merujuk ke ID produk integrasi
                    // Tidak perlu lookup ke CRM lagi
                    $integrasiService->kurangiStok(
                        $detail->id_produk, // Langsung pakai id_produk dari detail transaksi
                        $order->id_cabang,
                        $detail->qty
                    );

                    Log::info("✅ Stok berhasil dikurangi", [
                        'order_code' => $order->kode_transaksi,
                        'product_id' => $detail->id_produk,
                        'id_cabang' => $order->id_cabang,
                        'nama_cabang' => $cabang ? $cabang->nama_cabang : 'Unknown',
                        'qty' => $detail->qty
                    ]);
                } catch (\Exception $e) {
                    Log::error("❌ Gagal mengurangi stok", [
                        'order_code' => $order->kode_transaksi,
                        'product_id' => $detail->id_produk,
                        'id_cabang' => $order->id_cabang,
                        'nama_cabang' => $cabang ? $cabang->nama_cabang : 'Unknown',
                        'qty' => $detail->qty,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("❌ Error kurangiStokSetelahPembayaran: " . $e->getMessage());
        }
    }

    /**
     * Kembalikan stok di database integrasi (untuk pembatalan)
     */
    private function kembalikanStok($order)
    {
        if (!$order->id_cabang) {
            Log::warning("Order {$order->kode_transaksi} tidak memiliki id_cabang, skip pengembalian stok");
            return;
        }

        try {
            $integrasiService = app(IntegrasiProdukService::class);

            // Get order details
            $orderDetails = DB::table('tb_detail_transaksi')
                ->where('id_transaksi', $order->id_transaksi)
                ->get();

            foreach ($orderDetails as $detail) {
                try {
                    // PENTING: id_produk di tb_detail_transaksi sekarang langsung merujuk ke ID produk integrasi
                    // Tidak perlu lookup ke CRM lagi
                    $integrasiService->tambahStok(
                        $detail->id_produk, // Langsung pakai id_produk dari detail transaksi
                        $order->id_cabang,
                        $detail->qty
                    );

                    Log::info("Stok berhasil dikembalikan untuk order {$order->kode_transaksi}", [
                        'product_id' => $detail->id_produk,
                        'id_cabang' => $order->id_cabang,
                        'qty' => $detail->qty
                    ]);
                } catch (\Exception $e) {
                    Log::error("Gagal mengembalikan stok untuk order {$order->kode_transaksi}: " . $e->getMessage(), [
                        'id_produk' => $detail->id_produk,
                        'id_cabang' => $order->id_cabang,
                        'qty' => $detail->qty
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error kembalikanStok: " . $e->getMessage());
        }
    }
}
