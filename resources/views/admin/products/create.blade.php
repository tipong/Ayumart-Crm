@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-primary"></i> Tambah Produk Baru
        </h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit"></i> Form Produk
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama Produk -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag"></i> Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Masukkan nama produk"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="mb-3">
                            <label for="category" class="form-label">
                                <i class="fas fa-list"></i> Kategori <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror"
                                    id="category"
                                    name="category"
                                    required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id_jenis }}" {{ old('category') == $cat->id_jenis ? 'selected' : '' }}>
                                        {{ $cat->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Harga & Stok -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Harga <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number"
                                           class="form-control @error('price') is-invalid @enderror"
                                           id="price"
                                           name="price"
                                           value="{{ old('price') }}"
                                           min="0"
                                           step="100"
                                           placeholder="0"
                                           required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">
                                    <i class="fas fa-boxes"></i> Stok <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('stock') is-invalid @enderror"
                                       id="stock"
                                       name="stock"
                                       value="{{ old('stock') }}"
                                       min="0"
                                       placeholder="0"
                                       required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left"></i> Deskripsi
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Masukkan deskripsi produk (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gambar -->
                        <div class="mb-3">
                            <label for="image" class="form-label">
                                <i class="fas fa-image"></i> Gambar Produk
                            </label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                            </small>

                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px;">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Panduan
                    </h6>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-lightbulb text-warning"></i> Tips:</h6>
                    <ul class="small">
                        <li>Pastikan nama produk jelas dan mudah dipahami</li>
                        <li>Pilih kategori yang sesuai</li>
                        <li>Harga yang dimasukkan adalah harga satuan</li>
                        <li>Update stok secara berkala</li>
                        <li>Tambahkan deskripsi yang informatif</li>
                        <li>Gunakan gambar berkualitas baik</li>
                    </ul>

                    <hr>

                    <h6><i class="fas fa-exclamation-triangle text-danger"></i> Perhatian:</h6>
                    <ul class="small">
                        <li>Field dengan tanda * wajib diisi</li>
                        <li>Gambar maksimal 2MB</li>
                        <li>Harga tidak boleh negatif</li>
                        <li>Stok minimal 0</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('imagePreview');

        reader.onload = function() {
            preview.src = reader.result;
            previewContainer.style.display = 'block';
        }

        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endpush
@endsection
