@extends('layouts.admin')

@section('title', 'Manajemen Pembatalan Transaksi')

@push('styles')
<style>
    /* Glow Stat Cards */
    .stat-card-glow {
        border-radius: 12px;
        padding: 1.5rem;
        border: none;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    
    .stat-card-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .stat-card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-card-glow .label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.85;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 0.5rem;
    }

    .stat-card-glow .value {
        font-size: 1.85rem;
        font-weight: 800;
        margin: 0;
    }

    .stat-card-glow .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .card-pending {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .card-completed {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .card-cancelled {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <!-- Header -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis mb-1">
                <i class="bi bi-x-octagon-fill text-success"></i> Pembatalan Transaksi
            </h1>
            <p class="text-muted mb-0">Kelola dan tinjau semua pengajuan pembatalan transaksi dari pelanggan.</p>
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

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Menunggu Konfirmasi -->
        <div class="col-md-4">
            <div class="stat-card-glow card-pending h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Menunggu Review</span>
                        <h3 class="value">{{ number_format($statusCounts['pending'] ?? 0) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disetujui -->
        <div class="col-md-4">
            <div class="stat-card-glow card-completed h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Disetujui</span>
                        <h3 class="value">{{ number_format($statusCounts['approved'] ?? 0) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="col-md-4">
            <div class="stat-card-glow card-cancelled h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Ditolak</span>
                        <h3 class="value">{{ number_format($statusCounts['rejected'] ?? 0) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellations Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 ps-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="m-0 fw-bold text-success-emphasis">Daftar Permintaan Pembatalan</h5>
            
            <form action="{{ route('admin.cancellations.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari transaksi / alasan / pelanggan..." value="{{ request('search') }}">
                </div>
                
                <select name="status" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Perlu Review</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                
                <button type="submit" class="btn btn-sm btn-success text-white">Filter</button>
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('admin.cancellations.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th scope="col" class="ps-4 py-3" width="8%">No</th>
                            <th scope="col" class="py-3" width="18%">Kode Transaksi</th>
                            <th scope="col" class="py-3" width="22%">Pelanggan</th>
                            <th scope="col" class="py-3" width="18%">Tanggal Pengajuan</th>
                            <th scope="col" class="py-3" width="20%">Alasan</th>
                            <th scope="col" class="py-3 text-center" width="14%">Status</th>
                            <th scope="col" class="pe-4 py-3 text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cancellations as $index => $cancellation)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">{{ $cancellations->firstItem() + $index }}</td>
                            <td>
                                @if($cancellation->transaksi)
                                    <a href="{{ route('admin.transactions.show', $cancellation->transaksi->id_transaksi) }}"
                                       class="text-success fw-bold text-decoration-none">
                                        {{ $cancellation->transaksi->kode_transaksi }}
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($cancellation->transaksi && $cancellation->transaksi->pelanggan)
                                    <span class="fw-semibold text-dark">{{ $cancellation->transaksi->pelanggan->nama_pelanggan }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($cancellation->created_at)
                                    <span class="fw-medium text-dark">{{ $cancellation->created_at->format('d/m/Y') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $cancellation->created_at->format('H:i') }}</small>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small" style="max-width: 250px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $cancellation->alasan_pembatalan }}">
                                    {{ $cancellation->alasan_pembatalan }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($cancellation->status_pembatalan === 'diajukan')
                                    <span class="badge bg-warning text-dark px-3 py-1.5 fw-semibold">
                                        <i class="bi bi-hourglass-split me-1"></i> Perlu Review
                                    </span>
                                @elseif($cancellation->status_pembatalan === 'disetujui')
                                    <span class="badge bg-success px-3 py-1.5 fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                    </span>
                                @elseif($cancellation->status_pembatalan === 'ditolak')
                                    <span class="badge bg-danger px-3 py-1.5 fw-semibold">
                                        <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-1.5 fw-semibold">
                                        {{ ucfirst($cancellation->status_pembatalan) }}
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                <a href="{{ route('admin.cancellations.show', $cancellation->id_pembatalan_transaksi) }}"
                                   class="btn btn-sm btn-outline-success rounded-pill px-3"
                                   title="Review">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                Belum ada pengajuan pembatalan transaksi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info & Links -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4 border-top">
                <div class="text-muted small mb-3 mb-md-0">
                    Menampilkan <strong>{{ $cancellations->firstItem() }}</strong> sampai <strong>{{ $cancellations->lastItem() }}</strong> dari <strong>{{ $cancellations->total() }}</strong> permintaan
                </div>
                @if($cancellations->hasPages())
                    <div>
                        {{ $cancellations->appends(request()->query())->links('pagination.bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "searching": false,
            "paging": false,
            "info": false,
            "ordering": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
</script>
@endpush
