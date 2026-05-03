@extends('layouts.pelanggan')

@section('title', 'Berikan Review')

@push('styles')
<style>
    .rating-stars {
        font-size: 2rem;
        cursor: pointer;
        color: #ddd;
    }

    .rating-stars i {
        transition: color 0.2s;
    }

    .rating-stars i:hover,
    .rating-stars i.active {
        color: #ffc107;
    }

    .preview-image {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back Button -->
            <a href="{{ route('pelanggan.orders') }}" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Kembali ke Pesanan
            </a>

            <!-- Alert Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-star"></i> Berikan Review</h4>
                </div>
                <div class="card-body">
                    <!-- Order Info -->
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Kode Pesanan:</strong> {{ $order->kode_transaksi }}
                            </div>
                            <div>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> {{ ucfirst(str_replace('_', ' ', $order->status_pembayaran)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    @if($product->foto_produk)
                                        <img src="{{ asset('storage/' . $product->foto_produk) }}"
                                             alt="{{ $product->nama_produk }}"
                                             class="img-fluid rounded">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}"
                                             alt="No Image"
                                             class="img-fluid rounded">
                                    @endif
                                </div>
                                <div class="col-md-9">
                                    <h5>{{ $product->nama_produk }}</h5>
                                    <p class="text-muted mb-2">{{ $product->kode_produk }}</p>
                                    @if($product->deskripsi_produk)
                                        <p class="text-muted small">{{ Str::limit($product->deskripsi_produk, 150) }}</p>
                                    @endif
                                    <p class="mb-0">
                                        <strong>Harga:</strong>
                                        <span class="text-primary">Rp {{ number_format($product->getCurrentPrice(), 0, ',', '.') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Form -->
                    <form action="{{ route('pelanggan.review.store') }}" method="POST" enctype="multipart/form-data" id="reviewForm">
                        @csrf
                        <input type="hidden" name="id_transaksi" value="{{ $order->id_transaksi }}">
                        <input type="hidden" name="id_produk" value="{{ $product->id_produk }}">
                        <input type="hidden" name="rating" id="ratingInput" value="0">

                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-star"></i> Rating Produk <span class="text-danger">*</span>
                            </label>
                            <div class="rating-stars" id="ratingStars">
                                <i class="bi bi-star" data-rating="1"></i>
                                <i class="bi bi-star" data-rating="2"></i>
                                <i class="bi bi-star" data-rating="3"></i>
                                <i class="bi bi-star" data-rating="4"></i>
                                <i class="bi bi-star" data-rating="5"></i>
                            </div>
                            <div class="form-text" id="ratingText">Pilih rating Anda</div>
                            @error('rating')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Review Text -->
                        <div class="mb-4">
                            <label for="review" class="form-label fw-bold">
                                <i class="bi bi-chat-left-text"></i> Review Anda <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('review') is-invalid @enderror"
                                      id="review"
                                      name="review"
                                      rows="5"
                                      maxlength="1000"
                                      placeholder="Ceritakan pengalaman Anda dengan produk ini...&#10;&#10;Contoh:&#10;- Kualitas produk sangat bagus&#10;- Pengiriman cepat&#10;- Sesuai dengan deskripsi&#10;- Dll."
                                      required>{{ old('review') }}</textarea>
                            <div class="form-text">
                                <span id="charCount">0</span>/1000 karakter (minimal 10 karakter)
                            </div>
                            @error('review')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Photo Upload (Optional) -->
                        <div class="mb-4">
                            <label for="foto_review" class="form-label fw-bold">
                                <i class="bi bi-camera"></i> Foto Produk (Opsional)
                            </label>
                            <input class="form-control @error('foto_review') is-invalid @enderror"
                                   type="file"
                                   id="foto_review"
                                   name="foto_review"
                                   accept="image/jpeg,image/jpg,image/png">
                            <div class="form-text">Format: JPG, JPEG, PNG. Maksimal 2MB</div>
                            @error('foto_review')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img src="" alt="Preview" class="preview-image">
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Review Anda akan ditampilkan di halaman produk</li>
                                <li>Berikan review yang jujur dan membantu pembeli lain</li>
                                <li>Hindari kata-kata kasar atau tidak pantas</li>
                            </ul>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pelanggan.orders') }}" class="btn btn-secondary">
                                <i class="bi bi-x"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-send"></i> Kirim Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Rating Stars
    const ratingStars = document.querySelectorAll('.rating-stars i');
    const ratingInput = document.getElementById('ratingInput');
    const ratingText = document.getElementById('ratingText');

    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;

            // Update star display
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill', 'active');
                } else {
                    s.classList.remove('bi-star-fill', 'active');
                    s.classList.add('bi-star');
                }
            });

            // Update text
            const ratingTexts = ['', 'Buruk', 'Kurang', 'Cukup', 'Bagus', 'Sangat Bagus'];
            ratingText.textContent = ratingTexts[rating];
            ratingText.className = 'form-text text-warning fw-bold';
        });

        // Hover effect
        star.addEventListener('mouseenter', function() {
            const rating = this.getAttribute('data-rating');
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            const currentRating = ratingInput.value;
            ratingStars.forEach((s, index) => {
                if (index >= currentRating) {
                    s.classList.remove('active');
                }
            });
        });
    });

    // Character count
    const reviewTextarea = document.getElementById('review');
    const charCount = document.getElementById('charCount');

    reviewTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Image preview
    const fotoInput = document.getElementById('foto_review');
    const imagePreview = document.getElementById('imagePreview');

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.querySelector('img').src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });

    // Form validation
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        const rating = parseInt(ratingInput.value);
        const review = reviewTextarea.value.trim();

        if (rating === 0) {
            e.preventDefault();
            alert('Mohon berikan rating untuk produk ini');
            ratingStars[0].scrollIntoView({ behavior: 'smooth' });
            return false;
        }

        if (review.length < 10) {
            e.preventDefault();
            alert('Review minimal 10 karakter');
            reviewTextarea.focus();
            return false;
        }

        // Show loading
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
    });

    // Initialize char count
    charCount.textContent = reviewTextarea.value.length;
</script>
@endpush
