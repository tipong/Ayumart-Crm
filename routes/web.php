<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dashboard\OwnerController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\CustomerServiceController;
use App\Http\Controllers\Dashboard\KurirController;
use App\Http\Controllers\Dashboard\PelangganController;
use App\Http\Controllers\Dashboard\MembershipController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CancellationController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\WishlistApiController;
use App\Http\Controllers\Api\TicketApiController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');

// API routes for cart, wishlist, and ticket count
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/cart/count', [CartApiController::class, 'count'])->name('api.cart.count');
    Route::get('/wishlist/count', [WishlistApiController::class, 'count'])->name('api.wishlist.count');
    Route::get('/tickets/count', [TicketApiController::class, 'count'])->name('api.tickets.count');
});

// Public API for location and branch
Route::post('/api/set-user-location', [HomeController::class, 'setUserLocation'])->name('api.set-user-location');
Route::post('/api/change-branch', [HomeController::class, 'changeBranch'])->name('api.change-branch');
Route::get('/api/nearest-branch', [OrderController::class, 'getNearestBranch'])->name('api.nearest-branch');
Route::get('/api/get-session-branch', [HomeController::class, 'getSessionBranch'])->name('api.get-session-branch');

// Protected API for address details
Route::middleware(['auth'])->get('/api/address/{id}', [AddressController::class, 'getAddressDetails'])->name('api.address.details');

// Auth routes for customers
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Customer registration
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Staff login routes
Route::get('/staff/login', [LoginController::class, 'showStaffLoginForm'])->name('staff.login');
Route::post('/staff/login', [LoginController::class, 'staffLogin'])->name('staff.login.submit');

