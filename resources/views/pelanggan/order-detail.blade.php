@extends('layouts.pelanggan')

@section('title', 'Detail Pesanan #' . $order->kode_transaksi)

@section('content')
<div class="container py-5">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('pelanggan.orders') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Pesanan Saya
        </a>
    </div>

    <div class="row">
        <!-- Left Column: Order Items -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <!-- Order Header Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Kode Transaksi</h6>
                            <h4 class="text-primary mb-0">{{ $order->kode_transaksi }}</h4>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted mb-2">Status Pesanan</h6>
                            <span class="badge bg-{{ $order->status_pembayaran == 'sudah_bayar' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->status_pembayaran) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <!-- Order Items -->
                    <h6 class="mb-3"><i class="bi bi-cart3"></i> Item Pesanan</h6>
                    @if($orderItems && count($orderItems) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(isset($item->foto_produk) && $item->foto_produk)
                                                    <img src="{{ asset('storage/' . $item->foto_produk) }}"
                                                         alt="{{ $item->nama_produk }}"
                                                         class="img-thumbnail me-3"
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light me-3 d-flex align-items-center justify-content-center"
                                                         style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $item->nama_produk }}</strong>
                                                    @if(isset($item->kategori_produk))
                                                        <br><small class="text-muted">{{ $item->kategori_produk }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->qty }}</span>
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format($item->harga_item, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Tidak ada detail item untuk pesanan ini.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Informasi Pengiriman</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Metode Pengiriman:</strong>
                        {{ $order->metode_pengiriman === 'kurir' ? 'Kurir' : 'Ambil Sendiri' }}
                    </div>

                    @if($order->shipment)
                        <div class="text-muted small">
                            <strong>Status:</strong> {{ ucfirst($order->status_pengiriman) }}
                            @if($order->shipment->staff && $order->shipment->staff->user)
                                <br><strong>Kurir:</strong> {{ $order->shipment->staff->user->getName() }}
                            @endif
                        </div>
                    @endif

                    @if($order->cabang)
                        <div class="mt-2">
                            <strong>Cabang:</strong> {{ $order->cabang->nama_cabang }}
                            <br>
                            <small class="text-muted">{{ $order->cabang->alamat }}</small>
                        </div>
                    @endif

                    @if($order->catatan)
                        <hr>
                        <small class="text-muted">
                            <i class="bi bi-chat-left-text"></i> <strong>Catatan:</strong><br>
                            {{ $order->catatan }}
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Summary & Status -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Status Pesanan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status Pembayaran</small>
                        @if($order->status_pembayaran == 'sudah_bayar')
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle"></i> Sudah Bayar
                            </span>
                        @elseif($order->status_pembayaran == 'kadaluarsa')
                            <span class="badge bg-danger fs-6">
                                <i class="bi bi-x-circle"></i> Kadaluarsa
                            </span>
                        @else
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="bi bi-clock"></i> Belum Bayar
                            </span>
                        @endif
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status Pengiriman</small>
                        @if($order->status_pengiriman == 'sampai' || $order->status_pengiriman == 'selesai')
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle-fill"></i> Terkirim
                            </span>
                        @elseif($order->status_pengiriman == 'dikirim')
                            <span class="badge bg-info fs-6">
                                <i class="bi bi-truck"></i> Sedang Dikirim
                            </span>
                        @elseif($order->status_pengiriman == 'dikemas')
                            <span class="badge bg-primary fs-6">
                                <i class="bi bi-box"></i> Dikemas
                            </span>
                        @elseif($order->status_pengiriman == 'siap_diambil')
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="bi bi-bag-check"></i> Siap Diambil
                            </span>
                        @elseif($order->status_pengiriman == 'pending')
                            <span class="badge bg-secondary fs-6">
                                <i class="bi bi-hourglass-split"></i> Pending
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6">
                                <i class="bi bi-hourglass-split"></i> {{ ucfirst($order->status_pengiriman ?? 'Pending') }}
                            </span>
                        @endif
                    </div>

                    <!-- Info Kurir (jika sedang dikirim) -->
                    @if($order->status_pengiriman == 'dikirim' && $order->shipment && $order->shipment->staff && $order->shipment->staff->user)
                    <div class="alert alert-info mt-3 mb-3">
                        <h6 class="alert-heading mb-2">
                            <i class="bi bi-person-badge"></i> Informasi Kurir
                        </h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Nama Kurir</small>
                                    <strong>{{ $order->shipment->staff->user->getName() }}</strong>
                                </div>
                                @php
                                    $phone = $order->shipment->staff->user->getPhone();
                                @endphp
                                @if($phone)
                                <div class="mb-2">
                                    <small class="text-muted d-block">No. Telepon</small>
                                    <a href="tel:{{ $phone }}" class="text-decoration-none">
                                        <i class="bi bi-telephone-fill"></i> {{ $phone }}
                                    </a>
                                </div>
                                @endif
                                @if($order->shipment->no_resi)
                                <div class="mb-2">
                                    <small class="text-muted d-block">No. Resi</small>
                                    <code class="bg-light px-2 py-1 rounded">{{ $order->shipment->no_resi }}</code>
                                </div>
                                @endif
                                @if($order->shipment->tgl_kirim)
                                <div class="mb-0">
                                    <small class="text-muted d-block">Tanggal Pengiriman</small>
                                    <strong>{{ \Carbon\Carbon::parse($order->shipment->tgl_kirim)->format('d F Y, H:i') }}</strong>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Metode Pengiriman</small>
                        @if($order->metode_pengiriman == 'kurir')
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-truck"></i> Kurir
                            </span>
                        @elseif($order->metode_pengiriman == 'ambil_sendiri')
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-shop"></i> Ambil Sendiri
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>

                    @if($order->status_pembayaran == 'belum_bayar')
                        <hr>
                        <div class="d-grid">
                            <a href="{{ route('pelanggan.orders.continue-payment', $order->id_transaksi) }}"
                               class="btn btn-warning">
                                <i class="bi bi-credit-card"></i> Lanjutkan Pembayaran
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-calculator"></i> Ringkasan Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal Produk</span>
                        <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>

                    @if($order->total_diskon > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>
                            <i class="bi bi-tag"></i> Diskon Membership
                        </span>
                        <span>- Rp {{ number_format($order->total_diskon, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(isset($order->biaya_membership) && $order->biaya_membership > 0)
                    <div class="d-flex justify-content-between mb-2 text-info">
                        <span>
                            <i class="bi bi-award"></i> Biaya Pembuatan Member
                            <span class="badge bg-info">Baru</span>
                        </span>
                        <span>Rp {{ number_format($order->biaya_membership, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if($order->ongkir > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-truck"></i> Ongkos Kirim</span>
                        <span>Rp {{ number_format($order->ongkir, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between">
                        <strong>Total Pembayaran</strong>
                        <strong class="text-primary fs-5">
                            Rp {{ number_format($order->total_harga - $order->total_diskon + ($order->biaya_membership ?? 0) + $order->ongkir, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            {{-- @if($order->status_pembayaran == 'sudah_bayar' && in_array($order->status_pengiriman, ['sampai', 'selesai', 'siap_diambil']))
            <div class="card shadow-sm mt-4">
                <div class="card-body text-center">
                    <p class="text-muted mb-3">Apakah Anda puas dengan pesanan ini?</p>
                    <button class="btn btn-outline-primary btn-sm" disabled>
                        <i class="bi bi-star"></i> Beri Rating (Coming Soon)
                    </button>
                </div>
            </div>
            @endif --}}
        </div>
    </div>
</div>

@if($order->status_pembayaran == 'belum_bayar')
<script>
    // Payment Status Checker - Check every 5 seconds
    (function() {
        function checkPaymentStatus() {
            fetch("{{ route('order.check-status', $order->kode_transaksi) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'sudah_bayar') {
                        // Payment successful, reload page
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error checking payment status:', error));
        }

        // Check status every 5 seconds
        setInterval(checkPaymentStatus, 5000);
    })();
</script>
@endif

@endsection
