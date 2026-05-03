@extends('layouts.staff')

@section('title', 'Laporan Penjualan')

@section('sidebar-menu')
    <a class="nav-link" href="{{ route('owner.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
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
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-file-earmark-bar-graph"></i>
            Laporan Penjualan
        </h1>
        <p class="text-muted mb-0">Download laporan penjualan berdasarkan periode yang Anda pilih</p>
    </div>

    <!-- Report Form Cards -->
    <div class="row justify-content-center">
        <!-- Laporan Semua Cabang -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i>
                        Laporan Semua Cabang
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('owner.laporan.download') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="bulan_all" class="form-label fw-bold">
                                <i class="bi bi-calendar"></i> Bulan
                            </label>
                            <select name="bulan" id="bulan_all" class="form-select" required>
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

                        <div class="mb-3">
                            <label for="tahun_all" class="form-label fw-bold">
                                <i class="bi bi-calendar-event"></i> Tahun
                            </label>
                            <select name="tahun" id="tahun_all" class="form-select" required>
                                <option value="">-- Pilih Tahun --</option>
                                @for($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('tahun')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i>
                            <small>Laporan gabungan dari semua cabang</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-download"></i>
                                Download Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Laporan Per Cabang -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-geo-alt"></i>
                        Laporan Per Cabang
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('owner.laporan.download-cabang') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="id_cabang" class="form-label fw-bold">
                                <i class="bi bi-building"></i> Cabang
                            </label>
                            <select name="id_cabang" id="id_cabang" class="form-select" required>
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

                        <div class="mb-3">
                            <label for="bulan_cabang" class="form-label fw-bold">
                                <i class="bi bi-calendar"></i> Bulan
                            </label>
                            <select name="bulan" id="bulan_cabang" class="form-select" required>
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

                        <div class="mb-3">
                            <label for="tahun_cabang" class="form-label fw-bold">
                                <i class="bi bi-calendar-event"></i> Tahun
                            </label>
                            <select name="tahun" id="tahun_cabang" class="form-select" required>
                                <option value="">-- Pilih Tahun --</option>
                                @for($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="alert alert-success mb-3">
                            <i class="bi bi-info-circle"></i>
                            <small>Laporan khusus untuk cabang yang dipilih</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-download"></i>
                                Download Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row justify-content-center mt-4">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <h6 class="alert-heading">
                    <i class="bi bi-info-circle"></i> Informasi Laporan
                </h6>
                <hr>
                <ul class="mb-0">
                    <li><strong>Laporan Semua Cabang:</strong> Menampilkan data transaksi gabungan dari seluruh cabang</li>
                    <li><strong>Laporan Per Cabang:</strong> Menampilkan data transaksi spesifik untuk satu cabang tertentu</li>
                    <li>Laporan hanya mencakup transaksi dengan status pembayaran: <span class="badge bg-success">Sudah Bayar</span></li>
                    <li>Format file: PDF (siap cetak)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row justify-content-center mt-3">
        <div class="col-lg-12 text-center">
            <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        border-radius: 10px 10px 0 0 !important;
        border-bottom: none;
    }

    .form-select, .form-control {
        border-radius: 8px;
    }

    .btn {
        border-radius: 8px;
        padding: 12px 24px;
    }

    .alert {
        border-radius: 8px;
        border: none;
    }
</style>
@endpush
