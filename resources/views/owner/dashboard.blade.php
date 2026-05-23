@extends('layouts.staff')

@section('title', 'Dashboard Owner - AyuMart')

@section('sidebar-menu')
    <a class="nav-link active" href="{{ route('owner.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Transaksi</div>

    <a class="nav-link" href="{{ route('owner.transactions.index') }}">
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
    <!-- Page Header -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis">
                <i class="bi bi-speedometer2 text-success"></i> Dashboard Owner
            </h1>
            <p class="text-muted mb-0">Selamat datang, <strong>{{ auth()->user()->name }}</strong>! Berikut adalah ringkasan bisnis Anda hari ini.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <span class="btn btn-light border-0 shadow-sm text-dark pointer-none">
                <i class="bi bi-calendar3 text-success"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
            <select class="form-select border-0 shadow-sm filter-year-select" id="yearSelect" style="width: auto;">
                <option value="2026" {{ $currentYear == 2026 ? 'selected' : '' }}>Tahun 2026</option>
                <option value="2025" {{ $currentYear == 2025 ? 'selected' : '' }}>Tahun 2025</option>
                <option value="2024" {{ $currentYear == 2024 ? 'selected' : '' }}>Tahun 2024</option>
            </select>
        </div>
    </div>

    <!-- Premium Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-premium stat-revenue h-100">
                <div class="stat-content">
                    <div class="stat-info">
                        <span class="label">Total Pendapatan</span>
                        <h3 class="value">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-success bg-opacity-25">
                        <i class="bi bi-wallet2 text-success"></i>
                    </div>
                </div>
                <div class="stat-footer bg-success-dark">
                    <span class="small"><i class="bi bi-arrow-up-circle-fill"></i> Total pendapatan terjual</span>
                </div>
            </div>
        </div>

        <!-- Transactions Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-premium stat-transactions h-100">
                <div class="stat-content">
                    <div class="stat-info">
                        <span class="label">Total Transaksi</span>
                        <h3 class="value">{{ number_format($totalTransactions ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-info bg-opacity-25">
                        <i class="bi bi-cart-check-fill text-info"></i>
                    </div>
                </div>
                <div class="stat-footer bg-info-dark">
                    <span class="small"><i class="bi bi-bar-chart-fill"></i> Semua transaksi sukses</span>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-premium stat-customers h-100">
                <div class="stat-content">
                    <div class="stat-info">
                        <span class="label">Total Pelanggan</span>
                        <h3 class="value">{{ number_format($totalCustomers ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-warning bg-opacity-25">
                        <i class="bi bi-people-fill text-warning"></i>
                    </div>
                </div>
                <div class="stat-footer bg-warning-dark">
                    <span class="small"><i class="bi bi-people"></i> Member terdaftar</span>
                </div>
            </div>
        </div>

        <!-- Products Sold Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-premium stat-products h-100">
                <div class="stat-content">
                    <div class="stat-info">
                        <span class="label">Produk Terjual</span>
                        <h3 class="value">{{ number_format($totalProducts ?? 0, 0, ',', '.') }} pcs</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-danger bg-opacity-25">
                        <i class="bi bi-box-seam-fill text-danger"></i>
                    </div>
                </div>
                <div class="stat-footer bg-danger-dark">
                    <span class="small"><i class="bi bi-tag-fill"></i> Volume barang terjual</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Sales Line Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-success-emphasis">
                        <i class="bi bi-graph-up text-success"></i> Grafik Tren Penjualan
                    </h5>
                    <span class="badge bg-light text-muted border px-2 py-1">Juta Rupiah (Rp)</span>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:280px; width:100%">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Selling Products Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-success-emphasis">
                        <i class="bi bi-pie-chart-fill text-success"></i> Produk Terlaris
                    </h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="chart-container mb-3" style="position: relative; height:200px; width:100%">
                        <canvas id="productChart"></canvas>
                    </div>
                    @if(empty($topProductLabels))
                        <p class="text-center text-muted small my-3">Belum ada data penjualan produk</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Transactions Table Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-success-emphasis">
                <i class="bi bi-receipt text-success"></i> Transaksi Terbaru
            </h5>
            <a href="{{ route('owner.transactions.index') }}" class="btn btn-sm btn-success text-white">
                Lihat Semua <i class="bi bi-arrow-right-short"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode</th>
                            <th>Pelanggan</th>
                            <th>Cabang</th>
                            <th>Total</th>
                            <th>Status Pembayaran</th>
                            <th>Pengiriman</th>
                            <th>Waktu</th>
                            <th class="pe-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTransactions as $transaction)
                            @php
                                $tgl = $transaction->tanggal_transaksi;
                                if (is_string($tgl)) {
                                    $tgl = \Carbon\Carbon::parse($tgl);
                                }
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-success-emphasis">{{ $transaction->kode_transaksi }}</td>
                                <td>
                                    @if($transaction->pelanggan)
                                        <span class="fw-semibold">{{ $transaction->pelanggan->nama_pelanggan }}</span>
                                    @else
                                        <span class="text-muted small"><em>-</em></span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->cabang)
                                        <span class="badge bg-light text-dark border"><i class="bi bi-shop text-success"></i> {{ $transaction->cabang->nama_cabang }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">Rp {{ number_format($transaction->getTotalAmount(), 0, ',', '.') }}</td>
                                <td>
                                    @if($transaction->status_pembayaran === 'belum_bayar')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Belum Bayar</span>
                                    @elseif($transaction->status_pembayaran === 'sudah_bayar')
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Sudah Bayar</span>
                                    @elseif($transaction->status_pembayaran === 'kadaluarsa')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Kadaluarsa / Batal</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($transaction->status_pembayaran) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->metode_pengiriman === 'ambil_sendiri')
                                        <span class="badge bg-info text-white"><i class="bi bi-bag"></i> Ambil Sendiri</span>
                                    @else
                                        <span class="badge bg-primary text-white"><i class="bi bi-truck"></i> Kurir</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $tgl ? $tgl->translatedFormat('d M Y, H:i') : '-' }}</small>
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="{{ route('owner.transactions.show', $transaction->id_transaksi) }}" class="btn btn-xs btn-outline-success">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    Belum ada data transaksi masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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

    /* Stat Cards Premium */
    .stat-card-premium {
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.04);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.25rem;
    }

    .stat-info .label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #8c8c8c;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 0.5rem;
    }

    .stat-info .value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon-wrapper i {
        font-size: 1.5rem;
    }

    .stat-footer {
        padding: 0.5rem 1.5rem;
        margin: 0 -1.5rem -1.5rem -1.5rem;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        color: #ffffff;
        font-weight: 500;
    }

    /* Gradients and specific themes */
    .stat-revenue .stat-footer { background: #047857; }
    .stat-transactions .stat-footer { background: #0369a1; }
    .stat-customers .stat-footer { background: #b45309; }
    .stat-products .stat-footer { background: #be123c; }

    /* Button adjustments */
    .btn-xs {
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    .filter-year-select {
        border-radius: 8px;
        padding-left: 1rem;
        padding-right: 2.25rem;
        font-weight: 600;
        color: #015b1e;
        background-color: #f0fdf4;
    }

    .filter-year-select:focus {
        box-shadow: 0 0 0 3px rgba(1, 91, 30, 0.15);
        border-color: #015b1e;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sales Chart with gorgeous gradient
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const monthlySalesData = @json($salesData ?? array_fill(0, 12, 0));
        
        // Create custom gradient
        const primaryColor = '#10b981'; // AyuMart Emerald Green
        const gradientFill = salesCtx.createLinearGradient(0, 0, 0, 250);
        gradientFill.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
        gradientFill.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Pendapatan',
                    data: monthlySalesData,
                    borderColor: primaryColor,
                    borderWidth: 3,
                    backgroundColor: gradientFill,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: primaryColor,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBorderWidth: 3,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { size: 13, weight: 'bold', family: 'Inter' },
                        bodyFont: { size: 12, family: 'Inter' },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return ' Rp ' + context.parsed.y.toFixed(2) + ' Juta';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: 'Inter', size: 11, weight: '500' }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            callback: function(value) {
                                return 'Rp ' + value + 'jt';
                            }
                        }
                    }
                }
            }
        });

        // Year select change redirection
        document.getElementById('yearSelect').addEventListener('change', function() {
            window.location.href = '{{ route('owner.dashboard') }}?year=' + this.value;
        });

        // Product Chart with custom color palette
        const productCtx = document.getElementById('productChart').getContext('2d');
        const productLabels = @json($topProductLabels ?? ['Produk A', 'Produk B', 'Produk C']);
        const productData = @json($topProductData ?? [30, 25, 20]);

        new Chart(productCtx, {
            type: 'doughnut',
            data: {
                labels: productLabels,
                datasets: [{
                    data: productData,
                    backgroundColor: [
                        '#10b981', // Emerald
                        '#3b82f6', // Blue
                        '#f59e0b', // Amber
                        '#ef4444', // Red
                        '#8b5cf6'  // Purple
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            font: { family: 'Inter', size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endpush
