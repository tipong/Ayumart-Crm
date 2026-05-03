@extends('layouts.admin')

@section('title', 'Edit Diskon Member - ' . $product->nama_produk)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-percentage text-info"></i> Diskon Member Tier: {{ $product->nama_produk }}
        </h1>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Product Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-box"></i> Informasi Produk
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    @if($product->foto_produk)
                        <img src="{{ asset('storage/' . $product->foto_produk) }}"
                             alt="{{ $product->nama_produk }}"
                             class="img-thumbnail w-100">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center"
                             style="width: 100%; height: 150px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <h4>{{ $product->nama_produk }}</h4>
                    <p class="text-muted mb-2"><small>{{ $product->kode_produk }}</small></p>
                    <p class="mb-2">
                        <strong>Harga Normal:</strong>
                        <span class="text-primary h5 font-weight-bold">
                            Rp {{ number_format($product->harga_produk, 0, ',', '.') }}
                        </span>
                    </p>
                    <p class="mb-0">
                        <strong>Kategori:</strong> {{ $product->jenis->nama_jenis ?? 'Tanpa Kategori' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Discount Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-crown"></i> Pengaturan Diskon Member Tier
            </h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.discounts.update-member-discount', $product->id_produk) }}"
                  method="POST"
                  id="memberDiscountForm">
                @csrf
                @method('PUT')

                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle"></i>
                    Atur diskon khusus untuk setiap tier member. Pelanggan akan melihat harga yang berbeda sesuai tier membership mereka.
                </div>

                <div class="row">
                    @php $index = 0; @endphp
                    @foreach($tiers as $tierCode => $tierName)
                        @php
                            $discount = $discounts[$tierCode] ?? null;
                            $isActive = $discount ? $discount->is_active : false;
                            $discountPercentage = $discount ? $discount->discount_percentage : 0;
                        @endphp
                        <div class="col-md-6 mb-4">
                            <div class="card border-{{
                                $tierCode === 'platinum' ? 'warning' :
                                ($tierCode === 'gold' ? 'info' :
                                ($tierCode === 'silver' ? 'secondary' : 'success'))
                            }}">
                                <div class="card-header py-2 bg-{{
                                    $tierCode === 'platinum' ? 'warning' :
                                    ($tierCode === 'gold' ? 'info' :
                                    ($tierCode === 'silver' ? 'secondary' : 'success'))
                                }} text-white">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-crown"></i> {{ $tierName }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="discounts[{{ $index }}][tier]" value="{{ $tierCode }}">

                                    <div class="form-group mb-3">
                                        <label for="discount_{{ $index }}" class="form-label font-weight-bold">
                                            <i class="fas fa-percentage"></i> Diskon (%)
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="discounts[{{ $index }}][discount_percentage]"
                                                   id="discount_{{ $index }}"
                                                   class="form-control member-discount-input"
                                                   data-tier="{{ $tierCode }}"
                                                   data-index="{{ $index }}"
                                                   value="{{ old('discounts.' . $index . '.discount_percentage', $discountPercentage) }}"
                                                   min="0"
                                                   max="100"
                                                   step="0.01">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="hidden" name="discounts[{{ $index }}][is_active]" value="0">
                                            <input type="checkbox"
                                                   name="discounts[{{ $index }}][is_active]"
                                                   id="is_active_{{ $index }}"
                                                   class="custom-control-input"
                                                   value="1"
                                                   {{ old('discounts.' . $index . '.is_active', $isActive) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_active_{{ $index }}">
                                                Aktifkan diskon untuk tier ini
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Price Preview -->
                                    <div class="alert alert-light mt-3 mb-0 small">
                                        <p class="mb-1"><strong>Harga untuk Member {{ $tierName }}:</strong></p>
                                        <p class="mb-0">
                                            <span class="text-muted">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</span>
                                            <span class="float-right">
                                                <strong class="text-success">Rp <span class="price-preview-{{ $index }}">{{ number_format($product->harga_produk - ($product->harga_produk * ($discountPercentage / 100)), 0, ',', '.') }}</span></strong>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php $index++; @endphp
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save"></i> Simpan Diskon Member
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-lightbulb"></i> Informasi
            </h6>
        </div>
        <div class="card-body">
            <p><strong>Apa itu Diskon Member Tier?</strong></p>
            <p>Diskon khusus yang diberikan kepada pelanggan berdasarkan tier membership mereka. Setiap tier dapat menerima diskon yang berbeda pada produk yang sama.</p>
            <p class="mb-0"><strong>Contoh:</strong> Produk "Smartphone" bisa memiliki diskon 5% untuk Bronze, 10% untuk Silver, 15% untuk Gold, dan 20% untuk Platinum.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const basePrice = {{ $product->harga_produk }};

    document.querySelectorAll('.member-discount-input').forEach((input) => {
        input.addEventListener('input', function() {
            const index = this.getAttribute('data-index');
            const discount = parseFloat(this.value) || 0;
            const finalPrice = basePrice - (basePrice * (discount / 100));

            // Update price preview
            document.querySelector(`.price-preview-${index}`).textContent =
                finalPrice.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    });
</script>
@endpush
