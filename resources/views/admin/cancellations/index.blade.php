@extends('layouts.admin')

@section('title', 'Manajemen Pembatalan Transaksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Pembatalan Transaksi</h1>
            <p class="text-muted">Kelola permintaan pembatalan transaksi dari pelanggan</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Konfirmasi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Disetujui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Ditolak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Pembatalan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Transaksi</th>
                            <th>Pelanggan</th>
                            <th>Tanggal Pembatalan</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cancellations as $index => $cancellation)
                        <tr>
                            <td>{{ $cancellations->firstItem() + $index }}</td>
                            <td>
                                @if($cancellation->transaksi)
                                    <a href="{{ route('admin.transactions.show', $cancellation->transaksi->id_transaksi) }}"
                                       class="text-primary font-weight-bold">
                                        {{ $cancellation->transaksi->kode_transaksi }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($cancellation->transaksi && $cancellation->transaksi->pelanggan)
                                    {{ $cancellation->transaksi->pelanggan->nama_pelanggan }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $cancellation->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="text-muted" style="max-width: 200px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $cancellation->alasan_pembatalan }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($cancellation->status_pembatalan === 'diajukan')
                                    <span class="text-warning shadow badge badge-warning px-3 py-2">
                                        <i class="fas fa-clock mr-1"></i> Menunggu Konfirmasi
                                    </span>
                                @elseif($cancellation->status_pembatalan === 'disetujui')
                                    <span class="text-success shadow badge badge-success px-3 py-2">
                                        <i class="fas fa-check-circle mr-1"></i> Disetujui
                                    </span>
                                @elseif($cancellation->status_pembatalan === 'ditolak')
                                    <span class="text-danger shadow badge badge-danger px-3 py-2">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                @else
                                    <span class="text-secondary shadow badge badge-secondary px-3 py-2">
                                        <i class="fas fa-question-circle mr-1"></i>
                                        {{ $cancellation->status_pembatalan ?: 'Unknown' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.cancellations.show', $cancellation->id_pembatalan_transaksi) }}"
                                   class="btn btn-sm btn-info"
                                   title="Lihat detail pembatalan #{{ $cancellation->id_pembatalan_transaksi }}">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Tidak ada data pembatalan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $cancellations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with disable auto search
        $('#dataTable').DataTable({
            "searching": false,
            "paging": false,
            "info": false,
            "ordering": true,
        });
    });
</script>
@endpush
