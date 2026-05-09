@extends('layouts.admin')

@section('title', 'Detail Pembatalan Transaksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Pembatalan Transaksi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cancellations.index') }}">Pembatalan</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.cancellations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Cancellation Info -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pembatalan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Kode Transaksi:</th>
                            <td>
                                <a href="{{ route('admin.transactions.show', $cancellation->transaksi->id_transaksi) }}">
                                    {{ $cancellation->transaksi->kode_transaksi }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Pelanggan:</th>
                            <td>{{ $cancellation->transaksi->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email Pelanggan:</th>
                            <td>
                                @if($cancellation->transaksi->pelanggan && $cancellation->transaksi->pelanggan->user)
                                    {{ $cancellation->transaksi->pelanggan->user->email }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Pembatalan:</th>
                            <td>{{ $cancellation->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($cancellation->status_pembatalan == 'diajukan')
                                    <span class="badge text-warning">Menunggu Konfirmasi</span>
                                @elseif($cancellation->status_pembatalan == 'disetujui')
                                    <span class="badge text-success">Disetujui</span>
                                @else
                                    <span class="badge text-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Alasan Pembatalan:</th>
                            <td>{{ $cancellation->alasan_pembatalan }}</td>
                        </tr>
                        @if($cancellation->catatan_admin)
                        <tr>
                            <th>Catatan Admin:</th>
                            <td>{{ $cancellation->catatan_admin }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Transaksi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Total Transaksi:</th>
                            <td>Rp {{ number_format($cancellation->transaksi->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Diskon:</th>
                            <td>Rp {{ number_format($cancellation->transaksi->total_diskon, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Ongkir:</th>
                            <td>Rp {{ number_format($cancellation->transaksi->ongkir, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Grand Total:</th>
                            <td class="font-weight-bold">
                                Rp {{ number_format($cancellation->transaksi->getTotalAmount(), 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Status Pembayaran:</th>
                            <td>
                                @if($cancellation->transaksi->status_pembayaran == 'sudah_bayar')
                                    <span class="badge badge-success text-success">Sudah Dibayar</span>
                                @elseif($cancellation->transaksi->status_pembayaran == 'belum_bayar')
                                    <span class="badge badge-warning text-warning">Belum Dibayar</span>
                                @else
                                    <span class="badge badge-danger text-danger">Kadaluarsa</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Metode Pengiriman:</th>
                            <td>{{ $cancellation->transaksi->metode_pengiriman === 'kurir' ? 'Dikirim Kurir' : 'Ambil Sendiri' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Produk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Harga Asli</th>
                            <th>Harga Diskon</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cancellation->transaksi->details as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->nama_produk ?? 'Produk tidak ditemukan' }}</td>
                            <td>
                                @php
                                    $hargaAsli = 0;
                                    if ($item->product) {
                                        $hargaAsli = $item->product->harga_produk ?? 0;
                                    }
                                @endphp
                                Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                            </td>
                            <td>
                                @php
                                    $hargaDiskon = 0;
                                    if ($item->product && ($item->product->harga_diskon ?? 0) > 0 && ($item->product->hasActiveDiscount() ?? false)) {
                                        $hargaDiskon = $item->product->harga_diskon;
                                    }
                                @endphp
                                @if($hargaDiskon > 0)
                                    <strong class="text-success">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</strong>
                                    <small class="text-danger d-block">
                                        {{ round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100) }}% off
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($cancellation->status_pembatalan == 'diajukan')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tindakan</h6>
        </div>
        <div class="card-body">
            <form id="processForm" method="POST" action="{{ route('admin.cancellations.process', $cancellation) }}">
                @csrf
                <input type="hidden" name="action" id="actionInput" value="">

                <div class="form-group">
                    <label for="catatan_admin">Catatan Admin (opsional):</label>
                    <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="3"
                              placeholder="Berikan catatan jika diperlukan..."></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success mr-2" onclick="processAction('approve')">
                        <i class="fas fa-check"></i> Setujui Pembatalan
                    </button>
                    <button type="button" class="btn btn-danger" onclick="processAction('reject')">
                        <i class="fas fa-times"></i> Tolak Pembatalan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function processAction(action) {
    const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
    const confirmText = `Apakah Anda yakin ingin ${actionText} pembatalan transaksi ini?`;

    if (confirm(confirmText)) {
        document.getElementById('actionInput').value = action;
        document.getElementById('processForm').submit();
    }
}
</script>
@endpush
@endsection
