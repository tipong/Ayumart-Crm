@extends('layouts.staff')

@section('title', 'Detail Transaksi #' . $transaction->kode_transaksi . ' - AyuMart')

@section('sidebar-menu')
    <a class="nav-link" href="{{ route('owner.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Transaksi</div>

    <a class="nav-link active" href="{{ route('owner.transactions.index') }}">
        <i class="bi bi-wallet2"></i>
        <span>Daftar Transaksi</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Laporan</div>

    <a class="nav-link" href="{{ route('owner.laporan') }}">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span>Laporan Penjualan</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Akun</div>

    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
@endsection

@section('content')
<div class="container-fluid py-2">
    <!-- Page Heading -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis">
                <i class="bi bi-file-invoice text-success"></i> Detail Transaksi
            </h1>
            <p class="text-muted mb-0">Kode Transaksi: <strong class="text-success">{{ $transaction->kode_transaksi }}</strong></p>
        </div>
        <a href="{{ route('owner.transactions.index') }}" class="btn btn-outline-success rounded-pill mt-3 mt-sm-0">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Transaksi
        </a>
    </div>

    <!-- Info Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Left Side: Transaction Details and Items -->
        <div class="col-lg-8">
            <!-- Items Card -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-success text-white py-3 ps-4 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-basket3-fill me-1"></i> Item yang Dibeli</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th scope="col" class="ps-4 py-3" width="45%">Produk</th>
                                    <th scope="col" class="text-center py-3" width="15%">Jumlah</th>
                                    <th scope="col" class="text-end py-3" width="20%">Harga Satuan</th>
                                    <th scope="col" class="text-end pe-4 py-3" width="20%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->details as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->foto_produk)
                                                    <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($item->product->foto_produk, 50, 50) }}" 
                                                         class="rounded me-3 border" 
                                                         width="50" 
                                                         height="50" 
                                                         style="object-fit: cover;"
                                                         alt="{{ $item->product->nama_produk }}">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 border" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bi bi-image text-muted fs-4"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="fw-semibold d-block text-dark">{{ $item->product->nama_produk ?? 'Produk Terhapus/Tidak Ditemukan' }}</span>
                                                    @if($item->product && $item->product->jenis)
                                                        <span class="badge bg-light text-secondary border small mt-1" style="font-size: 0.75rem;">{{ $item->product->jenis->nama_jenis }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold">{{ $item->qty }} pcs</td>
                                        <td class="text-end text-muted">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end pe-4 fw-semibold text-success-emphasis">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment Summary Card -->
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="mb-0 fw-bold text-success-emphasis"><i class="bi bi-calculator-fill me-1"></i> Ringkasan Pembayaran</h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row justify-content-end">
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal Belanja:</span>
                                <span class="fw-semibold text-dark">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</span>
                            </div>
                            @if($transaction->total_diskon > 0)
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>Diskon Member:</span>
                                    <span class="fw-semibold">- Rp {{ number_format($transaction->total_diskon, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($transaction->ongkir > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Ongkos Kirim:</span>
                                    <span class="fw-semibold text-dark">Rp {{ number_format($transaction->ongkir, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($transaction->biaya_membership > 0)
                                <div class="d-flex justify-content-between mb-2 text-info">
                                    <span>Biaya Pembuatan Member:</span>
                                    <span class="fw-semibold">Rp {{ number_format($transaction->biaya_membership, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center mb-0 bg-success bg-opacity-10 p-3 rounded-3">
                                <strong class="text-success-emphasis">Total Bayar:</strong>
                                <strong class="h4 mb-0 text-success fw-bold">Rp {{ number_format($transaction->getTotalAmount(), 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Order Info & Status -->
        <div class="col-lg-4">
            <!-- Basic Transaction Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="mb-0 fw-bold text-success-emphasis"><i class="bi bi-info-circle-fill me-1"></i> Informasi Transaksi</h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Tanggal Transaksi</label>
                        <span class="fw-semibold text-dark">
                            @if($transaction->tanggal_transaksi)
                                {{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->translatedFormat('d F Y, H:i:s') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Status Pembayaran</label>
                        @if($transaction->status_pembayaran === 'belum_bayar')
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-clock-history me-1"></i> Belum Bayar</span>
                        @elseif($transaction->status_pembayaran === 'sudah_bayar')
                            <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Sudah Bayar</span>
                        @elseif($transaction->status_pembayaran === 'kadaluarsa')
                            <span class="badge bg-danger fs-6 px-3 py-2"><i class="bi bi-x-circle-fill me-1"></i> Kadaluarsa / Batal</span>
                        @else
                            <span class="badge bg-secondary fs-6 px-3 py-2">{{ ucfirst($transaction->status_pembayaran) }}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Metode Pengiriman</label>
                        @if($transaction->metode_pengiriman === 'ambil_sendiri')
                            <span class="badge bg-info text-white px-3 py-2 fs-6"><i class="bi bi-bag-fill me-1"></i> Ambil Sendiri</span>
                        @elseif($transaction->metode_pengiriman === 'kurir')
                            <span class="badge bg-primary text-white px-3 py-2 fs-6"><i class="bi bi-truck me-1"></i> Kirim via Kurir</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2 fs-6">{{ ucfirst($transaction->metode_pengiriman) }}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Cabang Pembelian</label>
                        <span class="fw-semibold text-dark">
                            <i class="bi bi-shop text-success me-1"></i> 
                            {{ $transaction->cabang->nama_cabang ?? '-' }} 
                            ({{ $transaction->cabang->kode_cabang ?? '-' }})
                        </span>
                    </div>

                    @if($transaction->catatan)
                        <div class="mb-0">
                            <label class="text-muted small d-block mb-1">Catatan Pelanggan</label>
                            <div class="p-3 bg-light rounded text-dark small border">
                                "{{ $transaction->catatan }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Customer Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="mb-0 fw-bold text-success-emphasis"><i class="bi bi-person-fill me-1"></i> Informasi Pelanggan</h5>
                </div>
                <div class="card-body p-4 pt-2">
                    @if($transaction->pelanggan)
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">Nama Lengkap</label>
                            <span class="fw-semibold text-dark">{{ $transaction->pelanggan->nama_pelanggan }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">No. Telepon</label>
                            <span class="fw-semibold text-dark">{{ $transaction->pelanggan->no_telepon ?? '-' }}</span>
                        </div>
                        @if($transaction->pelanggan->user)
                            <div class="mb-0">
                                <label class="text-muted small d-block mb-1">Alamat Email</label>
                                <span class="fw-semibold text-dark">{{ $transaction->pelanggan->user->email }}</span>
                            </div>
                        @endif
                    @else
                        <span class="text-muted small">Data pelanggan tidak lengkap atau terhapus.</span>
                    @endif
                </div>
            </div>

            <!-- Shipping Details (For Kurir) -->
            @if($transaction->metode_pengiriman === 'kurir')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3 ps-4">
                        <h5 class="mb-0 fw-bold text-success-emphasis"><i class="bi bi-geo-alt-fill me-1"></i> Alamat Pengiriman</h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @if($transaction->address)
                            <div class="mb-3">
                                <span class="fw-bold text-dark d-block mb-1">{{ $transaction->address->recipient_name }}</span>
                                <span class="text-muted d-block small">{{ $transaction->address->recipient_phone }}</span>
                            </div>
                            <div class="p-3 bg-light rounded text-dark small border mb-0">
                                {{ $transaction->address->address_line1 }}<br>
                                @if($transaction->address->address_line2)
                                    {{ $transaction->address->address_line2 }}<br>
                                @endif
                                {{ $transaction->address->city }}, {{ $transaction->address->state }} {{ $transaction->address->postal_code }}
                            </div>
                        @else
                            <span class="text-muted small">Detail alamat pengiriman tidak ditemukan.</span>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Cancellation Details (If Any) -->
            @if($transaction->cancellation)
                <div class="card border-0 shadow-sm mb-4 overflow-hidden border-start border-4 border-danger">
                    <div class="card-header bg-danger text-white py-3 ps-4 border-0">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-x-circle-fill me-1"></i> Detail Pembatalan</h5>
                    </div>
                    <div class="card-body p-4 pt-3 bg-danger bg-opacity-10 text-danger-emphasis">
                        <div class="mb-3">
                            <label class="small fw-semibold d-block mb-1">Status Pembatalan</label>
                            @if($transaction->cancellation->status_pembatalan === 'pending')
                                <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                            @elseif($transaction->cancellation->status_pembatalan === 'disetujui')
                                <span class="badge bg-success">Disetujui (Transaksi Batal)</span>
                            @elseif($transaction->cancellation->status_pembatalan === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="small fw-semibold d-block mb-1">Alasan Pembatalan</label>
                            <span class="small text-dark">"{{ $transaction->cancellation->alasan_pembatalan }}"</span>
                        </div>
                        <div class="mb-0">
                            <label class="small fw-semibold d-block mb-1">Waktu Pengajuan</label>
                            <span class="small text-muted">{{ \Carbon\Carbon::parse($transaction->cancellation->created_at)->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling Detail Transaksi */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .main-content {
        font-family: 'Inter', sans-serif !important;
    }

    .card {
        border-radius: 12px !important;
    }
</style>
@endpush
