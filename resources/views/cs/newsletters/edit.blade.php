@extends('layouts.staff')

@section('title', 'Edit Newsletter')

@push('styles')
<style>
    /* Modern Edit Form Styling */
    .form-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e9ecef;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }

    .form-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .form-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .form-card-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-card-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 600;
        color: #212529;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label i {
        color: #667eea;
        font-size: 0.9rem;
    }

    .form-control {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        background-color: white;
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.15);
    }

    .invalid-feedback {
        display: block;
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .form-text {
        display: block;
        margin-top: 0.5rem;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .custom-control {
        position: relative;
        display: block;
        padding-left: 1.5rem;
    }

    .custom-control-input {
        position: absolute;
        left: 0;
        top: 0;
        margin: 0;
        cursor: pointer;
    }

    .custom-control-label {
        margin-bottom: 0;
        cursor: pointer;
        font-size: 0.9rem;
        color: #212529;
    }

    .form-divider {
        margin: 1.5rem 0;
        border: none;
        border-top: 1px solid #e9ecef;
    }

    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-group-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn {
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Sidebar Cards */
    .sidebar-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e9ecef;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .sidebar-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .sidebar-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .sidebar-card-header.warning {
        background: linear-gradient(135deg, #f6c23e 0%, #daa520 100%);
    }

    .sidebar-card-header.info {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .sidebar-card-header.success {
        background: linear-gradient(135deg, #1cc88a 0%, #169b6b 100%);
    }

    .sidebar-card-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sidebar-card-body {
        padding: 1.25rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 700;
        text-align: center;
        background: linear-gradient(135deg, #f6c23e 0%, #daa520 100%);
        color: white;
        margin-bottom: 1rem;
        width: 100%;
    }

    .info-row {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-bottom: 1rem;
    }

    .info-row:last-child {
        margin-bottom: 0;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.8rem;
        text-transform: uppercase;
    }

    .info-value {
        color: #212529;
        font-size: 0.95rem;
    }

    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tips-list li {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
        color: #495057;
    }

    .tips-list li:last-child {
        margin-bottom: 0;
    }

    .tips-list i {
        color: #4e73df;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    .preview-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem;
        min-height: 180px;
    }

    .preview-subject {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e9ecef;
    }

    .preview-content {
        font-size: 0.85rem;
        color: #495057;
        line-height: 1.5;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .preview-empty {
        color: #adb5bd;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
            justify-content: center;
        }

        .btn-group-actions {
            width: 100%;
            flex-direction: column;
        }

        .btn-group-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .form-card-body {
            padding: 1rem;
        }

        .sidebar-card-body {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('sidebar-menu')
    <a class="nav-link" href="{{ route('cs.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Layanan Pelanggan</div>

    <a class="nav-link" href="{{ route('cs.tickets.index') }}">
        <i class="bi bi-ticket-perforated"></i>
        <span>Ticketing</span>
    </a>

    <a class="nav-link active" href="{{ route('cs.newsletters.index') }}">
        <i class="bi bi-envelope-paper"></i>
        <span>Newsletter</span>
    </a>

    <a class="nav-link" href="{{ route('cs.dashboard') }}#subscribers">
        <i class="bi bi-people"></i>
        <span>Subscribers</span>
    </a>

    <div class="sidebar-divider"></div>

    <div class="menu-section-title">Akun</div>

    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 mb-1 text-gray-900">
                <i class="fas fa-edit"></i> Edit Newsletter
            </h1>
            <p class="text-muted mb-0">Update konten dan detail newsletter Anda</p>
        </div>
        <a href="{{ route('cs.newsletters.show', $newsletter->id_newsletter) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-left-danger rounded-lg mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terjadi Kesalahan</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Content -->
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Newsletter Form Card -->
            <div class="form-card">
                <div class="form-card-header">
                    <h6>
                        <i class="fas fa-pen-fancy"></i> Form Edit Newsletter
                    </h6>
                </div>
                <div class="form-card-body">
                    <form action="{{ route('cs.newsletters.update', $newsletter->id_newsletter) }}" method="POST" id="newsletterForm">
                        @csrf
                        @method('PUT')

                        <!-- Metode Pengiriman -->
                        <div class="form-group">
                            <label for="jenis_newsletter" class="form-label">
                                <i class="fas fa-paper-plane"></i> Metode Pengiriman
                            </label>
                            <select class="form-control @error('jenis_newsletter') is-invalid @enderror"
                                    id="jenis_newsletter"
                                    name="jenis_newsletter"
                                    required>
                                <option value="">-- Pilih Metode Pengiriman --</option>
                                <option value="mailchimp" {{ old('jenis_newsletter', $newsletter->jenis_newsletter ?? '') === 'mailchimp' ? 'selected' : '' }}>
                                    ✉️ Mailchimp (Email)
                                </option>
                                <option value="fonnte" {{ old('jenis_newsletter', $newsletter->jenis_newsletter ?? '') === 'fonnte' ? 'selected' : '' }}>
                                    💬 Fonnte (WhatsApp)
                                </option>
                                <option value="keduanya" {{ old('jenis_newsletter', $newsletter->jenis_newsletter ?? '') === 'keduanya' ? 'selected' : '' }}>
                                    🔄 Keduanya (Mailchimp + Fonnte)
                                </option>
                            </select>
                            <small class="form-text">Pilih metode pengiriman untuk newsletter ini</small>
                            @error('jenis_newsletter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Judul Newsletter -->
                        <div class="form-group">
                            <label for="judul" class="form-label">
                                <i class="fas fa-heading"></i> Judul Newsletter
                            </label>
                            <input type="text"
                                   class="form-control @error('judul') is-invalid @enderror"
                                   id="judul"
                                   name="judul"
                                   value="{{ old('judul', $newsletter->judul) }}"
                                   placeholder="Contoh: Promo Flash Sale - Diskon 40%"
                                   required>
                            <small class="form-text">Judul internal untuk identifikasi newsletter di sistem</small>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subjek Email -->
                        <div class="form-group">
                            <label for="subjek_email" class="form-label">
                                <i class="fas fa-envelope"></i> Subjek Email
                            </label>
                            <input type="text"
                                   class="form-control @error('subjek_email') is-invalid @enderror"
                                   id="subjek_email"
                                   name="subjek_email"
                                   value="{{ old('subjek_email', $newsletter->subjek_email) }}"
                                   placeholder="Contoh: 🎉 Flash Sale! Diskon 40% - Jangan Sampai Ketinggalan"
                                   required>
                            <small class="form-text">Subjek yang akan tampil di inbox email pelanggan</small>
                            @error('subjek_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konten Email -->
                        <div class="form-group">
                            <label for="konten_email" class="form-label">
                                <i class="fas fa-align-left"></i> Konten Email
                            </label>
                            <textarea class="form-control @error('konten_email') is-invalid @enderror"
                                      id="konten_email"
                                      name="konten_email"
                                      rows="10"
                                      placeholder="Tulis konten email Anda di sini..."
                                      required>{{ old('konten_email', $newsletter->konten_email) }}</textarea>
                            <small class="form-text">Konten utama email dalam format teks biasa</small>
                            @error('konten_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- HTML Content Section -->
                        <div class="form-group">
                            <div class="custom-control">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="useHtmlEditor"
                                       {{ $newsletter->konten_html ? 'checked' : '' }}>
                                <label class="custom-control-label" for="useHtmlEditor">
                                    <i class="fas fa-code me-1"></i> Gunakan konten HTML custom (Opsional)
                                </label>
                            </div>
                        </div>

                        <!-- Konten HTML -->
                        <div class="form-group" id="htmlEditorGroup" style="display: {{ $newsletter->konten_html ? 'block' : 'none' }};">
                            <label for="konten_html" class="form-label">
                                <i class="fas fa-code"></i> Konten HTML
                            </label>
                            <textarea class="form-control @error('konten_html') is-invalid @enderror"
                                      id="konten_html"
                                      name="konten_html"
                                      rows="8"
                                      placeholder="&lt;html&gt;...&lt;/html&gt;">{{ old('konten_html', $newsletter->konten_html) }}</textarea>
                            <small class="form-text">Jika tidak diisi, sistem akan otomatis membuat HTML dari konten teks di atas</small>
                            @error('konten_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-divider"></div>
                        <div class="action-buttons">
                            <a href="{{ route('cs.newsletters.show', $newsletter->id_newsletter) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <div class="btn-group-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="sidebar-card">
                <div class="sidebar-card-header warning">
                    <h6>
                        <i class="fas fa-info-circle"></i> Status Newsletter
                    </h6>
                </div>
                <div class="sidebar-card-body">
                    <div class="status-badge">
                        DRAFT
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dibuat Oleh</span>
                        <span class="info-value">{{ $newsletter->creator->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal Dibuat</span>
                        <span class="info-value">{{ $newsletter->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Terakhir Diupdate</span>
                        <span class="info-value">{{ $newsletter->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="sidebar-card">
                <div class="sidebar-card-header info">
                    <h6>
                        <i class="fas fa-lightbulb"></i> Tips Edit
                    </h6>
                </div>
                <div class="sidebar-card-body">
                    <ul class="tips-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Review kembali subjek dan konten sebelum menyimpan</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Pastikan tidak ada typo atau kesalahan penulisan</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Gunakan preview untuk melihat hasil akhir</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Newsletter tetap berstatus draft sampai dikirim</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="sidebar-card">
                <div class="sidebar-card-header success">
                    <h6>
                        <i class="fas fa-eye"></i> Preview
                    </h6>
                </div>
                <div class="sidebar-card-body">
                    <div class="preview-box">
                        <div class="preview-subject" id="preview-subject">
                            {{ $newsletter->subjek_email ?: '(Subjek kosong)' }}
                        </div>
                        <div class="preview-content" id="preview-content">
                            {{ Str::limit($newsletter->konten_email, 200) ?: '(Konten kosong)' }}
                        </div>
                    </div>
                    <small class="form-text mt-2">Preview akan update saat Anda mengetik</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle HTML editor visibility
    const useHtmlCheckbox = document.getElementById('useHtmlEditor');
    const htmlEditorGroup = document.getElementById('htmlEditorGroup');

    useHtmlCheckbox.addEventListener('change', function() {
        htmlEditorGroup.style.display = this.checked ? 'block' : 'none';
    });

    // Live preview update for subjek
    document.getElementById('subjek_email').addEventListener('input', function() {
        const previewSubject = document.getElementById('preview-subject');
        previewSubject.textContent = this.value || '(Subjek kosong)';
    });

    // Live preview update for konten
    document.getElementById('konten_email').addEventListener('input', function() {
        const content = this.value.substring(0, 200);
        const previewContent = document.getElementById('preview-content');
        previewContent.textContent = content || '(Konten kosong)';
    });
</script>
@endpush
