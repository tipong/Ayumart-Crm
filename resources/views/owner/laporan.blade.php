@extends('layouts.staff')

@section('title', 'Laporan Penjualan - AyuMart')

@section('sidebar-menu')
    <a class="nav-link" href="{{ route('owner.dashboard') }}">
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

    <a class="nav-link active" href="{{ route('owner.laporan') }}">
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
    <div class="page-header mb-4">
        <h1 class="fw-extrabold text-success-emphasis">
            <i class="bi bi-file-earmark-bar-graph text-success"></i> Laporan Penjualan
        </h1>
        <p class="text-muted mb-0">Unduh laporan penjualan komprehensif dalam format PDF siap cetak berdasarkan periode waktu.</p>
    </div>

    <!-- Report Cards Grid -->
    <div class="row g-4 justify-content-center">
        <!-- Laporan Semua Cabang -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 report-card report-card-primary">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="mb-0 fw-bold text-success-emphasis">
                        <i class="bi bi-building-fill text-success"></i> Laporan Semua Cabang
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <form action="{{ route('owner.laporan.download') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="bulan_all" class="form-label fw-semibold text-muted">
                                <i class="bi bi-calendar3 text-success"></i> Pilih Bulan
                            </label>
                            <select name="bulan" id="bulan_all" class="form-select custom-input" required>
                                <option value="">-- Pilih Bulan --</option>
                                <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Januari</option>
                                <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Februari</option>
                                <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Maret</option>
                                <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                                <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Mei</option>
                                <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Juni</option>
                                <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Juli</option>
                                <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agustus</option>
                                <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                                <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                                <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Desember</option>
                            </select>
                            @error('bulan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tahun_all" class="form-label fw-semibold text-muted">
                                <i class="bi bi-calendar-event text-success"></i> Pilih Tahun
                            </label>
                            <select name="tahun" id="tahun_all" class="form-select custom-input" required>
                                <option value="">-- Pilih Tahun --</option>
                                @for($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                        Tahun {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('tahun')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info-emphasis rounded-3 mb-4 d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <span class="small">Laporan gabungan berisi rekapitulasi data penjualan dari seluruh cabang yang aktif.</span>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success text-white py-2 fw-bold shadow-sm">
                                <i class="bi bi-download me-1"></i> Download PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Laporan Per Cabang -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 report-card report-card-secondary">
                <div class="card-header bg-white border-0 py-3 ps-4">
                    <h5 class="mb-0 fw-bold text-success-emphasis">
                        <i class="bi bi-geo-alt-fill text-success"></i> Laporan Per Cabang
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <form action="{{ route('owner.laporan.download-cabang') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="id_cabang" class="form-label fw-semibold text-muted">
                                <i class="bi bi-shop text-success"></i> Pilih Cabang
                            </label>
                            <select name="id_cabang" id="id_cabang" class="form-select custom-input" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id_cabang }}">
                                        {{ $branch->nama_cabang }} ({{ $branch->kode_cabang }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_cabang')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="bulan_cabang" class="form-label fw-semibold text-muted">
                                    <i class="bi bi-calendar3 text-success"></i> Bulan
                                </label>
                                <select name="bulan" id="bulan_cabang" class="form-select custom-input" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Januari</option>
                                    <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Februari</option>
                                    <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Maret</option>
                                    <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                                    <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Mei</option>
                                    <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Juni</option>
                                    <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Juli</option>
                                    <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agustus</option>
                                    <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                                    <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Oktober</option>
                                    <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                                    <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Desember</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tahun_cabang" class="form-label fw-semibold text-muted">
                                    <i class="bi bi-calendar-event text-success"></i> Tahun
                                </label>
                                <select name="tahun" id="tahun_cabang" class="form-select custom-input" required>
                                    <option value="">-- Pilih Tahun --</option>
                                    @for($year = date('Y'); $year >= 2020; $year--)
                                        <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success-emphasis rounded-3 mb-4 d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <span class="small">Laporan terfilter khusus menampilkan ringkasan data transaksi pada satu cabang terpilih saja.</span>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-success py-2 fw-bold shadow-sm">
                                <i class="bi bi-download me-1"></i> Download PDF Cabang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light-green-gradient">
                <div class="card-body p-4 text-success-emphasis">
                    <h5 class="fw-bold d-flex align-items-center mb-3">
                        <i class="bi bi-shield-lock-fill me-2 text-success"></i> Kebijakan & Ketentuan Laporan
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <ul class="mb-0 ps-3">
                                <li class="mb-2">Laporan penjualan hanya merekapitulasi transaksi dengan status <span class="badge bg-success">Sudah Bayar</span>.</li>
                                <li class="mb-2">Transaksi yang dibatalkan atau kedaluwarsa tidak akan dimasukkan ke dalam perhitungan laba.</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0 ps-3">
                                <li class="mb-2">Gunakan format **Tahun Sekarang** untuk data yang sedang berjalan guna mencerminkan kondisi real-time cabang.</li>
                                <li>Dokumen PDF yang dihasilkan telah disesuaikan dengan format cetak kertas ukuran **A4 (Potrait)**.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling Laporan Penjualan */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .main-content {
        font-family: 'Inter', sans-serif !important;
    }

    .report-card {
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 4px solid transparent !important;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
    }

    .report-card-primary {
        border-top-color: #10b981 !important;
    }

    .report-card-secondary {
        border-top-color: #059669 !important;
    }

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

    .bg-light-green-gradient {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid rgba(16, 185, 129, 0.15);
    }
</style>
@endpush
