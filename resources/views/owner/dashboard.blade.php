@extends('layouts.staff')

@section('title', 'Dashboard Owner')

@section('sidebar-menu')
    <a class="nav-link active" href="{{ route('owner.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
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
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-speedometer2"></i>
            Dashboard Owner
        </h1>
        <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}! Berikut adalah ringkasan bisnis Anda.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-primary-gradient">
                <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-success-gradient">
                <div class="stat-icon">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value">{{ $totalTransactions ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-info-gradient">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-label">Total Pelanggan</div>
                <div class="stat-value">{{ $totalCustomers ?? 0 }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-warning-gradient">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-label">Total Produk</div>
                <div class="stat-value">{{ $totalProducts ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up text-primary"></i>
                        Grafik Penjualan Bulanan
                    </h6>
                    <select class="form-select form-select-sm" id="yearSelect" style="width: auto;">
                        <option value="2026" {{ $currentYear == 2026 ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ $currentYear == 2025 ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ $currentYear == 2024 ? 'selected' : '' }}>2024</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-pie-chart text-primary"></i>
                        Produk Terlaris
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="productChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const monthlySalesData = @json($salesData ?? array_fill(0, 12, 0));
    const currentYear = @json($currentYear ?? date('Y'));

    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Penjualan (Juta Rupiah)',
                data: monthlySalesData,
                borderColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color'),
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toFixed(2) + ' juta';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value + 'jt';
                        }
                    }
                }
            }
        }
    });

    // Handle year selection change
    document.getElementById('yearSelect').addEventListener('change', function() {
        const selectedYear = this.value;
        window.location.href = '{{ route('owner.dashboard') }}?year=' + selectedYear;
    });

    // Product Chart
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
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
