@extends('layouts.staff')

@section('title', 'Dashboard Kurir')

@section('sidebar-menu')
    <a class="nav-link active" href="{{ route('kurir.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Pengiriman</div>

    {{-- <a class="nav-link" href="{{ route('kurir.shipping.index') }}">
        <i class="bi bi-gear"></i>
        <span>Kelola Pengiriman</span>
    </a> --}}

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
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history text-warning"></i>
                        Pending Deliveries
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Pelanggan</th>
                                    <th>Lokasi Pickup</th>
                                    <th>Alamat Tujuan</th>
                                    <th>Tanggal Pengiriman</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingOrders ?? [] as $pengiriman)
                                <tr>
                                    <td>
                                        <strong>{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">Resi: {{ $pengiriman->no_resi ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div><strong>{{ $pengiriman->nama_pelanggan ?? 'N/A' }}</strong></div>
                                        <small class="text-muted">Penerima: {{ $pengiriman->nama_penerima ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($pengiriman->nama_cabang)
                                            <div><strong class="text-primary"><i class="bi bi-shop"></i> {{ $pengiriman->nama_cabang }}</strong></div>
                                            <small class="text-muted">{{ Str::limit($pengiriman->alamat_cabang ?? '-', 30) }}</small>
                                            @if($pengiriman->telp_cabang)
                                                <br><small class="text-muted"><i class="bi bi-telephone"></i> {{ $pengiriman->telp_cabang }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pengiriman->alamat_penerima)
                                            <div>
                                                <strong>{{ $pengiriman->nama_penerima ?? 'N/A' }}</strong>
                                                <br><small class="text-muted">{{ $pengiriman->no_telp_penerima ?? '-' }}</small>
                                            </div>
                                            <div class="mt-2">
                                                <small><strong>Alamat:</strong> {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}</small>
                                                @if($pengiriman->kota || $pengiriman->kecamatan)
                                                    <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                                @endif
                                            </div>
                                            @if($pengiriman->latitude && $pengiriman->longitude)
                                                <div class="mt-2">
                                                    <small class="text-primary">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <a href="https://maps.google.com/?q={{ $pengiriman->latitude }},{{ $pengiriman->longitude }}" target="_blank" class="text-decoration-none">
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
                                    <td>
                                        @php
                                            $idStaff = $pengiriman->id_staff ?? null;
                                            $statusPengiriman = $pengiriman->status_pengiriman ?? 'pending';
                                        @endphp
                                        @if($idStaff && $idStaff == $staffId)
                                            {{-- Assigned to current kurir, ready to be picked up --}}
                                            @if($statusPengiriman == 'dikemas')
                                                <form action="{{ route('kurir.deliveries.claim', $pengiriman->id_pengiriman) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirmPickup(event)">
                                                        <i class="bi bi-truck"></i> Mulai Kirim
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-info">
                                                    <i class="bi bi-clock"></i> {{ ucfirst(str_replace('_', ' ', $statusPengiriman)) }}
                                                </span>
                                            @endif
                                        @elseif($idStaff && $idStaff != $currentStaffId)
                                            {{-- Assigned to another kurir --}}
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-person-x"></i> Kurir Lain
                                            </span>
                                        @elseif(!$idStaff && $statusPengiriman == 'dikemas')
                                            {{-- Not assigned yet - kurir can claim it --}}
                                            <form action="{{ route('kurir.deliveries.claim', $pengiriman->id_pengiriman) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" onclick="return confirmPickup(event)">
                                                    <i class="bi bi-hand-thumbs-up"></i> Ambil Pengiriman
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass-split"></i> Belum Siap
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2">Tidak ada pending deliveries</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- On Delivery -->
    <div class="row mb-4" id="inprogress">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-truck text-info"></i>
                        On Delivery
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Pelanggan</th>
                                    <th>Lokasi Pickup</th>
                                    <th>Alamat Tujuan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ongoingOrders ?? [] as $pengiriman)
                                <tr>
                                    <td>
                                        <strong>{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">Resi: {{ $pengiriman->no_resi ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div><strong>{{ $pengiriman->nama_pelanggan ?? 'N/A' }}</strong></div>
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
                                                <br><small class="text-muted">{{ $pengiriman->no_telp_penerima ?? '-' }}</small>
                                            </div>
                                            <div class="mt-2">
                                                <small><strong>Alamat:</strong> {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}</small>
                                                @if($pengiriman->kota || $pengiriman->kecamatan)
                                                    <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                                @endif
                                            </div>
                                            @if($pengiriman->latitude && $pengiriman->longitude)
                                                <div class="mt-2">
                                                    <small class="text-primary">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <a href="https://maps.google.com/?q={{ $pengiriman->latitude }},{{ $pengiriman->longitude }}" target="_blank" class="text-decoration-none">
                                                            Buka Maps
                                                        </a>
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info">Dalam Pengiriman</span></td>
                                    <td>
                                        <form action="{{ route('kurir.deliveries.complete', $pengiriman->id_pengiriman) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i> Selesai
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2">Tidak ada ongoing deliveries</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Deliveries Today -->
    <div class="row" id="completed">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-check-circle text-success"></i>
                        Completed Deliveries (30 Hari Terakhir)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Pelanggan</th>
                                    <th>Lokasi Pickup</th>
                                    <th>Alamat Tujuan</th>
                                    <th>Waktu Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedOrders ?? [] as $pengiriman)
                                <tr>
                                    <td>
                                        <strong>{{ $pengiriman->kode_transaksi_full ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">Resi: {{ $pengiriman->no_resi ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div><strong>{{ $pengiriman->nama_pelanggan ?? 'N/A' }}</strong></div>
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
                                                <br><small class="text-muted">{{ $pengiriman->no_telp_penerima ?? '-' }}</small>
                                            </div>
                                            <div class="mt-2">
                                                <small><strong>Alamat:</strong> {{ $pengiriman->alamat_lengkap ?? $pengiriman->alamat_penerima }}</small>
                                                @if($pengiriman->kota || $pengiriman->kecamatan)
                                                    <br><small class="text-muted">{{ $pengiriman->kecamatan ?? '-' }}, {{ $pengiriman->kota ?? '-' }} {{ $pengiriman->kode_pos ?? '' }}</small>
                                                @endif
                                            </div>
                                            @if($pengiriman->latitude && $pengiriman->longitude)
                                                <div class="mt-2">
                                                    <small class="text-primary">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <a href="https://maps.google.com/?q={{ $pengiriman->latitude }},{{ $pengiriman->longitude }}" target="_blank" class="text-decoration-none">
                                                            Buka Maps
                                                        </a>
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($pengiriman->tgl_sampai)->format('d M Y H:i') }}</td>
                                    <td><span class="badge bg-success">Delivered</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2">Belum ada completed deliveries dalam 30 hari terakhir</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function confirmPickup(orderId) {
        Swal.fire({
            title: 'Konfirmasi Pickup',
            text: 'Apakah Anda yakin ingin mengambil order ini untuk dikirim?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Ambil!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form or AJAX request to update delivery status
                showLoading();
                // Simulate API call
                setTimeout(() => {
                    hideLoading();
                    Swal.fire('Berhasil!', 'Order berhasil diambil untuk pengiriman.', 'success')
                    .then(() => window.location.reload());
                }, 1000);
            }
        });
    }

    function confirmComplete(orderId) {
        Swal.fire({
            title: 'Konfirmasi Pengiriman Selesai',
            text: 'Apakah pengiriman untuk order ini sudah selesai?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form or AJAX request to complete delivery
                showLoading();
                // Simulate API call
                setTimeout(() => {
                    hideLoading();
                    Swal.fire('Berhasil!', 'Pengiriman berhasil diselesaikan.', 'success')
                    .then(() => window.location.reload());
                }, 1000);
            }
        });
    }
</script>
@endpush
