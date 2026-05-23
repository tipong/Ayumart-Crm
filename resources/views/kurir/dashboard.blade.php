@extends('layouts.staff')

@section('title', 'Dashboard Kurir')

@section('sidebar-menu')
    <a class="nav-link active" href="{{ route('kurir.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Pengiriman</div>

    <!-- <a class="nav-link" href="{{ route('kurir.shipping.index') }}">
        <i class="bi bi-gear"></i>
        <span>Kelola Pengiriman</span>
    </a> -->

    <a class="nav-link" href="{{ route('kurir.dashboard') }}#pending">
        <i class="bi bi-clock-history"></i>
        <span>Pending Delivery</span>
    </a>

    <a class="nav-link" href="{{ route('kurir.dashboard') }}#inprogress">
        <i class="bi bi-truck"></i>
        <span>On Delivery</span>
    </a>

    <a class="nav-link" href="{{ route('kurir.dashboard') }}#completed">
        <i class="bi bi-check-circle"></i>
        <span>Completed</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Akun</div>

    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-truck"></i>
            Dashboard Kurir
        </h1>
        <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}! Kelola pengiriman Anda dengan efisien.</p>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-warning-gradient">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-label">Pending Delivery</div>
                <div class="stat-value">{{ $pendingDeliveries ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-info-gradient">
                <div class="stat-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stat-label">On Delivery</div>
                <div class="stat-value">{{ $ongoingDeliveries ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-success-gradient">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-label">Completed Today</div>
                <div class="stat-value">{{ $completedToday ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-primary-gradient">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-label">Total Delivery</div>
                <div class="stat-value">{{ $totalDeliveries ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Pending Deliveries -->
    <div class="row mb-4" id="pending">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 ps-4 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-clock-history text-warning me-1"></i>
                        Pending Deliveries
                    </h6>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.75rem;">
                        {{ count($pendingOrders ?? []) }} Pending
                    </span>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop View Table (d-none d-md-block) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Order ID</th>
                                    <th>Pelanggan</th>
                                    <th>Lokasi Pickup</th>
                                    <th>Alamat Tujuan</th>
                                    <th>Tanggal Pengiriman</th>
                                    <th class="pe-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingOrders ?? [] as $pengiriman)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark d-block">#{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</span>
                                        <small class="text-muted">Resi: {{ $pengiriman->no_resi ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block">{{ $pengiriman->nama_pelanggan ?? 'N/A' }}</span>
                                        <small class="text-muted">Penerima: {{ $pengiriman->nama_penerima ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($pengiriman->nama_cabang)
                                            <div><strong class="text-primary"><i class="bi bi-shop"></i> {{ $pengiriman->nama_cabang }}</strong></div>
                                            <small class="text-muted">{{ Str::limit($pengiriman->alamat_cabang ?? '-', 30) }}</small>
                                            @if($pengiriman->telp_cabang)
                                                <br><small class="text-muted"><i class="bi bi-telephone"></i> <a href="tel:{{ $pengiriman->telp_cabang }}" class="text-decoration-none text-muted">{{ $pengiriman->telp_cabang }}</a></small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pengiriman->alamat_penerima)
                                            <div>
                                                <strong>{{ $pengiriman->nama_penerima ?? 'N/A' }}</strong>
                                                @if($pengiriman->no_telp_penerima)
                                                    <br><small class="text-muted"><i class="bi bi-phone"></i> <a href="tel:{{ $pengiriman->no_telp_penerima }}" class="text-decoration-none text-muted">{{ $pengiriman->no_telp_penerima }}</a></small>
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted"><strong>Alamat:</strong> {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}</small>
                                                @if($pengiriman->kota || $pengiriman->kecamatan)
                                                    <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                                @endif
                                            </div>
                                            @if($pengiriman->latitude && $pengiriman->longitude)
                                                <div class="mt-2">
                                                    <small class="text-primary">
                                                        <i class="bi bi-geo-alt-fill"></i>
                                                        <a href="https://maps.google.com/?q={{ $pengiriman->latitude }},{{ $pengiriman->longitude }}" target="_blank" class="text-decoration-none fw-bold">
                                                            Buka Maps
                                                        </a>
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($pengiriman->tgl_kirim ?? $pengiriman->tanggal_transaksi ?? now())->format('d M Y H:i') }}</td>
                                    <td class="pe-4 text-end">
                                        @php
                                            $idStaff = $pengiriman->id_staff ?? null;
                                            $statusPengiriman = $pengiriman->status_pengiriman ?? 'pending';
                                        @endphp
                                        @if($idStaff && $idStaff == $staffId)
                                            @if($statusPengiriman == 'dikemas')
                                                <form action="{{ route('kurir.deliveries.claim', $pengiriman->id_pengiriman) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-success btn-claim rounded-pill px-3">
                                                        <i class="bi bi-truck"></i> Mulai Kirim
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-2.5 py-1 fw-bold">
                                                    <i class="bi bi-clock"></i> {{ ucfirst(str_replace('_', ' ', $statusPengiriman)) }}
                                                </span>
                                            @endif
                                        @elseif($idStaff && $idStaff != $staffId)
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2.5 py-1 fw-bold">
                                                <i class="bi bi-person-x"></i> Kurir Lain
                                            </span>
                                        @elseif(!$idStaff && $statusPengiriman == 'dikemas')
                                            <form action="{{ route('kurir.deliveries.claim', $pengiriman->id_pengiriman) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-primary btn-claim rounded-pill px-3">
                                                    <i class="bi bi-hand-thumbs-up"></i> Ambil Pengiriman
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 px-2.5 py-1 fw-bold">
                                                <i class="bi bi-hourglass-split"></i> Belum Siap
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50 text-warning"></i>
                                        Tidak ada pending deliveries.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View (d-md-none) -->
                    <div class="d-md-none p-3 bg-light">
                        @forelse($pendingOrders ?? [] as $pengiriman)
                        <div class="card mb-3 border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 fw-bold px-2 py-1 small">Pending</span>
                                    <span class="ms-1 fw-extrabold text-dark text-uppercase" style="font-size: 0.85rem;">#{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($pengiriman->tanggal_transaksi ?? now())->format('d M') }}</small>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.65rem;">Pickup Cabang</span>
                                        <strong class="text-dark">{{ $pengiriman->nama_cabang ?? '-' }}</strong>
                                        <p class="text-muted mb-1 small">{{ $pengiriman->alamat_cabang ?? '-' }}</p>
                                        @if($pengiriman->telp_cabang)
                                        <a href="tel:{{ $pengiriman->telp_cabang }}" class="btn btn-sm btn-light py-1 px-2 text-secondary border border-light-subtle" style="font-size: 0.75rem;">
                                            <i class="bi bi-telephone me-1 text-primary"></i> Hubungi Cabang
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2 opacity-50">
                                <div class="d-flex align-items-start">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div class="w-100">
                                        <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.65rem;">Alamat Penerima</span>
                                        <strong class="text-dark">{{ $pengiriman->nama_penerima ?? 'N/A' }}</strong>
                                        <p class="text-muted mb-2 small mt-1">
                                            {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}
                                            @if($pengiriman->kecamatan || $pengiriman->kota)
                                            <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                            @endif
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @if($pengiriman->no_telp_penerima)
                                            <a href="tel:{{ $pengiriman->no_telp_penerima }}" class="btn btn-sm btn-outline-success py-1 px-2.5" style="font-size: 0.75rem;">
                                                <i class="bi bi-telephone-fill me-1"></i> Hubungi Penerima
                                            </a>
                                            @endif
                                            @if($pengiriman->latitude && $pengiriman->longitude)
                                            <a href="https://maps.google.com/?q={{ $pengiriman->latitude }},{{ $pengiriman->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2.5" style="font-size: 0.75rem;">
                                                <i class="bi bi-map-fill me-1"></i> Rute Maps
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 p-3 pt-0">
                                @php
                                    $idStaff = $pengiriman->id_staff ?? null;
                                    $statusPengiriman = $pengiriman->status_pengiriman ?? 'pending';
                                @endphp
                                @if($idStaff && $idStaff == $staffId)
                                    @if($statusPengiriman == 'dikemas')
                                        <form action="{{ route('kurir.deliveries.claim', $pengiriman->id_pengiriman) }}" method="POST">
                                            @csrf
                                            <button type="button" class="btn btn-success w-100 py-2 btn-claim fw-bold rounded-3">
                                                <i class="bi bi-truck me-1"></i> Mulai Kirim
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary w-100 py-2 fw-bold rounded-3" disabled>
                                            <i class="bi bi-clock me-1"></i> {{ ucfirst(str_replace('_', ' ', $statusPengiriman)) }}
                                        </button>
                                    @endif
                                @elseif($idStaff && $idStaff != $staffId)
                                    <button class="btn btn-secondary w-100 py-2 fw-bold rounded-3" disabled>
                                        <i class="bi bi-person-x me-1"></i> Kurir Lain
                                    </button>
                                @elseif(!$idStaff && $statusPengiriman == 'dikemas')
                                    <form action="{{ route('kurir.deliveries.claim', $pengiriman->id_pengiriman) }}" method="POST">
                                        @csrf
                                        <button type="button" class="btn btn-primary w-100 py-2 btn-claim fw-bold rounded-3">
                                            <i class="bi bi-hand-thumbs-up-fill me-1"></i> Ambil Pengiriman
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-warning text-dark w-100 py-2 fw-bold rounded-3" disabled>
                                        <i class="bi bi-hourglass-split me-1"></i> Belum Siap
                                    </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4 card border-0">
                            <i class="bi bi-inbox fs-2 opacity-50 text-warning"></i>
                            <p class="mb-0 mt-2">Tidak ada pending deliveries.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- On Delivery -->
    <div class="row mb-4" id="inprogress">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 ps-4 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-truck text-info me-1"></i>
                        On Delivery
                    </h6>
                    <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.75rem;">
                        {{ count($ongoingOrders ?? []) }} Aktif
                    </span>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop View Table (d-none d-md-block) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Order ID</th>
                                    <th>Pelanggan</th>
                                    <th>Lokasi Pickup</th>
                                    <th>Alamat Tujuan</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ongoingOrders ?? [] as $pengiriman)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark d-block">#{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</span>
                                        <small class="text-muted">Resi: {{ $pengiriman->no_resi ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block">{{ $pengiriman->nama_pelanggan ?? 'N/A' }}</span>
                                        <small class="text-muted">Penerima: {{ $pengiriman->nama_penerima ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($pengiriman->nama_cabang)
                                            <div><strong class="text-primary"><i class="bi bi-shop"></i> {{ $pengiriman->nama_cabang }}</strong></div>
                                            <small class="text-muted">{{ Str::limit($pengiriman->alamat_cabang ?? '-', 30) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pengiriman->alamat_penerima)
                                            <div>
                                                <strong>{{ $pengiriman->nama_penerima ?? 'N/A' }}</strong>
                                                @if($pengiriman->no_telp_penerima)
                                                    <br><small class="text-muted"><i class="bi bi-phone"></i> <a href="tel:{{ $pengiriman->no_telp_penerima }}" class="text-decoration-none text-muted">{{ $pengiriman->no_telp_penerima }}</a></small>
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted"><strong>Alamat:</strong> {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}</small>
                                                @if($pengiriman->kota || $pengiriman->kecamatan)
                                                    <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                                @endif
                                            </div>
                                            @if($pengiriman->latitude && $pengiriman->longitude)
                                                <div class="mt-2">
                                                    <small class="text-primary">
                                                        <i class="bi bi-geo-alt-fill"></i>
                                                        <a href="https://maps.google.com/?q={{ $pengiriman->latitude }},{{ $pengiriman->longitude }}" target="_blank" class="text-decoration-none fw-bold">
                                                            Buka Maps
                                                        </a>
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-2.5 py-1 fw-bold">
                                            Dalam Pengiriman
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <form action="{{ route('kurir.deliveries.complete', $pengiriman->id_pengiriman) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-success btn-complete rounded-pill px-3">
                                                <i class="bi bi-check-circle"></i> Selesai
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50 text-info"></i>
                                        Tidak ada ongoing deliveries.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View (d-md-none) -->
                    <div class="d-md-none p-3 bg-light">
                        @forelse($ongoingOrders ?? [] as $pengiriman)
                        <div class="card mb-3 border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 fw-bold px-2 py-1 small">On Delivery</span>
                                    <span class="ms-1 fw-extrabold text-dark text-uppercase" style="font-size: 0.85rem;">#{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($pengiriman->tanggal_transaksi ?? now())->format('d M') }}</small>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.65rem;">Pickup Cabang</span>
                                        <strong class="text-dark">{{ $pengiriman->nama_cabang ?? '-' }}</strong>
                                        <p class="text-muted mb-1 small">{{ $pengiriman->alamat_cabang ?? '-' }}</p>
                                    </div>
                                </div>
                                <hr class="my-2 opacity-50">
                                <div class="d-flex align-items-start">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div class="w-100">
                                        <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.65rem;">Alamat Penerima</span>
                                        <strong class="text-dark">{{ $pengiriman->nama_penerima ?? 'N/A' }}</strong>
                                        <p class="text-muted mb-2 small mt-1">
                                            {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}
                                            @if($pengiriman->kecamatan || $pengiriman->kota)
                                            <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                            @endif
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @if($pengiriman->no_telp_penerima)
                                            <a href="tel:{{ $pengiriman->no_telp_penerima }}" class="btn btn-sm btn-outline-success py-1 px-2.5" style="font-size: 0.75rem;">
                                                <i class="bi bi-telephone-fill me-1"></i> Hubungi Penerima
                                            </a>
                                            @endif
                                            @if($pengiriman->latitude && $pengiriman->longitude)
                                            <a href="https://maps.google.com/?q={{ $pengiriman->latitude }},{{ $pengiriman->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2.5" style="font-size: 0.75rem;">
                                                <i class="bi bi-map-fill me-1"></i> Rute Maps
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 p-3 pt-0">
                                <form action="{{ route('kurir.deliveries.complete', $pengiriman->id_pengiriman) }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-success w-100 py-2 btn-complete fw-bold rounded-3">
                                        <i class="bi bi-check-circle me-1"></i> Selesai
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4 card border-0">
                            <i class="bi bi-inbox fs-2 opacity-50 text-info"></i>
                            <p class="mb-0 mt-2">Tidak ada ongoing deliveries.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Deliveries Today -->
    <div class="row" id="completed">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 ps-4 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Completed Deliveries (30 Hari Terakhir)
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.75rem;">
                        {{ count($completedOrders ?? []) }} Selesai
                    </span>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop View Table (d-none d-md-block) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Order ID</th>
                                    <th>Pelanggan</th>
                                    <th>Lokasi Pickup</th>
                                    <th>Alamat Tujuan</th>
                                    <th>Waktu Selesai</th>
                                    <th class="pe-4 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedOrders ?? [] as $pengiriman)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark d-block">#{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</span>
                                        <small class="text-muted">Resi: {{ $pengiriman->no_resi ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block">{{ $pengiriman->nama_pelanggan ?? 'N/A' }}</span>
                                        <small class="text-muted">Penerima: {{ $pengiriman->nama_penerima ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($pengiriman->nama_cabang)
                                            <div><strong class="text-primary"><i class="bi bi-shop"></i> {{ $pengiriman->nama_cabang }}</strong></div>
                                            <small class="text-muted">{{ Str::limit($pengiriman->alamat_cabang ?? '-', 30) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pengiriman->alamat_penerima)
                                            <div>
                                                <strong>{{ $pengiriman->nama_penerima ?? 'N/A' }}</strong>
                                                @if($pengiriman->no_telp_penerima)
                                                    <br><small class="text-muted"><i class="bi bi-phone"></i> <a href="tel:{{ $pengiriman->no_telp_penerima }}" class="text-decoration-none text-muted">{{ $pengiriman->no_telp_penerima }}</a></small>
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted"><strong>Alamat:</strong> {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}</small>
                                                @if($pengiriman->kota || $pengiriman->kecamatan)
                                                    <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($pengiriman->tgl_sampai)->format('d M Y H:i') }}</td>
                                    <td class="pe-4 text-end">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2.5 py-1 fw-bold">
                                            Delivered
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50 text-success"></i>
                                        Belum ada completed deliveries dalam 30 hari terakhir.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View (d-md-none) -->
                    <div class="d-md-none p-3 bg-light">
                        @forelse($completedOrders ?? [] as $pengiriman)
                        <div class="card mb-3 border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 fw-bold px-2 py-1 small">Delivered</span>
                                    <span class="ms-1 fw-extrabold text-dark text-uppercase" style="font-size: 0.85rem;">#{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($pengiriman->tgl_sampai ?? now())->format('d M') }}</small>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.65rem;">Pickup Cabang</span>
                                        <strong class="text-dark">{{ $pengiriman->nama_cabang ?? '-' }}</strong>
                                        <p class="text-muted mb-1 small">{{ $pengiriman->alamat_cabang ?? '-' }}</p>
                                    </div>
                                </div>
                                <hr class="my-2 opacity-50">
                                <div class="d-flex align-items-start">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div class="w-100">
                                        <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.65rem;">Alamat Penerima</span>
                                        <strong class="text-dark">{{ $pengiriman->nama_penerima ?? 'N/A' }}</strong>
                                        <p class="text-muted mb-1 small mt-1">
                                            {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}
                                            @if($pengiriman->kecamatan || $pengiriman->kota)
                                            <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 p-3 pt-0 text-center">
                                <small class="text-muted fw-bold">Selesai pada: {{ \Carbon\Carbon::parse($pengiriman->tgl_sampai)->format('d M Y H:i') }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4 card border-0">
                            <i class="bi bi-inbox fs-2 opacity-50 text-success"></i>
                            <p class="mb-0 mt-2">Belum ada completed deliveries.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Intercept claim button click
        $(document).on('click', '.btn-claim', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Konfirmasi Ambil Pengiriman',
                text: 'Apakah Anda yakin ingin mengambil order ini untuk dikirim sekarang?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Ambil!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success px-4 py-2 me-2',
                    cancelButton: 'btn btn-secondary px-4 py-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    form.submit();
                }
            });
        });

        // Intercept complete button click
        $(document).on('click', '.btn-complete', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Konfirmasi Pengiriman Selesai',
                text: 'Apakah pengiriman untuk order ini sudah selesai diantar ke alamat tujuan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success px-4 py-2 me-2',
                    cancelButton: 'btn btn-secondary px-4 py-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
