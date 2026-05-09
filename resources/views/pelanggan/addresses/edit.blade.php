@extends('layouts.pelanggan')

@section('title', 'Edit Alamat')

@push('styles')
    {{-- Load Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Alamat</h5>
                </div>
                <div class="card-body">
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
                                <label class="form-label">Lokasi (GPS/Maps)</label>
                                <div class="btn-group mb-2 d-flex" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="getGPSLocation()">
                                        <i class="bi bi-geo-alt-fill"></i> Perbarui Lokasi GPS
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="pickLocationFromMapsEditPage()">
                                        <i class="bi bi-map"></i> Pilih dari Maps
                                    </button>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Latitude</label>
                                        <input type="text" class="form-control" name="latitude" id="latitude" value="{{ old('latitude', $address->latitude) }}" readonly placeholder="-6.200000">
                                    </div>
                                    <div class="col-md-6 mt-2 mt-md-0">
                                        <label class="form-label small text-muted">Longitude</label>
                                        <input type="text" class="form-control" name="longitude" id="longitude" value="{{ old('longitude', $address->longitude) }}" readonly placeholder="106.816666">
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2" id="gpsStatus">
                                    @if($address->latitude && $address->longitude)
                                        <i class="bi bi-check-circle text-success"></i> GPS tersimpan: {{ $address->latitude }}, {{ $address->longitude }}
                                    @endif
                                </small>
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
function getGPSLocation() {
    const gpsStatus = document.getElementById('gpsStatus');

    if (navigator.geolocation) {
        gpsStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise"></i> Mengambil lokasi GPS...</span>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                gpsStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Lokasi GPS berhasil diperbarui! (Lat: ' +
                    position.coords.latitude.toFixed(6) + ', Long: ' + position.coords.longitude.toFixed(6) + ')</span>';
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
