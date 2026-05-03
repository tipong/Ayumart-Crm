@extends('layouts.pelanggan')

@section('title', 'Edit Alamat')

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
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="getGPSLocation()">
                                    <i class="bi bi-geo-alt-fill"></i> Perbarui Lokasi GPS
                                </button>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address->latitude) }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address->longitude) }}">
                                <small class="text-muted d-block mt-1" id="gpsStatus">
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
</script>
@endsection
