@extends('layouts.admin')

@section('title', 'Edit Diskon')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-warning"></i> Edit Diskon
        </h1>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
                // Member discount price preview
    const basePriceForMember = {{ $product->harga_produk }};
    const currentDiscountedPrice = {{ $product->hasActiveDiscount() ? $product->harga_diskon : $product->harga_produk }};

    document.querySelectorAll('.member-discount-input').forEach((input) => {
        input.addEventListener('input', function() {
            const index = this.getAttribute('data-index');
            const discount = parseFloat(this.value) || 0;
            // Calculate based on current price (with regular discount if active)
            const finalPrice = currentDiscountedPrice - (currentDiscountedPrice * (discount / 100));

            // Update price preview
            document.querySelector(`.member-price-preview-${index}`).textContent =
                finalPrice.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    });fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Tabs for Regular and Member Discounts -->
    <ul class="nav nav-tabs mb-4" id="discountTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="regular-tab" data-bs-toggle="tab"
                    data-bs-target="#regular-discount" type="button" role="tab" aria-controls="regular-discount" aria-selected="true">
                <i class="fas fa-tag"></i> Diskon Reguler
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="member-tab" data-bs-toggle="tab"
                    data-bs-target="#member-discount" type="button" role="tab" aria-controls="member-discount" aria-selected="false">
                <i class="fas fa-crown"></i> Diskon Member Tier
            </button>
        </li>
    </ul>

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
        <div class="card-header py-3 bg-warning text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-tags"></i> Kelola Diskon Produk
            </h6>
        </div>
        <div class="card-body">
            <!-- Tab Content -->
            <div class="tab-content" id="discountTabContent">
                <!-- Regular Discount Tab -->
                <div class="tab-pane fade show active" id="regular-discount" role="tabpanel" aria-labelledby="regular-tab">
            <!-- Display All Errors -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan!</h5>
                    <hr>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Display Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Display Error Message -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.discounts.update', $product->id_produk) }}" method="POST" id="discountForm">
                @csrf
                @method('PUT')

                @php
                    // Calculate discount type based on existing data
                    $discountType = 'percentage';
                    $discountValue = $product->persentase_diskon;
                @endphp

                <div class="row">
                    <!-- Discount Type -->
                    <div class="col-md-6 mb-3">
                        <label for="discount_type" class="form-label">
                            <i class="fas fa-percentage"></i> Tipe Diskon <span class="text-danger">*</span>
                        </label>
                        <select name="discount_type" id="discount_type" class="form-control @error('discount_type') is-invalid @enderror" required>
                            <option value="">-- Pilih Tipe Diskon --</option>
                            <option value="percentage" {{ old('discount_type', $discountType) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed" {{ old('discount_type', $discountType) == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
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
                               value="{{ old('discount_value', $discountValue) }}"
                               min="0"
                               step="0.01"
                               required
                               placeholder="Masukkan nilai diskon">
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" id="discount_hint">
                            Masukkan persentase diskon (contoh: 20 untuk diskon 20%)
                        </small>
                    </div>
                </div>

                <div class="row">
                    <!-- Start Date -->
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Tanggal Mulai <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="start_date"
                               id="start_date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $product->tanggal_mulai_diskon?->format('Y-m-d')) }}"
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
                               value="{{ old('end_date', $product->tanggal_akhir_diskon?->format('Y-m-d')) }}"
                               required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Active Status -->
                <div class="mb-3">
                    <div class="custom-control custom-checkbox">
                        <!-- Hidden input to ensure a value is always sent -->
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="is_active"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $product->is_diskon_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">
                            <strong>Aktifkan Diskon</strong>
                            <small class="text-muted d-block">Centang untuk mengaktifkan diskon ini</small>
                        </label>
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

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Diskon
                    </button>
                </div>
                </form>
                </div>
                <!-- End Regular Discount Tab -->

                <!-- Member Discount Tab -->
                <div class="tab-pane fade" id="member-discount" role="tabpanel" aria-labelledby="member-tab">
                    @php
                        $tiers = \App\Models\ProductMemberDiscount::TIERS;
                        $discounts = \App\Models\ProductMemberDiscount::where('product_id', $product->id_produk)
                            ->get()
                            ->keyBy('tier');
                    @endphp

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
                                                <label for="member_discount_{{ $index }}" class="form-label font-weight-bold">
                                                    <i class="fas fa-percentage"></i> Diskon (%)
                                                </label>
                                                <div class="input-group">
                                                    <input type="number"
                                                           name="discounts[{{ $index }}][discount_percentage]"
                                                           id="member_discount_{{ $index }}"
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
                                                           id="member_is_active_{{ $index }}"
                                                           class="custom-control-input"
                                                           value="1"
                                                           {{ old('discounts.' . $index . '.is_active', $isActive) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="member_is_active_{{ $index }}">
                                                        Aktifkan diskon untuk tier ini
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Price Preview -->
                                            <div class="alert alert-light mt-3 mb-0 small">
                                                <p class="mb-1"><strong>Harga Diskon untuk Member {{ $tierName }}:</strong></p>
                                                @php
                                                    // Get current price (base price or discounted price if regular discount is active)
                                                    $currentPrice = $product->hasActiveDiscount() ? $product->harga_diskon : $product->harga_produk;
                                                    $memberDiscountedPrice = $currentPrice - ($currentPrice * ($discountPercentage / 100));
                                                @endphp
                                                <p class="mb-0">
                                                    <span class="text-muted">
                                                        @if($product->hasActiveDiscount())
                                                            <s>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</s>
                                                            Rp {{ number_format($currentPrice, 0, ',', '.') }}
                                                        @else
                                                            Rp {{ number_format($currentPrice, 0, ',', '.') }}
                                                        @endif
                                                    </span>
                                                    <span class="float-right">
                                                        <strong class="text-success">Rp <span class="member-price-preview-{{ $index }}">{{ number_format($memberDiscountedPrice, 0, ',', '.') }}</span></strong>
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
                <!-- End Member Discount Tab -->
            </div>
        </div>
</div>
@endsection

@push('scripts')
<script>
    const productPrice = {{ $product->harga_produk }};

    // Update discount hint based on type
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

    // Calculate preview when discount value changes
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

    // Member discount price preview
    const basePriceForMember = {{ $product->harga_produk }};
    const currentDiscountedPrice = {{ $product->hasActiveDiscount() ? $product->harga_diskon : $product->harga_produk }};

    document.querySelectorAll('.member-discount-input').forEach((input) => {
        input.addEventListener('input', function() {
            const index = this.getAttribute('data-index');
            const discount = parseFloat(this.value) || 0;
            // Calculate based on current price (with regular discount if active)
            const finalPrice = currentDiscountedPrice - (currentDiscountedPrice * (discount / 100));

            // Update price preview
            document.querySelector(`.member-price-preview-${index}`).textContent =
                finalPrice.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    });

    // Set minimum end date to start date
    $('#start_date').change(function() {
        $('#end_date').attr('min', $(this).val());
    });

    // Trigger initial calculation on page load
    $(document).ready(function() {
        calculatePreview();
    });

    // Form validation before submit
    $('#discountForm').on('submit', function(e) {
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val()) || 0;
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        console.log('Form submit - Data:', {
            discount_type: discountType,
            discount_value: discountValue,
            start_date: startDate,
            end_date: endDate,
            is_active: $('#is_active').is(':checked')
        });

        // Validate discount type
        if (!discountType) {
            e.preventDefault();
            alert('Silakan pilih tipe diskon!');
            $('#discount_type').focus();
            return false;
        }

        // Validate discount value
        if (discountValue <= 0) {
            e.preventDefault();
            alert('Nilai diskon harus lebih dari 0!');
            $('#discount_value').focus();
            return false;
        }

        // Validate percentage
        if (discountType === 'percentage' && discountValue > 100) {
            e.preventDefault();
            alert('Persentase diskon tidak boleh lebih dari 100%!');
            $('#discount_value').focus();
            return false;
        }

        // Validate fixed amount
        if (discountType === 'fixed' && discountValue > productPrice) {
            e.preventDefault();
            alert('Nominal diskon tidak boleh lebih dari harga produk!');
            $('#discount_value').focus();
            return false;
        }

        // Validate dates
        if (!startDate || !endDate) {
            e.preventDefault();
            alert('Tanggal mulai dan berakhir harus diisi!');
            return false;
        }

        if (new Date(endDate) < new Date(startDate)) {
            e.preventDefault();
            alert('Tanggal berakhir tidak boleh lebih awal dari tanggal mulai!');
            $('#end_date').focus();
            return false;
        }

        console.log('Form validation passed, submitting...');
        return true;
    });

    // Handle tab navigation via hash
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash) {
            const hash = window.location.hash;
            if (hash === '#member-discount') {
                // Activate member discount tab
                const memberTab = new bootstrap.Tab(document.getElementById('member-tab'));
                memberTab.show();
            }
        }
    });
</script>
@endpush
