@extends('layouts.pelanggan')

@section('title', 'Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="bi bi-credit-card"></i> Pembayaran</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Silakan selesaikan pembayaran Anda
                        </div>

                        <div class="mb-3">
                            <p class="text-muted mb-1">Kode Transaksi</p>
                            <h5 class="text-primary">{{ $order->kode_transaksi }}</h5>
                        </div>

                        <div class="mb-4">
                            <p class="text-muted mb-1">Total Pembayaran</p>
                            <h3 class="text-success" id="displayTotal">
                                @php
                                    $totalAmount = $order->total_harga - $order->total_diskon + $order->ongkir + ($order->biaya_membership ?? 0);
                                @endphp
                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </h3>
                        </div>

                        <!-- Payment Expiry Timer -->
                        @if($order->status_pembayaran === 'belum_bayar' && $order->payment_expired_at)
                        <div class="alert alert-warning" id="paymentExpiryAlert">
                            <i class="bi bi-clock"></i>
                            <strong>Pembayaran harus diselesaikan dalam:</strong>
                            <div id="expiryTimer" class="text-danger mt-2" style="font-size: 1.3rem; font-weight: bold;">
                                15:00
                            </div>
                            <small class="text-muted d-block mt-2">Jika waktu habis, pesanan otomatis dibatalkan</small>
                        </div>
                        @endif

                        <!-- Debug Info (Development Only) -->
                        <div class="alert alert-secondary mt-3" id="paymentDebugInfo" style="display: none; font-size: 0.85rem;">
                            <strong>Debug Info:</strong>
                            <div id="debugContent"></div>
                        </div>
                    </div>

                    <!-- Payment button -->
                    @if($snapToken && config('services.midtrans.client_key'))
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-primary btn-lg" id="pay-button">
                            <i class="bi bi-credit-card"></i> Bayar Sekarang
                        </button>
                        <div class="mt-2">
                            <small class="text-muted" id="paymentLoadingStatus">Mempersiapkan sistem pembayaran...</small>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-danger text-center">
                        <h6><i class="bi bi-exclamation-triangle"></i> Sistem Pembayaran Tidak Tersedia</h6>
                        <p class="mb-2">
                            @if(!$snapToken)
                                ❌ Token pembayaran tidak tergenerate
                            @elseif(!config('services.midtrans.client_key'))
                                ❌ Midtrans client key tidak dikonfigurasi
                            @else
                                ❌ Ada masalah dengan sistem pembayaran
                            @endif
                        </p>
                        <p class="mb-3">Silakan lakukan pembayaran manual:</p>
                        <div class="text-start mt-3">
                            <strong>Transfer ke:</strong><br>
                            Bank BCA<br>
                            No. Rek: 1234567890<br>
                            A.n. Supermarket ABC<br>
                            <br>
                            <strong>Jumlah: Rp {{ number_format($total, 0, ',', '.') }}</strong><br>
                            <strong>Kode Transaksi: {{ $order->kode_transaksi }}</strong>
                        </div>
                        <p class="mt-3 mb-2 small text-muted">
                            Setelah transfer, hubungi CS untuk konfirmasi pembayaran
                        </p>
                        <p class="mb-0 small">
                            <a href="{{ route('pelanggan.orders') }}" class="text-decoration-none">Kembali ke Daftar Pesanan</a>
                        </p>
                    </div>
                    @endif

                    <!-- Order Details -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-receipt"></i> Detail Pesanan</h6>

                            <div class="row">
                                <div class="col-6 mb-2">
                                    <small class="text-muted">Metode Pengiriman</small>
                                    <p class="mb-0">
                                        @if($order->metode_pengiriman === 'kurir')
                                            <i class="bi bi-truck"></i> Dikirim Kurir
                                        @else
                                            <i class="bi bi-shop"></i> Ambil Sendiri
                                        @endif
                                    </p>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted">Status Pembayaran</small>
                                    <p class="mb-0">
                                        <span class="badge bg-warning" id="paymentStatus">Menunggu Pembayaran</span>
                                    </p>
                                </div>
                                <div class="col-12 mt-2">
                                    <small class="text-muted">Tanggal Transaksi</small>
                                    <p class="mb-0">{{ $order->tanggal_transaksi ? $order->tanggal_transaksi->format('d M Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Breakdown -->
                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-calculator"></i> Rincian Pembayaran</h6>

                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted">Subtotal Produk</span>
                                <span class="text-muted">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            </div>

                            @if($order->total_diskon > 0)
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom text-success">
                                <span>
                                    <i class="bi bi-tag-fill"></i> Diskon Membership
                                </span>
                                <span>- Rp {{ number_format($order->total_diskon, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($order->ongkir > 0)
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span>
                                    <i class="bi bi-truck"></i> Ongkos Kirim
                                </span>
                                <span>Rp {{ number_format($order->ongkir, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($order->biaya_membership > 0)
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom text-info">
                                <span>
                                    <i class="bi bi-award"></i> Biaya Pembuatan Member
                                </span>
                                <span>Rp {{ number_format($order->biaya_membership, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between mt-3 pt-2">
                                <strong>Total Pembayaran</strong>
                                <strong class="text-success">Rp {{ number_format($order->total_harga - $order->total_diskon + $order->ongkir + ($order->biaya_membership ?? 0), 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted small">
                            <i class="bi bi-shield-check"></i> Pembayaran aman dengan Midtrans
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('pelanggan.orders') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pesanan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap -->
@if($snapToken && config('services.midtrans.client_key'))
<script type="text/javascript">
    /**
     * ✅ ULTRA-ROBUST Midtrans Payment Flow v2
     *
     * CRITICAL IMPROVEMENTS:
     * 1. Load snap.js first with error handling
     * 2. Use Promise to wait for snap.js load
     * 3. Multiple fallback mechanisms for snap detection
     * 4. Explicit manual trigger + auto trigger backup
     * 5. Comprehensive logging and error reporting
     */

    // ========== Global State ==========
    let snapLoaded = false;
    let snapError = false;
    let autoTriggered = false;
    let paymentInProgress = false;

    // ========== DOM Elements ==========
    const payButton = document.getElementById('pay-button');
    const paymentStatus = document.getElementById('paymentStatus');
    const paymentLoadingStatus = document.getElementById('paymentLoadingStatus');

    // ========== Constants ==========
    const snapToken = '{{ $snapToken }}';
    const orderId = '{{ $order->id_transaksi }}';
    const midtransOrderId = '{{ $order->midtrans_order_id ?? $order->kode_transaksi }}';
    const clientKey = '{{ config("services.midtrans.client_key") }}';

    // ========== Initial Validation ==========
    console.log('%c💳 PAYMENT PAGE INITIALIZED', 'color: blue; font-weight: bold;', {
        orderId: orderId,
        midtransOrderId: midtransOrderId,
        snapTokenPresent: !!snapToken,
        snapTokenLength: snapToken.length,
        clientKeyPresent: !!clientKey,
        clientKeyLength: clientKey.length
    });

    // Validate critical data
    if (!snapToken || snapToken.length < 10) {
        console.error('❌ CRITICAL: snapToken is missing or invalid!');
        if (paymentLoadingStatus) {
            paymentLoadingStatus.textContent = '❌ Error: Token pembayaran tidak valid. Hubungi CS.';
            paymentLoadingStatus.classList.add('text-danger');
        }
    }

    if (!clientKey || clientKey.length < 10) {
        console.error('❌ CRITICAL: Midtrans client key is missing or invalid!');
        if (paymentLoadingStatus) {
            paymentLoadingStatus.textContent = '❌ Error: Konfigurasi Midtrans tidak valid.';
            paymentLoadingStatus.classList.add('text-danger');
        }
    }

    // ========== Promise-based Snap Loading ==========
    const snapLoadPromise = new Promise((resolve, reject) => {
        // Check if snap already loaded (from previous script)
        if (typeof window.snap !== 'undefined') {
            console.log('✅ Snap already available globally');
            snapLoaded = true;
            resolve(window.snap);
            return;
        }

        // Create script tag dynamically with proper error handling
        const snapScript = document.createElement('script');
        snapScript.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
        snapScript.setAttribute('data-client-key', clientKey);

        // On successful load
        snapScript.onload = function() {
            console.log('✅ Snap.js loaded from CDN');
            snapLoaded = true;

            // Verify snap object exists
            if (typeof window.snap !== 'undefined') {
                console.log('✅ Snap object verified globally available');
                resolve(window.snap);
            } else {
                console.error('❌ Snap loaded but object not found');
                snapError = true;
                reject(new Error('Snap object not available'));
            }
        };

        // On load error
        snapScript.onerror = function() {
            console.error('❌ Failed to load snap.js from CDN');
            snapError = true;
            reject(new Error('Failed to load snap.js'));
        };

        // Set a timeout as additional safety
        const loadTimeout = setTimeout(function() {
            if (!snapLoaded && !snapError) {
                console.error('❌ Snap.js load timeout (5 seconds)');
                snapError = true;
                reject(new Error('Snap.js load timeout'));
            }
        }, 5000);

        // Append script to head
        document.head.appendChild(snapScript);
    });

    // ========== Midtrans Payment Execution ==========
    async function executePayment() {
        if (paymentInProgress) {
            console.warn('⚠️ Payment already in progress');
            return;
        }

        paymentInProgress = true;

        try {
            // Wait for snap to be available
            if (!snapLoaded) {
                if (paymentLoadingStatus) {
                    paymentLoadingStatus.textContent = '⏳ Menunggu sistem pembayaran siap...';
                }

                console.log('⏳ Waiting for Snap.js to load before payment...');
                await snapLoadPromise;
            }

            // Double-check snap is available
            if (typeof snap === 'undefined') {
                throw new Error('Snap object not available');
            }

            console.log('🚀 EXECUTING PAYMENT with snap token');

            if (paymentLoadingStatus) {
                paymentLoadingStatus.textContent = '⏳ Membuka popup pembayaran...';
            }

            // Execute payment
            snap.pay(snapToken, {
                onSuccess: handlePaymentSuccess,
                onPending: handlePaymentPending,
                onError: handlePaymentError,
                onClose: handlePaymentClose
            });

        } catch (error) {
            console.error('❌ Payment execution failed:', error);
            paymentInProgress = false;

            if (paymentLoadingStatus) {
                paymentLoadingStatus.textContent = '❌ Error: ' + error.message + '. Refresh halaman.';
                paymentLoadingStatus.classList.add('text-danger');
            }

            alert('Gagal membuka popup pembayaran: ' + error.message);
        }
    }

    // ========== Payment Status Handlers ==========
    function handlePaymentSuccess(result) {
        console.log('✅ PAYMENT SUCCESS FROM MIDTRANS:', result);
        paymentInProgress = false;

        if (paymentStatus) {
            paymentStatus.textContent = 'Pembayaran Berhasil';
            paymentStatus.classList.remove('bg-warning');
            paymentStatus.classList.add('bg-success');
        }

        // CRITICAL: Immediately verify payment with backend before redirect
        // This ensures database is updated before user sees orders page
        console.log('🔄 Starting immediate payment verification...');

        // Wait 1 second for Midtrans callback to process, then verify with force flag
        setTimeout(() => {
            verifyPaymentWithRetry(true); // Force update from Midtrans
        }, 1000);
    }

    function handlePaymentPending(result) {
        console.log('⏳ PAYMENT PENDING:', result);
        paymentInProgress = false;

        if (paymentStatus) {
            paymentStatus.textContent = 'Menunggu Konfirmasi';
            paymentStatus.classList.remove('bg-warning');
            paymentStatus.classList.add('bg-info');
        }

        // Check status immediately and then periodically
        if (paymentLoadingStatus) {
            paymentLoadingStatus.textContent = '⏳ Menunggu sistem memproses... Jangan tutup halaman ini';
        }
        checkPaymentStatusWithRetry();
    }

    function handlePaymentError(result) {
        console.log('❌ PAYMENT ERROR:', result);
        paymentInProgress = false;

        if (paymentStatus) {
            paymentStatus.textContent = 'Pembayaran Gagal';
            paymentStatus.classList.remove('bg-warning');
            paymentStatus.classList.add('bg-danger');
        }

        alert('Pembayaran gagal. Error: ' + (result.status_message || 'Unknown error'));
    }

    function handlePaymentClose() {
        console.log('⚠️ PAYMENT POPUP CLOSED');
        paymentInProgress = false;

        // CRITICAL: When user closes popup, verify payment immediately
        // User might have completed payment in Midtrans but closed the popup early
        console.log('🔄 Verifying payment after popup close...');
        verifyPaymentWithRetry();
    }

    // ========== Enhanced Payment Verification with Retry Logic ==========
    /**
     * Verify payment status with force query to Midtrans
     * @param {boolean} forceUpdate - Force backend to query Midtrans directly
     */
    function verifyPaymentWithRetry(forceUpdate = false) {
        const maxAttempts = 8; // Increased from 5 to 8 attempts
        let attempt = 0;

        function attemptVerify() {
            attempt++;
            console.log(`🔍 Payment verification attempt ${attempt}/${maxAttempts}...`);

            const url = forceUpdate
                ? `/payment-status/${orderId}?force=1`
                : `/payment-status/${orderId}`;

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Verification response:', data);

                    const isPaid = data.is_paid === true || data.status_pembayaran === 'sudah_bayar';
                    const isVerified = data.verified_from_midtrans === true;

                    if (isPaid) {
                        console.log('✅✅✅ PAYMENT CONFIRMED!', {
                            is_paid: data.is_paid,
                            status_pembayaran: data.status_pembayaran,
                            verified_from_midtrans: isVerified
                        });

                        if (paymentStatus) {
                            paymentStatus.textContent = 'Pembayaran Berhasil';
                            paymentStatus.classList.remove('bg-warning', 'bg-info');
                            paymentStatus.classList.add('bg-success');
                        }

                        // Show success alert
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            <i class="bi bi-check-circle"></i> <strong>Pembayaran Berhasil!</strong><br>
                            Pesanan Anda sedang diproses. Anda akan dialihkan ke halaman pesanan.
                            <br><small>Status: ${data.status_pembayaran}</small>
                        `;
                        const container = document.querySelector('.container');
                        if (container) {
                            container.insertBefore(alertDiv, container.firstChild);
                        }

                        // WAIT 3 seconds to let user see success message, then redirect
                        setTimeout(() => {
                            console.log('🚀 Redirecting to orders page...');
                            window.location.href = '{{ route("pelanggan.orders") }}';
                        }, 3000);

                    } else if (attempt < maxAttempts) {
                        // Not paid yet, retry after delay with progressive backoff
                        // First 3 attempts: 1s, 2s, 3s (checking frequently)
                        // Remaining attempts: 3s each (checking less frequently)
                        let delay;
                        if (attempt <= 3) {
                            delay = 1000 * attempt; // 1s, 2s, 3s
                        } else {
                            delay = 3000; // 3s for remaining attempts
                        }

                        console.log(`⏳ Payment not confirmed yet. Retrying in ${delay}ms (attempt ${attempt}/${maxAttempts})...`);
                        console.log('Current backend status:', data.status_pembayaran);
                        setTimeout(attemptVerify, delay);

                    } else {
                        // Max attempts reached
                        console.warn('⚠️ Max verification attempts reached');
                        if (paymentLoadingStatus) {
                            paymentLoadingStatus.innerHTML = '⏳ Sistem sedang memproses pembayaran Anda...<br>' +
                                '<small>Jika halaman tidak berubah dalam 1 menit, refresh atau hubungi CS</small>';
                        }
                    }
                })
                .catch(error => {
                    console.error(`Verification attempt ${attempt} failed:`, error);
                    if (attempt < maxAttempts) {
                        const retryDelay = attempt <= 3 ? 1000 * attempt : 3000;
                        setTimeout(attemptVerify, retryDelay);
                    }
                });
        }

        attemptVerify();
    }

    // ========== Payment Status Polling with Retry ==========
    function checkPaymentStatusWithRetry() {
        let retryCount = 0;
        const maxRetries = 10;
        const checkInterval = 2000; // 2 seconds

        function periodicCheck() {
            if (retryCount >= maxRetries) {
                console.warn('⚠️ Max polling retries reached');
                return;
            }

            if (paymentStatus && paymentStatus.textContent === 'Pembayaran Berhasil') {
                console.log('✅ Payment already confirmed, stopping polls');
                return;
            }

            retryCount++;
            console.log(`📊 Periodic check #${retryCount}...`);

            fetch(`/payment-status/${orderId}`)
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    const isPaid = data.is_paid === true || data.status_pembayaran === 'sudah_bayar';

                    if (isPaid) {
                        console.log('✅ Payment detected in periodic check!');
                        verifyPaymentWithRetry(true); // Force fresh verification
                    } else {
                        setTimeout(periodicCheck, checkInterval);
                    }
                })
                .catch(error => {
                    console.error('Polling error:', error);
                    setTimeout(periodicCheck, checkInterval);
                });
        }

        periodicCheck();
    }

    // ========== Legacy Payment Status Check (for backward compatibility) ==========
    function checkPaymentStatus() {
        verifyPaymentWithRetry();
    }

    // ========== Button Click Handler ==========
    if (payButton) {
        payButton.addEventListener('click', async function() {
            console.log('�️ PAY BUTTON CLICKED');
            payButton.disabled = true;

            try {
                await executePayment();
            } finally {
                // Re-enable button after a delay if payment didn't complete
                setTimeout(function() {
                    if (paymentInProgress === false) {
                        payButton.disabled = false;
                    }
                }, 500);
            }
        });
    }

    // ========== Auto-Trigger on Page Load ==========
    window.addEventListener('load', async function() {
        console.log('📄 Page load event fired');

        try {
            // Wait for snap.js to load
            await snapLoadPromise;
            console.log('✅ Snap loaded on page load');

            // Auto-trigger payment after a short delay
            if (paymentLoadingStatus) {
                paymentLoadingStatus.textContent = '⏳ Membuka popup pembayaran...';
            }

            setTimeout(async function() {
                console.log('🚀 Auto-triggering payment popup...');
                autoTriggered = true;
                await executePayment();
            }, 500);

        } catch (error) {
            console.error('❌ Failed to auto-trigger payment:', error);
            autoTriggered = false;

            if (paymentLoadingStatus) {
                paymentLoadingStatus.innerHTML = '❌ Snap.js gagal dimuat. <br>' +
                    '<small>Klik tombol "Bayar Sekarang" untuk mencoba manual.</small>';
                paymentLoadingStatus.classList.add('text-danger');
            }
        }
    });

    // ========== Periodic Status Check (Fallback) ==========
    // Check status every 2-3 seconds if still on payment page
    const statusCheckInterval = setInterval(function() {
        if (paymentStatus && paymentStatus.textContent !== 'Pembayaran Berhasil') {
            console.log('📊 Periodic status check...');
            checkPaymentStatus();
        } else {
            // Payment successful, stop polling
            clearInterval(statusCheckInterval);
        }
    }, 2500);

    // ========== Global Error Handler ==========
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('snap')) {
            console.error('❌ Global error related to Snap:', e);
        }
    });

    // ========== Payment Expiry Timer ==========
    function startPaymentExpiryTimer() {
        @if($order->status_pembayaran === 'belum_bayar' && $order->payment_expired_at)
        const expiryTime = new Date('{{ $order->payment_expired_at->toIso8601String() }}').getTime();
        const timerDisplay = document.getElementById('expiryTimer');
        const expiryAlert = document.getElementById('paymentExpiryAlert');

        if (!timerDisplay || !expiryAlert) return;

        const updateTimer = function() {
            const now = new Date().getTime();
            const timeLeft = expiryTime - now;

            if (timeLeft <= 0) {
                timerDisplay.textContent = '⏰ Waktu habis!';
                timerDisplay.classList.add('text-danger');
                expiryAlert.classList.add('alert-danger');
                expiryAlert.classList.remove('alert-warning');

                // Stop polling for payment
                return;
            }

            const minutes = Math.floor(timeLeft / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            const displayStr = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            timerDisplay.textContent = displayStr;

            // Change color as time runs out
            if (minutes <= 2) {
                timerDisplay.classList.add('text-danger');
                expiryAlert.classList.add('alert-danger');
            } else if (minutes <= 5) {
                timerDisplay.classList.add('text-warning');
            }
        };

        // Update immediately
        updateTimer();

        // Update every second
        setInterval(updateTimer, 1000);
        @endif
    }

    // Start timer on page load
    window.addEventListener('load', startPaymentExpiryTimer);

</script>
@endif

<style>
.card {
    border-radius: 15px;
    overflow: hidden;
}

#pay-button {
    padding: 15px 50px;
    font-size: 1.1rem;
    border-radius: 50px;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endsection
