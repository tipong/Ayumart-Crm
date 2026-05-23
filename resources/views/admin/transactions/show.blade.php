@extends('layouts.admin')

@section('title', 'Detail Transaksi #' . $transaction->id_transaksi)

@push('styles')
<style>
    .info-label {
        font-weight: 700;
        color: #4b5563;
        font-size: 0.8rem;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #1f2937;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .product-img-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
        border: 1px solid #e5e7eb;
    }

    .product-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-img-placeholder {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-row {
        padding: 0.75rem 0;
        border-top: 1px solid #f3f4f6;
    }

    .summary-row:last-child {
        border-bottom: 1px solid #f3f4f6;
    }

    .total-row {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 10px;
        margin-top: 0.75rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <!-- Page Heading -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis mb-1">
                <i class="bi bi-file-earmark-text-fill text-success"></i> Detail Transaksi
            </h1>
            <p class="text-muted mb-0">ID: <strong>#{{ $transaction->id_transaksi }}</strong> | Kode: <strong>{{ $transaction->kode_transaksi }}</strong></p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Transaction Items Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-bag-check-fill text-success me-1"></i> Item Pembelian
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th scope="col" class="ps-4 py-3" width="40%">Produk</th>
                                    <th scope="col" class="py-3 text-center" width="12%">Qty</th>
                                    <th scope="col" class="py-3 text-end" width="15%">Harga Asli</th>
                                    <th scope="col" class="py-3 text-end" width="15%">Harga Diskon</th>
                                    <th scope="col" class="py-3 text-end" width="18%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaction->details as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->foto_produk)
                                                    <div class="product-img-wrapper me-3">
                                                        <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($item->product->foto_produk, 60, 60) }}"
                                                             alt="{{ $item->product->nama_produk }}">
                                                    </div>
                                                @else
                                                    <div class="product-img-placeholder me-3">
                                                        <i class="bi bi-image text-white fs-4"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="fw-bold text-dark d-block">{{ $item->product->nama_produk ?? 'Produk Tidak Ditemukan' }}</span>
                                                    @if($item->product && $item->product->jenis)
                                                        <small class="text-muted">
                                                            <i class="bi bi-tag-fill text-success"></i> {{ $item->product->jenis->nama_jenis ?? '-' }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-3 py-2 fw-bold" style="font-size: 0.9rem;">
                                                {{ $item->qty }} unit
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $hargaAsli = $item->product->harga_produk ?? 0;
                                            @endphp
                                            <span class="text-muted d-block small">Per unit</span>
                                            <span class="fw-semibold text-dark">Rp {{ number_format($hargaAsli, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $hargaDiskon = 0;
                                                if ($item->product && ($item->product->harga_diskon ?? 0) > 0 && ($item->product->hasActiveDiscount() ?? false)) {
                                                    $hargaDiskon = $item->product->harga_diskon;
                                                }
                                            @endphp
                                            @if($hargaDiskon > 0)
                                                <span class="text-muted d-block small">Per unit</span>
                                                <span class="fw-semibold text-success">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                                                <span class="badge bg-danger bg-opacity-10 text-danger d-block mt-1" style="font-size: 0.75rem;">
                                                    Diskon {{ round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100) }}%
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            @php
                                                $hargaPakai = $hargaDiskon > 0 ? $hargaDiskon : $hargaAsli;
                                            @endphp
                                            <span class="fw-bold text-success-emphasis">
                                                Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}
                                            </span>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                ({{ $item->qty }} × Rp {{ number_format($hargaPakai, 0, ',', '.') }})
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                            Tidak ada item pembelian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="row justify-content-end p-4 border-top">
                        <div class="col-md-6 col-lg-5">
                            <div class="summary-row d-flex justify-content-between">
                                <span class="text-muted fw-semibold">Subtotal:</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($transaction->total_harga ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @if($transaction->total_diskon > 0)
                                <div class="summary-row d-flex justify-content-between">
                                    <span class="text-muted fw-semibold">Total Diskon:</span>
                                    <span class="fw-bold text-danger">- Rp {{ number_format($transaction->total_diskon, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if(isset($transaction->biaya_membership) && $transaction->biaya_membership > 0)
                                <div class="summary-row d-flex justify-content-between">
                                    <span class="text-muted fw-semibold">
                                        <i class="bi bi-award-fill text-warning me-1"></i> Biaya Membership:
                                    </span>
                                    <span class="fw-bold text-teal" style="color: #0f766e;">Rp {{ number_format($transaction->biaya_membership, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($transaction->ongkir > 0)
                                <div class="summary-row d-flex justify-content-between">
                                    <span class="text-muted fw-semibold">Ongkos Kirim:</span>
                                    <span class="fw-bold text-dark">Rp {{ number_format($transaction->ongkir, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="total-row d-flex justify-content-between align-items-center">
                                <span class="fw-bold small">TOTAL PEMBAYARAN:</span>
                                <span class="h4 mb-0 fw-extrabold">
                                    Rp {{ number_format($transaction->getTotalAmount(), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancellation Info (if exists) -->
            @if($transaction->cancellation)
                @php
                    $cancelStatus = $transaction->cancellation->status_pembatalan;
                    $borderClass = $cancelStatus === 'disetujui' ? 'border-left-success' : ($cancelStatus === 'ditolak' ? 'border-left-danger' : 'border-left-warning');
                    $badgeClass = $cancelStatus === 'disetujui' ? 'bg-success' : ($cancelStatus === 'ditolak' ? 'bg-danger' : 'bg-warning text-dark');
                @endphp
                <div class="card shadow-sm mb-4 {{ $borderClass }}">
                    <div class="card-header bg-white border-0 py-3 ps-4">
                        <h5 class="m-0 fw-bold text-danger">
                            <i class="bi bi-x-octagon-fill me-1"></i> Informasi Pembatalan Transaksi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="info-label mb-1">Status Pengajuan:</p>
                                <span class="badge {{ $badgeClass }} px-3 py-2 fw-semibold">
                                    @if($cancelStatus === 'diajukan')
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu Review Admin
                                    @elseif($cancelStatus === 'disetujui')
                                        <i class="bi bi-check-circle-fill me-1"></i> Pengajuan Disetujui
                                    @else
                                        <i class="bi bi-x-circle-fill me-1"></i> Pengajuan Ditolak
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-6">
                                <p class="info-label mb-1">Waktu Pengajuan:</p>
                                <p class="info-value">
                                    {{ $transaction->cancellation->created_at ? $transaction->cancellation->created_at->format('d M Y, H:i') : '-' }}
                                </p>
                            </div>
                            <div class="col-12 border-top pt-3">
                                <p class="info-label mb-1">Alasan Pembatalan:</p>
                                <div class="bg-light p-3 rounded border text-muted">
                                    {{ $transaction->cancellation->alasan_pembatalan }}
                                </div>
                            </div>
                            @if($transaction->cancellation->catatan_admin)
                                <div class="col-12">
                                    <p class="info-label mb-1">Catatan Admin/CS:</p>
                                    <div class="p-3 rounded border" style="background-color: #f0fdf4; color: #155724; border-color: #c3e6cb;">
                                        {{ $transaction->cancellation->catatan_admin }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Customer Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-person-fill text-success me-1"></i> Profil Pelanggan
                    </h5>
                </div>
                <div class="card-body">
                    @if($transaction->pelanggan)
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                {{ strtoupper(substr($transaction->pelanggan->nama_pelanggan, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $transaction->pelanggan->nama_pelanggan }}</h6>
                                <small class="text-muted">Pelanggan AyuMart</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="info-label"><i class="bi bi-envelope text-muted me-1"></i> Email</p>
                            <p class="info-value">
                                @if($transaction->pelanggan->user)
                                    <a href="mailto:{{ $transaction->pelanggan->user->email }}" class="text-decoration-none text-success">
                                        {{ $transaction->pelanggan->user->email }}
                                    </a>
                                @else
                                    <span class="text-muted small">Tidak tersedia</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <p class="info-label"><i class="bi bi-telephone text-muted me-1"></i> No. Telepon</p>
                            <p class="info-value">
                                @if($transaction->pelanggan->no_tlp_pelanggan)
                                    <a href="tel:{{ $transaction->pelanggan->no_tlp_pelanggan }}" class="text-decoration-none text-success">
                                        {{ $transaction->pelanggan->no_tlp_pelanggan }}
                                    </a>
                                @else
                                    <span class="text-muted small">Tidak tersedia</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-0">
                            <p class="info-label"><i class="bi bi-geo-alt text-muted me-1"></i> Alamat Terdaftar</p>
                            <p class="info-value text-muted small mb-0">{{ $transaction->pelanggan->alamat ?? '-' }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0 border-0 bg-warning bg-opacity-10 text-warning-emphasis">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Data pelanggan tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transaction Detail Stats Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-info-circle-fill text-success me-1"></i> Detail Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="info-label"><i class="bi bi-credit-card-2-front text-muted me-1"></i> Metode Pembayaran</p>
                        <p class="info-value mb-0">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-wallet2 text-success me-1"></i> Via Midtrans
                            </span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="info-label"><i class="bi bi-flag text-muted me-1"></i> Status Pembayaran</p>
                        <p class="info-value mb-0">
                            @if($transaction->status_pembayaran === 'belum_bayar')
                                <span class="badge bg-warning text-dark px-3 py-2 fw-semibold">
                                    <i class="bi bi-hourglass-split me-1"></i> Belum Bayar
                                </span>
                            @elseif($transaction->status_pembayaran === 'sudah_bayar')
                                <span class="badge bg-success px-3 py-2 fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1"></i> Sudah Bayar
                                </span>
                            @elseif($transaction->status_pembayaran === 'kadaluarsa')
                                <span class="badge bg-danger px-3 py-2 fw-semibold">
                                    <i class="bi bi-x-circle-fill me-1"></i> Kadaluarsa
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 fw-semibold">
                                    {{ ucfirst($transaction->status_pembayaran) }}
                                </span>
                            @endif
                        </p>
                    </div>

                    @if($transaction->tanggal_transaksi)
                        <div class="mb-0">
                            <p class="info-label"><i class="bi bi-calendar3 text-muted me-1"></i> Waktu Transaksi</p>
                            <p class="info-value mb-0">
                                @php
                                    $tanggalTransaksi = $transaction->tanggal_transaksi;
                                    if (is_string($tanggalTransaksi)) {
                                        $tanggalTransaksi = \Carbon\Carbon::parse($tanggalTransaksi);
                                    }
                                @endphp
                                {{ $tanggalTransaksi->format('d M Y') }} pada {{ $tanggalTransaksi->format('H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-truck text-success me-1"></i> Informasi Pengiriman
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="info-label">Metode:</p>
                        <p class="info-value mb-0">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1.5 fw-bold">
                                @if($transaction->metode_pengiriman === 'kurir')
                                    <i class="bi bi-truck me-1"></i> Kirim Kurir
                                @else
                                    <i class="bi bi-shop me-1"></i> Ambil Sendiri
                                @endif
                            </span>
                        </p>
                    </div>

                    @if($transaction->cabang)
                        <div class="mb-3">
                            <p class="info-label">Diambil di / Dikirim dari:</p>
                            <p class="info-value mb-0">
                                <strong>{{ $transaction->cabang->nama_cabang }}</strong><br>
                                <small class="text-muted">{{ $transaction->cabang->alamat_cabang }}</small>
                            </p>
                        </div>
                    @endif

                    @if($transaction->status_pengiriman)
                        <div class="mb-0 border-top pt-3">
                            <p class="info-label mb-2">Status Logistik:</p>
                            @if($transaction->status_pengiriman === 'pending')
                                <span class="badge bg-secondary px-3 py-2 fw-semibold">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Proses
                                </span>
                            @elseif($transaction->status_pengiriman === 'processing')
                                <span class="badge bg-info text-dark px-3 py-2 fw-semibold">
                                    <i class="bi bi-box-seam me-1"></i> Sedang Dikemas
                                </span>
                            @elseif($transaction->status_pengiriman === 'shipped')
                                <span class="badge bg-primary px-3 py-2 fw-semibold">
                                    <i class="bi bi-truck-flatbed me-1"></i> Dalam Pengiriman
                                </span>
                            @elseif($transaction->status_pengiriman === 'delivered')
                                <span class="badge bg-success px-3 py-2 fw-semibold">
                                    <i class="bi bi-check-all me-1"></i> Diterima Pelanggan
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 fw-semibold">
                                    {{ ucfirst($transaction->status_pengiriman) }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notes Card -->
            @if($transaction->catatan)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3 ps-4">
                        <h5 class="m-0 fw-bold text-success-emphasis">
                            <i class="bi bi-chat-right-text text-success me-1"></i> Catatan Pembeli
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted fs-6 italic">"{{ $transaction->catatan }}"</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
