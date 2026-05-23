@extends('layouts.admin')

@section('title', 'Manajemen Transaksi')

@push('styles')
<style>
    /* Custom Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        margin: 0 2px;
        cursor: pointer;
        border: 1px solid #e5e7eb;
        color: #0f766e;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover:not(.disabled) {
        background-color: #10b981;
        color: white;
        border-color: #10b981;
    }

    .pagination .page-item.active .page-link {
        background-color: #10b981;
        border-color: #10b981;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #9ca3af;
        cursor: not-allowed;
        background-color: #f3f4f6;
        border-color: #e5e7eb;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    /* Table styling */
    .table tbody tr {
        transition: background-color 0.2s ease;
    }

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

    /* Custom input */
    .custom-input {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 0.6rem 1rem;
        font-weight: 500;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }

    .custom-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        background-color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <!-- Page Heading -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis">
                <i class="bi bi-cart-fill text-success"></i> Manajemen Transaksi
            </h1>
            <p class="text-muted mb-0">Kelola dan pantau semua data transaksi penjualan AyuMart.</p>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row g-4 mb-4">
        <!-- Belum Bayar -->
        <div class="col-md-4">
            <div class="stat-card-glow card-pending h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Belum Bayar</span>
                        <h3 class="value">{{ number_format($statusCounts['pending'] ?? 0) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sudah Bayar -->
        <div class="col-md-4">
            <div class="stat-card-glow card-completed h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Sudah Bayar</span>
                        <h3 class="value">{{ number_format($statusCounts['completed'] ?? 0) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kadaluarsa -->
        <div class="col-md-4">
            <div class="stat-card-glow card-cancelled h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Kadaluarsa / Batal</span>
                        <h3 class="value">{{ number_format($statusCounts['cancelled'] ?? 0) }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <!-- Search and Filter Row -->
            <form id="searchForm" method="GET" action="{{ route('admin.transactions.index') }}" class="mb-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 custom-input"
                                   id="searchInput"
                                   name="search"
                                   value="{{ $search ?? '' }}"
                                   placeholder="Cari nama pelanggan, kode transaksi, atau cabang...">
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end text-start">
                        @if($search)
                            <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary rounded-pill">
                                <i class="bi bi-x-circle"></i> Hapus Filter
                            </a>
                        @endif
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <span class="text-muted small fw-semibold" id="searchResults">
                            @if($search)
                                <i class="bi bi-info-circle text-success me-1"></i> Hasil pencarian untuk "{{ $search }}" (<strong>{{ $transactions->total() }}</strong> hasil)
                            @else
                                <i class="bi bi-info-circle text-success me-1"></i> Menampilkan semua transaksi (Total: <strong>{{ $transactions->total() }}</strong>)
                            @endif
                        </span>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="transactionsTable">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th scope="col" class="ps-4 py-3">Pelanggan</th>
                                <th scope="col" class="py-3">Cabang</th>
                                <th scope="col" class="py-3">Kode Transaksi</th>
                                <th scope="col" class="py-3">Total Belanja</th>
                                <th scope="col" class="py-3">Status</th>
                                <th scope="col" class="py-3">Tanggal Transaksi</th>
                                <th scope="col" class="py-3">Pembatalan</th>
                                <th scope="col" class="pe-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach($transactions as $transaction)
                                @php
                                    $tanggalTransaksi = $transaction->tanggal_transaksi;
                                    if (is_string($tanggalTransaksi)) {
                                        $tanggalTransaksi = \Carbon\Carbon::parse($tanggalTransaksi);
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        @if($transaction->pelanggan && $transaction->pelanggan->user)
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $transaction->pelanggan->user->name }}</span>
                                                <small class="text-muted" style="font-size: 0.8rem;">{{ $transaction->pelanggan->user->email }}</small>
                                            </div>
                                        @elseif($transaction->pelanggan)
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-warning-emphasis">{{ $transaction->pelanggan->nama_pelanggan }}</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">(Tanpa Akun User)</small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-person-x"></i> Data Terhapus
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->cabang)
                                            <span class="badge bg-light text-dark border"><i class="bi bi-shop text-success me-1"></i> {{ $transaction->cabang->nama_cabang }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $transaction->kode_transaksi }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">Rp {{ number_format($transaction->getTotalAmount(), 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusPembayaran = $transaction->status_pembayaran ?? 'unknown';
                                        @endphp

                                        @if($statusPembayaran === 'belum_bayar')
                                            <span class="badge bg-warning text-dark px-2 py-1">
                                                <i class="bi bi-clock-history"></i> Belum Bayar
                                            </span>
                                        @elseif($statusPembayaran === 'sudah_bayar')
                                            <span class="badge bg-success px-2 py-1">
                                                <i class="bi bi-check-circle-fill"></i> Sudah Bayar
                                            </span>
                                        @elseif($statusPembayaran === 'kadaluarsa')
                                            <span class="badge bg-danger px-2 py-1">
                                                <i class="bi bi-x-circle-fill"></i> Kadaluarsa
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-2 py-1">
                                                {{ ucfirst($statusPembayaran) }}
                                            </span>
                                        @endif

                                        @if($transaction->status_pengiriman && $transaction->status_pengiriman !== 'pending')
                                            <br>
                                            <small class="badge bg-info mt-1">
                                                <i class="bi bi-truck"></i>
                                                {{ ucfirst(str_replace('_', ' ', $transaction->status_pengiriman)) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->tanggal_transaksi)
                                            @php
                                                $tanggalTransaksi = $transaction->tanggal_transaksi;
                                                if (is_string($tanggalTransaksi)) {
                                                    $tanggalTransaksi = \Carbon\Carbon::parse($tanggalTransaksi);
                                                }
                                            @endphp
                                            <span class="fw-semibold text-dark">{{ $tanggalTransaksi->format('d/m/Y') }}</span>
                                            <br>
                                            <small class="text-muted">{{ $tanggalTransaksi->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->cancellation)
                                            @if($transaction->cancellation->status_pembatalan === 'diajukan')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> Perlu Review
                                                </span>
                                                <br>
                                                <a href="{{ route('admin.cancellations.show', $transaction->cancellation->id_pembatalan_transaksi) }}"
                                                   class="btn btn-xs btn-outline-warning mt-1"
                                                   title="Review Pembatalan">
                                                    <i class="bi bi-eye"></i> Review
                                                </a>
                                            @elseif($transaction->cancellation->status_pembatalan === 'disetujui')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Disetujui
                                                </span>
                                            @elseif($transaction->cancellation->status_pembatalan === 'ditolak')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> Ditolak
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">Tidak Ada</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-center">
                                        <a href="{{ route('admin.transactions.show', $transaction->id_transaksi) }}"
                                           class="btn btn-sm btn-outline-success rounded-pill px-3"
                                           title="Lihat Detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info & Links -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4 border-top">
                    <div class="text-muted small mb-3 mb-md-0">
                        Menampilkan <strong>{{ $transactions->firstItem() }}</strong> sampai <strong>{{ $transactions->lastItem() }}</strong> dari <strong>{{ $transactions->total() }}</strong> transaksi
                    </div>
                    @if($transactions->hasPages())
                        <div>
                            {{ $transactions->appends(request()->query())->links('pagination.bootstrap-4') }}
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                    Belum ada data transaksi masuk.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        let searchTimeout;

        // Show popup if no results found
        @if($noResults ?? false)
            Swal.fire({
                icon: 'info',
                title: 'Data Tidak Ditemukan',
                text: 'Tidak ada transaksi yang cocok dengan pencarian "{{ $search }}".',
                confirmButtonColor: '#10b981'
            }).then(() => {
                window.location.href = "{{ route('admin.transactions.index') }}";
            });
        @endif

        // Debounce search submit
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                clearTimeout(searchTimeout);
                if (e.key === 'Enter') {
                    searchForm.submit();
                } else {
                    searchTimeout = setTimeout(() => {
                        searchForm.submit();
                    }, 800);
                }
            });
        }
    });
</script>
@endsection
