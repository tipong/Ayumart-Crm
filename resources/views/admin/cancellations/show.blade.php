@extends('layouts.admin')

@section('title', 'Detail Pembatalan Transaksi')

@section('content')
<div class="container-fluid py-2">
    <!-- Header -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis mb-1">
                <i class="bi bi-x-octagon-fill text-success"></i> Detail Pembatalan Transaksi
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cancellations.index') }}" class="text-success text-decoration-none">Pembatalan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="{{ route('admin.cancellations.index') }}" class="btn btn-outline-secondary">
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
        <!-- Cancellation Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-info-circle-fill text-success me-1"></i> Informasi Pembatalan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <th width="40%" class="text-muted fw-semibold py-2">Kode Transaksi:</th>
                            <td class="py-2">
                                <a href="{{ route('admin.transactions.show', $cancellation->transaksi->id_transaksi) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $cancellation->transaksi->kode_transaksi }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Pelanggan:</th>
                            <td class="py-2 fw-semibold text-dark">{{ $cancellation->transaksi->pelanggan->nama_pelanggan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Email Pelanggan:</th>
                            <td class="py-2">
                                @if($cancellation->transaksi->pelanggan && $cancellation->transaksi->pelanggan->user)
                                    <a href="mailto:{{ $cancellation->transaksi->pelanggan->user->email }}" class="text-success text-decoration-none">
                                        {{ $cancellation->transaksi->pelanggan->user->email }}
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Tanggal Pengajuan:</th>
                            <td class="py-2 text-dark">{{ $cancellation->created_at->format('d/m/Y H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Status:</th>
                            <td class="py-2">
                                @if($cancellation->status_pembatalan == 'diajukan')
                                    <span class="badge bg-warning text-dark px-3 py-1.5 fw-semibold">Menunggu Konfirmasi</span>
                                @elseif($cancellation->status_pembatalan == 'disetujui')
                                    <span class="badge bg-success px-3 py-1.5 fw-semibold">Disetujui</span>
                                @else
                                    <span class="badge bg-danger px-3 py-1.5 fw-semibold">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Alasan Pelanggan:</th>
                            <td class="py-2 text-dark">
                                <div class="bg-light p-2.5 rounded border small text-muted">
                                    {{ $cancellation->alasan_pembatalan }}
                                </div>
                            </td>
                        </tr>
                        @if($cancellation->catatan_admin)
                        <tr>
                            <th class="text-muted fw-semibold py-2">Catatan Admin:</th>
                            <td class="py-2">
                                <div class="p-2.5 rounded border small" style="background-color: #f0fdf4; color: #155724; border-color: #c3e6cb;">
                                    {{ $cancellation->catatan_admin }}
                                </div>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-wallet2 text-success me-1"></i> Informasi Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <th width="40%" class="text-muted fw-semibold py-2">Total Harga Item:</th>
                            <td class="py-2 text-dark fw-medium">Rp {{ number_format($cancellation->transaksi->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Potongan Diskon:</th>
                            <td class="py-2 text-danger fw-medium">- Rp {{ number_format($cancellation->transaksi->total_diskon, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Biaya Kirim (Ongkir):</th>
                            <td class="py-2 text-dark fw-medium">Rp {{ number_format($cancellation->transaksi->ongkir, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top">
                            <th class="text-muted fw-bold py-3 fs-6">Grand Total:</th>
                            <td class="py-3 text-success fw-extrabold fs-5">
                                Rp {{ number_format($cancellation->transaksi->getTotalAmount(), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Status Pembayaran:</th>
                            <td class="py-2">
                                @if($cancellation->transaksi->status_pembayaran == 'sudah_bayar')
                                    <span class="badge bg-success px-3 py-1.5 fw-semibold">Sudah Dibayar</span>
                                @elseif($cancellation->transaksi->status_pembayaran == 'belum_bayar')
                                    <span class="badge bg-warning text-dark px-3 py-1.5 fw-semibold">Belum Dibayar</span>
                                @else
                                    <span class="badge bg-danger px-3 py-1.5 fw-semibold">Kadaluarsa / Batal</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Metode Pengiriman:</th>
                            <td class="py-2 text-dark fw-medium">{{ $cancellation->transaksi->metode_pengiriman === 'kurir' ? 'Dikirim Kurir' : 'Ambil Sendiri' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card border-0 shadow-sm my-4">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h5 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-box-seam text-success me-1"></i> Detail Produk Transaksi
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th scope="col" class="ps-4 py-3" width="8%">No</th>
                            <th scope="col" class="py-3" width="42%">Nama Produk</th>
                            <th scope="col" class="py-3 text-end" width="16%">Harga Asli</th>
                            <th scope="col" class="py-3 text-end" width="16%">Harga Diskon</th>
                            <th scope="col" class="py-3 text-center" width="10%">Qty</th>
                            <th scope="col" class="pe-4 py-3 text-end" width="16%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cancellation->transaksi->details as $index => $item)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $item->product->nama_produk ?? 'Produk tidak ditemukan' }}</td>
                            <td class="text-end fw-medium text-dark">
                                @php
                                    $hargaAsli = $item->product->harga_produk ?? 0;
                                @endphp
                                Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                @php
                                    $hargaDiskon = 0;
                                    if ($item->product && ($item->product->harga_diskon ?? 0) > 0 && ($item->product->hasActiveDiscount() ?? false)) {
                                        $hargaDiskon = $item->product->harga_diskon;
                                    }
                                @endphp
                                @if($hargaDiskon > 0)
                                    <span class="fw-semibold text-success">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                                    <span class="badge bg-danger bg-opacity-10 text-danger d-block mt-0.5" style="font-size: 0.7rem;">
                                        {{ round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100) }}% off
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-dark">{{ $item->qty }}</td>
                            <td class="pe-4 text-end fw-bold text-success-emphasis">Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($cancellation->status_pembatalan == 'diajukan')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h5 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-shield-check text-success me-1"></i> Keputusan Tindakan Admin
            </h5>
        </div>
        <div class="card-body p-4">
            <form id="processForm" method="POST" action="{{ route('admin.cancellations.process', $cancellation) }}">
                @csrf
                <input type="hidden" name="action" id="actionInput" value="">

                <div class="mb-3">
                    <label for="catatan_admin" class="form-label fw-bold text-secondary">Catatan Admin / CS (Opsional)</label>
                    <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="3"
                              placeholder="Tuliskan catatan respon persetujuan atau alasan penolakan..."></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success text-white fw-bold px-4" onclick="processAction('approve')">
                        <i class="bi bi-check-circle me-1"></i> Setujui & Kembalikan Stok
                    </button>
                    <button type="button" class="btn btn-danger text-white fw-bold px-4" onclick="processAction('reject')">
                        <i class="bi bi-x-circle me-1"></i> Tolak Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function processAction(action) {
    const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
    const actionColor = action === 'approve' ? '#10b981' : '#ef4444';
    
    Swal.fire({
        title: 'Konfirmasi Keputusan',
        text: `Apakah Anda yakin ingin ${actionText} permintaan pembatalan transaksi ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: actionColor,
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, Proses!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('actionInput').value = action;
            document.getElementById('processForm').submit();
        }
    });
}
</script>
@endpush
