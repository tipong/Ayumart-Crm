@extends('layouts.pelanggan')

@section('title', 'Review Saya')

@push('styles')
<style>
    .review-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: white;
    }

    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }

    .product-image-wrapper {
        width: 100%;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .product-image-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .star-rating {
        color: #ffc107;
        font-size: 1.2rem;
    }

    .star-rating .bi-star {
        color: #e0e0e0;
    }

    .review-text {
        line-height: 1.6;
        color: #555;
    }

    .review-photo {
        border-radius: 8px;
        transition: all 0.3s;
        cursor: pointer;
    }

    .review-photo:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .action-buttons .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state i {
        font-size: 5rem;
        color: #d0d0d0;
        margin-bottom: 1.5rem;
    }

    /* Star Rating Input */
    .star-rating-input .stars {
        font-size: 2.5rem;
        cursor: pointer;
        display: flex;
        gap: 0.5rem;
    }

    .star-rating-input .stars i {
        color: #ddd;
        transition: all 0.2s;
    }

    .star-rating-input .stars i.active {
        color: #ffc107;
    }

    .star-rating-input .stars i:hover {
        color: #ffc107;
        transform: scale(1.2);
    }

    /* Pagination */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        margin: 0 4px;
        border: none;
        background: white;
        color: #3F4F44;
        font-weight: 500;
    }

    .pagination .page-link:hover {
        background-color: #3F4F44;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .pagination .page-item.active .page-link {
        background-color: #3F4F44;
        border-color: #3F4F44;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        background: #e9ecef;
        color: #6c757d;
    }

    .badge-date {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .order-info {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border-left: 4px solid #3F4F44;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-white">
                            <h2 class="mb-2 fw-bold">
                                <i class="bi bi-star-fill"></i>
                                Review Saya
                            </h2>
                            <p class="mb-0 opacity-75">Kelola semua review yang sudah Anda berikan</p>
                        </div>
                        <a href="{{ route('pelanggan.orders') }}" class="btn btn-light">
                            <i class="bi bi-arrow-left"></i> Kembali ke Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Reviews List -->
    <div class="row">
        @forelse($reviews as $review)
        <div class="col-12 mb-4">
            <div class="card review-card shadow-sm">
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Product Image -->
                        <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                            <div class="product-image-wrapper">
                                @if($review->product->foto_produk)
                                    <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($review->product->foto_produk, 150, 150) }}"
                                         alt="{{ $review->product->nama_produk }}">
                                @else
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                @endif
                            </div>
                        </div>

                        <!-- Review Details -->
                        <div class="col-lg-10 col-md-9">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-2 fw-bold">{{ $review->product->nama_produk }}</h5>
                                    <div class="star-rating mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-2 text-muted fw-semibold">{{ $review->rating }}.0</span>
                                    </div>
                                </div>
                                <span class="badge badge-date">
                                    <i class="bi bi-calendar"></i>
                                    {{ $review->created_at->format('d M Y') }}
                                </span>
                            </div>

                            <!-- Review Text -->
                            <p class="review-text mb-3">"{{ $review->review }}"</p>

                            <!-- Review Photo -->
                            @if($review->foto_review)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $review->foto_review) }}"
                                     alt="Review Photo"
                                     class="review-photo img-thumbnail"
                                     style="max-height: 200px; max-width: 100%;"
                                     onclick="window.open(this.src, '_blank')">
                            </div>
                            @endif

                            <!-- Order Info -->
                            <div class="order-info mb-3">
                                <i class="bi bi-receipt-cutoff"></i>
                                <strong>Pesanan:</strong> {{ $review->transaction->kode_transaksi ?? 'N/A' }}
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons d-flex flex-wrap gap-2">
                                <button type="button"
                                        class="btn btn-primary"
                                        onclick="showEditModal({{ $review->id_review }}, {{ $review->rating }}, '{{ addslashes($review->review) }}', '{{ $review->foto_review ? asset('storage/' . $review->foto_review) : '' }}')">
                                    <i class="bi bi-pencil-square"></i> Edit Review
                                </button>
                                <form action="{{ route('pelanggan.review.destroy', $review->id_review) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus review ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash3"></i> Hapus
                                    </button>
                                </form>
                                <a href="{{ route('product.show', $review->product->id_produk) }}"
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-box-seam"></i> Lihat Produk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card review-card shadow-sm">
                <div class="card-body empty-state">
                    <i class="bi bi-star-half"></i>
                    <h4 class="text-muted mb-3">Belum Ada Review</h4>
                    <p class="text-muted mb-4">Anda belum memberikan review untuk produk apapun.<br>Mulai berikan review setelah barang Anda diterima!</p>
                    <a href="{{ route('pelanggan.orders') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-bag-check"></i> Lihat Pesanan Saya
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $reviews->links() }}
    </div>
    @endif
