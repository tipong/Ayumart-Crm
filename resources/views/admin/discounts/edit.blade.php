@extends('layouts.admin')

@section('title', 'Edit Diskon')

@push('styles')
<style>
    .card-target-selected {
        border-color: #10b981 !important;
        border-width: 2px !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
    }
    #card-general:hover, #card-tier:hover {
        border-color: #10b981;
    }
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
                <i class="bi bi-pencil-square text-success"></i> Edit Program Diskon
            </h1>
            <p class="text-muted mb-0">Ubah pengaturan potongan harga pada produk AyuMart.</p>
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
                        <span class="badge bg-light text-dark border px-3 py-1.5 fw-bold">
                            Total Stok: <span class="text-success ms-1">{{ number_format($product->stokCabang->sum('total_stok'), 0, ',', '.') }} pcs</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Form Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h5 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-tags-fill text-success me-1"></i> Form Diskon Produk
            </h5>
        </div>
        <div class="card-body">
            <!-- Alert Messages -->
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

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @php
                $currentTarget = old('discount_target', $product->discount_target ?? 'general');
                $discountType  = 'percentage';
                $discountValue = $product->persentase_diskon;
                $tiers = \App\Models\ProductMemberDiscount::TIERS;
                $existingTierDiscounts = \App\Models\ProductMemberDiscount::where('product_id', $product->id_produk)->get()->keyBy('tier');
                $tierColors = [
                    'bronze'   => ['border' => 'warning',   'bg' => 'warning text-dark', 'icon' => 'bi bi-award-fill', 'label' => 'Bronze'],
                    'silver'   => ['border' => 'secondary', 'bg' => 'secondary text-white', 'icon' => 'bi bi-award-fill', 'label' => 'Silver'],
                    'gold'     => ['border' => 'info',      'bg' => 'info text-dark', 'icon' => 'bi bi-award-fill', 'label' => 'Gold'],
                    'platinum' => ['border' => 'danger',    'bg' => 'danger text-white', 'icon' => 'bi bi-gem', 'label' => 'Platinum'],
                ];
            @endphp

            <form action="{{ route('admin.discounts.update', $product->id_produk) }}" method="POST" id="discountForm">
                @csrf
                @method('PUT')

                {{-- ===== TARGET DISKON ===== --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary mb-3">
                        <i class="bi bi-bullseye text-success me-1"></i> Target Pelanggan Program Diskon <span class="text-danger">*</span>
                    </label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border h-100 {{ $currentTarget === 'general' ? 'card-target-selected' : '' }}"
                                 id="card-general" style="cursor: pointer; transition: all 0.2s;"
                                 onclick="selectTarget('general')">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle me-3">
                                        <i class="bi bi-globe fs-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-dark">Umum (General)</h6>
                                        <small class="text-muted d-block" style="font-size: 0.8rem;">Diskon berlaku untuk semua pelanggan AyuMart tanpa syarat tier.</small>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio"
                                               name="discount_target"
                                               id="target_general"
                                               value="general"
                                               class="form-check-input"
                                               {{ $currentTarget === 'general' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border h-100 {{ $currentTarget === 'tier' ? 'card-target-selected' : '' }}"
                                 id="card-tier" style="cursor: pointer; transition: all 0.2s;"
                                 onclick="selectTarget('tier')">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle me-3">
                                        <i class="bi bi-award fs-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-dark">Berdasarkan Tier Membership</h6>
                                        <small class="text-muted d-block" style="font-size: 0.8rem;">Diskon khusus yang nilainya bervariasi per tier (Bronze, Silver, Gold, Platinum).</small>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio"
                                               name="discount_target"
                                               id="target_tier"
                                               value="tier"
                                               class="form-check-input"
                                               {{ $currentTarget === 'tier' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('discount_target')
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- ===== DISKON UMUM (GENERAL) ===== --}}
                <div id="section-general" class="mb-4 {{ $currentTarget === 'general' ? '' : 'd-none' }}">
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success-emphasis mb-3 rounded-3">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Diskon Umum:</strong> Diskon ini akan memotong harga produk untuk seluruh pelanggan secara langsung.
                    </div>
                    <div class="row g-3">
                        <!-- Discount Type -->
                        <div class="col-md-6">
                            <label for="discount_type" class="form-label fw-bold text-secondary">Tipe Potongan Harga <span class="text-danger">*</span></label>
                            <select name="discount_type" id="discount_type" class="form-select custom-input @error('discount_type') is-invalid @enderror">
                                <option value="">-- Pilih Tipe Diskon --</option>
                                <option value="percentage" {{ old('discount_type', $discountType) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                <option value="fixed" {{ old('discount_type', $discountType) == 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                            </select>
                            @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Discount Value -->
                        <div class="col-md-6">
                            <label for="discount_value" class="form-label fw-bold text-secondary">Nilai Potongan <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="discount_value"
                                   id="discount_value"
                                   class="form-control custom-input @error('discount_value') is-invalid @enderror"
                                   value="{{ old('discount_value', $discountValue) }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="Masukkan besar potongan">
                            @error('discount_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="discount_hint">
                                Masukkan persentase diskon (contoh: 20 untuk diskon 20%)
                            </small>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="mb-4 mt-3 form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               class="form-check-input"
                               id="is_active"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $product->is_diskon_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-secondary" for="is_active">
                            Aktifkan Diskon Umum
                        </label>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Nonaktifkan jika ingin menangguhkan promo sementara tanpa menghapus konfigurasi ini.</small>
                    </div>

                    <!-- Preview Card -->
                    <div class="card border-0 bg-light my-4 rounded-3" id="preview_card" style="display: none;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-eye-fill text-success me-1"></i> Preview Perhitungan Diskon</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <span class="text-muted d-block small">Harga Awal</span>
                                    <h5 class="fw-bold text-dark">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</h5>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted d-block small">Nilai Potongan</span>
                                    <h5 class="fw-bold text-danger" id="preview_discount">-</h5>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted d-block small">Harga Akhir Pelanggan</span>
                                    <h5 class="fw-extrabold text-success" id="preview_price">-</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== DISKON PER TIER ===== --}}
                <div id="section-tier" class="mb-4 {{ $currentTarget === 'tier' ? '' : 'd-none' }}">
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success-emphasis mb-4 rounded-3">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Diskon per Tier:</strong> Tentukan persentase potongan harga yang berbeda untuk tiap level member di AyuMart.
                    </div>

                    <!-- Status Aktif untuk semua tier discounts -->
                    <div class="mb-4 form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               class="form-check-input"
                               id="is_active_tier"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $product->is_diskon_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-secondary" for="is_active_tier">
                            Aktifkan Diskon Tier
                        </label>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Nonaktifkan jika ingin menangguhkan promo sementara untuk seluruh level member.</small>
                    </div>

                    <div class="row g-4">
                        @php $tierIndex = 0; @endphp
                        @foreach($tiers as $tierCode => $tierName)
                            @php
                                $tc = $tierColors[$tierCode] ?? ['border'=>'secondary','bg'=>'secondary','icon'=>'bi bi-circle','label'=>$tierName];
                                $existingDiscount = $existingTierDiscounts[$tierCode] ?? null;
                                $isActive = $existingDiscount ? $existingDiscount->is_active : false;
                                $discountPct = $existingDiscount ? $existingDiscount->discount_percentage : 0;
                                $currentPrice = $product->harga_produk;
                                $previewPrice = $currentPrice - ($currentPrice * ($discountPct / 100));
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
                                        <input type="hidden" name="tier_discounts[{{ $tierIndex }}][tier]" value="{{ $tierCode }}">

                                        <div class="mb-3">
                                            <label for="tier_discount_{{ $tierIndex }}" class="form-label fw-bold text-secondary">
                                                Besar Potongan (%)
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                       name="tier_discounts[{{ $tierIndex }}][discount_percentage]"
                                                       id="tier_discount_{{ $tierIndex }}"
                                                       class="form-control custom-input tier-discount-input"
                                                       data-tier="{{ $tierCode }}"
                                                       data-index="{{ $tierIndex }}"
                                                       value="{{ old('tier_discounts.' . $tierIndex . '.discount_percentage', $discountPct) }}"
                                                       min="0"
                                                       max="100"
                                                       step="0.01">
                                                <span class="input-group-text bg-white border-start-0">%</span>
                                            </div>
                                        </div>

                                        <div class="mb-3 form-check form-switch">
                                            <input type="hidden" name="tier_discounts[{{ $tierIndex }}][is_active]" value="0">
                                            <input type="checkbox"
                                                   name="tier_discounts[{{ $tierIndex }}][is_active]"
                                                   id="tier_active_{{ $tierIndex }}"
                                                   class="form-check-input"
                                                   value="1"
                                                   {{ old('tier_discounts.' . $tierIndex . '.is_active', $isActive) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold text-secondary" for="tier_active_{{ $tierIndex }}">
                                                Aktifkan diskon untuk tier {{ $tierName }}
                                            </label>
                                        </div>

                                        <!-- Preview Harga -->
                                        <div class="bg-light p-3 rounded border small mt-3">
                                            <span class="text-muted d-block small">Estimasi harga member {{ $tierName }}:</span>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="text-decoration-line-through text-muted me-2">Rp {{ number_format($currentPrice, 0, ',', '.') }}</span>
                                                <i class="bi bi-arrow-right text-muted me-2"></i>
                                                <strong class="text-success fs-6">Rp <span class="tier-price-preview-{{ $tierIndex }}">{{ number_format($previewPrice, 0, ',', '.') }}</span></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php $tierIndex++; @endphp
                        @endforeach
                    </div>
                </div>

                {{-- ===== TANGGAL DISKON ===== --}}
                <div class="row g-3 border-top pt-4 mt-2">
                    <!-- Start Date -->
                    <div class="col-md-6">
                        <label for="start_date" class="form-label fw-bold text-secondary">
                            <i class="bi bi-calendar-event text-success me-1"></i> Tanggal Mulai Diskon <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="start_date"
                               id="start_date"
                               class="form-control custom-input @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $product->tanggal_mulai_diskon?->format('Y-m-d')) }}"
                               required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="col-md-6">
                        <label for="end_date" class="form-label fw-bold text-secondary">
                            <i class="bi bi-calendar-check text-success me-1"></i> Tanggal Berakhir Diskon <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="end_date"
                               id="end_date"
                               class="form-control custom-input @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date', $product->tanggal_akhir_diskon?->format('Y-m-d')) }}"
                               required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 border-top pt-4">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success text-white fw-bold rounded-pill px-4">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan Diskon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productPrice = {{ $product->harga_produk }};

    function selectTarget(target) {
        document.getElementById('target_' + target).checked = true;

        if (target === 'general') {
            document.getElementById('section-general').classList.remove('d-none');
            document.getElementById('section-tier').classList.add('d-none');
            document.getElementById('card-general').classList.add('card-target-selected');
            document.getElementById('card-tier').classList.remove('card-target-selected');
        } else {
            document.getElementById('section-general').classList.add('d-none');
            document.getElementById('section-tier').classList.remove('d-none');
            document.getElementById('card-tier').classList.add('card-target-selected');
            document.getElementById('card-general').classList.remove('card-target-selected');
        }
    }

    // Initialize initial state
    const currentTargetValue = '{{ $currentTarget }}';
    selectTarget(currentTargetValue);

    // ======= General Discount Preview =======
    $('#discount_type').change(function() {
        const type = $(this).val();
        if (type === 'percentage') {
            $('#discount_hint').text('Masukkan persentase diskon (contoh: 20 untuk diskon 20%)');
            $('#discount_value').attr('max', '100');
        } else if (type === 'fixed') {
            $('#discount_hint').text('Masukkan nominal diskon dalam Rupiah (contoh: 50000)');
            $('#discount_value').attr('max', productPrice);
        } else {
            $('#discount_hint').text('Pilih tipe potongan terlebih dahulu');
        }
        calculatePreview();
    });

    $('#discount_value').on('input', calculatePreview);

    function calculatePreview() {
        const type = $('#discount_type').val();
        const value = parseFloat($('#discount_value').val()) || 0;

        if (!type || !value) {
            $('#preview_card').hide();
            return;
        }

        let discountAmount, finalPrice, discountPercentage;

        if (type === 'percentage') {
            discountPercentage = Math.min(value, 100);
            discountAmount = productPrice * (discountPercentage / 100);
            finalPrice = productPrice - discountAmount;
        } else {
            discountAmount = Math.min(value, productPrice);
            finalPrice = productPrice - discountAmount;
            discountPercentage = (discountAmount / productPrice) * 100;
        }

        $('#preview_discount').text('-' + discountPercentage.toFixed(0) + '% (Rp ' + formatNumber(discountAmount) + ')');
        $('#preview_price').text('Rp ' + formatNumber(finalPrice));
        $('#preview_card').show();
    }

    function formatNumber(num) {
        return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // ======= Tier Discount Preview =======
    document.querySelectorAll('.tier-discount-input').forEach((input) => {
        input.addEventListener('input', function() {
            const index = this.getAttribute('data-index');
            const discount = parseFloat(this.value) || 0;
            const finalPrice = productPrice - (productPrice * (discount / 100));
            const preview = document.querySelector(`.tier-price-preview-${index}`);
            if (preview) {
                preview.textContent = finalPrice.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        });
    });

    $('#start_date').change(function() {
        $('#end_date').attr('min', $(this).val());
    });

    // Run preview once loaded
    $(document).ready(function() {
        calculatePreview();
    });

    // Form validation before submit
    $('#discountForm').on('submit', function(e) {
        const target = $('input[name="discount_target"]:checked').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        if (!target) {
            e.preventDefault();
            Swal.fire('Form Belum Lengkap', 'Silakan pilih target diskon!', 'warning');
            return false;
        }

        if (target === 'general') {
            const discountType  = $('#discount_type').val();
            const discountValue = parseFloat($('#discount_value').val()) || 0;

            if (!discountType) {
                e.preventDefault();
                Swal.fire('Form Belum Lengkap', 'Silakan pilih tipe diskon!', 'warning');
                $('#discount_type').focus();
                return false;
            }
            if (discountValue <= 0) {
                e.preventDefault();
                Swal.fire('Form Belum Lengkap', 'Nilai diskon harus lebih dari 0!', 'warning');
                $('#discount_value').focus();
                return false;
            }
            if (discountType === 'percentage' && discountValue > 100) {
                e.preventDefault();
                Swal.fire('Kesalahan Input', 'Persentase diskon tidak boleh lebih dari 100%!', 'warning');
                $('#discount_value').focus();
                return false;
            }
            if (discountType === 'fixed' && discountValue > productPrice) {
                e.preventDefault();
                Swal.fire('Kesalahan Input', 'Nominal diskon tidak boleh melebihi harga produk!', 'warning');
                $('#discount_value').focus();
                return false;
            }
        }

        if (!startDate || !endDate) {
            e.preventDefault();
            Swal.fire('Form Belum Lengkap', 'Tanggal mulai dan berakhir harus diisi!', 'warning');
            return false;
        }

        if (new Date(endDate) < new Date(startDate)) {
            e.preventDefault();
            Swal.fire('Kesalahan Tanggal', 'Tanggal berakhir tidak boleh lebih awal dari tanggal mulai!', 'warning');
            $('#end_date').focus();
            return false;
        }

        return true;
    });
</script>
@endpush