// Protected routes - requires authentication
Route::middleware(['auth'])->group(function () {

    // Dashboard routes based on role
    Route::middleware(['checkRole:owner'])->group(function () {
        Route::get('/owner/dashboard', [OwnerController::class, 'index'])->name('owner.dashboard');
        Route::get('/owner/laporan', [OwnerController::class, 'laporanPenjualan'])->name('owner.laporan');
        Route::post('/owner/laporan/download', [OwnerController::class, 'downloadLaporan'])->name('owner.laporan.download');
        Route::post('/owner/laporan/download-cabang', [OwnerController::class, 'downloadLaporanPerCabang'])->name('owner.laporan.download-cabang');
        Route::get('/owner/reports', [OwnerController::class, 'reports'])->name('owner.reports');
        Route::get('/owner/reports/generate', [OwnerController::class, 'generateReport'])->name('owner.reports.generate');
        Route::get('/owner/analytics', [OwnerController::class, 'analytics'])->name('owner.analytics');
    });

    Route::middleware(['checkRole:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        // Discount Management
        Route::get('admin/discounts', [\App\Http\Controllers\Dashboard\DiscountController::class, 'index'])->name('admin.discounts.index');
        Route::get('admin/discounts/{product}/create', [\App\Http\Controllers\Dashboard\DiscountController::class, 'create'])->name('admin.discounts.create');
        Route::post('admin/discounts/{product}/store', [\App\Http\Controllers\Dashboard\DiscountController::class, 'store'])->name('admin.discounts.store');
        Route::get('admin/discounts/{product}/edit', [\App\Http\Controllers\Dashboard\DiscountController::class, 'edit'])->name('admin.discounts.edit');
        Route::put('admin/discounts/{product}/update', [\App\Http\Controllers\Dashboard\DiscountController::class, 'update'])->name('admin.discounts.update');
        Route::delete('admin/discounts/{product}/destroy', [\App\Http\Controllers\Dashboard\DiscountController::class, 'destroy'])->name('admin.discounts.destroy');
        Route::patch('admin/discounts/{product}/toggle', [\App\Http\Controllers\Dashboard\DiscountController::class, 'toggleStatus'])->name('admin.discounts.toggle');

        // Member Tier Discount Management
        Route::get('admin/discounts/{product}/member-discount', [\App\Http\Controllers\Dashboard\DiscountController::class, 'editMemberDiscount'])->name('admin.discounts.member-discount');
        Route::put('admin/discounts/{product}/member-discount', [\App\Http\Controllers\Dashboard\DiscountController::class, 'updateMemberDiscount'])->name('admin.discounts.update-member-discount');

        // Product Management (Keep for other roles if needed)
        Route::get('admin/products', [AdminController::class, 'products'])->name('admin.products.index');
        Route::get('admin/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
        Route::post('admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
        Route::get('admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
        Route::put('admin/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
        Route::delete('admin/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

        // Product Discount Management (deprecated, use discount routes instead)
        Route::post('admin/products/{product}/discount', [AdminController::class, 'updateDiscount'])->name('admin.products.discount.update');
        Route::delete('admin/products/{product}/discount', [AdminController::class, 'removeDiscount'])->name('admin.products.discount.remove');

        // Transaction Management
        Route::get('admin/transactions', [AdminController::class, 'transactions'])->name('admin.transactions.index');
        Route::get('admin/transactions/{transaction}', [AdminController::class, 'showTransaction'])->name('admin.transactions.show');
        Route::post('admin/transactions/{transaction}/cancel', [AdminController::class, 'cancelTransaction'])->name('admin.transactions.cancel');
        Route::put('admin/transactions/{transaction}/status', [AdminController::class, 'updateTransactionStatus'])->name('admin.transactions.status');
        Route::put('admin/transactions/{transaction}/shipping-status', [AdminController::class, 'updateShippingStatus'])->name('admin.transactions.update-shipping-status');

        // Cancellation Management
        Route::get('admin/cancellations', [CancellationController::class, 'index'])->name('admin.cancellations.index');
        Route::get('admin/cancellations/{cancellation}', [CancellationController::class, 'show'])->name('admin.cancellations.show');
        Route::post('admin/cancellations/{cancellation}/process', [CancellationController::class, 'processCancellation'])->name('admin.cancellations.process');

        // Staff Management
        Route::get('admin/staff', [AdminController::class, 'staff'])->name('admin.staff.index');
        Route::post('admin/staff', [AdminController::class, 'storeStaff'])->name('admin.staff.store');
        Route::put('admin/staff/{user}', [AdminController::class, 'updateStaff'])->name('admin.staff.update');
        Route::delete('admin/staff/{user}', [AdminController::class, 'destroyStaff'])->name('admin.staff.destroy');

        // Membership Management (jika diperlukan)
        Route::resource('admin/memberships', MembershipController::class)->names([
            'index' => 'admin.memberships.index',
            'create' => 'admin.memberships.create',
            'store' => 'admin.memberships.store',
            'edit' => 'admin.memberships.edit',
            'update' => 'admin.memberships.update',
            'destroy' => 'admin.memberships.destroy',
        ]);
    });

    Route::middleware(['checkRole:cs'])->group(function () {
        Route::get('/cs/dashboard', [CustomerServiceController::class, 'index'])->name('cs.dashboard');

        // Ticketing
        Route::get('cs/tickets', [TicketController::class, 'index'])->name('cs.tickets.index');
        Route::post('cs/tickets', [TicketController::class, 'store'])->name('cs.tickets.store');
        Route::get('cs/tickets/{ticket}', [TicketController::class, 'show'])->name('cs.tickets.show');
        Route::put('cs/tickets/{ticket}', [TicketController::class, 'update'])->name('cs.tickets.update');
        Route::post('cs/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('cs.tickets.reply');
        Route::delete('cs/tickets/{ticket}', [TicketController::class, 'destroy'])->name('cs.tickets.destroy');

        // Newsletter
        Route::resource('cs/newsletters', NewsletterController::class)->names([
            'index' => 'cs.newsletters.index',
            'create' => 'cs.newsletters.create',
            'store' => 'cs.newsletters.store',
            'show' => 'cs.newsletters.show',
            'edit' => 'cs.newsletters.edit',
            'update' => 'cs.newsletters.update',
            'destroy' => 'cs.newsletters.destroy',
        ]);
        Route::post('cs/newsletters/{newsletter}/send', [NewsletterController::class, 'send'])->name('cs.newsletters.send');

        // Subscribers
        Route::get('cs/subscribers', [CustomerServiceController::class, 'subscribers'])->name('cs.subscribers.index');
        Route::post('cs/subscribers', [CustomerServiceController::class, 'storeSubscriber'])->name('cs.subscribers.store');
        Route::delete('cs/subscribers', [CustomerServiceController::class, 'destroySubscriber'])->name('cs.subscribers.destroy');

        // Fonnte Subscribers
        Route::post('cs/fonnte-subscribers', [CustomerServiceController::class, 'storeFonnteSubscriber'])->name('cs.fonnte-subscribers.store');
    });

    Route::middleware(['checkRole:kurir'])->group(function () {
        Route::get('/kurir/dashboard', [KurirController::class, 'index'])->name('kurir.dashboard');
        Route::post('/kurir/deliveries/{id}/claim', [KurirController::class, 'claimDelivery'])->name('kurir.deliveries.claim');
        Route::post('/kurir/deliveries/{id}/complete', [KurirController::class, 'completeDelivery'])->name('kurir.deliveries.complete');

        // Shipping Management (Kelola Pengiriman)
        Route::get('/kurir/shipping', [KurirController::class, 'manageShipping'])->name('kurir.shipping.index');
        Route::post('/kurir/shipping/{shipment}/assign', [KurirController::class, 'assignCourier'])->name('kurir.shipping.assign');
        Route::post('/kurir/shipping/{shipment}/unassign', [KurirController::class, 'unassignCourier'])->name('kurir.shipping.unassign');

        // Delivery Management
        Route::post('/kurir/deliveries/{id}/claim', [KurirController::class, 'claimDelivery'])->name('kurir.deliveries.claim');
        Route::post('/kurir/deliveries/{id}/complete', [KurirController::class, 'completeDelivery'])->name('kurir.deliveries.complete');
    });

    Route::middleware(['checkRole:pelanggan'])->group(function () {
        // Cart
        Route::get('/cart', [PelangganController::class, 'cart'])->name('pelanggan.cart');
        Route::post('/cart/add/{productId}', [PelangganController::class, 'addToCart'])->name('cart.add');
        Route::put('/cart/{cartItem}', [PelangganController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/{cartItem}', [PelangganController::class, 'removeFromCart'])->name('cart.remove');

        // Wishlist
        Route::get('/wishlist', [PelangganController::class, 'wishlist'])->name('pelanggan.wishlist');
        Route::post('/wishlist/add/{productId}', [PelangganController::class, 'addToWishlist'])->name('wishlist.add');
        Route::delete('/wishlist/{wishlist}', [PelangganController::class, 'removeFromWishlist'])->name('wishlist.remove');

        // Checkout & Orders
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [OrderController::class, 'placeOrder'])->name('order.place');
        Route::get('/orders', [OrderController::class, 'orders'])->name('pelanggan.orders');
        Route::get('/orders/{id}', [OrderController::class, 'orderDetail'])->name('pelanggan.orders.detail');
        Route::get('/orders/{id}/continue-payment', [OrderController::class, 'continuePayment'])->name('pelanggan.orders.continue-payment');
        Route::get('/payment-status/{orderId}', [OrderController::class, 'checkPaymentStatus'])->name('payment.status');
        Route::get('/order-status/{orderId}', [OrderController::class, 'checkPaymentStatus'])->name('order.check-status');

        // Order Cancellation
        Route::post('/orders/{order}/cancel', [CancellationController::class, 'requestCancellation'])->name('pelanggan.orders.cancel');

        // Order Confirmation (Customer confirms received)
        Route::post('/orders/{orderId}/confirm-received', [OrderController::class, 'confirmOrderReceived'])->name('pelanggan.orders.confirm-received');

        // Order Pickup Confirmation (Customer confirms picked up for ambil_sendiri)
        Route::post('/orders/{orderId}/confirm-pickup', [OrderController::class, 'confirmPickup'])->name('pelanggan.orders.confirm-pickup');

        // Addresses
        Route::get('/addresses', [AddressController::class, 'index'])->name('pelanggan.addresses.index');
        Route::get('/addresses/create', [AddressController::class, 'create'])->name('pelanggan.addresses.create');
        Route::post('/addresses', [AddressController::class, 'store'])->name('pelanggan.addresses.store');
        Route::get('/addresses/{id}/edit', [AddressController::class, 'edit'])->name('pelanggan.addresses.edit');
        Route::put('/addresses/{id}', [AddressController::class, 'update'])->name('pelanggan.addresses.update');
        Route::delete('/addresses/{id}', [AddressController::class, 'destroy'])->name('pelanggan.addresses.destroy');
        Route::put('/addresses/{id}/set-default', [AddressController::class, 'setDefault'])->name('pelanggan.addresses.set-default');
        Route::get('/addresses/checkout', [AddressController::class, 'getForCheckout'])->name('pelanggan.addresses.checkout');

        // Reviews
        Route::get('/orders/{orderId}/review/{productId}', [\App\Http\Controllers\ReviewController::class, 'create'])->name('pelanggan.review.create');
        Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('pelanggan.review.store');
        Route::get('/my-reviews', [\App\Http\Controllers\ReviewController::class, 'myReviews'])->name('pelanggan.reviews.index');
        Route::get('/reviews/{reviewId}', [\App\Http\Controllers\ReviewController::class, 'show'])->name('pelanggan.review.show');
        Route::put('/reviews/{reviewId}', [\App\Http\Controllers\ReviewController::class, 'update'])->name('pelanggan.review.update');
        Route::delete('/reviews/{reviewId}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('pelanggan.review.destroy');

        // Product Reviews
        Route::get('/products/{productId}/reviews', [\App\Http\Controllers\ReviewController::class, 'showProductReviews'])->name('products.reviews');

        // Tickets (Pelanggan)
        Route::get('/tickets', [TicketController::class, 'customerIndex'])->name('pelanggan.tickets.index');
        Route::get('/tickets/create', [TicketController::class, 'create'])->name('pelanggan.tickets.create');
        Route::post('/tickets', [TicketController::class, 'store'])->name('pelanggan.tickets.store');
        Route::get('/tickets/{ticket}', [TicketController::class, 'customerShow'])->name('pelanggan.tickets.show');
        Route::post('/tickets/{ticket}/reply', [TicketController::class, 'customerReply'])->name('pelanggan.tickets.reply');
        Route::put('/tickets/{ticket}/close', [TicketController::class, 'customerClose'])->name('pelanggan.tickets.close');

        // Profile
        Route::get('/profile', [PelangganController::class, 'profile'])->name('pelanggan.profile');
        Route::put('/profile', [PelangganController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [PelangganController::class, 'updatePassword'])->name('profile.update.password');
        Route::get('/membership', [PelangganController::class, 'membership'])->name('membership');
    });
});

// Newsletter subscription (public)
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Midtrans Payment Callback
Route::post('/midtrans/callback', [OrderController::class, 'paymentCallback'])->name('midtrans.callback');

// API Routes for AJAX calls (using web middleware for session)
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/notifications/count', [App\Http\Controllers\Api\NotificationApiController::class, 'count'])->name('api.notifications.count');
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationApiController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationApiController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationApiController::class, 'markAllAsRead'])->name('api.notifications.readAll');
});

// Test routes
require __DIR__ . '/test-dashboard.php';
require __DIR__ . '/test-login.php';

// Test routes for debugging
Route::get('/test-branch-session', function () {
    $service = app(\App\Services\NearestBranchService::class);

    // Save test branch
    $service->saveToSession(2, 'Test Branch Badung', 5.5);

    // Get back
    $current = $service->getCurrentBranch();

    return response()->json([
        'saved' => true,
        'current_branch' => $current,
        'session_nearest_branch' => session('nearest_branch'),
        'session_nearest_branch_id' => session('nearest_branch_id'),
        'all_session_keys' => array_keys(session()->all())
    ]);
});

// Test Member Growth Logic

