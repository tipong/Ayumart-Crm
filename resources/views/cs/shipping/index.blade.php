@extends('layouts.staff')

@section('title', 'Kelola Pengiriman')

@section('sidebar-menu')
    <a class="nav-link" href="{{ route('cs.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Layanan</div>

    <a class="nav-link" href="{{ route('cs.tickets.index') }}">
        <i class="bi bi-headset"></i>
        <span>Tickets</span>
    </a>

    <a class="nav-link" href="{{ route('cs.newsletters.index') }}">
        <i class="bi bi-envelope"></i>
        <span>Newsletter</span>
    </a>

    <a class="nav-link active" href="{{ route('cs.shipping.index') }}">
        <i class="bi bi-truck"></i>
        <span>Pengiriman</span>
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
        <p class="text-muted mb-0">Assign kurir untuk setiap pengiriman yang siap dikirim</p>
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
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-danger-gradient">
                <div class="stat-icon">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <div class="stat-label">Belum Assign Kurir</div>
                <div class="stat-value">{{ $statusCounts['unassigned'] ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-warning-gradient">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-label">Sudah Assign</div>
                <div class="stat-value">{{ $statusCounts['assigned'] ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-info-gradient">
                <div class="stat-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stat-label">Dalam Pengiriman</div>
                <div class="stat-value">{{ $statusCounts['in_transit'] ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-list-ul"></i>
                Daftar Pengiriman
            </h6>
            <div>
                <span class="badge bg-secondary">Total: {{ $shipments->total() }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="shipmentsTable">
                    <thead>
                        <tr>
                            <th>No Resi</th>
                            <th>Kode Transaksi</th>
                            <th>Pelanggan</th>
                            <th>Penerima</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Kurir</th>
                            <th width="200px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                        <tr>
                            <td>
                                <strong>{{ $shipment->no_resi }}</strong>
                            </td>
                            <td>{{ $shipment->kode_transaksi }}</td>
                            <td>
                                <div><strong>{{ $shipment->nama_pelanggan }}</strong></div>
                                <small class="text-muted">{{ $shipment->email_pelanggan }}</small>
                            </td>
                            <td>
                                <div>{{ $shipment->nama_penerima }}</div>
                                <small class="text-muted">{{ $shipment->no_tlp_penerima }}</small>
                            </td>
                            <td>
                                <small>{{ Str::limit($shipment->alamat_penerima ?? '-', 40) }}</small>
                            </td>
                            <td>
                                @if($shipment->status_pengiriman === 'dikemas')
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-box"></i> Dikemas
                                    </span>
                                @elseif($shipment->status_pengiriman === 'siap_diambil')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock"></i> Siap Diambil
                                    </span>
                                @elseif($shipment->status_pengiriman === 'dalam_pengiriman')
                                    <span class="badge bg-info">
                                        <i class="bi bi-truck"></i> Dalam Pengiriman
                                    </span>
                                @elseif($shipment->status_pengiriman === 'terkirim')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Terkirim
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $shipment->status_pengiriman)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($shipment->id_kurir)
                                    <div>
                                        <i class="bi bi-person-check text-success"></i>
                                        <strong>{{ $shipment->nama_kurir }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $shipment->email_kurir }}</small>
                                @else
                                    <span class="text-danger">
                                        <i class="bi bi-person-x"></i> Belum Assign
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(!$shipment->id_kurir)
                                    {{-- Belum assign kurir - tampilkan dropdown assign --}}
                                    @if(in_array($shipment->status_pengiriman, ['dikemas', 'siap_diambil']))
                                        <form action="{{ route('cs.shipping.assign', $shipment->id_pengiriman) }}" method="POST" class="d-inline">
                                            @csrf
                                            <div class="input-group input-group-sm">
                                                <select name="id_kurir" class="form-select form-select-sm" required>
                                                    <option value="">Pilih Kurir</option>
                                                    @foreach($couriers as $courier)
                                                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-check"></i> Assign
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @else
                                    {{-- Sudah assign - tampilkan tombol unassign jika belum dalam pengiriman --}}
                                    @if(!in_array($shipment->status_pengiriman, ['dalam_pengiriman', 'terkirim']))
                                        <form action="{{ route('cs.shipping.unassign', $shipment->id_pengiriman) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin unassign kurir {{ $shipment->nama_kurir }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle"></i> Unassign
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-lock"></i> Locked
                                        </span>
                                    @endif
                                @endif

                                @if($shipment->latitude && $shipment->longitude)
                                <a href="https://maps.google.com/?q={{ $shipment->latitude }},{{ $shipment->longitude }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary ms-1"
                                   title="Lihat di Maps">
                                    <i class="bi bi-geo-alt"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">Tidak ada data pengiriman</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($shipments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $shipments->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Available Couriers Info -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-people"></i>
                Kurir Tersedia
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($couriers as $courier)
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-person" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">{{ $courier->name }}</h6>
                                <small class="text-muted">{{ $courier->email }}</small>
                                @if($courier->phone)
                                <div><small class="text-muted"><i class="bi bi-phone"></i> {{ $courier->phone }}</small></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Tidak ada kurir yang tersedia saat ini.
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush
