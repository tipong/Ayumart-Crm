@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }

    .product-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #4e73df;
        transition: all 0.3s ease;
    }

    .product-item:hover {
        background: #e7f0ff;
        box-shadow: 0 2px 8px rgba(78, 115, 223, 0.15);
        transform: translateX(5px);
    }

    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-weight: bold;
        color: #333;
        margin-bottom: 3px;
        font-size: 0.95rem;
    }

    .product-sold {
        font-size: 0.85rem;
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .product-item .text-right {
        margin-left: 10px;
        flex-shrink: 0;
    }

    .stat-box {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #4e73df;
    }

    .stat-label {
        color: #666;
        margin-top: 5px;
    }

    /* Responsive layout */
    @media (max-width: 991px) {
        .col-lg-6 {
            margin-bottom: 20px;
        }
    }

    /* Scrollbar styling for product list */
    .card-body {
        scrollbar-width: thin;
        scrollbar-color: rgba(78, 115, 223, 0.3) transparent;
    }

    .card-body::-webkit-scrollbar {
        width: 6px;
    }

    .card-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .card-body::-webkit-scrollbar-thumb {
        background-color: rgba(78, 115, 223, 0.3);
        border-radius: 3px;
    }

    .card-body::-webkit-scrollbar-thumb:hover {
        background-color: rgba(78, 115, 223, 0.5);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt text-primary"></i> Dashboard Admin
        </h1>
        <div>
            <span class="text-muted">
                <i class="fas fa-calendar"></i> {{ now()->format('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Transaksi Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Staff
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStaff }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Membership
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMemberships }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crown fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section - Two Column Layout -->
    <div class="row mb-4">
        <!-- Top Products Chart - Left Side -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star"></i> Barang Paling Banyak Dibeli
                    </h6>
                </div>
                <div class="card-body" style="overflow-y: auto; max-height: 450px;">
                    @if($topProducts->count() > 0)
                        @foreach($topProducts as $index => $product)
                            <div class="product-item">
                                <div class="flex-shrink-0" style="width: 50px; height: 50px;">
                                    @if($product->foto_produk)
                                        <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 50, 50) }}"
                                             alt="{{ $product->nama_produk }}"
                                             class="product-image"
                                             onerror="this.src='{{ asset('images/no-image.png') }}'">
                                    @else
                                        <div style="width: 50px; height: 50px; background: #e9ecef; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="product-name">
                                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                        {{ Str::limit($product->nama_produk, 30) }}
                                    </div>
                                    <div class="product-sold">
                                        <i class="fas fa-shopping-bag"></i>
                                        Terjual: <strong>{{ $product->total_sold }}</strong> unit
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div style="font-size: 1.5rem; font-weight: bold; color: #28a745;">
                                        {{ $product->total_sold }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info text-center mb-0">
                            <i class="fas fa-info-circle"></i> Belum ada data penjualan produk
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Member Growth Chart - Right Side -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Pertumbuhan Member (12 Bulan Terakhir)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px; margin-bottom: 20px;">
                        <canvas id="memberGrowthChart"></canvas>
                    </div>
                    <div id="chartMessage" class="alert alert-info text-center d-none">
                        <i class="fas fa-info-circle"></i> Belum ada data
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Transaksi Tahunan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line"></i> Grafik Transaksi Per Bulan
                    </h6>
                    <div class="d-flex align-items-center">
                        <label for="filterYear" class="mb-0 text-muted small fw-bold text-nowrap me-2">
                            <i class="fas fa-filter"></i> Tahun:
                        </label>
                        <select id="filterYear" class="form-select form-select-sm" style="width: auto;">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="transactionLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Member Growth Bar Chart ---
        const memberCtx = document.getElementById('memberGrowthChart');
        if (memberCtx) {
            const memberLabels = @json($memberChartLabels ?? []);
            const memberData = @json($memberChartValues ?? []);
            const primaryColor = '#4e73df';

            new Chart(memberCtx, {
                type: 'bar',
                data: {
                    labels: memberLabels,
                    datasets: [{
                        label: 'Member Baru',
                        data: memberData,
                        backgroundColor: 'rgba(78, 115, 223, 0.85)',
                        borderColor: primaryColor,
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: { size: 12, weight: 'bold' },
                                color: '#333'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    return 'Member Baru: ' + context.parsed.y + ' orang';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                callback: function(value) { return value + ' orang'; }
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // --- 2. Transaction Line Chart with Filter ---
        const txCtx = document.getElementById('transactionLineChart');
        if (txCtx) {
            const txLabels = @json($transactionChartLabels ?? []);
            const txValues = @json($transactionChartValues ?? []);
            let txRevenues = @json($transactionChartRevenues ?? []);

            const transactionChart = new Chart(txCtx, {
                type: 'line',
                data: {
                    labels: txLabels,
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: txValues,
                        borderColor: '#2e7d32', // Forest Green accent
                        backgroundColor: 'rgba(46, 125, 50, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 5,
                        pointBackgroundColor: '#2e7d32',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#1b5e20'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: { size: 12, weight: 'bold' },
                                color: '#333'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const count = context.parsed.y;
                                    const revenue = txRevenues[index] ?? 0;
                                    const formattedRevenue = 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(revenue);
                                    return [
                                        'Transaksi: ' + count + ' kali',
                                        'Total Bersih: ' + formattedRevenue
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                callback: function(value) { return value + ' tx'; }
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        }
                    }
                }
            });

            // Filter year handler
            const filterYear = document.getElementById('filterYear');
            if (filterYear) {
                filterYear.addEventListener('change', function() {
                    const year = this.value;
                    filterYear.disabled = true;

                    fetch(`{{ route('admin.dashboard.transaction-chart-data') }}?year=${year}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(res => {
                        filterYear.disabled = false;
                        transactionChart.data.datasets[0].data = res.data;
                        txRevenues = res.revenues;
                        transactionChart.update();
                    })
                    .catch(err => {
                        filterYear.disabled = false;
                        console.error('Error loading transaction chart data:', err);
                        alert('Gagal memuat data transaksi untuk tahun ' + year);
                    });
                });
            }
        }
    });
</script>
@endpush
