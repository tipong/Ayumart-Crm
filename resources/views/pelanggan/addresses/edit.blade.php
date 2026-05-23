@extends('layouts.pelanggan')

@section('title', 'Edit Alamat')

@push('styles')
    {{-- Load Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
@endpush

@section('content')
<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h1>Edit Alamat</h1>
                    <p>Ubah detail alamat pengiriman Anda</p>
                </div>
            </div>
            <a href="{{ route('pelanggan.profile') }}" class="btn" style="background:rgba(255,255,255,0.2);color:#fff;border-radius:100px;font-weight:700;font-size:14px;padding:8px 20px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="container py-3 pb-5">
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pelanggan.profile') }}">Profil</a></li>
            <li class="breadcrumb-item active">Edit Alamat</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ay-card">
                <div class="ay-card-header">
                    <i class="bi bi-pencil"></i> Edit Alamat
                </div>
                <div class="ay-card-body">
                    <form action="{{ route('pelanggan.addresses.update', $address->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Label Alamat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('label') is-invalid @enderror"
                                       name="label" value="{{ old('label', $address->label) }}" placeholder="Rumah, Kantor, dll" required>
                                @error('label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_penerima') is-invalid @enderror"
                                       name="nama_penerima" value="{{ old('nama_penerima', $address->nama_penerima) }}" required>
                                @error('nama_penerima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('no_telp_penerima') is-invalid @enderror"
                                       name="no_telp_penerima" value="{{ old('no_telp_penerima', $address->no_telp_penerima) }}" required>
                                @error('no_telp_penerima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kota') is-invalid @enderror"
                                       name="kota" value="{{ old('kota', $address->kota) }}" required>
                                @error('kota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" class="form-control @error('kecamatan') is-invalid @enderror"
                                       name="kecamatan" value="{{ old('kecamatan', $address->kecamatan) }}">
                                @error('kecamatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control @error('kode_pos') is-invalid @enderror"
                                       name="kode_pos" value="{{ old('kode_pos', $address->kode_pos) }}">
                                @error('kode_pos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat_lengkap') is-invalid @enderror"
                                          name="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap', $address->alamat_lengkap) }}</textarea>
                                @error('alamat_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label d-block fw-bold">Lokasi Peta (GPS/Maps) <span class="text-danger">*</span></label>
                                
                                <!-- Hidden inputs for coordinates -->
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address->latitude) }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address->longitude) }}">

                                <!-- Visual status card -->
                                <div id="locationStatusCard" class="card border-1 mb-2" style="border-radius: 10px; border: 1px dashed #ffc107; background-color: rgba(255, 193, 7, 0.05);">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="status-icon bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="bi bi-geo-alt fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold text-warning-emphasis" style="font-size: 0.9rem;">Titik Koordinat Belum Disematkan</h6>
                                            <p class="mb-0 text-muted small">Silakan pilih lokasi dari peta atau gunakan GPS untuk menghitung ongkir kurir.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group d-flex gap-2" role="group" style="max-width: 450px;">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-3 py-2" onclick="getGPSLocation()">
                                        <i class="bi bi-geo-alt-fill"></i> Perbarui GPS
                                    </button>
                                    <button type="button" class="btn btn-success text-white btn-sm rounded-3 py-2" onclick="pickLocationFromMapsEditPage()">
                                        <i class="bi bi-map-fill"></i> Pilih Titik di Maps
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-2" id="gpsStatus"></small>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1"
                                           {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Jadikan alamat utama
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('pelanggan.profile') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Alamat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateLocationStatusCard() {
    const lat = document.getElementById('latitude').value;
    const lng = document.getElementById('longitude').value;
    const statusCard = document.getElementById('locationStatusCard');
    
    if (statusCard) {
        if (lat && lng && parseFloat(lat) !== 0 && parseFloat(lng) !== 0) {
            statusCard.style.border = '1px solid #198754';
            statusCard.style.backgroundColor = 'rgba(25, 135, 84, 0.05)';
            statusCard.querySelector('.status-icon').className = 'status-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center';
            statusCard.querySelector('.status-icon i').className = 'bi bi-check-circle-fill fs-5';
            statusCard.querySelector('h6').className = 'mb-0 fw-bold text-success';
            statusCard.querySelector('h6').textContent = 'Lokasi Tersemat!';
            statusCard.querySelector('p').textContent = 'Titik peta telah berhasil disimpan (' + parseFloat(lat).toFixed(5) + ', ' + parseFloat(lng).toFixed(5) + ').';
        } else {
            statusCard.style.border = '1px dashed #ffc107';
            statusCard.style.backgroundColor = 'rgba(255, 193, 7, 0.05)';
            statusCard.querySelector('.status-icon').className = 'status-icon bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center';
            statusCard.querySelector('.status-icon i').className = 'bi bi-geo-alt fs-5';
            statusCard.querySelector('h6').className = 'mb-0 fw-bold text-warning-emphasis';
            statusCard.querySelector('h6').textContent = 'Titik Koordinat Belum Disematkan';
            statusCard.querySelector('p').textContent = 'Silakan pilih lokasi dari peta atau gunakan GPS untuk menghitung ongkir kurir.';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    if (latInput && lngInput) {
        latInput.addEventListener('change', updateLocationStatusCard);
        lngInput.addEventListener('change', updateLocationStatusCard);
        updateLocationStatusCard(); // Initial check
    }
});

function getGPSLocation() {
    const gpsStatus = document.getElementById('gpsStatus');

    if (navigator.geolocation) {
        gpsStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise"></i> Mengambil lokasi GPS...</span>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latEl = document.getElementById('latitude');
                const lngEl = document.getElementById('longitude');
                latEl.value = position.coords.latitude;
                lngEl.value = position.coords.longitude;
                latEl.dispatchEvent(new Event('change', { bubbles: true }));
                lngEl.dispatchEvent(new Event('change', { bubbles: true }));
                gpsStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Lokasi GPS berhasil diperbarui!</span>';
            },
            function(error) {
                gpsStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Gagal mengambil lokasi GPS: ' + error.message + '</span>';
                console.error('GPS Error:', error);
            }
        );
    } else {
        gpsStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Browser tidak mendukung fitur GPS</span>';
    }
}

/**
 * Membuka Modal Map Picker di halaman Edit Alamat
 */
function pickLocationFromMapsEditPage() {
    if (typeof initMapPicker === 'function') {
        initMapPicker('latitude', 'longitude', 'edit');
    } else {
        console.error('Map picker not loaded.');
        alert('Fitur peta belum siap. Silakan muat ulang halaman.');
    }
}
</script>

@push('scripts')
    {{-- Load Leaflet.js for Interactive Maps --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
         integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
         crossorigin=""></script>

    {{-- Load Custom Map Picker --}}
    <script src="{{ asset('js/map-picker.js') }}"></script>
@endpush
@endsection
