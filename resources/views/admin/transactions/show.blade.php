@extends('layouts.admin')

@section('title', 'Detail Transaksi #' . $transaction->id_transaksi)

@push('styles')
<style>
    .info-label {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: #3a3b45;
        font-size: 0.95rem;
    }

    .product-img-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 0.5rem;
        overflow: hidden;
        flex-shrink: 0;
    }

    .product-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-img-placeholder {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-row {
        padding: 0.75rem 0;
        border-top: 1px solid #e3e6f0;
    }

    .summary-row:last-child {
        border-bottom: 1px solid #e3e6f0;
    }

    .total-row {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 0.5rem;
    }

    .status-timeline {
        position: relative;
        padding-left: 2rem;
    }

    .status-timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e3e6f0;
    }

    .status-timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .status-timeline-item::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 0.25rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #e3e6f0;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #e3e6f0;
    }

    .status-timeline-item.active::before {
        background: #4e73df;
        box-shadow: 0 0 0 2px #4e73df;
    }

    .status-timeline-item.success::before {
        background: #1cc88a;
        box-shadow: 0 0 0 2px #1cc88a;
    }

    .status-timeline-item.danger::before {
        background: #e74a3b;
        box-shadow: 0 0 0 2px #e74a3b;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice text-primary"></i> Detail Transaksi
            </h1>
            <p class="text-muted mb-0">ID: <strong>#{{ $transaction->id_transaksi }}</strong> | Kode: <strong>{{ $transaction->kode_transaksi }}</strong></p>
        </div>
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Transaction Items Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-shopping-bag"></i> Item Transaksi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th width="35%">Produk</th>
                                    <th width="12%" class="text-center">Qty</th>
                                    <th width="15%" class="text-end">Harga Asli</th>
                                    <th width="15%" class="text-end">Harga Diskon</th>
                                    <th width="12%" class="text-end">Harga Pakai</th>
                                    <th width="15%" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaction->details as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->foto_produk)
                                                    <div class="product-img-wrapper me-3">
                                                        <img src="{{ asset('storage/' . $item->product->foto_produk) }}"
                                                             alt="{{ $item->product->nama_produk }}">
                                                    </div>
                                                @else
                                                    <div class="product-img-placeholder me-3">
                                                        <i class="fas fa-image text-white fa-lg"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong class="d-block">{{ $item->product->nama_produk ?? 'Produk Tidak Ditemukan' }}</strong>
                                                    @if($item->product && $item->product->jenis)
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag"></i> {{ $item->product->jenis->nama_jenis ?? '-' }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-primary" style="font-size: 0.9rem;">
                                                {{ $item->qty }} pcs
                                            </span>
                                        </td>
                                        <!-- Harga Asli -->
                                        <td class="text-end align-middle">
                                            @php
                                                // Harga asli dari produk (bukan dari item)
                                                $hargaAsli = 0;
                                                if ($item->product) {
                                                    $hargaAsli = $item->product->harga_produk ?? 0;
                                                }
                                            @endphp
                                            <small class="text-muted d-block">Per unit</small>
                                            <strong>Rp {{ number_format($hargaAsli, 0, ',', '.') }}</strong>
                                        </td>
                                        <!-- Harga Diskon (jika ada) -->
                                        <td class="text-end align-middle">
                                            @php
                                                // Harga diskon dari produk
                                                $hargaDiskon = 0;
                                                if ($item->product && ($item->product->harga_diskon ?? 0) > 0 && ($item->product->hasActiveDiscount() ?? false)) {
                                                    $hargaDiskon = $item->product->harga_diskon;
                                                }
                                            @endphp
                                            @if($hargaDiskon > 0)
                                                <small class="text-muted d-block">Per unit</small>
                                                <strong class="text-success">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</strong>
                                                <small class="text-danger d-block">
                                                    <i class="fas fa-arrow-down"></i>
                                                    {{ round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100) }}% off
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <!-- Harga Pakai (Harga Akhir per unit) -->
                                        <td class="text-end align-middle">
                                            @php
                                                // Harga yang benar-benar digunakan
                                                $hargaPakai = $hargaAsli;
                                                if ($hargaDiskon > 0) {
                                                    $hargaPakai = $hargaDiskon;
                                                }
                                            @endphp
                                            <small class="text-muted d-block">Per unit</small>
                                            <strong class="text-primary">Rp {{ number_format($hargaPakai, 0, ',', '.') }}</strong>
                                        </td>
                                        <!-- Subtotal per item -->
                                        <td class="text-end align-middle">
                                            <strong class="text-success">
                                                Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}
                                            </strong>
                                            <small class="text-muted d-block">
                                                ({{ $item->qty }} × Rp {{ number_format($hargaPakai, 0, ',', '.') }})
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            Tidak ada item
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="row justify-content-end mt-4">
                        <div class="col-md-6 col-lg-5">
                            <div class="summary-row d-flex justify-content-between">
                                <span class="text-muted">Subtotal:</span>
                                <strong>Rp {{ number_format($transaction->total_harga ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            @if($transaction->total_diskon > 0)
                                <div class="summary-row d-flex justify-content-between">
                                    <span class="text-muted">Total Diskon:</span>
                                    <strong class="text-danger">- Rp {{ number_format($transaction->total_diskon, 0, ',', '.') }}</strong>
                                </div>
                            @endif
                            @if(isset($transaction->biaya_membership) && $transaction->biaya_membership > 0)
                                <div class="summary-row d-flex justify-content-between">
                                    <span class="text-muted">
                                        <i class="fas fa-crown text-warning"></i> Biaya Membership:
                                    </span>
                                    <strong class="text-primary">Rp {{ number_format($transaction->biaya_membership, 0, ',', '.') }}</strong>
                                </div>
                            @endif
                            @if($transaction->ongkir > 0)
                                <div class="summary-row d-flex justify-content-between">
                                    <span class="text-muted">Ongkir:</span>
                                    <strong>Rp {{ number_format($transaction->ongkir, 0, ',', '.') }}</strong>
                                </div>
                            @endif
                            <div class="total-row d-flex justify-content-between align-items-center">
                                <span class="h6 mb-0">TOTAL PEMBAYARAN:</span>
                                <span class="h4 mb-0">
                                    Rp {{ number_format($transaction->getTotalAmount(), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancellation Info (if cancelled) -->
            @if($transaction->cancellation)
                <div class="card shadow mb-4 border-left-{{ $transaction->cancellation->status_pembatalan === 'disetujui' ? 'success' : ($transaction->cancellation->status_pembatalan === 'ditolak' ? 'danger' : 'warning') }}">
                    <div class="card-header py-3 bg-{{ $transaction->cancellation->status_pembatalan === 'disetujui' ? 'success' : ($transaction->cancellation->status_pembatalan === 'ditolak' ? 'danger' : 'warning') }} text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-ban"></i> Informasi Pembatalan Transaksi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="info-label mb-1">Status Pembatalan:</p>
                            @if($transaction->cancellation->status_pembatalan === 'diajukan')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock"></i> Menunggu Review
                                </span>
                            @elseif($transaction->cancellation->status_pembatalan === 'disetujui')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Disetujui
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle"></i> Ditolak
                                </span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <p class="info-label mb-1">Alasan Pembatalan:</p>
                            <p class="text-muted mb-0">{{ $transaction->cancellation->alasan_pembatalan }}</p>
                        </div>
                        @if($transaction->cancellation->catatan_admin)
                            <div class="mb-3">
                                <p class="info-label mb-1">Catatan Admin:</p>
                                <p class="text-muted mb-0">{{ $transaction->cancellation->catatan_admin }}</p>
                            </div>
                        @endif
                        @if($transaction->cancellation->created_at)
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-clock"></i> Diajukan pada: {{ $transaction->cancellation->created_at->format('d M Y, H:i') }}
                        </p>
                        @endif
                        @if($transaction->cancellation->status_pembatalan !== 'diajukan' && $transaction->cancellation->updated_at)
                            <p class="mb-0 small text-muted mt-1">
                                <i class="fas fa-check"></i> Diproses pada: {{ $transaction->cancellation->updated_at->format('d M Y, H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Customer Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-user"></i> Informasi Pelanggan
                    </h6>
                </div>
                <div class="card-body">
                    @if($transaction->pelanggan)
                        <div class="mb-3">
                            <p class="info-label"><i class="fas fa-user"></i> Nama</p>
                            <p class="info-value">{{ $transaction->pelanggan->nama_pelanggan }}</p>
                        </div>
                        @if($transaction->pelanggan->user)
                        <div class="mb-3">
                            <p class="info-label"><i class="fas fa-envelope"></i> Email</p>
                            <p class="info-value">
                                <a href="mailto:{{ $transaction->pelanggan->user->email }}">
                                    {{ $transaction->pelanggan->user->email }}
                                </a>
                            </p>
                        </div>
                        @endif
                        @if($transaction->pelanggan->no_tlp_pelanggan)
                        <div class="mb-3">
                            <p class="info-label"><i class="fas fa-phone"></i> No. Telepon</p>
                            <p class="info-value">
                                <a href="tel:{{ $transaction->pelanggan->no_tlp_pelanggan }}">
                                    {{ $transaction->pelanggan->no_tlp_pelanggan }}
                                </a>
                            </p>
                        </div>
                        @endif
                        @if($transaction->pelanggan->alamat)
                            <div class="mb-0">
                                <p class="info-label"><i class="fas fa-map-marker-alt"></i> Alamat</p>
                                <p class="info-value">{{ $transaction->pelanggan->alamat }}</p>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            Data pelanggan tidak ditemukan
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transaction Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-info">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-info-circle"></i> Detail Transaksi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="info-label"><i class="fas fa-hashtag"></i> ID Transaksi</p>
                        <p class="info-value"><code>#{{ $transaction->id_transaksi }}</code></p>
                    </div>

                    @if($transaction->kode_transaksi)
                        <div class="mb-3">
                            <p class="info-label"><i class="fas fa-barcode"></i> Kode Transaksi</p>
                            <p class="info-value"><code>{{ $transaction->kode_transaksi }}</code></p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <p class="info-label"><i class="fas fa-credit-card"></i> Metode Pembayaran</p>
                        <p class="info-value">
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-credit-card"></i> Via Midtrans
                            </span>
                        </p>
                    </div>

                    <div class="mb-0">
                        <p class="info-label"><i class="fas fa-flag"></i> Status Pembayaran</p>
                        <p class="info-value">
                            @if($transaction->status_pembayaran === 'belum_bayar')
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    <i class="fas fa-clock"></i> Belum Bayar
                                </span>
                            @elseif($transaction->status_pembayaran === 'sudah_bayar')
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-check-circle"></i> Sudah Bayar
                                </span>
                            @elseif($transaction->status_pembayaran === 'kadaluarsa')
                                <span class="badge bg-danger px-3 py-2">
                                    <i class="fas fa-times-circle"></i> Kadaluarsa
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">
                                    {{ ucfirst($transaction->status_pembayaran) }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Shipping Address Card -->
            @if($transaction->alamat_pengiriman)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-gradient-success">
                        <h6 class="m-0 font-weight-bold text-black">
                            <i class="fas fa-map-marker-alt"></i> Alamat Pengiriman
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            <strong>Metode:</strong> {{ $transaction->metode_pengiriman === 'kurir' ? 'Kurir' : 'Ambil Sendiri' }}
                        </p>
                        @if($transaction->cabang)
                            <p class="mb-0 mt-2">
                                <strong>Cabang:</strong> {{ $transaction->cabang->nama_cabang }}<br>
                                <small class="text-muted">{{ $transaction->cabang->alamat }}</small>
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Shipping Status Card -->
            @if($transaction->status_pengiriman)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-gradient-warning">
                        <h6 class="m-0 font-weight-bold text-black">
                            <i class="fas fa-truck"></i> Status Pengiriman
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($transaction->status_pengiriman === 'pending')
                            <span class="badge bg-secondary px-3 py-2">
                                <i class="fas fa-clock"></i> Menunggu Proses
                            </span>
                        @elseif($transaction->status_pengiriman === 'processing')
                            <span class="badge bg-info px-3 py-2">
                                <i class="fas fa-box"></i> Sedang Dikemas
                            </span>
                        @elseif($transaction->status_pengiriman === 'shipped')
                            <span class="badge bg-primary px-3 py-2">
                                <i class="fas fa-shipping-fast"></i> Dalam Pengiriman
                            </span>
                        @elseif($transaction->status_pengiriman === 'delivered')
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-double"></i> Terkirim
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">
                                {{ ucfirst($transaction->status_pengiriman) }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Notes Card -->
            @if($transaction->catatan)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-dark">
                            <i class="fas fa-sticky-note"></i> Catatan
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted">{{ $transaction->catatan }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Transaction Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fas fa-ban"></i> Batalkan Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.transactions.cancel', $transaction->id_transaksi) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian!</strong> Pembatalan transaksi akan mengembalikan stok produk.
                    </div>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">
                            <i class="fas fa-comment"></i> Alasan Pembatalan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="cancellation_reason"
                                  name="cancellation_reason"
                                  rows="4"
                                  placeholder="Masukkan alasan pembatalan transaksi..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Batalkan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function cancelTransaction() {
        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Anda akan membatalkan transaksi ini dan mengembalikan stok produk!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
                modal.show();
            }
        });
    }
</script>
@endsection