</div>

<!-- Edit Review Modal -->
<div class="modal fade" id="editReviewModal" tabindex="-1" aria-labelledby="editReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <h5 class="modal-title fw-bold" id="editReviewModalLabel">
                    <i class="bi bi-pencil-square"></i> Edit Review Anda
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReviewForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Rating -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-star-fill text-warning"></i> Rating
                            <span class="text-danger">*</span>
                        </label>
                        <div class="star-rating-input">
                            <input type="hidden" name="rating" id="edit_rating" value="5">
                            <div class="stars justify-content-center justify-content-md-start" id="edit_stars">
                                <i class="bi bi-star-fill" data-rating="1"></i>
                                <i class="bi bi-star-fill" data-rating="2"></i>
                                <i class="bi bi-star-fill" data-rating="3"></i>
                                <i class="bi bi-star-fill" data-rating="4"></i>
                                <i class="bi bi-star-fill" data-rating="5"></i>
                            </div>
                            <small class="text-muted d-block mt-2">Klik bintang untuk memberikan rating</small>
                        </div>
                    </div>

                    <!-- Review Text -->
                    <div class="mb-4">
                        <label for="edit_review_text" class="form-label fw-bold">
                            <i class="bi bi-chat-left-text"></i> Review
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="edit_review_text"
                                  name="review"
                                  rows="5"
                                  maxlength="1000"
                                  required
                                  placeholder="Ceritakan pengalaman Anda dengan produk ini..."
                                  style="border-radius: 12px;"></textarea>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">Maksimal 1000 karakter</small>
                            <small class="text-muted"><span id="charCount">0</span>/1000</small>
                        </div>
                    </div>

                    <!-- Current Photo -->
                    <div class="mb-4" id="currentPhotoDiv" style="display: none;">
                        <label class="form-label fw-bold">
                            <i class="bi bi-image"></i> Foto Saat Ini
                        </label>
                        <div class="text-center">
                            <img id="currentPhoto" src="" alt="Current Photo" class="img-thumbnail rounded" style="max-height: 200px;">
                            <p class="text-muted small mt-2">Foto akan diganti jika Anda upload foto baru</p>
                        </div>
                    </div>

                    <!-- Upload New Photo -->
                    <div class="mb-3">
                        <label for="edit_foto_review" class="form-label fw-bold">
                            <i class="bi bi-camera"></i> Upload Foto Baru (Opsional)
                        </label>
                        <input type="file"
                               class="form-control"
                               id="edit_foto_review"
                               name="foto_review"
                               accept="image/jpeg,image/jpg,image/png"
                               style="border-radius: 12px;">
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Format: JPEG, JPG, PNG. Maksimal 2MB
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Edit Review Modal
function showEditModal(reviewId, rating, reviewText, photoUrl) {
    // Set form action
    document.getElementById('editReviewForm').action = `/reviews/${reviewId}`;

    // Set rating
    document.getElementById('edit_rating').value = rating;
    setEditStarRating(rating);

    // Set review text and update character count
    const textarea = document.getElementById('edit_review_text');
    textarea.value = reviewText;
    updateCharCount();

    // Show current photo if exists
    if (photoUrl) {
        document.getElementById('currentPhotoDiv').style.display = 'block';
        document.getElementById('currentPhoto').src = photoUrl;
    } else {
        document.getElementById('currentPhotoDiv').style.display = 'none';
    }

    // Show modal with animation
    const modal = new bootstrap.Modal(document.getElementById('editReviewModal'));
    modal.show();
}

function setEditStarRating(rating) {
    const stars = document.querySelectorAll('#edit_stars i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

function updateCharCount() {
    const textarea = document.getElementById('edit_review_text');
    const charCount = document.getElementById('charCount');
    if (textarea && charCount) {
        charCount.textContent = textarea.value.length;
    }
}

// Star rating interaction for edit modal
document.addEventListener('DOMContentLoaded', function() {
    const editStars = document.querySelectorAll('#edit_stars i');

    editStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.getElementById('edit_rating').value = rating;
            setEditStarRating(rating);
        });

        // Add hover effect
        star.addEventListener('mouseenter', function() {
            const rating = this.getAttribute('data-rating');
            editStars.forEach((s, index) => {
                if (index < rating) {
                    s.style.transform = 'scale(1.2)';
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            editStars.forEach(s => {
                s.style.transform = 'scale(1)';
            });
        });
    });

    // Character count for textarea
    const textarea = document.getElementById('edit_review_text');
    if (textarea) {
        textarea.addEventListener('input', updateCharCount);
    }

    // Auto-dismiss alerts after 5 seconds with fade effect
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Add smooth scroll to top when page loads
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>
@endpush
@endsection
