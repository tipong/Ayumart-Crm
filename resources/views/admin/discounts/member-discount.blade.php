@extends('layouts.admin')

@section('title', 'Edit Diskon Member - ' . $product->nama_produk)

@push('styles')
<style>
    .custom-input {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 0.6rem 1rem;
        font-weight: 500;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }
    .custom-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        background-color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <!-- Page Heading -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis mb-1">
                <i class="bi bi-award-fill text-success"></i> Diskon Member Tier: {{ $product->nama_produk }}
            </h1>
            <p class="text-muted mb-0">Atur potongan harga spesial berdasarkan level tier loyalitas pelanggan.</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Product Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h5 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-box-seam-fill text-success me-1"></i> Informasi Produk
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center g-4">
                <div class="col-md-2 text-center text-md-start">
                    @if($product->foto_produk)
                        <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 120, 120) }}"
                             alt="{{ $product->nama_produk }}"
                             class="rounded border shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded border d-flex align-items-center justify-content-center mx-auto mx-md-0"
                             style="width: 120px; height: 120px;">
                            <i class="bi bi-image text-muted fs-1"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <h3 class="fw-bold text-dark mb-1">{{ $product->nama_produk }}</h3>
                    <p class="text-muted mb-2 small">{{ $product->kode_produk }}</p>
                    <div class="d-flex flex-wrap gap-3">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-3 py-1.5 fw-bold">
                            <i class="bi bi-tag-fill me-1"></i> Kategori: {{ $product->jenis->nama_jenis ?? 'Tanpa Kategori' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-1.5 fw-bold">
                            Harga Normal: <span class="text-success ms-1">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Discount Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h5 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-gear-fill text-success me-1"></i> Konfigurasi Diskon Tier Membership
            </h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <h6 class="fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Perbaiki Kesalahan Form:</h6>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.discounts.update-member-discount', $product->id_produk) }}"
                  method="POST"
                  id="memberDiscountForm">
                @csrf
                @method('PUT')

                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success-emphasis mb-4 rounded-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Diskon per Tier:</strong> Tentukan persentase potongan harga yang berbeda untuk tiap level member di AyuMart.
                </div>

                @php
                    $tierColors = [
                        'bronze'   => ['border' => 'warning', 'bg' => 'warning text-dark', 'icon' => 'bi bi-award-fill', 'label' => 'Bronze'],
                        'silver'   => ['border' => 'secondary','bg' => 'secondary text-white', 'icon' => 'bi bi-award-fill', 'label' => 'Silver'],
                        'gold'     => ['border' => 'info',    'bg' => 'info text-dark', 'icon' => 'bi bi-award-fill', 'label' => 'Gold'],
                        'platinum' => ['border' => 'danger',  'bg' => 'danger text-white', 'icon' => 'bi bi-gem', 'label' => 'Platinum'],
                    ];
                @endphp

                <div class="row g-4">
                    @php $index = 0; @endphp
                    @foreach($tiers as $tierCode => $tierName)
                        @php
                            $discount = $discounts[$tierCode] ?? null;
                            $isActive = $discount ? $discount->is_active : false;
                            $discountPercentage = $discount ? $discount->discount_percentage : 0;
                            $tc = $tierColors[$tierCode] ?? ['border'=>'secondary','bg'=>'secondary','icon'=>'bi bi-circle','label'=>$tierName];
                        @endphp
                        <div class="col-md-6">
                            <div class="card border shadow-sm h-100">
                                <div class="card-header py-3 bg-light border-0 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <i class="{{ $tc['icon'] }} text-success me-1"></i> Member {{ $tierName }}
                                    </h6>
                                    <span class="badge bg-{{ $tc['bg'] }} px-2.5 py-1 text-uppercase">{{ $tierName }}</span>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="discounts[{{ $index }}][tier]" value="{{ $tierCode }}">

                                    <div class="mb-3">
                                        <label for="discount_{{ $index }}" class="form-label fw-bold text-secondary">
                                            Besar Potongan (%)
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="discounts[{{ $index }}][discount_percentage]"
                                                   id="discount_{{ $index }}"
                                                   class="form-control custom-input member-discount-input"
                                                   data-tier="{{ $tierCode }}"
                                                   data-index="{{ $index }}"
                                                   value="{{ old('discounts.' . $index . '.discount_percentage', $discountPercentage) }}"
                                                   min="0"
                                                   max="100"
                                                   step="0.01">
                                            <span class="input-group-text bg-white border-start-0">%</span>
                                        </div>
                                    </div>

                                    <div class="mb-3 form-check form-switch">
                                        <input type="hidden" name="discounts[{{ $index }}][is_active]" value="0">
                                        <input type="checkbox"
                                               name="discounts[{{ $index }}][is_active]"
                                               id="is_active_{{ $index }}"
                                               class="form-check-input"
                                               value="1"
                                               {{ old('discounts.' . $index . '.is_active', $isActive) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-secondary" for="is_active_{{ $index }}">
                                            Aktifkan diskon untuk tier {{ $tierName }}
                                        </label>
                                    </div>

                                    <!-- Price Preview -->
                                    <div class="bg-light p-3 rounded border small mt-3">
                                        <span class="text-muted d-block small">Estimasi harga member {{ $tierName }}:</span>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="text-decoration-line-through text-muted me-2">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</span>
                                            <i class="bi bi-arrow-right text-muted me-2"></i>
                                            <strong class="text-success fs-6">Rp <span class="price-preview-{{ $index }}">{{ number_format($product->harga_produk - ($product->harga_produk * ($discountPercentage / 100)), 0, ',', '.') }}</span></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php $index++; @endphp
                    @endforeach
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 border-top pt-4">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success text-white fw-bold rounded-pill px-4">
                        <i class="bi bi-check-circle me-1"></i> Simpan Diskon Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h5 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-info-circle text-success me-1"></i> Cara Kerja Diskon Member Tier
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Apa itu Diskon Member Tier?</strong></p>
            <p class="text-muted small mb-3">Diskon khusus yang diberikan kepada pelanggan berdasarkan tier membership mereka. Setiap tier dapat menerima diskon yang berbeda pada produk yang sama.</p>
            <p class="mb-1"><strong>Contoh:</strong></p>
            <p class="text-muted small mb-0">Produk dengan harga Rp 100.000 bisa disetting diskon 5% untuk Bronze (Rp 95.000), 10% untuk Silver (Rp 90.000), 15% untuk Gold (Rp 85.000), dan 20% untuk Platinum (Rp 80.000).</p>
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
