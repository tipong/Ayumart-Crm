@extends('layouts.staff')

@section('title', 'Kelola Pengiriman')

@section('sidebar-menu')
    <a class="nav-link" href="{{ route('kurir.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Pengiriman</div>

    <a class="nav-link active" href="{{ route('kurir.shipping.index') }}">
        <i class="bi bi-gear"></i>
        <span>Kelola Pengiriman</span>
    </a>

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
            Kelola Pengiriman
        </h1>
        <p class="text-muted mb-0">Kelola dan tugaskan kurir untuk setiap pengiriman yang siap dikirim.</p>
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
            <div class="stat-card bg-danger-gradient">
                <div class="stat-icon">
                    <i class="bi bi-person-x"></i>
                </div>
                <div class="stat-label">Belum Assign Kurir</div>
                <div class="stat-value">{{ $statusCounts['unassigned'] ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-warning-gradient">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-label">Sudah Assign</div>
                <div class="stat-value">{{ $statusCounts['assigned'] ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-info-gradient">
                <div class="stat-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stat-label">Dalam Pengiriman</div>
                <div class="stat-value">{{ $statusCounts['in_transit'] ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-success-gradient">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-label">Terkirim</div>
                <div class="stat-value">{{ $statusCounts['delivered'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Shipments List -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 py-3 ps-4 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-list-ul me-1"></i>
                Daftar Pengiriman
            </h6>
            <div class="d-flex gap-2">
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.75rem;">
                    Total: {{ $shipments->total() }}
                </span>
                <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.75rem;">
                    Hal: {{ $shipments->currentPage() }} / {{ $shipments->lastPage() }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Desktop View Table (d-none d-md-block) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No Resi / Order</th>
                            <th>Pelanggan</th>
                            <th>Penerima & Alamat</th>
                            <th>Status</th>
                            <th>Kurir</th>
                            <th class="pe-4 text-end" style="width: 250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark d-block">Resi: {{ $shipment->no_resi }}</strong>
                                <small class="text-muted">Order: #{{ $shipment->kode_transaksi ?? $shipment->kode_transaksi_full ?? '-' }}</small>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $shipment->nama_pelanggan ?? 'N/A' }}</strong>
                                @if($shipment->email_pelanggan)
                                    <small class="text-muted d-block">{{ $shipment->email_pelanggan }}</small>
                                @endif
                                @if($shipment->no_tlp_pelanggan)
                                    <small class="text-muted"><i class="bi bi-phone"></i> <a href="tel:{{ $shipment->no_tlp_pelanggan }}" class="text-decoration-none text-muted">{{ $shipment->no_tlp_pelanggan }}</a></small>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $shipment->nama_penerima ?? 'N/A' }}</strong>
                                    @if($shipment->no_tlp_penerima)
                                        <small class="text-muted ms-1"><i class="bi bi-telephone"></i> <a href="tel:{{ $shipment->no_tlp_penerima }}" class="text-decoration-none text-muted">{{ $shipment->no_tlp_penerima }}</a></small>
                                    @endif
                                </div>
                                <div class="mt-1 small text-muted text-wrap" style="max-width: 300px;">
                                    <strong>Alamat:</strong> {{ $shipment->alamat_lengkap ?? $shipment->alamat_penerima }}
                                    @if($shipment->kecamatan || $shipment->kota)
                                        <br><span class="text-secondary">{{ $shipment->kecamatan ?? '-' }}, {{ $shipment->kota ?? '-' }} {{ $shipment->kode_pos ?? '' }}</span>
                                    @endif
                                </div>
                                @if($shipment->latitude && $shipment->longitude)
                                    <div class="mt-1">
                                        <small class="text-primary">
                                            <i class="bi bi-geo-alt-fill"></i>
                                            <a href="https://maps.google.com/?q={{ $shipment->latitude }},{{ $shipment->longitude }}" target="_blank" class="text-decoration-none fw-bold">
                                                Buka Maps
                                            </a>
                                        </small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($shipment->status_pengiriman === 'pending')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">
                                        <i class="bi bi-hourglass-split"></i> Pending
                                    </span>
                                @elseif($shipment->status_pengiriman === 'dikemas')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10">
                                        <i class="bi bi-box-seam"></i> Dikemas
                                    </span>
                                @elseif($shipment->status_pengiriman === 'siap_diambil')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10">
                                        <i class="bi bi-clock"></i> Siap Diambil
                                    </span>
                                @elseif($shipment->status_pengiriman === 'dalam_pengiriman')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10">
                                        <i class="bi bi-truck"></i> Dalam Pengiriman
                                    </span>
                                @elseif($shipment->status_pengiriman === 'terkirim')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10">
                                        <i class="bi bi-check-circle"></i> Terkirim
                                    </span>
                                @elseif($shipment->status_pengiriman === 'selesai')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10">
                                        <i class="bi bi-check-circle-fill"></i> Selesai
                                    </span>
                                @elseif($shipment->status_pengiriman === 'gagal')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">
                                        <i class="bi bi-x-circle"></i> Gagal
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">{{ ucfirst(str_replace('_', ' ', $shipment->status_pengiriman ?? 'Unknown')) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($shipment->id_staff && $shipment->nama_staff)
                                    <div>
                                        <i class="bi bi-person-check-fill text-success me-1"></i>
                                        <strong>{{ $shipment->nama_staff }}</strong>
                                    </div>
                                    @if($shipment->email_kurir)
                                        <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $shipment->email_kurir }}</small>
                                    @endif
                                    @if($shipment->phone_kurir)
                                        <small class="text-muted"><i class="bi bi-phone"></i> <a href="tel:{{ $shipment->phone_kurir }}" class="text-decoration-none text-muted">{{ $shipment->phone_kurir }}</a></small>
                                    @endif
                                @else
                                    <span class="text-danger fw-semibold">
                                        <i class="bi bi-person-x-fill me-1"></i> Belum Assign
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                @if(!$shipment->id_staff)
                                    {{-- Belum assign kurir - tampilkan form assign --}}
                                    @if(in_array($shipment->status_pengiriman, ['pending', 'dikemas', 'siap_diambil']))
                                        <form action="{{ route('kurir.shipping.assign', $shipment->id_pengiriman) }}" method="POST" class="d-inline assign-form">
                                            @csrf
                                            <div class="input-group input-group-sm justify-content-end">
                                                <select name="id_staff" class="form-select form-select-sm" style="max-width: 130px;" required>
                                                    <option value="">Pilih Kurir</option>
                                                    @foreach($couriers as $courier)
                                                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-sm btn-primary btn-assign">
                                                    <i class="bi bi-person-plus text-white"></i>
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <span class="text-muted small">Status: {{ $shipment->status_pengiriman }}</span>
                                    @endif
                                @else
                                    {{-- Sudah assign - tampilkan tombol unassign jika belum dalam pengiriman --}}
                                    @if(in_array($shipment->status_pengiriman, ['pending', 'dikemas', 'siap_diambil']))
                                        <form action="{{ route('kurir.shipping.unassign', $shipment->id_pengiriman) }}" method="POST" class="d-inline unassign-form">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-unassign" data-courier="{{ $shipment->nama_staff }}">
                                                <i class="bi bi-person-dash"></i> Unassign
                                            </button>
                                        </form>
                                    @elseif(in_array($shipment->status_pengiriman, ['dalam_pengiriman', 'terkirim', 'selesai']))
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2 py-1 fw-bold">
                                            <i class="bi bi-lock-fill"></i> Terkunci
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50 text-warning"></i>
                                Tidak ada data pengiriman.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (d-md-none) -->
            <div class="d-md-none p-3 bg-light">
                @forelse($shipments as $shipment)
                <div class="card mb-3 border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <div>
                            @if($shipment->status_pengiriman === 'pending')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 small">Pending</span>
                            @elseif($shipment->status_pengiriman === 'dikemas')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 small">Dikemas</span>
                            @elseif($shipment->status_pengiriman === 'siap_diambil')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 small">Siap Diambil</span>
                            @elseif($shipment->status_pengiriman === 'dalam_pengiriman')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 small">In Transit</span>
                            @elseif($shipment->status_pengiriman === 'terkirim' || $shipment->status_pengiriman === 'selesai')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 small">Selesai</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 small">{{ ucfirst($shipment->status_pengiriman) }}</span>
                            @endif
                            <span class="ms-1 fw-extrabold text-dark text-uppercase" style="font-size: 0.85rem;">Resi: {{ $shipment->no_resi }}</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">#{{ $shipment->kode_transaksi ?? $shipment->kode_transaksi_full ?? '-' }}</small>
                    </div>
                    <div class="card-body p-3">
                        <!-- Customer Info -->
                        <div class="d-flex align-items-start mb-2">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-1.5 me-2" style="font-size: 0.85rem;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.6rem;">Pelanggan</span>
                                <strong class="text-dark">{{ $shipment->nama_pelanggan ?? 'N/A' }}</strong>
                                @if($shipment->no_tlp_pelanggan)
                                <br><small class="text-muted"><i class="bi bi-phone"></i> <a href="tel:{{ $shipment->no_tlp_pelanggan }}" class="text-decoration-none text-muted">{{ $shipment->no_tlp_pelanggan }}</a></small>
                                @endif
                            </div>
                        </div>

                        <!-- Recipient Info -->
                        <hr class="my-2 opacity-50">
                        <div class="d-flex align-items-start mb-2">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-1.5 me-2" style="font-size: 0.85rem;">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div class="w-100">
                                <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.6rem;">Penerima & Alamat</span>
                                <strong class="text-dark">{{ $shipment->nama_penerima ?? 'N/A' }}</strong>
                                <p class="text-muted mb-1 small mt-0.5">
                                    {{ $shipment->alamat_lengkap ?? $shipment->alamat_penerima }}
                                    @if($shipment->kecamatan || $shipment->kota)
                                    <br><small class="text-muted">{{ $shipment->kecamatan ?? '-' }}, {{ $shipment->kota ?? '-' }} {{ $shipment->kode_pos ?? '' }}</small>
                                    @endif
                                </p>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @if($shipment->no_tlp_penerima)
                                    <a href="tel:{{ $shipment->no_tlp_penerima }}" class="btn btn-sm btn-outline-success py-0.5 px-2" style="font-size: 0.7rem;">
                                        <i class="bi bi-telephone-fill me-1"></i> Hubungi Penerima
                                    </a>
                                    @endif
                                    @if($shipment->latitude && $shipment->longitude)
                                    <a href="https://maps.google.com/?q={{ $shipment->latitude }},{{ $shipment->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary py-0.5 px-2" style="font-size: 0.7rem;">
                                        <i class="bi bi-map-fill me-1"></i> Rute Maps
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Courier Info -->
                        <hr class="my-2 opacity-50">
                        <div class="d-flex align-items-start">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-1.5 me-2" style="font-size: 0.85rem;">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small text-uppercase fw-bold" style="font-size: 0.6rem;">Kurir</span>
                                @if($shipment->id_staff && $shipment->nama_staff)
                                    <strong class="text-dark">{{ $shipment->nama_staff }}</strong>
                                    @if($shipment->phone_kurir)
                                    <br><small class="text-muted"><i class="bi bi-phone"></i> <a href="tel:{{ $shipment->phone_kurir }}" class="text-decoration-none text-muted">{{ $shipment->phone_kurir }}</a></small>
                                    @endif
                                @else
                                    <span class="text-danger fw-bold small">Belum Ditugaskan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 p-3 pt-0">
                        @if(!$shipment->id_staff)
                            @if(in_array($shipment->status_pengiriman, ['pending', 'dikemas', 'siap_diambil']))
                                <form action="{{ route('kurir.shipping.assign', $shipment->id_pengiriman) }}" method="POST" class="assign-form">
                                    @csrf
                                    <div class="input-group">
                                        <select name="id_staff" class="form-select" required>
                                            <option value="">Pilih Kurir</option>
                                            @foreach($couriers as $courier)
                                                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-primary btn-assign px-3">
                                            <i class="bi bi-person-plus-fill me-1"></i> Assign
                                        </button>
                                    </div>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100 py-2 fw-bold rounded-3" disabled>
                                    Terkunci (Status: {{ $shipment->status_pengiriman }})
                                </button>
                            @endif
                        @else
                            @if(in_array($shipment->status_pengiriman, ['pending', 'dikemas', 'siap_diambil']))
                                <form action="{{ route('kurir.shipping.unassign', $shipment->id_pengiriman) }}" method="POST" class="unassign-form">
                                    @csrf
                                    <button type="button" class="btn btn-outline-danger w-100 py-2 btn-unassign fw-bold rounded-3" data-courier="{{ $shipment->nama_staff }}">
                                        <i class="bi bi-person-dash-fill me-1"></i> Unassign Kurir
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100 py-2 fw-bold rounded-3" disabled>
                                    <i class="bi bi-lock-fill me-1"></i> Pengiriman Sedang Berjalan
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 card border-0">
                    <i class="bi bi-inbox fs-2 opacity-50 text-warning"></i>
                    <p class="mb-0 mt-2">Tidak ada data pengiriman.</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($shipments->hasPages())
            <div class="d-flex justify-content-center p-4">
                {{ $shipments->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Available Couriers Info -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h6 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-people-fill me-1"></i>
                Kurir Aktif & Tersedia
            </h6>
        </div>
        <div class="card-body px-4 pb-4 pt-2">
            <div class="row">
                @forelse($couriers as $courier)
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="border border-light-subtle rounded-3 p-3 h-100 shadow-sm hover-lift bg-white">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-person" style="font-size: 1.25rem;"></i>
                            </div>
                            <div class="ms-3 w-100 overflow-hidden">
                                <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $courier->name }}</h6>
                                <small class="text-muted d-block text-truncate" style="font-size: 0.8rem;">{{ $courier->email }}</small>
                                @if($courier->phone)
                                <div class="mt-1"><small class="text-success"><i class="bi bi-telephone-fill"></i> <a href="tel:{{ $courier->phone }}" class="text-decoration-none text-success fw-semibold" style="font-size: 0.8rem;">{{ $courier->phone }}</a></small></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning mb-0 border-0 bg-warning bg-opacity-10 text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Tidak ada kurir yang aktif atau tersedia saat ini.
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .hover-lift {
        transition: all 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Intercept assign button click
        $(document).on('click', '.btn-assign', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const selectVal = form.find('select').val();
            const selectText = form.find('select option:selected').text();
            
            if (!selectVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kurir',
                    text: 'Silakan pilih kurir terlebih dahulu sebelum melakukan penugasan.',
                    confirmButtonColor: '#10b981'
                });
                return;
            }
            
            Swal.fire({
                title: 'Konfirmasi Penugasan',
                text: `Apakah Anda yakin ingin menugaskan kurir ${selectText} untuk pengiriman ini?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Tugaskan!',
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

        // Intercept unassign button click
        $(document).on('click', '.btn-unassign', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const courierName = $(this).data('courier');
            
            Swal.fire({
                title: 'Konfirmasi Unassign',
                text: `Apakah Anda yakin ingin membatalkan penugasan kurir ${courierName} dari pengiriman ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger px-4 py-2 me-2',
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
