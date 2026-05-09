@extends('layouts.admin')

@section('title', 'Tambah Diskon')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-success"></i> Tambah Diskon
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
                        <img src="{{ \App\Helpers\ImageHelper::getProductImage($product->foto_produk) }}"
                             alt="{{ $product->nama_produk }}"
                             class="img-thumbnail">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center"
                             style="width: 100%; height: 150px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <h4>{{ $product->nama_produk }}</h4>
                    <p class="text-muted mb-2">{{ $product->kode_produk }}</p>
                    <p class="mb-2"><strong>Kategori:</strong> <span class="badge badge-info">{{ $product->jenis->nama_jenis ?? 'Tanpa Kategori' }}</span></p>
                    <p class="mb-2"><strong>Harga Normal:</strong> <span class="text-primary h5">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</span></p>
                    <p class="mb-0"><strong>Total Stok:</strong> {{ number_format($product->stokCabang->sum('total_stok'), 0, ',', '.') }} pcs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-tags"></i> Form Diskon
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.discounts.store', $product->id_produk) }}" method="POST">
                @csrf

                {{-- ===== TARGET DISKON ===== --}}
                <div class="mb-4">
                    <label class="form-label font-weight-bold">
                        <i class="fas fa-bullseye"></i> Target Diskon <span class="text-danger">*</span>
                    </label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success {{ old('discount_target', 'general') === 'general' ? 'shadow' : '' }}"
                                 id="card-general" style="cursor: pointer; transition: all 0.2s;"
                                 onclick="selectTarget('general')">
                                <div class="card-body d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-globe fa-2x text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 font-weight-bold">Umum (General)</h6>
                                        <small class="text-muted">Diskon berlaku untuk semua pelanggan tanpa memandang tier membership.</small>
                                    </div>
                                    <div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio"
                                                   name="discount_target"
                                                   id="target_general"
                                                   value="general"
                                                   class="custom-control-input"
                                                   {{ old('discount_target', 'general') === 'general' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="target_general"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info {{ old('discount_target') === 'tier' ? 'shadow' : '' }}"
                                 id="card-tier" style="cursor: pointer; transition: all 0.2s;"
                                 onclick="selectTarget('tier')">
                                <div class="card-body d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-crown fa-2x text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 font-weight-bold">Berdasarkan Tier Member</h6>
                                        <small class="text-muted">Diskon khusus untuk setiap tier (Bronze, Silver, Gold, Platinum).</small>
                                    </div>
                                    <div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio"
                                                   name="discount_target"
                                                   id="target_tier"
                                                   value="tier"
                                                   class="custom-control-input"
                                                   {{ old('discount_target') === 'tier' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="target_tier"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('discount_target')
                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- ===== DISKON UMUM (GENERAL) ===== --}}
                <div id="section-general" class="{{ old('discount_target', 'general') === 'general' ? '' : 'd-none' }}">
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle"></i>
                        <strong>Diskon Umum:</strong> Diskon ini akan berlaku untuk seluruh pelanggan yang membeli produk ini.
                    </div>
                    <div class="row">
                        <!-- Discount Type -->
                        <div class="col-md-6 mb-3">
                            <label for="discount_type" class="form-label">
                                <i class="fas fa-percentage"></i> Tipe Diskon <span class="text-danger">*</span>
                            </label>
                            <select name="discount_type" id="discount_type" class="form-control @error('discount_type') is-invalid @enderror">
                                <option value="">-- Pilih Tipe Diskon --</option>
                                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                            </select>
                            @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Discount Value -->
                        <div class="col-md-6 mb-3">
                            <label for="discount_value" class="form-label">
                                <i class="fas fa-money-bill"></i> Nilai Diskon <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="discount_value"
                                   id="discount_value"
                                   class="form-control @error('discount_value') is-invalid @enderror"
                                   value="{{ old('discount_value') }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="Masukkan nilai diskon">
                            @error('discount_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="discount_hint">
                                Pilih tipe diskon terlebih dahulu
                            </small>
                        </div>
                    </div>

                    <!-- Preview Card -->
                    <div class="alert alert-info" id="preview_card" style="display: none;">
                        <h5 class="alert-heading"><i class="fas fa-eye"></i> Preview Diskon</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Harga Normal:</strong></p>
                                <h4>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</h4>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Diskon:</strong></p>
                                <h4 class="text-danger" id="preview_discount">-</h4>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Harga Setelah Diskon:</strong></p>
                                <h4 class="text-success" id="preview_price">-</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== DISKON PER TIER ===== --}}
                <div id="section-tier" class="{{ old('discount_target') === 'tier' ? '' : 'd-none' }}">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Diskon per Tier:</strong> Atur diskon berbeda untuk setiap tier membership pelanggan. Pelanggan akan mendapatkan diskon sesuai tier mereka.
                    </div>

                    @php
                        $tiers = \App\Models\ProductMemberDiscount::TIERS;
                        $tierColors = [
                            'bronze'   => ['border' => 'warning', 'bg' => 'warning',   'icon' => 'fas fa-medal',  'label' => 'Bronze'],
                            'silver'   => ['border' => 'secondary','bg' => 'secondary', 'icon' => 'fas fa-medal',  'label' => 'Silver'],
                            'gold'     => ['border' => 'info',    'bg' => 'info',      'icon' => 'fas fa-crown',  'label' => 'Gold'],
                            'platinum' => ['border' => 'danger',  'bg' => 'danger',    'icon' => 'fas fa-gem',    'label' => 'Platinum'],
                        ];
                    @endphp

                    <div class="row">
                        @php $tierIndex = 0; @endphp
                        @foreach($tiers as $tierCode => $tierName)
                            @php $tc = $tierColors[$tierCode] ?? ['border'=>'secondary','bg'=>'secondary','icon'=>'fas fa-circle','label'=>$tierName]; @endphp
                            <div class="col-md-6 mb-4">
                                <div class="card border-{{ $tc['border'] }}">
                                    <div class="card-header py-2 bg-{{ $tc['bg'] }} text-white">
                                        <h6 class="mb-0 font-weight-bold">
                                            <i class="{{ $tc['icon'] }}"></i> {{ $tierName }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" name="tier_discounts[{{ $tierIndex }}][tier]" value="{{ $tierCode }}">

                                        <div class="form-group mb-3">
                                            <label for="tier_discount_{{ $tierIndex }}" class="form-label font-weight-bold">
                                                <i class="fas fa-percentage"></i> Diskon untuk {{ $tierName }} (%)
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                       name="tier_discounts[{{ $tierIndex }}][discount_percentage]"
                                                       id="tier_discount_{{ $tierIndex }}"
                                                       class="form-control tier-discount-input"
                                                       data-tier="{{ $tierCode }}"
                                                       data-index="{{ $tierIndex }}"
                                                       value="{{ old('tier_discounts.' . $tierIndex . '.discount_percentage', 0) }}"
                                                       min="0"
                                                       max="100"
                                                       step="0.01">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="tier_discounts[{{ $tierIndex }}][is_active]" value="0">
                                                <input type="checkbox"
                                                       name="tier_discounts[{{ $tierIndex }}][is_active]"
                                                       id="tier_active_{{ $tierIndex }}"
                                                       class="custom-control-input"
                                                       value="1"
                                                       {{ old('tier_discounts.' . $tierIndex . '.is_active', 1) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="tier_active_{{ $tierIndex }}">
                                                    Aktifkan diskon untuk tier ini
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Preview Harga -->
                                        <div class="alert alert-light mt-2 mb-0 small">
                                            <p class="mb-1"><strong>Preview Harga Member {{ $tierName }}:</strong></p>
                                            <p class="mb-0">
                                                <span class="text-muted">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</span>
                                                <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                                <strong class="text-success">Rp <span class="tier-price-preview-{{ $tierIndex }}">{{ number_format($product->harga_produk, 0, ',', '.') }}</span></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php $tierIndex++; @endphp
                        @endforeach
                    </div>
                </div>

                {{-- ===== TANGGAL DISKON ===== --}}
                <div class="row mt-3">
                    <!-- Start Date -->
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Tanggal Mulai <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="start_date"
                               id="start_date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', date('Y-m-d')) }}"
                               required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">
                            <i class="fas fa-calendar-check"></i> Tanggal Berakhir <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="end_date"
                               id="end_date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date') }}"
                               required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Diskon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-target-selected {
        border-width: 2px !important;
        box-shadow: 0 0 0 3px rgba(0,123,255,.25);
    }
    #card-general:hover, #card-tier:hover {
        border-width: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
    const productPrice = {{ $product->harga_produk }};

    // ======= Target Diskon Toggle =======
    function selectTarget(target) {
        // Update radio
        document.getElementById('target_' + target).checked = true;

        // Toggle sections
        if (target === 'general') {
            document.getElementById('section-general').classList.remove('d-none');
            document.getElementById('section-tier').classList.add('d-none');
            document.getElementById('card-general').classList.add('card-target-selected');
            document.getElementById('card-tier').classList.remove('card-target-selected');
            // Hapus required dari tier inputs jika ada
            document.querySelectorAll('.tier-discount-input').forEach(el => el.removeAttribute('required'));
        } else {
            document.getElementById('section-general').classList.add('d-none');
            document.getElementById('section-tier').classList.remove('d-none');
            document.getElementById('card-tier').classList.add('card-target-selected');
            document.getElementById('card-general').classList.remove('card-target-selected');
            // Hapus required dari general inputs
            document.getElementById('discount_type').removeAttribute('required');
            document.getElementById('discount_value').removeAttribute('required');
        }
    }

    // Init selected state
    const initialTarget = '{{ old('discount_target', 'general') }}';
    selectTarget(initialTarget);

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
            $('#discount_hint').text('Pilih tipe diskon terlebih dahulu');
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

    function formatNumber(num) {
        return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Set minimum end date to start date
    $('#start_date').change(function() {
        $('#end_date').attr('min', $(this).val());
    });
</script>
@endpush
