@extends('layouts.pelanggan')

@section('title', 'Alamat Saya')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-geo-alt"></i> Alamat Saya</h3>
        <a href="{{ route('pelanggan.addresses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Alamat Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($addresses->count() > 0)
        <div class="row">
            @foreach($addresses as $address)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ $address->label }}</h5>
                                    @if($address->is_default)
                                        <span class="badge bg-success">Alamat Utama</span>
                                    @endif
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(!$address->is_default)
                                            <li>
                                                <form action="{{ route('pelanggan.addresses.set-default', $address->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-star"></i> Jadikan Utama
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="{{ route('pelanggan.addresses.edit', $address->id) }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('pelanggan.addresses.destroy', $address->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <p class="mb-2"><strong>{{ $address->nama_penerima }}</strong></p>
                            <p class="mb-2"><i class="bi bi-telephone"></i> {{ $address->no_telp_penerima }}</p>
                            <p class="text-muted mb-0">
                                <i class="bi bi-geo-alt"></i> {{ $address->formatted_address }}
                            </p>

                            @if($address->latitude && $address->longitude)
                                <small class="text-success">
                                    <i class="bi bi-pin-map"></i> Koordinat GPS tersimpan
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-geo-alt display-1 text-muted"></i>
            <h5 class="mt-3 text-muted">Anda belum memiliki alamat tersimpan</h5>
            <p class="text-muted">Tambahkan alamat untuk mempermudah proses checkout</p>
            <a href="{{ route('pelanggan.addresses.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle"></i> Tambah Alamat Pertama
            </a>
        </div>
    @endif
</div>
@endsection
