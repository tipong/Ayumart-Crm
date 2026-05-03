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
                                        <img src="{{ asset('storage/' . $product->foto_produk) }}"
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
                        <i class="fas fa-chart-line"></i> Pertumbuhan Member (12 Bulan Terakhir)
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

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list"></i> Transaksi Terbaru
                    </h6>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-success">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="8%">ID</th>
                                        <th width="20%">Pelanggan</th>
                                        <th width="18%">Total</th>
                                        <th width="15%">Status</th>
                                        <th width="18%">Tanggal</th>
                                        <th width="21%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td><strong>#{{ $order->id_transaksi }}</strong></td>
                                        <td>
                                            <i class="fas fa-user text-primary"></i>
                                            {{ $order->pelanggan->nama_pelanggan ?? 'N/A' }}
                                        </td>
                                        <td class="text-success font-weight-bold">
                                            Rp {{ number_format($order->total_harga - $order->total_diskon + $order->ongkir, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if($order->status_pembayaran === 'belum_bayar')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock"></i> Belum Bayar
                                                </span>
                                            @elseif($order->status_pembayaran === 'sudah_bayar')
                                                @if($order->status_pengiriman === 'dikemas')
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-box"></i> Dikemas
                                                    </span>
                                                @elseif($order->status_pengiriman === 'dikirim')
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-truck"></i> Dikirim
                                                    </span>
                                                @elseif($order->status_pengiriman === 'sampai')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i> Selesai
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Sudah Bayar
                                                    </span>
                                                @endif
                                            @elseif($order->status_pembayaran === 'kadaluarsa')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> Kadaluarsa
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-question"></i> {{ ucfirst($order->status_pembayaran) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar"></i>
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.transactions.show', $order->id_transaksi) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center mb-0">
                            <i class="fas fa-info-circle"></i> Belum ada transaksi
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    // Member Growth Chart with Admin UI Colors
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('memberGrowthChart');

        if (!ctx) {
            console.error('Canvas element not found');
            return;
        }

        const memberLabels = @json($memberChartLabels ?? []);
        const memberData = @json($memberChartValues ?? []);

        // Admin UI Primary Color
        const primaryColor = '#4e73df';
        const accentColor = '#224abe';
        const lightColor = 'rgba(78, 115, 223, 0.1)';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: memberLabels,
                datasets: [{
                    label: 'Member Baru',
                    data: memberData,
                    borderColor: primaryColor,
                    backgroundColor: lightColor,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: primaryColor,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
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
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: 15,
                            usePointStyle: true,
                            color: '#333'
                        }
                    },
                    tooltip: {
                        enabled: true,
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
                            font: { size: 12 },
                            callback: function(value) {
                                return value + ' orang';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: true
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Member Baru'
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 12 }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawOnChartArea: true
                        },
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
