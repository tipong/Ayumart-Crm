@extends('layouts.staff')

@section('title', 'Daftar Transaksi - AyuMart')

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
    <div class="page-header mb-4">
        <h1 class="fw-extrabold text-success-emphasis">
            <i class="bi bi-wallet2 text-success"></i> Daftar Transaksi
        </h1>
        <p class="text-muted mb-0">Lihat, cari, dan pantau status transaksi penjualan di seluruh cabang AyuMart secara detail.</p>
    </div>

    <!-- Premium Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Belum Bayar Card -->
        <div class="col-md-4">
            <div class="stat-card-glow card-pending h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Belum Bayar</span>
                        <h3 class="value">{{ number_format($statusCounts['pending'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sudah Bayar Card -->
        <div class="col-md-4">
            <div class="stat-card-glow card-completed h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Sudah Bayar</span>
                        <h3 class="value">{{ number_format($statusCounts['completed'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kadaluarsa / Batal Card -->
        <div class="col-md-4">
            <div class="stat-card-glow card-cancelled h-100">
                <div class="stat-card-body">
                    <div>
                        <span class="label">Kadaluarsa / Batal</span>
                        <h3 class="value">{{ number_format($statusCounts['cancelled'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Form -->
    @php
        $hasAdvancedFilters = !empty($branchFilter) || !empty($shippingFilter) || !empty($startDate) || !empty($endDate) || !empty($statusFilter);
    @endphp
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('owner.transactions.index') }}" id="searchForm">
                <!-- Row 1: Search & Basic Sort -->
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold text-muted small"><i class="bi bi-search text-success"></i> Kata Kunci / Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   class="form-control border-start-0 custom-input" 
                                   placeholder="Cari kode transaksi, nama pelanggan, cabang...">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small"><i class="bi bi-sort-down text-success"></i> Urutkan Berdasarkan</label>
                        <select name="sort_by" class="form-select custom-input" onchange="this.form.submit()">
                            <option value="date_desc" {{ $sortBy === 'date_desc' ? 'selected' : '' }}>Terbaru (Tanggal)</option>
                            <option value="date_asc" {{ $sortBy === 'date_asc' ? 'selected' : '' }}>Terlama (Tanggal)</option>
                            <option value="total_desc" {{ $sortBy === 'total_desc' ? 'selected' : '' }}>Total Harga (Tertinggi)</option>
                            <option value="total_asc" {{ $sortBy === 'total_asc' ? 'selected' : '' }}>Total Harga (Terendah)</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-outline-success fw-bold flex-grow-1" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="{{ $hasAdvancedFilters ? 'true' : 'false' }}">
                            <i class="bi bi-sliders2-vertical me-1"></i> Filter Lanjutan
                        </button>
                        <button type="submit" class="btn btn-success text-white fw-bold px-4">
                            Terapkan
                        </button>
                    </div>
                </div>

                <!-- Row 2: Advanced Filters (Collapsible) -->
                <div class="collapse {{ $hasAdvancedFilters ? 'show' : '' }} mt-3 pt-3 border-top" id="advancedFilters">
                    <div class="row g-3">
                        <!-- Cabang -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted small"><i class="bi bi-shop text-success"></i> Cabang</label>
                            <select name="branch" class="form-select custom-input" onchange="this.form.submit()">
                                <option value="">-- Semua Cabang --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id_cabang }}" {{ $branchFilter == $branch->id_cabang ? 'selected' : '' }}>
                                        {{ $branch->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Pembayaran -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted small"><i class="bi bi-wallet2 text-success"></i> Status Pembayaran</label>
                            <select name="status" class="form-select custom-input" onchange="this.form.submit()">
                                <option value="">-- Semua Status --</option>
                                <option value="belum_bayar" {{ $statusFilter === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="sudah_bayar" {{ $statusFilter === 'sudah_bayar' ? 'selected' : '' }}>Sudah Bayar</option>
                                <option value="kadaluarsa" {{ $statusFilter === 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa / Batal</option>
                            </select>
                        </div>

                        <!-- Metode Pengiriman -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted small"><i class="bi bi-truck text-success"></i> Metode Pengiriman</label>
                            <select name="shipping_method" class="form-select custom-input" onchange="this.form.submit()">
                                <option value="">-- Semua Metode --</option>
                                <option value="ambil_sendiri" {{ $shippingFilter === 'ambil_sendiri' ? 'selected' : '' }}>Ambil Sendiri</option>
                                <option value="kurir" {{ $shippingFilter === 'kurir' ? 'selected' : '' }}>Kirim Kurir</option>
                            </select>
                        </div>

                        <!-- Rentang Tanggal Mulai -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small"><i class="bi bi-calendar-date text-success"></i> Tanggal Transaksi Mulai</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control custom-input" onchange="this.form.submit()">
                        </div>

                        <!-- Rentang Tanggal Selesai -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small"><i class="bi bi-calendar-date text-success"></i> Tanggal Transaksi Selesai</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control custom-input" onchange="this.form.submit()">
                        </div>
                    </div>
                </div>
            </form>
            @if($search || $statusFilter || $branchFilter || $shippingFilter || $startDate || $endDate || $sortBy !== 'date_desc')
                <div class="mt-3">
                    <a href="{{ route('owner.transactions.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-x-circle me-1"></i> Hapus Semua Filter
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Transactions List Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 ps-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-success-emphasis">Daftar Transaksi AyuMart</h5>
            <span class="text-muted small">Total: <strong>{{ $transactions->total() }}</strong> transaksi ditemukan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th scope="col" class="ps-4 py-3">Kode Transaksi</th>
                            <th scope="col" class="py-3">Pelanggan</th>
                            <th scope="col" class="py-3">Cabang</th>
                            <th scope="col" class="py-3">Total Harga</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3">Pengiriman</th>
                            <th scope="col" class="py-3">Tanggal Transaksi</th>
                            <th scope="col" class="pe-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            @php
                                $tanggalTransaksi = $transaction->tanggal_transaksi;
                                if (is_string($tanggalTransaksi)) {
                                    $tanggalTransaksi = \Carbon\Carbon::parse($tanggalTransaksi);
                                }
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-success-emphasis">{{ $transaction->kode_transaksi }}</span>
                                    @if($transaction->midtrans_order_id)
                                        <br><small class="text-muted text-xs">ID: {{ $transaction->midtrans_order_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->pelanggan && $transaction->pelanggan->user)
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark">{{ $transaction->pelanggan->user->name }}</span>
                                            <small class="text-muted" style="font-size: 0.8rem;">{{ $transaction->pelanggan->user->email }}</small>
                                        </div>
                                    @elseif($transaction->pelanggan)
                                        <span class="fw-semibold text-dark">{{ $transaction->pelanggan->nama_pelanggan }}</span>
                                    @else
                                        <span class="text-muted small"><em>Pelanggan Terhapus</em></span>
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
                                    <span class="fw-bold text-success">Rp {{ number_format($transaction->getTotalAmount(), 0, ',', '.') }}</span>
                                    <br>
                                    <small class="text-muted" style="font-size: 0.8rem;">{{ $transaction->details->sum('qty') }} pcs item</small>
                                </td>
                                <td>
                                    @if($transaction->status_pembayaran === 'belum_bayar')
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-clock-history me-1"></i> Belum Bayar</span>
                                    @elseif($transaction->status_pembayaran === 'sudah_bayar')
                                        <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> Sudah Bayar</span>
                                    @elseif($transaction->status_pembayaran === 'kadaluarsa')
                                        <span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i> Kadaluarsa / Batal</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">{{ ucfirst($transaction->status_pembayaran) }}</span>
                                    @endif

                                    @if($transaction->cancellation && $transaction->cancellation->status_pembatalan === 'disetujui')
                                        <span class="badge bg-danger ms-1 px-2 py-1">Batal Disetujui</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->metode_pengiriman === 'ambil_sendiri')
                                        <span class="badge bg-info text-white px-2 py-1"><i class="bi bi-bag-fill me-1"></i> Ambil Sendiri</span>
                                    @elseif($transaction->metode_pengiriman === 'kurir')
                                        <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-truck me-1"></i> Kurir</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1">{{ ucfirst($transaction->metode_pengiriman) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-dark small">{{ $tanggalTransaksi ? $tanggalTransaksi->translatedFormat('d M Y, H:i') : '-' }}</span>
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="{{ route('owner.transactions.show', $transaction->id_transaksi) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                    Tidak ada data transaksi yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @if($transactions->hasPages())
                <div class="d-flex justify-content-center py-4 border-top">
                    {{ $transactions->appends(request()->query())->links('pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Premium Styling and Typography */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    .main-content {
        font-family: 'Inter', sans-serif !important;
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

    /* Filter Inputs */
    .custom-input {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 0.6rem 1rem;
        font-weight: 500;
        color: #4b5563;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }

    .custom-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        background-color: #ffffff;
    }

    .table thead th {
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
</style>
@endpush
