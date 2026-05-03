@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-warning"></i> Edit Produk
        </h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Produk
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-box"></i> Form Edit Produk
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product->id_produk) }}" method="POST" enctype="multipart/form-data" id="editProductForm">
                        @csrf
                        @method('PUT')

                        <!-- Nama Produk -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag"></i> Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $product->name) }}"
                                   placeholder="Contoh: Indomie Goreng"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="mb-3">
                            <label for="category" class="form-label">
                                <i class="fas fa-folder"></i> Kategori <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror"
                                    id="category"
                                    name="category"
                                    required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id_jenis }}" {{ old('category', $product->id_jenis) == $cat->id_jenis ? 'selected' : '' }}>
                                        {{ $cat->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Harga dan Stok -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">
                                        <i class="fas fa-money-bill-wave"></i> Harga <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number"
                                               class="form-control @error('price') is-invalid @enderror"
                                               id="price"
                                               name="price"
                                               value="{{ old('price', $product->price) }}"
                                               placeholder="10000"
                                               min="0"
                                               step="100"
                                               required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">
                                        <i class="fas fa-cubes"></i> Stok <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           class="form-control @error('stock') is-invalid @enderror"
                                           id="stock"
                                           name="stock"
                                           value="{{ old('stock', $product->stock) }}"
                                           placeholder="100"
                                           min="0"
                                           required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left"></i> Deskripsi Produk
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Masukkan deskripsi produk (opsional)">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Upload Gambar -->
                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image"></i> Gambar Produk
                            </label>

                            @if($product->image)
                                <div class="mb-3">
                                    <p class="text-muted small mb-2">Gambar saat ini:</p>
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         alt="{{ $product->name }}"
                                         class="img-thumbnail mb-2"
                                         id="currentImage"
                                         style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                    <p class="text-muted small">Upload gambar baru untuk mengganti</p>
                                </div>
                            @endif

                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Format: JPG, PNG, GIF. Maksimal 2MB
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Preview Gambar Baru -->
                            <div id="imagePreviewContainer" class="mt-3" style="display: none;">
                                <p class="text-muted small mb-2">Preview gambar baru:</p>
                                <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Produk
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Discount Management Card -->
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-percentage"></i> Manajemen Diskon
                    </h6>
                    @if($product->hasActiveDiscount())
                        <span class="badge bg-success text-white">
                            <i class="fas fa-check-circle"></i> Aktif
                        </span>
                    @else
                        <span class="badge bg-secondary text-white">
                            <i class="fas fa-times-circle"></i> Tidak Aktif
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    @if($product->hasActiveDiscount())
                        <!-- Display Active Discount -->
                        <div class="alert alert-success mb-3">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-tags"></i> Diskon Aktif
                            </h6>
                            <div class="small">
                                <p class="mb-1"><strong>Harga Normal:</strong> Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</p>
                                <p class="mb-1"><strong>Harga Diskon:</strong>
                                    <span class="text-success fw-bold">Rp {{ number_format($product->harga_diskon, 0, ',', '.') }}</span>
                                </p>
                                <p class="mb-1"><strong>Persentase:</strong> {{ number_format($product->persentase_diskon, 0) }}%</p>
                                <p class="mb-1"><strong>Periode:</strong><br>
                                    {{ $product->tanggal_mulai_diskon ? $product->tanggal_mulai_diskon->format('d M Y') : '-' }} s/d
                                    {{ $product->tanggal_akhir_diskon ? $product->tanggal_akhir_diskon->format('d M Y') : '-' }}
                                </p>
                                @if($product->tanggal_akhir_diskon)
                                    @php
                                        $daysLeft = now()->diffInDays($product->tanggal_akhir_diskon, false);
                                    @endphp
                                    @if($daysLeft >= 0)
                                        <p class="mb-0">
                                            <i class="fas fa-clock text-warning"></i>
                                            <strong>Sisa {{ ceil($daysLeft) }} hari lagi</strong>
                                        </p>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Remove Discount Form -->
                        <form action="{{ route('admin.products.discount.remove', $product->id_produk) }}"
                              method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash"></i> Hapus Diskon
                            </button>
                        </form>

                        <hr class="my-3">
                        <p class="small text-muted mb-2"><strong>Update Diskon:</strong></p>
                    @else
                        <!-- Display info when no discount -->
                        <div class="alert alert-info mb-3">
                            <p class="small mb-0">
                                <i class="fas fa-info-circle"></i>
                                Produk ini belum memiliki diskon aktif.
                            </p>
                        </div>
                    @endif

                    <!-- Add/Update Discount Form -->
                    <form action="{{ route('admin.products.discount.update', $product->id_produk) }}"
                          method="POST"
                          id="discountForm">
                        @csrf

                        <!-- Harga Diskon -->
                        <div class="mb-3">
                            <label for="discount_price" class="form-label small">
                                <i class="fas fa-tag"></i> Harga Diskon <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       class="form-control @error('discount_price') is-invalid @enderror"
                                       id="discount_price"
                                       name="discount_price"
                                       value="{{ old('discount_price', $product->harga_diskon) }}"
                                       placeholder="8000"
                                       min="100"
                                       max="{{ $product->harga_produk - 100 }}"
                                       step="100"
                                       required>
                            </div>
                            <div class="form-text small">
                                Max: Rp {{ number_format($product->harga_produk - 100, 0, ',', '.') }}
                            </div>
                            @error('discount_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Persentase Diskon (Auto Calculate) -->
                        <div class="mb-3">
                            <label for="discount_percentage" class="form-label small">
                                <i class="fas fa-percent"></i> Persentase Diskon
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number"
                                       class="form-control"
                                       id="discount_percentage"
                                       name="discount_percentage"
                                       value="{{ old('discount_percentage', $product->persentase_diskon) }}"
                                       placeholder="20"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       readonly>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text small">Otomatis dihitung dari harga diskon</div>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="mb-3">
                            <label for="start_date" class="form-label small">
                                <i class="fas fa-calendar-alt"></i> Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                                   id="start_date"
                                   name="start_date"
                                   value="{{ old('start_date', $product->tanggal_mulai_diskon ? $product->tanggal_mulai_diskon->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal Akhir -->
                        <div class="mb-3">
                            <label for="end_date" class="form-label small">
                                <i class="fas fa-calendar-check"></i> Tanggal Akhir <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   class="form-control form-control-sm @error('end_date') is-invalid @enderror"
                                   id="end_date"
                                   name="end_date"
                                   value="{{ old('end_date', $product->tanggal_akhir_diskon ? $product->tanggal_akhir_diskon->format('Y-m-d') : now()->addDays(7)->format('Y-m-d')) }}"
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-save"></i>
                            {{ $product->hasActiveDiscount() ? 'Update Diskon' : 'Tambah Diskon' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Helper Card -->
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-lightbulb"></i> Tips Edit Produk
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li class="mb-2"><strong>Nama Produk:</strong> Gunakan nama yang jelas dan mudah dicari pelanggan</li>
                        <li class="mb-2"><strong>Kategori:</strong> Pilih kategori yang sesuai agar mudah ditemukan</li>
                        <li class="mb-2"><strong>Harga:</strong> Pastikan harga sudah termasuk pajak jika ada</li>
                        <li class="mb-2"><strong>Stok:</strong> Update stok secara berkala untuk akurasi</li>
                        <li class="mb-2"><strong>Gambar:</strong> Gambar baru akan mengganti gambar lama</li>
                    </ul>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle"></i> Informasi Produk
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2"><strong>ID Produk:</strong> #{{ $product->id }}</p>
                    <p class="small mb-2"><strong>Dibuat:</strong> {{ $product->created_at->format('d M Y, H:i') }}</p>
                    <p class="small mb-0"><strong>Update Terakhir:</strong> {{ $product->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto calculate discount percentage
    const normalPrice = {{ $product->harga_produk }};
    const discountPriceInput = document.getElementById('discount_price');
    const discountPercentageInput = document.getElementById('discount_percentage');

    discountPriceInput.addEventListener('input', function() {
        const discountPrice = parseFloat(this.value) || 0;
        if (discountPrice > 0 && discountPrice < normalPrice) {
            const percentage = ((normalPrice - discountPrice) / normalPrice * 100).toFixed(2);
            discountPercentageInput.value = percentage;
        } else {
            discountPercentageInput.value = '';
        }
    });

    // Trigger calculation on page load if discount exists
    if (discountPriceInput.value) {
        discountPriceInput.dispatchEvent(new Event('input'));
    }

    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }
    });

    // Form validation with SweetAlert
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value;
        const price = document.getElementById('price').value;
        const stock = document.getElementById('stock').value;

        if (!name || !price || !stock) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Mohon lengkapi semua field yang wajib diisi!',
            });
            return false;
        }
    });

    // Discount form validation
    document.getElementById('discountForm').addEventListener('submit', function(e) {
        const discountPrice = parseFloat(document.getElementById('discount_price').value);
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);

        if (discountPrice >= normalPrice) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Harga Diskon Tidak Valid',
                text: 'Harga diskon harus lebih kecil dari harga normal!',
            });
            return false;
        }

        if (endDate < startDate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Tanggal akhir harus setelah atau sama dengan tanggal mulai!',
            });
            return false;
        }
    });
</script>
@endpush
