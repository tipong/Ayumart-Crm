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

                <div class="row">
                    <!-- Discount Type -->
                    <div class="col-md-6 mb-3">
                        <label for="discount_type" class="form-label">
                            <i class="fas fa-percentage"></i> Tipe Diskon <span class="text-danger">*</span>
                        </label>
                        <select name="discount_type" id="discount_type" class="form-control @error('discount_type') is-invalid @enderror" required>
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
                               required
                               placeholder="Masukkan nilai diskon">
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" id="discount_hint">
                            Pilih tipe diskon terlebih dahulu
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
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Diskon
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

    // Set minimum end date to start date
    $('#start_date').change(function() {
        $('#end_date').attr('min', $(this).val());
    });
</script>
@endpush
