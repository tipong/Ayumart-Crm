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
        background: #ffffff;
        border-radius: 10px;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    .product-item:hover {
        background: #f0fdf4;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
        transform: translateX(4px);
    }

    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 15px;
        flex-shrink: 0;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 3px;
        font-size: 0.95rem;
    }

    .product-sold {
        font-size: 0.85rem;
        color: #6b7280;
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
        background: #f9fafb;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .stat-number {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--primary-color);
    }

    .stat-label {
        color: #4b5563;
        margin-top: 5px;
        font-weight: 600;
    }

    /* Scrollbar styling for product list */
    .product-list-card {
        scrollbar-width: thin;
        scrollbar-color: rgba(16, 185, 129, 0.3) transparent;
    }

    .product-list-card::-webkit-scrollbar {
        width: 6px;
    }

    .product-list-card::-webkit-scrollbar-track {
        background: transparent;
    }

    .product-list-card::-webkit-scrollbar-thumb {
        background-color: rgba(16, 185, 129, 0.3);
        border-radius: 3px;
    }

    .product-list-card::-webkit-scrollbar-thumb:hover {
        background-color: rgba(16, 185, 129, 0.5);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <!-- Page Heading -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis">
                <i class="bi bi-grid-1x2-fill text-success"></i> Dashboard Administrator
            </h1>
            <p class="text-muted mb-0">Selamat datang kembali! Berikut data operasional sistem terintegrasi AyuMart.</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <span class="badge bg-white text-dark shadow-sm border px-3 py-2 fs-6 fw-semibold">
                <i class="bi bi-calendar-event text-success me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-teal text-uppercase mb-1" style="color: #0f766e;">
                                Total Transaksi
                            </div>
                            <div class="h3 mb-0 font-weight-extrabold text-dark">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-teal bg-opacity-10 p-3 rounded-circle text-teal" style="color: #0f766e; background-color: rgba(15, 118, 110, 0.1);">
                                <i class="bi bi-cart-check fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Transaksi Pending
                            </div>
                            <div class="h3 mb-0 font-weight-extrabold text-dark">{{ number_format($pendingOrders) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                                <i class="bi bi-clock-history fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Staff Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Staff
                            </div>
                            <div class="h3 mb-0 font-weight-extrabold text-dark">{{ number_format($totalStaff) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Membership Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Membership
                            </div>
                            <div class="h3 mb-0 font-weight-extrabold text-dark">{{ number_format($totalMemberships) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <i class="bi bi-award fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section - Two Column Layout -->
    <div class="row g-4 mb-4">
        <!-- Top Products - Left Side -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 bg-white border-0">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-star-fill text-warning me-1"></i> Produk Terlaris
                    </h5>
                </div>
                <div class="card-body product-list-card" style="overflow-y: auto; max-height: 450px;">
                    @if($topProducts->count() > 0)
                        @foreach($topProducts as $index => $product)
                            <div class="product-item">
                                <div class="flex-shrink-0">
                                    @if($product->foto_produk)
                                        <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 50, 50) }}"
                                             alt="{{ $product->nama_produk }}"
                                             class="product-image"
                                             onerror="this.src='{{ asset('images/no-image.png') }}'">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="product-name">
                                        <span class="badge text-dark bg-warning bg-opacity-15 text-success me-2">{{ $index + 1 }}</span>
                                        {{ Str::limit($product->nama_produk, 35) }}
                                    </div>
                                    <div class="product-sold">
                                        <i class="bi bi-bag-check-fill text-success"></i>
                                        Total Terjual: <strong>{{ $product->total_sold }}</strong> unit
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="h4 mb-0 fw-bold text-success">
                                        {{ $product->total_sold }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info text-center mb-0 border-0 bg-info bg-opacity-10 text-info-emphasis rounded-3">
                            <i class="bi bi-info-circle-fill me-1"></i> Belum ada data penjualan produk
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Member Growth Chart - Right Side -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 bg-white border-0">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-graph-up-arrow text-success me-1"></i> Pertumbuhan Member
                    </h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="memberGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Transaksi Tahunan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold text-success-emphasis">
                        <i class="bi bi-bar-chart-line-fill text-success me-1"></i> Grafik Volume Transaksi Bulanan
                    </h5>
                    <div class="d-flex align-items-center">
                        <label for="filterYear" class="mb-0 text-muted small fw-bold text-nowrap me-2">
                            <i class="bi bi-funnel-fill text-success"></i> Pilih Tahun:
                        </label>
                        <select id="filterYear" class="form-select form-select-sm" style="width: auto;">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                    Tahun {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 320px;">
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
        const primaryColor = '#10b981'; // Emerald
        const accentColor = '#0f766e'; // Teal

        // --- 1. Member Growth Bar Chart ---
        const memberCtx = document.getElementById('memberGrowthChart');
        if (memberCtx) {
            const memberLabels = @json($memberChartLabels ?? []);
            const memberData = @json($memberChartValues ?? []);

            new Chart(memberCtx, {
                type: 'bar',
                data: {
                    labels: memberLabels,
                    datasets: [{
                        label: 'Member Baru',
                        data: memberData,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderColor: primaryColor,
                        borderWidth: 1,
                        borderRadius: 6
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
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold', family: 'Inter' },
                            bodyFont: { size: 12, family: 'Inter' },
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return ' Member Baru: ' + context.parsed.y + ' orang';
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
                                callback: function(value) { return value + ' orang'; },
                                font: { family: 'Inter' }
                            },
                            grid: { color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', weight: '500' } }
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
                        borderColor: accentColor,
                        backgroundColor: 'rgba(15, 118, 110, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: accentColor,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: accentColor
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
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold', family: 'Inter' },
                            bodyFont: { size: 12, family: 'Inter' },
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const count = context.parsed.y;
                                    const revenue = txRevenues[index] ?? 0;
                                    const formattedRevenue = 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(revenue);
                                    return [
                                        ' Transaksi: ' + count + ' kali',
                                        ' Pendapatan: ' + formattedRevenue
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
                                callback: function(value) { return value + ' tx'; },
                                font: { family: 'Inter' }
                            },
                            grid: { color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', weight: '500' } }
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
