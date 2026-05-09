@extends('layouts.staff')

@section('title', 'Buat Newsletter Baru')

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
        @php
            $unreadCount = \App\Models\Ticket::where('is_read', false)->count();
        @endphp
        @if($unreadCount > 0)
            <span class="badge bg-danger ms-auto">{{ $unreadCount }}</span>
        @endif
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
                <i class="fas fa-compose"></i> Buat Newsletter Baru
            </h1>
            <p class="text-muted mb-0">Kelola kampanye email dan WhatsApp Anda dengan mudah</p>
        </div>
        <a href="{{ route('cs.newsletters.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-left-danger" role="alert">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="alert-content">
                <h6 class="alert-heading mb-2">Terjadi Kesalahan</h6>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Newsletter Form -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('cs.newsletters.store') }}" method="POST" id="newsletterForm">
                        @csrf

                        <!-- Step 1: Pilih Metode Pengiriman -->
                        <div class="step-group mb-5">
                            <div class="step-header mb-3">
                                <h5 class="fw-bold text-dark">
                                    <span class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">1</span>
                                    Metode Pengiriman
                                </h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="delivery-option-card position-relative" data-method="mailchimp">
                                        <input type="radio" name="jenis_newsletter" value="mailchimp" id="method_mailchimp" class="delivery-radio" {{ old('jenis_newsletter') === 'mailchimp' ? 'checked' : '' }} required>
                                        <label for="method_mailchimp" class="w-100 h-100 cursor-pointer">
                                            <div class="p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-envelope text-primary fa-lg me-2"></i>
                                                    <h6 class="mb-0">Email</h6>
                                                </div>
                                                <p class="text-muted small mb-2">Kirim melalui Mailchimp</p>
                                                <div class="badge bg-info text-white small">
                                                    {{ $subscribersCount }} Subscriber
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="delivery-option-card position-relative" data-method="fonnte" @if(!config('services.fonnte.api_key')) style="opacity: 0.5; pointer-events: none;" @endif>
                                        <input type="radio" name="jenis_newsletter" value="fonnte" id="method_fonnte" class="delivery-radio" {{ old('jenis_newsletter') === 'fonnte' ? 'checked' : '' }} @if(!config('services.fonnte.api_key')) disabled @endif>
                                        <label for="method_fonnte" class="w-100 h-100 cursor-pointer">
                                            <div class="p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fab fa-whatsapp text-success fa-lg me-2"></i>
                                                    <h6 class="mb-0">WhatsApp</h6>
                                                </div>
                                                <p class="text-muted small mb-2">Kirim melalui Fonnte</p>
                                                @if(config('services.fonnte.api_key'))
                                                    <div class="badge bg-success text-white small">
                                                        {{ $fonteCount }} Subscriber
                                                    </div>
                                                @else
                                                    <div class="badge bg-warning text-dark small">
                                                        <i class="fas fa-exclamation-triangle"></i> Belum terkonfigurasi
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="delivery-option-card position-relative" data-method="keduanya" @if(!config('services.fonnte.api_key')) style="opacity: 0.5; pointer-events: none;" @endif>
                                        <input type="radio" name="jenis_newsletter" value="keduanya" id="method_keduanya" class="delivery-radio" {{ old('jenis_newsletter') === 'keduanya' ? 'checked' : '' }} @if(!config('services.fonnte.api_key')) disabled @endif>
                                        <label for="method_keduanya" class="w-100 h-100 cursor-pointer">
                                            <div class="p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-paper-plane text-success fa-lg me-2"></i>
                                                    <h6 class="mb-0">Email + WhatsApp</h6>
                                                </div>
                                                <p class="text-muted small mb-2">Kirim ke kedua platform</p>
                                                @if(config('services.fonnte.api_key'))
                                                    <div class="badge bg-success text-white small">
                                                        {{ $subscribersCount + $fonteCount }} Total
                                                    </div>
                                                @else
                                                    <div class="badge bg-warning text-dark small">
                                                        <i class="fas fa-exclamation-triangle"></i> Fonnte belum terkonfigurasi
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('jenis_newsletter')
                                <div class="alert alert-danger small mt-2 mb-0">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Step 2: Target Penerima (Filter) -->
                        <div class="step-group mb-5" id="target-filter-group">
                            <div class="step-header mb-3">
                                <h5 class="fw-bold text-dark">
                                    <span class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">2</span>
                                    Filter Target Penerima
                                </h5>
                                <p class="text-muted small ms-3">Opsional - Biarkan kosong untuk mengirim ke semua subscriber</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-5 text-dark mb-3">Pilih Tier Membership</label>
                                    <div class="row g-2" id="tierFilterContainer">
                                        <div class="col-md-6">
                                            <div class="form-check tier-checkbox-card">
                                                <input type="checkbox" name="target_tiers[]" value="bronze" id="tier_bronze" class="form-check-input tier-filter">
                                                <label class="form-check-label tier-label w-100" for="tier_bronze">
                                                    <div class="tier-badge bg-light-bronze">
                                                        <i class="fas fa-medal text-warning"></i>
                                                    </div>
                                                    <div class="tier-info">
                                                        <div class="tier-name">Bronze</div>
                                                        <div class="tier-count text-muted small">{{ $availableTiers['bronze']['count'] }} subscribers</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-check tier-checkbox-card">
                                                <input type="checkbox" name="target_tiers[]" value="silver" id="tier_silver" class="form-check-input tier-filter">
                                                <label class="form-check-label tier-label w-100" for="tier_silver">
                                                    <div class="tier-badge bg-light-silver">
                                                        <i class="fas fa-medal text-secondary"></i>
                                                    </div>
                                                    <div class="tier-info">
                                                        <div class="tier-name">Silver</div>
                                                        <div class="tier-count text-muted small">{{ $availableTiers['silver']['count'] }} subscribers</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-check tier-checkbox-card">
                                                <input type="checkbox" name="target_tiers[]" value="gold" id="tier_gold" class="form-check-input tier-filter">
                                                <label class="form-check-label tier-label w-100" for="tier_gold">
                                                    <div class="tier-badge bg-light-gold">
                                                        <i class="fas fa-medal" style="color: #FFD700;"></i>
                                                    </div>
                                                    <div class="tier-info">
                                                        <div class="tier-name">Gold</div>
                                                        <div class="tier-count text-muted small">{{ $availableTiers['gold']['count'] }} subscribers</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-check tier-checkbox-card">
                                                <input type="checkbox" name="target_tiers[]" value="platinum" id="tier_platinum" class="form-check-input tier-filter">
                                                <label class="form-check-label tier-label w-100" for="tier_platinum">
                                                    <div class="tier-badge bg-light-platinum">
                                                        <i class="fas fa-medal text-info"></i>
                                                    </div>
                                                    <div class="tier-info">
                                                        <div class="tier-name">Platinum</div>
                                                        <div class="tier-count text-muted small">{{ $availableTiers['platinum']['count'] }} subscribers</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">Filter ini hanya berlaku untuk metode pengiriman WhatsApp (Fonnte)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Konten Newsletter -->
                        <div class="step-group mb-5">
                            <div class="step-header mb-3">
                                <h5 class="fw-bold text-dark">
                                    <span class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">3</span>
                                    Konten Newsletter
                                </h5>
                            </div>

                            <!-- Judul Newsletter -->
                            <div class="mb-4">
                                <label for="judul" class="form-label fw-5 text-dark">
                                    Judul <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg @error('judul') is-invalid @enderror"
                                       id="judul"
                                       name="judul"
                                       value="{{ old('judul') }}"
                                       placeholder="Contoh: Promo Flash Sale - Diskon 40%"
                                       required>
                                <small class="form-text text-muted">
                                    Judul internal untuk identifikasi newsletter di sistem
                                </small>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Subjek Email -->
                            <div class="mb-4">
                                <label for="subjek_email" class="form-label fw-5 text-dark">
                                    Subjek Email <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg @error('subjek_email') is-invalid @enderror"
                                       id="subjek_email"
                                       name="subjek_email"
                                       value="{{ old('subjek_email') }}"
                                       placeholder="Contoh: 🎉 Flash Sale! Diskon 40% - Jangan Sampai Ketinggalan"
                                       required>
                                <small class="form-text text-muted">
                                    Subjek yang akan tampil di inbox email pelanggan
                                </small>
                                @error('subjek_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Konten Email -->
                            <div class="mb-4">
                                <label for="konten_email" class="form-label fw-5 text-dark">
                                    Konten <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('konten_email') is-invalid @enderror"
                                          id="konten_email"
                                          name="konten_email"
                                          rows="10"
                                          placeholder="Tulis konten newsletter Anda di sini..."
                                          required
                                          style="min-height: 200px;">{{ old('konten_email') }}</textarea>
                                <small class="form-text text-muted d-block mt-2">
                                    💡 Tip: Gunakan bahasa yang personal dan jangan lupa tambahkan call-to-action yang jelas
                                </small>
                                @error('konten_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 mt-5 pt-4 border-top">
                            <a href="{{ route('cs.newsletters.index') }}" class="btn btn-lg btn-outline-secondary rounded-pill flex-grow-1">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary rounded-pill flex-grow-1">
                                <i class="fas fa-save"></i> Simpan sebagai Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar: Quick Stats & Tips -->
        <div class="col-lg-4">
            <!-- Summary Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-chart-pie text-primary me-2"></i> Ringkasan
                    </h6>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <p class="text-muted small mb-1">Email (Mailchimp)</p>
                            <h5 class="fw-bold text-primary mb-0">{{ $subscribersCount }}</h5>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-envelope text-primary fa-2x opacity-50"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <p class="text-muted small mb-1">WhatsApp (Fonnte)</p>
                            <h5 class="fw-bold text-success mb-0">{{ $fonteCount }}</h5>
                        </div>
                        <div class="text-center">
                            <i class="fab fa-whatsapp text-success fa-2x opacity-50"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Subscribers</p>
                            <h5 class="fw-bold text-info mb-0">{{ $subscribersCount + $fonteCount }}</h5>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-users text-info fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Membership Tiers Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-medal text-warning me-2"></i> Tier Membership
                    </h6>

                    <div class="tier-stats">
                        @foreach(['bronze' => 'warning', 'silver' => 'secondary', 'gold' => 'warning', 'platinum' => 'info'] as $tier => $color)
                            @php $tierData = $availableTiers[$tier]; @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <small class="text-muted">{{ $tierData['label'] }}</small>
                                <span class="badge bg-{{ $color }} text-white">{{ $tierData['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Templates Card -->
            <div class="card shadow-sm border-0 rounded-3 border-left-primary">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-copy text-primary me-2"></i> Template Cepat
                    </h6>
                    <div class="btn-group-vertical w-100" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('promo')">
                            <i class="fas fa-tag"></i> Promo / Flash Sale
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('announcement')">
                            <i class="fas fa-bullhorn"></i> Pengumuman Penting
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('newsletter')">
                            <i class="fas fa-newspaper"></i> Newsletter Bulanan
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('welcome')">
                            <i class="fas fa-hand-spock"></i> Sambutan Welcome
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('product-launch')">
                            <i class="fas fa-rocket"></i> Produk Baru
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('abandoned-cart')">
                            <i class="fas fa-shopping-cart"></i> Abandoned Cart
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('survey')">
                            <i class="fas fa-poll"></i> Survey / Feedback
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('birthday')">
                            <i class="fas fa-birthday-cake"></i> Ucapan Ulang Tahun
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 text-start" onclick="loadTemplate('exclusive-offer')">
                            <i class="fas fa-gem"></i> Penawaran Eksklusif
                        </button>
                        <div class="btn-divider my-2"></div>
                        <div class="template-category-label text-muted small mb-2">Kampanye Musiman</div>
                        <button type="button" class="btn btn-outline-success btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('seasonal-new-year')">
                            <i class="fas fa-snowflake"></i> Tahun Baru 2026
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('seasonal-valentine')">
                            <i class="fas fa-heart"></i> Valentine Special
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('seasonal-holiday')">
                            <i class="fas fa-gift"></i> Holiday Sale
                        </button>
                        <div class="btn-divider my-2"></div>
                        <div class="template-category-label text-muted small mb-2">Retensi Pelanggan</div>
                        <button type="button" class="btn btn-outline-info btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('customer-winback')">
                            <i class="fas fa-undo"></i> Win-back Campaign
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('vip-upgrade')">
                            <i class="fas fa-arrow-up"></i> VIP Upgrade
                        </button>
                        <div class="btn-divider my-2"></div>
                        <div class="template-category-label text-muted small mb-2">Content & Event</div>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('content-tips')">
                            <i class="fas fa-lightbulb"></i> Tips & Trik
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 mb-2 text-start" onclick="loadTemplate('event-webinar')">
                            <i class="fas fa-video"></i> Webinar Invitation
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 text-start" onclick="loadTemplate('product-review')">
                            <i class="fas fa-star"></i> Produk Review
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Template Data
    const templates = {
        promo: {
            judul: 'Promo Flash Sale - Diskon 40%',
            subjek: '🎉 Flash Sale! Diskon 40% - Jangan Sampai Ketinggalan',
            konten: `Halo Pelanggan Setia!

FLASH SALE ALERT! ⚡

Dapatkan diskon 40% untuk SEMUA PRODUK!
Berlaku HANYA 24 JAM - jangan sampai ketinggalan!

🎁 Bonus:
- Gratis ongkir untuk pembelian di atas Rp 100.000
- Cashback 10% dengan kartu kredit
- Bonus voucher Rp 50.000 untuk pelanggan setia

Belanja sekarang: https://ayumart.com

Salam hangat,
Tim Kami`
        },
        announcement: {
            judul: 'Pengumuman Penting - Update Layanan',
            subjek: '📢 Pengumuman Penting dari Kami',
            konten: `Halo Pelanggan Setia,

Kami ingin menginformasikan beberapa update penting:

✨ Jam Operasional Baru
Mulai Senin, 15 Januari 2026:
Senin - Jumat: 08:00 - 20:00
Sabtu - Minggu: 09:00 - 18:00

📦 Gratis Ongkir
Nikmati gratis ongkir untuk semua pesanan!

💳 Metode Pembayaran Baru
Kini kami terima pembayaran via QRIS dan e-wallet.

Terima kasih atas kepercayaan Anda!

Salam,
Tim Kami`
        },
        newsletter: {
            judul: 'Newsletter Bulanan - Januari 2026',
            subjek: '📰 Newsletter Januari 2026 - Update Terbaru',
            konten: `Halo!

Selamat datang di newsletter bulanan kami!

📊 Highlight Bulan Ini:
• Produk terlaris: [Nama Produk]
• Total pelanggan baru: 500+
• Rating kepuasan: 4.8/5.0

🎯 Promo Bulan Depan:
Bersiaplah untuk promo Valentine dengan diskon hingga 50%!

💡 Tips Belanja Hemat:
1. Manfaatkan membership untuk poin reward
2. Belanja saat flash sale
3. Gunakan voucher gratis ongkir

⭐ Customer Review Pilihan:
"Pelayanan cepat, barang berkualitas!" - Pelanggan Setia

Terima kasih sudah menjadi bagian dari keluarga kami!

Salam,
Tim Kami`
        },
        welcome: {
            judul: 'Sambutan - Selamat Datang di Kami!',
            subjek: '🎉 Selamat Datang! Dapatkan Diskon 20% untuk Pembelian Pertama',
            konten: `Halo dan Selamat Datang! 🎉

Terima kasih telah bergabung dengan kami!

Kami sangat senang Anda menjadi bagian dari keluarga besar kami. Sebagai tanda apresiasi, kami memberikan DISKON SPESIAL untuk Anda:

🎁 PENAWARAN KHUSUS MEMBER BARU
✅ Diskon 20% untuk semua produk
✅ Gratis ongkir untuk pembelian pertama
✅ Poin reward berlipat ganda di bulan ini

🛍️ CARA MENGGUNAKAN KODE DISKON:
1. Pilih produk yang Anda inginkan
2. Gunakan kode: WELCOME20 di checkout
3. Diskon otomatis terdebit dari total belanja

📚 PANDUAN LENGKAP:
Kunjungi halaman bantuan kami untuk panduan cara berbelanja, kebijakan pengembalian, dan FAQ.

❓ BUTUH BANTUAN?
Chat langsung dengan tim kami di WhatsApp atau email ke support@kami.com

Selamat berbelanja dan nikmati pengalaman berbelanja terbaik bersama kami!

Salam hangat,
Tim Kami`
        },
        'product-launch': {
            judul: 'Peluncuran Produk Baru - Skincare Premium Series',
            subjek: '🚀 LAUNCHING! Produk Skincare Terbaru dengan Formula Eksklusif',
            konten: `Halo Pecinta Kecantikan!

Kami dengan bangga mempersembahkan koleksi terbaru kami:

✨ SKINCARE PREMIUM SERIES - Edisi Terbatas ✨

🌟 FITUR UNGGULAN:
✅ Formula alami 100% dari bahan organik
✅ Teruji dermatologi dan aman untuk semua jenis kulit
✅ Hasil terlihat dalam 14 hari
✅ Kemasan premium ramah lingkungan

📦 PAKET SPESIAL PELUNCURAN:
Bundle Starter Kit - Hemat 35%
- Face Cleanser 100ml
- Hydrating Serum 30ml
- Night Cream 50ml
- Eye Cream 15ml

💰 HARGA SPESIAL EARLY BIRD:
Hanya untuk 100 pembeli pertama!
Dapatkan diskon tambahan 25%

🎁 BONUS GRATIS:
- Kosmetik pouch limited edition
- Sample produk lain senilai Rp 500k
- Gratis konsultasi dengan beauty expert

🛍️ PESAN SEKARANG:
https://ayumart.com/skincare-premium

Stok terbatas! Jangan sampai kehabisan.

Salam,
Tim Kami`
        },
        'abandoned-cart': {
            judul: 'Jangan Lupa! Ada Produk di Keranjang Anda',
            subjek: '⏰ Produk Favorit Anda Masih Menunggu - Gratis Ongkir Hingga Hari Ini!',
            konten: `Hai! 👋

Kami ketahui ada beberapa produk yang Anda tinggalkan di keranjang belanja.

🛒 PRODUK DI KERANJANG ANDA:
Skincare Premium Bundle - Rp 245.000
Makeup Essential Set - Rp 180.000
Face Mask Pack - Rp 95.000

Total: Rp 520.000

⏰ PENAWARAN TERBATAS - BERLAKU SAMPAI HARI INI:
✅ Gratis Ongkir untuk seluruh pesanan
✅ Cashback 10% dengan kartu kredit
✅ Bonus voucher Rp 100.000 untuk pembelian berikutnya

🔐 KEAMANAN TRANSAKSI:
- Transaksi 100% aman dan terenkripsi
- Garansi uang kembali jika tidak puas
- Customer service siap membantu 24/7

👉 LANJUTKAN BELANJA:
https://ayumart.com/cart

Produk sedang dalam promo, stok terbatas!

Salam,
Tim Kami`
        },
        survey: {
            judul: 'Survey Kepuasan - Suara Anda Sangat Berharga',
            subjek: '📋 Bantuan Kami Evaluasi: Bagikan Pengalaman Berbelanja Anda!',
            konten: `Halo Pelanggan Setia!

Kami mengharapkan masukan berharga dari Anda untuk terus meningkatkan layanan.

🎯 SURVEY SINGKAT (HANYA 5 MENIT):
Jawab beberapa pertanyaan tentang pengalaman berbelanja Anda:

1. Bagaimana tingkat kepuasan Anda dengan kualitas produk?
2. Seberapa efisien proses pengiriman kami?
3. Apakah layanan customer service kami membantu?
4. Apa yang bisa kami tingkatkan?
5. Apakah Anda akan merekomendasikan kami ke teman?

🎁 HADIAH MENARIK:
Setiap responden akan mendapat:
- Voucher belanja Rp 50.000
- Kesempatan menang Grand Prize Rp 5.000.000
- Akses eksklusif ke produk baru

✍️ ISI SURVEY SEKARANG:
https://survey.ayumart.com/feedback

Terima kasih atas waktu dan masukan Anda!

Salam,
Tim Kami`
        },
        birthday: {
            judul: 'Ucapan Ulang Tahun - Rayakan Hari Istimewa Anda',
            subjek: '🎂 Selamat Ulang Tahun! Diskon 40% Hanya Untuk Anda Hari Ini',
            konten: `Halo dan SELAMAT ULANG TAHUN! 🎂🎉

Hari istimewa Anda sudah tiba! Kami ingin merayakan bersama Anda dengan penawaran spesial.

🎁 HADIAH ULANG TAHUN DARI KAMI:
✨ Diskon 40% untuk semua kategori produk
✨ Gratis ongkir tanpa minimum pembelian
✨ Hadiah misterius senilai hingga Rp 500.000

🎊 CARA MENGGUNAKAN:
1. Belanja apapun yang Anda inginkan
2. Gunakan kode: BIRTHDAY40 di checkout
3. Diskon otomatis diterapkan

🛍️ REKOMENDASI PRODUK TERLARIS:
- Skincare favorit pelanggan (⭐⭐⭐⭐⭐)
- Makeup essentials
- Beauty tools premium

🎉 BONUS KEJUTAN:
Setiap pembelian hari ini akan mendapatkan kartu ucapan istimewa dan hadiah bungkus cantik.

👉 MULAI BELANJA SEKARANG:
https://ayumart.com/birthday

Berikan diri Anda hadiah terbaik di hari spesial ini!

Salam dan doa terbaik untuk Anda,
Tim Kami`
        },
        'exclusive-offer': {
            judul: 'Penawaran Eksklusif - Hanya untuk Member VIP',
            subjek: '💎 EKSKLUSIF UNTUK ANDA! Akses Early Bird Koleksi Terbatas',
            konten: `Halo VIP Member!

Anda telah dipilih khusus untuk mendapatkan akses eksklusif ke penawaran terbatas kami.

💎 PRIVILEGE EKSKLUSIF VIP:
✅ Early access 48 jam sebelum public launch
✅ Diskon tambahan hingga 50%
✅ Free premium shipping worldwide
✅ Gratis konsultasi dengan beauty expert
✅ Personal shopping assistance

🌟 KOLEKSI TERBATAS EKSKLUSIF:
Limited Edition Luxury Collection
- Hanya 50 set diproduksi
- Packaging premium eksklusif
- Sertifikat keaslian
- Limited serial number

📦 PAKET VIP EKSKLUSIF:
SET A - Ultimate Bundle
- 5 produk premium pilihan
- VIP gift box eksklusif
- Personal recommendation letter
- Harga: Rp 2.450.000 (Hemat 45%)

SET B - Prestige Collection
- 8 produk premium terpilih
- VIP platinum box
- Exclusive merchandise
- Harga: Rp 3.950.000 (Hemat 50%)

⏱️ WAKTU TERBATAS:
Penawaran hanya berlaku sampai akhir bulan ini.
Slot terbatas, first come first served!

🔐 JAMINAN KEPUASAN VIP:
- 100% satisfaction guarantee
- Easy returns within 30 days
- Personal account manager
- Priority customer support

👉 AKSES EKSKLUSIF SEKARANG:
https://ayumart.com/vip-exclusive

Terima kasih atas loyalitas Anda!

Salam hangat,
Tim VIP Member Services`
        },
        'seasonal-new-year': {
            judul: 'Resolusi Tahun Baru - Diskon Spesial Januari',
            subjek: '🎆 Tahun Baru, Resolusi Baru! Dapatkan Diskon 50% untuk Produk Favorit',
            konten: `Halo dan Selamat Tahun Baru! 🎆✨

Semoga tahun ini membawa keberuntungan, kesehatan, dan kebahagiaan untuk Anda dan keluarga!

🎯 RESOLUSI BARU TAHUN 2026:
Mari mulai tahun dengan gaya dengan koleksi terbaru kami!

💝 PENAWARAN SPESIAL TAHUN BARU:
✅ Diskon hingga 50% untuk semua kategori
✅ Buy 1 Get 1 untuk item pilihan
✅ Gratis ongkir ke seluruh Indonesia
✅ Cashback 20% untuk transaksi hari ini

🛍️ KATEGORI BEST SELLER JANUARI:
• Skincare Perawatan Kulit (⭐⭐⭐⭐⭐)
• Beauty Essentials Bundle
• Hair & Body Care Collection
• Wellness & Supplement

🎁 PAKET BUNDLING HEMAT:
- Complete Beauty Bundle: Rp 1.290.000 (Hemat 40%)
- Premium Skincare Set: Rp 890.000 (Hemat 35%)
- New Year Fresh Start Kit: Rp 650.000 (Hemat 30%)

🎉 KESEMPATAN MENANG:
Setiap pembelian di bulan Januari berkesempatan memenangkan:
- 1st Prize: Voucher Belanja Rp 10.000.000
- 2nd Prize: Smart Watch Premium
- 3rd Prize: Skincare Bundle senilai Rp 2.000.000

⏰ PROMO TERBATAS - JANGAN SAMPAI TERLEWAT:
Flash Sale setiap hari jam 12.00 dan jam 20.00 WIB
Stok terbatas, first come first served!

👉 MULAI TAHUN DENGAN SEMPURNA:
https://ayumart.com/newyear2026

Terima kasih telah memilih kami!

Salam dan doa terbaik untuk tahun baru,
Tim Kami`
        },
        'seasonal-valentine': {
            judul: 'Spesial Valentine - Hadiah Sempurna untuk Orang Terkasih',
            subjek: '💕 Hari Valentine Spesial! Koleksi Hadiah Romantis Eksklusif',
            konten: `Halo Sayang! 💕

Hari Valentine sudah di depan mata! Wujudkan momen romantis dengan hadiah istimewa dari kami.

💝 KOLEKSI VALENTINE EKSKLUSIF:
🌹 Beauty & Wellness Gifts - Hadiah Mewah untuk Orang Terkasih
✨ Premium Skincare Couples Set
💄 Luxury Makeup Collection
🧴 Spa & Relaxation Bundle

🎁 PAKET HADIAH VALENTINE:
SET ROMANCE - Rp 1.450.000
- Luxury skincare duo
- Premium perfume
- Rose-scented candle
- Elegant gift box

SET PASSION - Rp 2.890.000
- Complete beauty bundle
- Luxury aromatherapy set
- Premium jewelry box
- Personalized greeting card

SET ETERNAL - Rp 4.950.000
- Ultimate luxury package
- Gold-plated accessories
- Premium leather case
- Personal message card

💕 PENAWARAN SPESIAL:
✅ Gratis kartu ucapan personal
✅ Gratis wrapping mewah
✅ Express delivery tersedia
✅ Diskon 35% untuk couple set

🎯 BONUS SURPRISE:
Setiap pembelian paket hadiah akan mendapat:
- Love token senilai Rp 100.000
- Couple voucher
- Free romantic candle

👉 PESAN SEKARANG SEBELUM KEHABISAN:
https://ayumart.com/valentine2026

Ungkapkan cinta Anda dengan cara yang istimewa!

Dengan cinta,
Tim Kami`
        },
        'seasonal-holiday': {
            judul: 'Liburan Akhir Tahun - Rayakan Bersama Diskon Fantastis',
            subjek: '🎄 Liburan Akhir Tahun! Diskon 60% + Hadiah Menarik untuk Anda',
            konten: `Halo dan Selamat Liburan! 🎄🎅

Waktunya merayakan akhir tahun dengan kegembiraan dan penghematan maksimal!

🎉 FESTIVAL AKHIR TAHUN TERBESAR:
Persiapan liburan sempurna dengan koleksi lengkap kami!

🎁 PENAWARAN LUAR BIASA:
✅ Diskon hingga 60% untuk pilihan produk
✅ Gratis ongkir untuk seluruh pesanan
✅ Buy more, save more (semakin banyak beli, semakin besar hemat)
✅ Loyalty points 3x lipat hingga akhir tahun

🛍️ TOP DEALS AKHIR TAHUN:
KATEGORI KECANTIKAN:
- Premium Skincare 60% OFF
- Makeup Collection 50% OFF
- Hair Care Bundle 45% OFF

KATEGORI HADIAH:
- Gift Sets Bundle 55% OFF
- Corporate Gifting 40% OFF
- Hampers Spesial 50% OFF

🎊 PAKET LIBURAN EKSKLUSIF:
FAMILY HOLIDAY PACKAGE - Rp 3.890.000
- Produk untuk seluruh keluarga
- Exclusive holiday gift box
- Hemat 50%

CORPORATE HAMPER - Rp 5.450.000
- Perfect untuk klien & karyawan
- Custom branding tersedia
- Minimum 10 pcs

PREMIUM GIFT SET - Rp 2.290.000
- Pilihan premium terlengkap
- Luxury packaging
- Personalisasi gratis

🎯 SEMAKIN SERING BELANJA, SEMAKIN HEMAT:
Belanja Rp 500rb → Gratis voucher Rp 50rb
Belanja Rp 1jt → Gratis voucher Rp 150rb
Belanja Rp 2jt+ → Gratis voucher Rp 300rb

🎄 LUCKY DRAW AKHIR TAHUN:
Hadiah utama: Uang tunai Rp 50.000.000
5 hadiah hiburan: Paket liburan ke Bali
50 hadiah undian: Voucher belanja Rp 1.000.000

⏰ COUNTDOWN SPESIAL:
Flash sale harian dengan produk berbeda
Jam 08.00, 12.00, 18.00, dan 20.00 WIB

👉 BELANJA SEKARANG, HEMAT MAKSIMAL:
https://ayumart.com/holiday-sale

Rayakan akhir tahun dengan sempurna bersama kami!

Selamat merayakan,
Tim Kami`
        },
        'customer-winback': {
            judul: 'Kami Kangen! Kembali Berbelanja dengan Diskon Spesial',
            subjek: '🥺 Kami Kangen! Diskon 50% Hanya untuk Anda yang Sudah Lama Tidak Berkunjung',
            konten: `Halo!

Kami menyadari sudah lama Anda tidak berkunjung dan kami benar-benar merasa kehilangan Anda! 🥺

Mari kita rekonsiliasi dengan penawaran spesial ini sebagai bentuk apresiasi kami.

🎁 WELCOME BACK SPECIAL:
✅ Diskon 50% untuk semua kategori (NO LIMIT!)
✅ Gratis ongkir tanpa minimum pembelian
✅ Bonus voucher belanja Rp 200.000
✅ Triple loyalty points untuk setiap transaksi

💝 KEKHUSUSAN UNTUK ANDA:
Sebagai pelanggan setia kami, Anda mendapat prioritas:
- Early access ke produk baru
- Undangan khusus sale preview
- Birthday special surprises
- Personal shopping assistance

📊 APA YANG BARU DI KAMI:
✨ 150+ produk baru dan trendy
✨ Sistem pembayaran lebih mudah
✨ Pengiriman lebih cepat (1-2 hari)
✨ Customer service yang lebih responsif

🛍️ PRODUK TERLARIS BULAN INI:
- Skincare Premium (⭐⭐⭐⭐⭐ 4.9/5)
- Makeup Essential Set
- Hair Treatment Bundle
- Wellness & Organic Products

💳 METODE PEMBAYARAN BARU:
- QRIS instant payment
- E-wallet dengan cashback
- Cicilan 0% hingga 12 bulan
- Buy now, pay later tersedia

❓ PERTANYAAN? KAMI SIAP MEMBANTU:
- Chat WhatsApp 24/7
- Email support
- Video consultation gratis
- Personal stylist advice

👉 KEMBALI BERBELANJA SEKARANG:
https://ayumart.com/welcome-back

Kami tunggu kedatangan Anda kembali!

Terima kasih atas dukungan Anda,
Tim Kami yang merindukan`
        },
        'vip-upgrade': {
            judul: 'Upgrade ke Member Premium - Nikmati Privilege Eksklusif',
            subjek: '⭐ Upgrade Member! Dapatkan Benefit Premium dan Diskon Lifetime 30%',
            konten: `Halo Member Setia!

Anda telah mencapai milestone berbelanja yang fantastis! Saatnya untuk upgrade ke level membership yang lebih tinggi.

⭐ UPGRADE KE MEMBERSHIP PREMIUM:
Nikmati privilege eksklusif yang tidak tersedia untuk member regular.

💎 BENEFIT PREMIUM MEMBERSHIP:
✅ Diskon permanen 30% untuk semua produk (tanpa batas)
✅ Free shipping selamanya (unlimited)
✅ Priority customer support (respons < 5 menit)
✅ Early access ke semua produk baru (48 jam lebih dulu)
✅ Birthday monthly voucher (Rp 500.000/tahun)
✅ VIP event invitation (live sale, workshop, product launching)
✅ Personal shopping assistant
✅ Loyalty points 2x lipat selamanya

🎁 BONUS UPGRADE SEKARANG:
- Voucher Rp 1.000.000 langsung hari pertama
- Gift box eksklusif premium member
- Premium membership card (limited edition)
- Akses ke private sale channel
- Exclusive merchandise package senilai Rp 750.000

📊 COMPARISON: MEMBER REGULAR vs PREMIUM:
MEMBER REGULAR:
- Diskon akses: Diskon flash sale
- Ongkos kirim: Berbayar/Gratis min. Rp 100k
- Support: Regular
- Loyalty: 1x point

MEMBER PREMIUM:
- Diskon akses: 30% setiap hari
- Ongkos kirim: FREE selama
- Support: Priority 24/7
- Loyalty: 2x point

💰 PRICING YANG SANGAT TERJANGKAU:
ANNUAL MEMBERSHIP: Rp 499.000
- Saving per tahun: Rp 5.000.000+
- ROI: Balik modal setelah transaksi 2-3x

LIFETIME MEMBERSHIP: Rp 2.990.000
- Benefit unlimited selamanya
- Investment terbaik untuk loyal customer
- Free upgrade di masa depan

🎯 DAHULU MEMBER PREMIUM SUDAH HEMAT:
Contoh transaksi Rp 1.000.000:
- Member regular: Bayar Rp 1.000.000
- Member premium: Bayar Rp 700.000 (hemat Rp 300.000!)

👉 UPGRADE SEKARANG DAN RASAKAN PERBEDAANNYA:
https://ayumart.com/upgrade-premium

Jadilah bagian dari komunitas VIP kami!

Terima kasih atas kepercayaan Anda,
Tim Kami`
        },
        'content-tips': {
            judul: 'Tips & Trik Bulanan - Maksimalkan Penggunaan Produk',
            subjek: '💡 Tips Kecantikan Gratis! 7 Cara Mendapatkan Kulit Glowing dalam 30 Hari',
            konten: `Halo Beauty Lovers!

Kami kembali dengan tips eksklusif bulan ini yang akan mengubah rutinitas kecantikan Anda!

💡 TIPS KECANTIKAN MINGGU INI:

📌 TIPS #1: SKINCARE ROUTINE YANG TEPAT
✓ Morning Routine (3 langkah):
  1. Cleanse dengan produk yang tepat
  2. Tone untuk balance pH
  3. Moisturize dengan SPF

✓ Night Routine (5 langkah):
  1. Makeup remover
  2. Gentle cleanser
  3. Essence atau toner
  4. Serum aktif
  5. Night cream

📌 TIPS #2: INGREDIENT YANG WAJIB DIKETAHUI
- Hyaluronic Acid: Super hydrating
- Retinol: Anti-aging powerhouse
- Vitamin C: Brightening & antioxidant
- Niacinamide: Pore minimizer

📌 TIPS #3: KAPAN WAKTU TERBAIK PAKAI PRODUK
- Pagi: Lightweight, SPF protection
- Malam: Heavy, nourishing formula
- 20 menit sebelum tidur: Mask treatment

📌 TIPS #4: COMMON MISTAKES HARUS DIHINDARI
❌ Menggunakan terlalu banyak produk sekaligus
❌ Skip moisturizer karena takut jerawatan
❌ Lupa sunscreen di hari mendung
❌ Eksfoliasi berlebihan

📌 TIPS #5: DIY SKINCARE NATURAL
🥑 Avocado Mask untuk dry skin
🍯 Honey & Oatmeal untuk sensitive skin
🥒 Cucumber untuk puffiness
🍋 Lemon juice (dengan caution) untuk brightening

🎁 PRODUK REKOMENDASI BULAN INI:
- Hydrating Essence: Rp 199.000
- Retinol Serum Pro: Rp 349.000
- Vitamin C Brightening: Rp 289.000
- Night Recovery Mask: Rp 179.000

🎬 BONUS: EXCLUSIVE VIDEO TUTORIAL
Tonton cara aplikasi skincare yang benar dengan expert kami:
https://youtube.com/ayumart/skincare-tutorial

❓ TANYA JAWAB DENGAN BEAUTY EXPERT:
Kirim pertanyaan kecantikan Anda ke support kami!
Setiap minggu kami jawab 5 pertanyaan terbaik di Instagram.

📲 BERGABUNG DENGAN KOMUNITAS KAMI:
Follow Instagram: @ayumart_beauty
Join WhatsApp Community: https://chat.whatsapp.com/ayumart
Subscribe YouTube: @ayumart_channel

👉 BELANJA PRODUK YANG DIREKOMENDASIKAN:
https://ayumart.com/skincare-tips

Invest pada diri sendiri, itu adalah keputusan terbaik!

Semoga bermanfaat,
Beauty Expert Tim Kami`
        },
        'event-webinar': {
            judul: 'Webinar Gratis - Workshop Beauty & Wellness Online',
            subjek: '📺 WEBINAR GRATIS! Expert Makeup & Skincare - Daftar Sekarang',
            konten: `Halo Semua!

Kami dengan senang hati mengundang Anda ke webinar eksklusif gratis kami!

📺 WEBINAR EKSKLUSIF GRATIS:
"Secrets to Radiant Skin & Flawless Makeup"

👤 PEMBICARA:
✨ Dr. Aesthetic Specialist Indonesia
✨ Celebrity Makeup Artist
✨ Wellness Coach Certified
✨ Q&A dengan Expert Panel

📅 DETAIL ACARA:
Tanggal: Sabtu, 20 Januari 2026
Waktu: 19.00 - 21.00 WIB
Platform: Zoom (link dikirim setelah registrasi)
Kuota: Terbatas 500 peserta

🎯 TOPIK YANG AKAN DIBAHAS:
1. Cara Mengenali Jenis Kulit Anda (15 menit)
2. Skincare Routine Anti-Aging (20 menit)
3. Makeup Techniques untuk Pemula (25 menit)
4. Nutrition untuk Glowing Skin (15 menit)
5. Live Q&A Session (30 menit)

🎁 BONUS UNTUK PESERTA:
✅ Sertifikat digital
✅ Recording video selamanya
✅ Exclusive discount code 40% untuk peserta
✅ Free sample kit senilai Rp 500.000
✅ Entry lucky draw Grand Prize Rp 10.000.000

🏆 HADIAH UNTUK PESERTA AKTIF:
- Top questioner: Voucher Rp 500.000
- Most engaged: Product bundle Rp 2.000.000
- Lucky draw: Smart device senilai Rp 3.000.000

💬 TANYA JAWAB:
Setiap peserta bisa bertanya langsung ke expert speaker.
Soal paling banyak di-like akan dijawab lebih detail.

👥 TESTIMONI PESERTA SEBELUMNYA:
"Sangat informatif dan praktis! Langsung beli produk yang direkomendasi." - Sarah, Jakarta

"Expert-nya sangat ramah dan profesional. Dapat banyak insight berharga!" - Budi, Surabaya

📱 CARA MENDAFTAR:
1. Klik link pendaftaran di bawah
2. Isi data diri Anda
3. Verifikasi email
4. Link Zoom dikirim 1 jam sebelum acara

👉 DAFTAR GRATIS SEKARANG:
https://ayumart.com/webinar-register

Tempat terbatas! Daftar sekarang jangan sampai kehabisan.

Untuk informasi lebih lanjut:
WhatsApp: 0812-xxxx-xxxx
Email: event@ayumart.com

Kami tunggu kehadiran Anda!

Salam,
Tim Event Kami`
        },
        'product-review': {
            judul: 'Produk Pilihan - Review dari Pelanggan Kami',
            subjek: '⭐ Produk Pilihan Minggu Ini - Rating 4.9/5 dari Ribuan Review!',
            konten: `Halo!

Minggu ini kami share produk pilihan dengan rating tertinggi dari ribuan review pelanggan kami.

⭐ PRODUK PILIHAN MINGGU INI:

🥇 SKINCARE HERO - Hydrating Serum Premium
Rating: ⭐⭐⭐⭐⭐ 4.9/5 (2,547 reviews)
Harga: Rp 349.000 (Hemat 25%)

Apa kata pelanggan?
"Sangat bagus! Kulit jadi lebih lembab dan cerah dalam 2 minggu!" - Ratna, 28 tahun

"Serum ini favorit saya! Tidak lengket dan hasilnya terlihat nyata." - Dinda, 25 tahun

Mengapa populer?
✓ Formula dengan hyaluronic acid 10%
✓ Cocok untuk semua jenis kulit
✓ Hasil visible dalam 7-10 hari
✓ Mengandung 5 jenis vitamin
✓ Dermatologist tested

🎁 PAKET HEMAT:
- 1 Botol: Rp 349.000
- Bundle 2 + 1: Rp 699.000 (Hemat 150rb)
- Bundle 4 + 1: Rp 1.299.000 (Hemat 450rb)

👉 BELI SEKARANG: https://ayumart.com/serum-premium

---

🥈 MAKEUP ESSENTIAL - Liquid Foundation Pro
Rating: ⭐⭐⭐⭐⭐ 4.8/5 (1,923 reviews)
Harga: Rp 199.000 (Hemat 30%)

Keunggulan:
✓ 25 shade tersedia
✓ Long-lasting 12 jam
✓ Lightweight formula
✓ Matte finish tanpa cakey
✓ SPF 30 protection

Customer feedback:
"Akhirnya ketemu foundation yang cocok! Terlihat natural dan tidak berkerak." - Maya, 32 tahun

👉 BELI SEKARANG: https://ayumart.com/foundation-pro

---

🥉 WELLNESS - Collagen + Vitamin C
Rating: ⭐⭐⭐⭐ 4.7/5 (1,654 reviews)
Harga: Rp 249.000 (Hemat 35%)

Benefit:
✓ Joint health support
✓ Skin elasticity
✓ Hair & nail growth
✓ Natural collagen peptide
✓ Mixed berry flavor

👉 BELI SEKARANG: https://ayumart.com/collagen-vc

---

💬 RATING SUMMARY KAMI:
Produk Rating 5 bintang: 67%
Produk Rating 4-5 bintang: 94%
Kepuasan pelanggan: 98.5%

📊 MENGAPA RATING KAMI TINGGI?
✅ Authentic reviews dari pembeli verified
✅ Quality control ketat
✅ Garansi 100% satisfaction
✅ Return policy fleksibel
✅ Customer service responsif

🎯 BELANJA DENGAN PERCAYA DIRI:
Setiap produk dilengkapi:
- Original guarantee certificate
- Authenticity seal
- 30-day money back guarantee
- Free consultation service

👉 EXPLORE SEMUA PRODUK TOP RATED:
https://ayumart.com/top-rated

Terima kasih atas kepercayaan dan review Anda!

Salam hangat,
Tim Kami`
        }
    };

    // Load Template
    function loadTemplate(type) {
        if (templates[type]) {
            if (confirm('Load template ini? Data yang sudah diisi akan ditimpa.')) {
                document.getElementById('judul').value = templates[type].judul;
                document.getElementById('subjek_email').value = templates[type].subjek;
                document.getElementById('konten_email').value = templates[type].konten;
            }
        }
    }

    // Delivery Method Selection Handler
    document.querySelectorAll('.delivery-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.delivery-option-card').forEach(card => {
                card.classList.remove('active');
            });
            this.closest('.delivery-option-card').classList.add('active');
        });
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set first delivery method card as active on load if one is checked
        const checkedRadio = document.querySelector('.delivery-radio:checked');
        if (checkedRadio) {
            checkedRadio.closest('.delivery-option-card').classList.add('active');
        } else {
            document.querySelector('.delivery-option-card').classList.add('active');
        }

        // Form Validation on submit
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            const judul = document.getElementById('judul').value.trim();
            const subjek = document.getElementById('subjek_email').value.trim();
            const konten = document.getElementById('konten_email').value.trim();
            const method = document.querySelector('input[name="jenis_newsletter"]:checked');

            if (!method) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Metode Pengiriman',
                    text: 'Mohon pilih metode pengiriman terlebih dahulu!',
                    confirmButtonColor: '#4e73df'
                });
                return false;
            }

            if (!judul || !subjek || !konten) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Form Tidak Lengkap',
                    text: 'Mohon lengkapi semua field yang wajib diisi!',
                    confirmButtonColor: '#4e73df'
                });
                return false;
            }

            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Modern Card Styles */
    .card {
        border: none !important;
        border-radius: 0.75rem !important;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    .card.shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    .rounded-3 {
        border-radius: 0.75rem !important;
    }

    /* Form Controls */
    .form-control {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
        outline: none;
    }

    .form-control-lg {
        padding: 0.85rem 1rem;
        font-size: 1rem;
    }

    /* Delivery Option Cards */
    .delivery-option-card {
        border: 2px solid #e3e6f0;
        border-radius: 0.75rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
    }

    .delivery-option-card:hover {
        border-color: #4e73df;
        background: #f8f9ff;
    }

    .delivery-option-card.active {
        border-color: #4e73df;
        background: #f0f3ff;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
    }

    .delivery-radio {
        display: none;
    }

    .delivery-radio:checked + label {
        margin: 0;
    }

    .delivery-radio:disabled + label {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Tier Filter Cards */
    .tier-checkbox-card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
    }

    .tier-checkbox-card:hover {
        border-color: #4e73df;
        box-shadow: 0 0.125rem 0.25rem rgba(78, 115, 223, 0.15);
    }

    .tier-checkbox-card input[type="checkbox"]:checked + .tier-label {
        color: #4e73df;
    }

    .tier-checkbox-card input[type="checkbox"]:checked ~ * {
        color: #4e73df !important;
    }

    .tier-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        gap: 1rem;
        margin: 0;
    }

    .tier-badge {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        font-size: 1.25rem;
    }

    .bg-light-bronze {
        background: #fff8e1;
    }

    .bg-light-silver {
        background: #f0f0f0;
    }

    .bg-light-gold {
        background: #fffde7;
    }

    .bg-light-platinum {
        background: #e1f5ff;
    }

    .tier-info {
        flex-grow: 1;
    }

    .tier-name {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .tier-count {
        font-size: 0.85rem;
    }

    /* Step Groups */
    .step-group {
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 2rem;
    }

    .step-group:last-of-type {
        border-bottom: none;
    }

    .step-header h5 {
        margin-bottom: 0;
    }

    .step-number {
        min-width: 28px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Buttons */
    .btn {
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #4e73df;
        border: none;
    }

    .btn-primary:hover {
        background: #3d5cc2;
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(78, 115, 223, 0.4);
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background: #6c757d;
        border-color: #6c757d;
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }

    /* Alert Styles */
    .alert-danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        border-right: none;
        border-top: none;
        border-bottom: none;
    }

    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }

    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }

    /* Badge Styles */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
        border-radius: 0.3rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }

        .col-lg-8, .col-lg-4 {
            margin-bottom: 2rem;
        }

        .btn-lg {
            padding: 0.6rem 1.2rem;
            font-size: 0.95rem;
        }

        .step-number {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.75rem;
        }
    }

    /* Accessibility */
    .form-check-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .form-check-input:checked {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    /* Smooth Transitions */
    * {
        transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    }

    /* Template Categories */
    .btn-divider {
        border-top: 1px solid #dee2e6;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .template-category-label {
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding-left: 0.5rem;
    }

    .btn-group-vertical .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }

    .btn-group-vertical .btn-outline-success:hover {
        background: #28a745;
        border-color: #28a745;
    }

    .btn-group-vertical .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-group-vertical .btn-outline-danger:hover {
        background: #dc3545;
        border-color: #dc3545;
    }

    .btn-group-vertical .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
    }

    .btn-group-vertical .btn-outline-warning:hover {
        background: #ffc107;
        border-color: #ffc107;
        color: #000;
    }

    .btn-group-vertical .btn-outline-info {
        color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-group-vertical .btn-outline-info:hover {
        background: #17a2b8;
        border-color: #17a2b8;
    }

</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const methodRadios = document.querySelectorAll('input[name="jenis_newsletter"]');
        const filterGroup = document.getElementById('target-filter-group');

        function toggleFilterGroup() {
            const selectedMethod = document.querySelector('input[name="jenis_newsletter"]:checked');
            if (selectedMethod && selectedMethod.value === 'fonnte') {
                filterGroup.style.display = 'block';
            } else {
                filterGroup.style.display = 'none';
                // Reset checkboxes
                const checkboxes = filterGroup.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = false);
            }
        }

        // Run on load
        toggleFilterGroup();

        // Run on change
        methodRadios.forEach(radio => {
            radio.addEventListener('change', toggleFilterGroup);
        });
    });
</script>
@endpush
