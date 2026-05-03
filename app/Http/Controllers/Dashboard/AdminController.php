<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Staff;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Pengiriman;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pelanggan;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        try {
            // Use Order model (tb_transaksi) - our restructured system
            $totalOrders = Order::count();
            $pendingOrders = Order::where('status_pembayaran', 'belum_bayar')->count();

            // Count total staff (users with id_role 1-4: Owner, Admin, CS, Kurir)
            $totalStaff = User::whereIn('id_role', [1, 2, 3, 4])->count();

            // Count total active memberships
            $totalMemberships = \App\Models\Membership::where('is_active', true)->count();

            // Recent orders with relationships
            $recentOrders = Order::with(['pelanggan', 'cabang'])->latest('tanggal_transaksi')->take(10)->get();

            // Get top 5 most sold products from tb_detail_transaksi with proper JOIN
            try {
                // Step 1: Get total quantity per product from tb_detail_transaksi
                $topProductIds = DB::table('tb_detail_transaksi as dt')
                    ->select(
                        'dt.id_produk',
                        DB::raw('SUM(dt.qty) as total_sold')
                    )
                    ->groupBy('dt.id_produk')
                    ->orderByDesc('total_sold')
                    ->limit(5)
                    ->get();

                Log::info('📊 Top Product IDs Query Result: ' . count($topProductIds) . ' products found', [
                    'product_ids' => $topProductIds->pluck('id_produk')->toArray(),
                    'quantities' => $topProductIds->pluck('total_sold')->toArray()
                ]);

                // Step 2: Get product details from integrasi database for those IDs
                if ($topProductIds->count() > 0) {
                    $productIds = $topProductIds->pluck('id_produk')->toArray();

                    // Use DB connection to integrasi database (mysql_integrasi in config/database.php)
                    $products = DB::connection('mysql_integrasi')
                        ->table('tb_produk')
                        ->whereIn('id_produk', $productIds)
                        ->select('id_produk', 'nama_produk', 'foto_produk')
                        ->get()
                        ->keyBy('id_produk');

                    Log::info('✅ Product Details Retrieved: ' . count($products) . ' products with info');

                    // Step 3: Merge the data - combine quantity with product info
                    $topProducts = $topProductIds->map(function ($item) use ($products) {
                        $product = $products[$item->id_produk] ?? null;
                        return (object)[
                            'id_produk' => $item->id_produk,
                            'nama_produk' => $product->nama_produk ?? 'Unknown Product',
                            'total_sold' => $item->total_sold,
                            'foto_produk' => $product->foto_produk ?? null,
                        ];
                    })->values();

                    Log::info('🎉 Top Products Final Result: ' . count($topProducts) . ' products prepared for view', [
                        'products' => $topProducts->map(fn($p) => [
                            'id' => $p->id_produk,
                            'name' => $p->nama_produk,
                            'sold' => $p->total_sold
                        ])->toArray()
                    ]);
                } else {
                    $topProducts = collect([]);
                    Log::warning('⚠️ No products found in tb_detail_transaksi');
                }

            } catch (\Exception $e) {
                Log::error('❌ Error in top products query: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());

                // Fallback: Show just product IDs with sales quantity
                try {
                    $topProducts = DB::table('tb_detail_transaksi as dt')
                        ->select(
                            'dt.id_produk',
                            DB::raw('CONCAT("Product #", dt.id_produk) as nama_produk'),
                            DB::raw('SUM(dt.qty) as total_sold'),
                            DB::raw('NULL as foto_produk')
                        )
                        ->groupBy('dt.id_produk')
                        ->orderByDesc('total_sold')
                        ->limit(5)
                        ->get();

                    Log::warning('⚠️ Using fallback query without product details. Result: ' . count($topProducts));
                } catch (\Exception $e2) {
                    Log::error('❌ Fallback query also failed: ' . $e2->getMessage());
                    $topProducts = collect([]);
                }
            }

            // Member statistics
            try {
                $totalMembers = Pelanggan::count();
                $activeMemberCount = DB::table('memberships')->where('is_active', true)->count();
                $memberPercentage = $totalMembers > 0
                    ? round(($activeMemberCount / $totalMembers) * 100, 2)
                    : 0;

                Log::info("Member Stats: Total=$totalMembers, Active=$activeMemberCount, Percentage=$memberPercentage%");
            } catch (\Exception $e) {
                Log::error('Error in member statistics: ' . $e->getMessage());
                $totalMembers = 0;
                $activeMemberCount = 0;
                $memberPercentage = 0;
            }

            // Member growth data (last 12 months)
            try {
                $memberGrowthData = DB::select("
                    SELECT
                        MONTH(created_at) as month,
                        YEAR(created_at) as year,
                        COUNT(*) as count
                    FROM memberships
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 13 MONTH)
                    GROUP BY year, month
                    ORDER BY year ASC, month ASC
                ");

                Log::info('✅ Member Growth Data Points: ' . count($memberGrowthData));

                if (empty($memberGrowthData)) {
                    Log::warning('⚠️ Member growth query returned 0 results');
                    $memberGrowthData = [];
                }
            } catch (\Exception $e) {
                Log::error('❌ Error in member growth query: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                $memberGrowthData = [];
            }

            // Format member growth data for chart - show last 12 months (simpler approach)
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $memberChartData = [];
            $memberChartLabels = [];

            // Create a map of month-year to count
            $dataMap = [];
            foreach ($memberGrowthData as $data) {
                $data = (array)$data;
                $key = $data['year'] . '-' . str_pad($data['month'], 2, '0', STR_PAD_LEFT);
                $dataMap[$key] = $data['count'];
                Log::debug("Data point: $key = " . $data['count']);
            }

            // Generate the last 12 months dynamically
            $now = now();
            $startDate = $now->copy()->subMonths(11)->startOfMonth(); // Go back 11 months from now

            for ($i = 0; $i < 12; $i++) {
                $monthNum = $startDate->month;
                $yearNum = $startDate->year;
                $key = $yearNum . '-' . str_pad($monthNum, 2, '0', STR_PAD_LEFT);

                $memberChartLabels[] = $months[$monthNum - 1] . " '" . substr($yearNum, -2);
                $memberChartData[] = $dataMap[$key] ?? 0;

                Log::debug("Chart slot $i: $key => " . ($dataMap[$key] ?? 0));
                $startDate->addMonth();
            }

            // Debug logging for member growth chart
            Log::info('📊 Member Growth Chart Debug Info', [
                'chart_values_count' => count($memberChartData),
                'chart_labels_count' => count($memberChartLabels),
                'chart_values' => $memberChartData,
                'chart_labels' => $memberChartLabels,
                'has_data' => count($memberChartData) > 0 ? 'YES' : 'NO',
                'sum_of_values' => array_sum($memberChartData),
                'max_value' => max($memberChartData) ?? 0,
            ]);

            // Ensure memberChartValues is array not collection
            $memberChartValues = is_array($memberChartData) ? $memberChartData : $memberChartData->toArray();
            $memberChartLabels = is_array($memberChartLabels) ? $memberChartLabels : $memberChartLabels->toArray();

            return view('admin.dashboard', compact(
                'totalOrders',
                'pendingOrders',
                'totalStaff',
                'totalMemberships',
                'recentOrders',
                'topProducts',
                'totalMembers',
                'activeMemberCount',
                'memberPercentage',
                'memberChartLabels',
                'memberChartValues'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading admin dashboard: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return dashboard with default values if error occurs
            return view('admin.dashboard', [
                'totalOrders' => 0,
                'pendingOrders' => 0,
                'totalStaff' => 0,
                'totalMemberships' => 0,
                'recentOrders' => collect([]),
                'topProducts' => collect([]),
                'totalMembers' => 0,
                'activeMemberCount' => 0,
                'memberPercentage' => 0,
                'memberChartLabels' => [],
                'memberChartValues' => []
            ]);
        }
    }

    public function staff()
    {
        try {
            // Get staff users with their biodata from tb_staff
            $staff = User::whereIn('id_role', [1, 2, 3, 4])
                        ->with('staff')
                        ->orderBy('id_role')
                        ->orderBy('email')
                        ->get();

            // Role options for dropdown
            $roles = [
                1 => 'Owner',
                2 => 'Admin',
                3 => 'Customer Service',
                4 => 'Kurir',
            ];

            return view('admin.staff.index', compact('staff', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error loading staff list: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat data staff.');
        }
    }

    public function storeStaff(Request $request)
    {
        try {
            $validated = $request->validate([
                'role_id' => 'required|in:1,2,3,4',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:8',
                'profil_staff' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'id_role' => $validated['role_id'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Map role_id to posisi_staff
            $posisiMap = [
                1 => 'Owner',
                2 => 'Admin',
                3 => 'Customer Service',
                4 => 'Kurir',
            ];

            // Create staff biodata
            Staff::create([
                'id_user' => $user->id_user,
                'nama_staff' => $validated['name'],
                'email_staff' => $validated['email'],
                'posisi_staff' => $posisiMap[$validated['role_id']],
                'profil_staff' => $validated['profil_staff'] ?? null,
                'no_tlp_staff' => $validated['phone'],
                'status_akun' => 'aktif',
            ]);

            DB::commit();

            return redirect()->route('admin.staff.index')->with('success', 'Staff berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating staff: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat menambahkan staff. Silakan coba lagi.')->withInput();
        }
    }

    public function updateStaff(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'role_id' => 'required|in:1,2,3,4',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
                'phone' => 'required|string|max:20',
                'is_active' => 'required|boolean',
                'profil_staff' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Update user account
            $user->update([
                'id_role' => $validated['role_id'],
                'email' => $validated['email'],
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            // Map role_id to posisi_staff
            $posisiMap = [
                1 => 'Owner',
                2 => 'Admin',
                3 => 'Customer Service',
                4 => 'Kurir',
            ];

            // Update or create staff biodata
            Staff::updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nama_staff' => $validated['name'],
                    'email_staff' => $validated['email'],
                    'posisi_staff' => $posisiMap[$validated['role_id']],
                    'profil_staff' => $validated['profil_staff'] ?? null,
                    'no_tlp_staff' => $validated['phone'],
                    'status_akun' => $validated['is_active'] ? 'aktif' : 'nonaktif',
                ]
            );

            DB::commit();

            return redirect()->route('admin.staff.index')->with('success', 'Staff berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating staff: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat mengupdate staff. Silakan coba lagi.')->withInput();
        }
    }

    public function destroyStaff(User $user)
    {
        try {
            DB::beginTransaction();

            // Delete staff biodata (will cascade from user_id foreign key)
            if ($user->staff) {
                $user->staff->delete();
            }

            // Delete user account
            $user->delete();

            DB::commit();

            return redirect()->route('admin.staff.index')->with('success', 'Staff berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting staff: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat menghapus staff. Silakan coba lagi.');
        }
    }

    // ==================== MANAJEMEN PRODUK ====================

    public function products()
    {
        try {
            $products = Product::orderBy('nama_produk')->paginate(15);
            return view('admin.products.index', compact('products'));
        } catch (\Exception $e) {
            Log::error('Error loading products: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat data produk.');
        }
    }

    public function createProduct()
    {
        $categories = DB::table('tb_jenis')->orderBy('nama_jenis')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category' => 'required|exists:tb_jenis,id_jenis',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Map English field names to Indonesian database columns
            // Generate kode_produk: PRD + random 5 digits
            $productData = [
                'kode_produk' => 'P' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'nama_produk' => $validated['name'],
                'deskripsi_produk' => $validated['description'],
                'id_jenis' => $validated['category'],
                'harga_produk' => $validated['price'],
                'stok_produk' => $validated['stock'],
                'status_produk' => 'aktif',
            ];

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $productData['foto_produk'] = $imagePath;
            }

            Product::create($productData);

            return redirect()->route('admin.products.index')
                           ->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat menambahkan produk. Silakan coba lagi.')
                       ->withInput();
        }
    }

    public function editProduct(Product $product)
    {
        $categories = DB::table('tb_jenis')->orderBy('nama_jenis')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category' => 'required|exists:tb_jenis,id_jenis',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Map English field names to Indonesian database columns
            $productData = [
                'nama_produk' => $validated['name'],
                'deskripsi_produk' => $validated['description'],
                'id_jenis' => $validated['category'],
                'harga_produk' => $validated['price'],
                'stok_produk' => $validated['stock'],
            ];

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
                    Storage::disk('public')->delete($product->foto_produk);
                }
                $imagePath = $request->file('image')->store('products', 'public');
                $productData['foto_produk'] = $imagePath;
            }

            $product->update($productData);

            return redirect()->route('admin.products.index')
                           ->with('success', 'Produk berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat mengupdate produk. Silakan coba lagi.')
                       ->withInput();
        }
    }

    public function destroyProduct(Product $product)
    {
        try {
            // Delete image if exists
            if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
                Storage::disk('public')->delete($product->foto_produk);
            }

            $product->delete();
            return redirect()->route('admin.products.index')
                           ->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat menghapus produk. Silakan coba lagi.');
        }
    }

    // ==================== MANAJEMEN DISKON PRODUK ====================

    /**
     * Update discount for a product
     */
    public function updateDiscount(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'discount_price' => 'required|numeric|min:0|lt:' . $product->harga_produk,
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'discount_price.lt' => 'Harga diskon harus lebih kecil dari harga normal',
                'end_date.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
            ]);

            // Calculate percentage if not provided
            $percentage = $validated['discount_percentage'] ??
                round((($product->harga_produk - $validated['discount_price']) / $product->harga_produk) * 100, 2);

            $product->update([
                'harga_diskon' => $validated['discount_price'],
                'persentase_diskon' => $percentage,
                'tanggal_mulai_diskon' => $validated['start_date'],
                'tanggal_akhir_diskon' => $validated['end_date'],
                'is_diskon_active' => true,
            ]);

            Log::info('Product discount updated', [
                'product_id' => $product->id_produk,
                'product_name' => $product->nama_produk,
                'original_price' => $product->harga_produk,
                'discount_price' => $validated['discount_price'],
                'percentage' => $percentage,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            return redirect()->back()
                ->with('success', 'Diskon produk berhasil ditambahkan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating product discount: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat menambahkan diskon. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Remove discount from a product
     */
    public function removeDiscount(Product $product)
    {
        try {
            $product->update([
                'harga_diskon' => null,
                'persentase_diskon' => null,
                'tanggal_mulai_diskon' => null,
                'tanggal_akhir_diskon' => null,
                'is_diskon_active' => false,
            ]);

            Log::info('Product discount removed', [
                'product_id' => $product->id_produk,
                'product_name' => $product->nama_produk,
            ]);

            return redirect()->back()
                ->with('success', 'Diskon produk berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error removing product discount: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat menghapus diskon.');
        }
    }

    // ==================== MANAJEMEN TRANSAKSI ====================

    public function transactions()
    {
        try {
            // Use Order model (tb_transaksi) which has tanggal_transaksi column
            $transactions = Order::with(['pelanggan.user', 'cancellation', 'cabang'])
                                ->orderBy('tanggal_transaksi', 'desc')
                                ->paginate(15);

            // Count based on actual status_pembayaran values
            $statusCounts = [
                'pending' => Order::where('status_pembayaran', 'belum_bayar')->count(),
                'completed' => Order::where('status_pembayaran', 'sudah_bayar')->count(),
                'cancelled' => Order::where('status_pembayaran', 'kadaluarsa')->count(),
            ];

            return view('admin.transactions.index', compact('transactions', 'statusCounts'));
        } catch (\Exception $e) {
            Log::error('Error loading transactions: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat data transaksi.');
        }
    }

    public function showTransaction(Order $transaction)
    {
        try {
            $transaction->load(['pelanggan.user', 'details.product', 'cancellation', 'cabang']);
            return view('admin.transactions.show', compact('transaction'));
        } catch (\Exception $e) {
            Log::error('Error loading transaction detail: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat detail transaksi.');
        }
    }

    public function cancelTransaction(Request $request, Order $transaction)
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:500',
            ]);

            if ($transaction->status === 'cancelled') {
                return back()->with('error', 'Transaksi sudah dibatalkan sebelumnya.');
            }

            if ($transaction->status === 'completed') {
                return back()->with('error', 'Transaksi yang sudah selesai tidak dapat dibatalkan.');
            }

            // Update transaction status
            $transaction->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
            ]);

            // Restore product stock
            foreach ($transaction->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            return redirect()->route('admin.transactions.index')
                           ->with('success', 'Transaksi berhasil dibatalkan dan stok produk telah dikembalikan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error cancelling transaction: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat membatalkan transaksi. Silakan coba lagi.');
        }
    }

    public function updateTransactionStatus(Request $request, Order $transaction)
    {
        try {
            $validated = $request->validate([
                'status_pembayaran' => 'required|in:belum_bayar,sudah_bayar,kadaluarsa',
            ]);

            $transaction->update(['status_pembayaran' => $validated['status_pembayaran']]);

            return back()->with('success', 'Status pembayaran berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error updating transaction status: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat mengupdate status transaksi.');
        }
    }

    public function updateShippingStatus(Request $request, Order $transaction)
    {
        try {
            $validated = $request->validate([
                'status_pengiriman' => 'required|in:pending,dikemas,dikirim,terkirim,selesai',
            ]);

            // Only allow shipping status update for paid transactions
            if ($transaction->status_pembayaran !== 'sudah_bayar') {
                return back()->with('error', 'Status pengiriman hanya dapat diupdate untuk transaksi yang sudah dibayar.');
            }

            $transaction->update(['status_pengiriman' => $validated['status_pengiriman']]);

            return back()->with('success', 'Status pengiriman berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error updating shipping status: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat mengupdate status pengiriman.');
        }
    }
}

