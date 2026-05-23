@extends('layouts.pelanggan')

@section('title', 'Membership - AyuMart')

@push('styles')
<style>
    /* ---- Tier Colors ---- */
    --tier-bronze:   #cd7f32;
    --tier-silver:   #6b7280;
    --tier-gold:     #d97706;
    --tier-platinum: #7c3aed;

    .tier-bg-bronze  { background: linear-gradient(135deg, #cd7f32, #a0522d); }
    .tier-bg-silver  { background: linear-gradient(135deg, #94a3b8, #64748b); }
    .tier-bg-gold    { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .tier-bg-platinum{ background: linear-gradient(135deg, #a855f7, #7c3aed); }

    .tier-text-bronze  { color: #cd7f32; }
    .tier-text-silver  { color: #64748b; }
    .tier-text-gold    { color: #d97706; }
    .tier-text-platinum{ color: #7c3aed; }

    /* ---- Member Hero Card ---- */
    .member-hero {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        color: #fff;
        padding: 32px 28px;
        margin-bottom: 24px;
    }
    .member-hero::before {
        content: '';
        position: absolute;
        top: -50px; right: -50px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .member-hero::after {
        content: '';
        position: absolute;
        bottom: -80px; left: -30px;
        width: 280px; height: 280px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .member-hero > * { position: relative; z-index: 1; }

    .tier-badge-lg {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(8px);
        border: 1.5px solid rgba(255,255,255,0.3);
        border-radius: 100px;
        padding: 6px 18px;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 14px;
    }
    .member-hero h2 {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 800;
        font-size: 2.2rem;
        margin-bottom: 4px;
    }
    .member-hero p { opacity: 0.85; font-size: 0.9rem; margin: 0; }

    /* ---- Stat Cards ---- */
    .stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        border: 1.5px solid var(--border);
        text-align: center;
        transition: all 0.28s;
        height: 100%;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(1,91,30,0.12); border-color: var(--primary); }
    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: var(--primary-light);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        color: var(--primary);
        margin: 0 auto 12px;
    }
    .stat-value {
        font-size: 1.7rem;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1;
        margin-bottom: 4px;
    }
    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ---- Progress Bar ---- */
    .tier-progress-wrap { margin: 24px 0; }
    .tier-progress-track {
        height: 14px;
        background: var(--primary-mid);
        border-radius: 100px;
        overflow: hidden;
        position: relative;
    }
    .tier-progress-fill {
        height: 100%;
        border-radius: 100px;
        background: linear-gradient(90deg, var(--primary), #43a047);
        transition: width 1s ease;
        position: relative;
    }
    .tier-progress-fill::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0;
        width: 30px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4));
        border-radius: 100px;
    }
    .tier-label { font-size: 12px; font-weight: 700; color: var(--text-muted); }

    /* ---- Tier List ---- */
    .tier-row {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 8px;
        border: 1.5px solid var(--border);
        transition: all 0.2s;
        gap: 12px;
    }
    .tier-row.active-tier {
        border-color: var(--primary);
        background: var(--primary-light);
    }
    .tier-row:hover { transform: translateX(4px); }
    .tier-row .tier-dot {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .tier-row .tier-info { flex: 1; }
    .tier-row .tier-name { font-weight: 800; font-size: 0.92rem; }
    .tier-row .tier-range { font-size: 12px; color: var(--text-muted); }
    .tier-row .tier-disc {
        font-weight: 800;
        font-size: 1rem;
        color: var(--primary);
        white-space: nowrap;
    }
    .tier-row .current-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--primary);
        background: var(--primary-light);
        border: 1px solid var(--primary);
        border-radius: 100px;
        padding: 2px 10px;
        white-space: nowrap;
    }

    /* ---- Benefit Card ---- */
    .benefit-card {
        background: #fff;
        border-radius: 12px;
        border: 1.5px solid var(--border);
        padding: 18px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        transition: all 0.28s;
        height: 100%;
    }
    .benefit-card:hover { border-color: var(--primary); box-shadow: 0 5px 18px rgba(1,91,30,0.1); }
    .benefit-icon {
        width: 46px; height: 46px;
        background: var(--primary-light);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        color: var(--primary);
        flex-shrink: 0;
    }
    .benefit-card h6 { font-weight: 800; color: var(--text-dark); margin-bottom: 4px; font-size: 0.9rem; }
    .benefit-card p { font-size: 13px; color: var(--text-muted); margin: 0; line-height: 1.5; }

    /* ---- No membership ---- */
    .no-member-card {
        background: linear-gradient(135deg, var(--primary-light), #dff0da);
        border: 1.5px solid var(--primary-mid);
        border-radius: 18px;
        padding: 40px 30px;
        text-align: center;
    }
    .no-member-icon {
        width: 80px; height: 80px;
        background: var(--primary-light);
        border: 3px solid var(--primary);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
        color: var(--primary);
        margin: 0 auto 20px;
    }

    @media (max-width: 768px) {
        .member-hero { padding: 24px 20px; }
        .member-hero h2 { font-size: 1.7rem; }
    }
</style>
@endpush

@section('content')
<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-icon">
                <i class="bi bi-award-fill"></i>
            </div>
            <div>
                <h1>Program Membership</h1>
                <p>Kumpulkan poin, naik tier, dan nikmati diskon eksklusif!</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-2 pb-5">
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Membership</li>
        </ol>
    </nav>

    @if(!$membership || !$membership->is_active)
    <!-- No Membership -->
    <div class="no-member-card mb-4">
        <div class="no-member-icon">
            <i class="bi bi-award"></i>
        </div>
        <h4 class="fw-800 mb-2" style="font-weight:800;color:var(--primary);">Belum Ada Membership Aktif</h4>
        <p style="color:var(--text-mid);max-width:480px;margin:0 auto 24px;">
            Mulai belanja di AyuMart dan kumpulkan poin untuk mendapatkan diskon eksklusif setiap transaksi!
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-bag-heart me-2"></i> Belanja Sekarang
        </a>
    </div>

    @else
    <!-- ====== ACTIVE MEMBERSHIP ====== -->

    @php
        $tierColors = [
            'bronze'   => 'tier-bg-bronze',
            'silver'   => 'tier-bg-silver',
            'gold'     => 'tier-bg-gold',
            'platinum' => 'tier-bg-platinum',
        ];
        $tierIcons = [
            'bronze'   => 'bi-star',
            'silver'   => 'bi-star-half',
            'gold'     => 'bi-star-fill',
            'platinum' => 'bi-gem',
        ];
        $colorClass = $tierColors[strtolower($membership->tier)] ?? 'tier-bg-bronze';
        $iconClass  = $tierIcons[strtolower($membership->tier)] ?? 'bi-star';
    @endphp

    <!-- Member Hero Card -->
    <div class="member-hero {{ $colorClass }} mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 text-black">
            <div>
                <h2>Halo, {{ auth()->user()->name }}! 👋</h2>
                <p>Member aktif sejak {{ $membership->created_at->format('d M Y') }} · Diskon <strong>{{ $membership->discount_percentage }}%</strong> setiap transaksi</p>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-star-fill" style="color:#f59e0b;"></i></div>
                <div class="stat-value">{{ number_format($membership->points) }}</div>
                <div class="stat-label">Total Poin</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-percent"></i></div>
                <div class="stat-value">{{ $membership->discount_percentage }}%</div>
                <div class="stat-label">Diskon Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
                <div class="stat-value" style="font-size:1.1rem;">
                    Rp {{ number_format($totalSpent, 0, ',', '.') }}
                </div>
                <div class="stat-label">Total Belanja</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-check-circle-fill" style="color:var(--primary);"></i></div>
                <div class="stat-value" style="font-size:1.2rem;color:var(--primary);">Aktif</div>
                <div class="stat-label">Status Member</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Tier Progress + Tier List -->
        <div class="col-lg-6">

            <!-- Progress to Next Tier -->
            @if($nextTier && $nextTier !== 'Maximum')
            <div class="ay-card mb-3">
                <div class="ay-card-header">
                    <i class="bi bi-graph-up-arrow"></i> Menuju Tier {{ ucfirst($nextTier) }}
                </div>
                <div class="ay-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:13px;font-weight:700;color:var(--text-mid);">
                            Tier {{ ucfirst($membership->tier) }}
                        </span>
                        <span style="font-size:13px;font-weight:700;color:var(--primary);">
                            Tier {{ ucfirst($nextTier) }}
                        </span>
                    </div>
                    <div class="tier-progress-track">
                        <div class="tier-progress-fill" style="width:{{ min($progressToNext, 100) }}%;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="tier-label">{{ number_format($membership->points) }} poin</span>
                        <span class="tier-label" style="color:var(--primary);">{{ round(min($progressToNext, 100)) }}% tercapai</span>
                    </div>
                    <div class="mt-3 p-3" style="background:var(--yellow-light);border-radius:10px;font-size:13px;color:#92400e;font-weight:600;">
                        <i class="bi bi-lightbulb-fill me-2" style="color:#f59e0b;"></i>
                        Terus belanja untuk naik ke tier <strong>{{ ucfirst($nextTier) }}</strong> dan dapatkan diskon lebih besar!
                    </div>
                </div>
            </div>
            @else
            <div class="ay-card mb-3">
                <div class="ay-card-header" style="background:linear-gradient(135deg,#a855f7,#7c3aed);">
                    <i class="bi bi-trophy-fill"></i> Tier Tertinggi Tercapai!
                </div>
                <div class="ay-card-body text-center py-4">
                    <div style="font-size:3rem;margin-bottom:12px;">🏆</div>
                    <h5 class="fw-bold" style="color:var(--text-dark);">Selamat, Platinum Member!</h5>
                    <p style="color:var(--text-muted);">Anda telah mencapai tier tertinggi. Nikmati diskon maksimal 20% setiap belanja!</p>
                </div>
            </div>
            @endif

            <!-- Tier Structure -->
            <div class="ay-card mb-3">
                <div class="ay-card-header">
                    <i class="bi bi-diagram-3"></i> Struktur Tier
                </div>
                <div class="ay-card-body">
                    @php
                        $tiers = [
                            ['key'=>'bronze',   'label'=>'Bronze',   'range'=>'0 – 100 poin',  'disc'=>'5%',  'icon'=>'bi-star',      'color'=>'#cd7f32'],
                            ['key'=>'silver',   'label'=>'Silver',   'range'=>'101 – 250 poin', 'disc'=>'10%', 'icon'=>'bi-star-half', 'color'=>'#64748b'],
                            ['key'=>'gold',     'label'=>'Gold',     'range'=>'251 – 400 poin', 'disc'=>'15%', 'icon'=>'bi-star-fill', 'color'=>'#d97706'],
                            ['key'=>'platinum', 'label'=>'Platinum', 'range'=>'401+ poin',      'disc'=>'20%', 'icon'=>'bi-gem',       'color'=>'#7c3aed'],
                        ];
                    @endphp
                    @foreach($tiers as $t)
                    <div class="tier-row {{ strtolower($membership->tier) === $t['key'] ? 'active-tier' : '' }}">
                        <div class="tier-dot" style="background:{{ $t['color'] }}22;color:{{ $t['color'] }};">
                            <i class="bi {{ $t['icon'] }}"></i>
                        </div>
                        <div class="tier-info">
                            <div class="tier-name" style="color:{{ $t['color'] }};">{{ $t['label'] }}</div>
                            <div class="tier-range">{{ $t['range'] }}</div>
                        </div>
                        <div class="tier-disc">{{ $t['disc'] }}</div>
                        @if(strtolower($membership->tier) === $t['key'])
                            <span class="current-label">Posisi Kamu</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="ay-card" style="border-color:var(--yellow);background:var(--yellow-light);">
                <div class="ay-card-header" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    <i class="bi bi-lightbulb"></i> Tips Naik Tier Lebih Cepat
                </div>
                <div class="ay-card-body" style="padding-top:16px;">
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                        <li class="d-flex align-items-start gap-3">
                            <span style="background:#f59e0b;color:#fff;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;">1</span>
                            <span style="font-size:13px;font-weight:600;color:#92400e;">Belanja rutin minimal 1× seminggu untuk akumulasi poin yang lebih cepat.</span>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <span style="background:#f59e0b;color:#fff;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;">2</span>
                            <span style="font-size:13px;font-weight:600;color:#92400e;">Manfaatkan produk promo untuk penghematan lebih besar setiap hari.</span>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <span style="background:#f59e0b;color:#fff;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;">3</span>
                            <span style="font-size:13px;font-weight:600;color:#92400e;">Berikan ulasan produk setelah belanja untuk membantu sesama pelanggan.</span>
                        </li>
                    </ul>
                    <a href="{{ route('home') }}" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-bag-heart me-2"></i> Belanja & Kumpulkan Poin
                    </a>
                </div>
            </div>
        </div>

        <!-- Right: Benefits + Tips -->
        <div class="col-lg-6">

            <!-- Benefits -->
            {{-- <div class="ay-card mb-3">
                <div class="ay-card-header">
                    <i class="bi bi-gift"></i> Keuntungan Member
                </div>
                <div class="ay-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="benefit-card">
                                <div class="benefit-icon"><i class="bi bi-percent"></i></div>
                                <div>
                                    <h6>Diskon Otomatis {{ $membership->discount_percentage }}%</h6>
                                    <p>Diskon langsung terpotong di setiap transaksi belanja Anda tanpa perlu kode kupon.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="benefit-card">
                                <div class="benefit-icon"><i class="bi bi-star"></i></div>
                                <div>
                                    <h6>Kumpulkan Poin</h6>
                                    <p>Setiap transaksi menambahkan poin ke akun Anda dan membantu Anda naik tier.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="benefit-card">
                                <div class="benefit-icon"><i class="bi bi-bell"></i></div>
                                <div>
                                    <h6>Notifikasi Eksklusif</h6>
                                    <p>Dapatkan pemberitahuan promo dan penawaran spesial khusus member lebih awal.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="benefit-card">
                                <div class="benefit-icon"><i class="bi bi-headset"></i></div>
                                <div>
                                    <h6>Prioritas Layanan</h6>
                                    <p>Pengaduan dan pertanyaan member diprioritaskan oleh tim customer service kami.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Member Card Section -->
            <div class="ay-card">
                <div class="ay-card-header">
                    <i class="bi bi-credit-card-2-front"></i> Kartu Member Digital
                </div>
                <div class="ay-card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-11">
                            <div class="member-card" style="background: linear-gradient(135deg, #015b1e 0%, #027826 100%); border-radius: 16px; padding: 2rem; color: white; box-shadow: 0 10px 30px rgba(1, 91, 30, 0.2); margin-bottom: 1.5rem; position: relative; overflow: hidden;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; position: relative; z-index: 1;">
                                    <span style="font-size: 0.9rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; opacity: 0.9;">Ayu Mart Member</span>
                                    <span style="font-size: 0.9rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; opacity: 0.9;">{{ strtoupper($membership->tier) }}</span>
                                </div>

                                <div style="font-size: 1.5rem; font-weight: 700; letter-spacing: 3px; margin-bottom: 0.5rem; text-align: center; position: relative; z-index: 1;">
                                    AYU-{{ str_pad(auth()->user()->id_user, 6, '0', STR_PAD_LEFT) }}
                                </div>

                                <div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0; text-align: center; opacity: 0.95; position: relative; z-index: 1;">
                                    {{ auth()->user()->name }}
                                </div>

                                <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-top: 1rem; position: relative; z-index: 1;">
                                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; border-bottom: 2px solid #e0e0e0;">
                                        <button class="member-code-tab active" data-tab="qr" onclick="switchMemberTab('qr')" style="flex: 1; padding: 0.75rem; border: none; background: transparent; color: #777; font-weight: 600; cursor: pointer; border-bottom: 3px solid transparent;">
                                            <i class="bi bi-qr-code"></i> QR Code
                                        </button>
                                        <button class="member-code-tab" data-tab="barcode" onclick="switchMemberTab('barcode')" style="flex: 1; padding: 0.75rem; border: none; background: transparent; color: #777; font-weight: 600; cursor: pointer; border-bottom: 3px solid transparent;">
                                            <i class="bi bi-upc-scan"></i> Barcode
                                        </button>
                                    </div>

                                    <!-- QR Code -->
                                    <div id="member-qr-tab" class="member-code-content active" style="display: block; text-align: center; padding: 1rem 0;">
                                        <div id="member-qrcode" class="d-flex" style="min-height: 200px; display: flex; align-items: center; justify-content: center;"></div>
                                        <div style="text-align: center; margin-top: 1rem;">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateMemberCard()">
                                                <i class="bi bi-arrow-clockwise"></i> Refresh QR Code
                                            </button>
                                        </div>
                                        <div style="background: #f5f5f5; border-radius: 8px; padding: 0.75rem; margin-top: 1rem; font-size: 0.85rem; color: #777;">
                                            <i class="bi bi-info-circle"></i>
                                            Tunjukkan QR Code ini di kasir untuk mendapatkan poin & diskon
                                        </div>
                                        <button class="btn btn-primary w-100" onclick="downloadMemberQRCode()" style="margin-top: 1rem; background: #015b1e; border: none; color: white; padding: 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
                                            <i class="bi bi-download"></i> Download QR Code
                                        </button>
                                    </div>

                                    <!-- Barcode -->
                                    <div id="member-barcode-tab" class="member-code-content" style="display: none; text-align: center; padding: 1rem 0;">
                                        <div style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                                            <svg id="member-barcode"></svg>
                                        </div>
                                        <div style="text-align: center; margin-top: 1rem;">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateMemberCard()">
                                                <i class="bi bi-arrow-clockwise"></i> Refresh Barcode
                                            </button>
                                        </div>
                                        <div style="background: #f5f5f5; border-radius: 8px; padding: 0.75rem; margin-top: 1rem; font-size: 0.85rem; color: #777;">
                                            <i class="bi bi-info-circle"></i>
                                            Scan barcode ini di kasir untuk mendapatkan poin & diskon
                                        </div>
                                        <button class="btn btn-primary w-100" onclick="downloadMemberBarcode()" style="margin-top: 1rem; background: #015b1e; border: none; color: white; padding: 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
                                            <i class="bi bi-download"></i> Download Barcode
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                                <i class="bi bi-exclamation-triangle" style="color: #ff6b6b; margin-right: 0.5rem;"></i>
                                <strong style="color: #cc5500;">Penting:</strong>
                                <p style="margin: 0.5rem 0 0 0; color: #cc5500;">Jangan bagikan kode member Anda kepada orang lain. Kode ini bersifat pribadi dan hanya untuk digunakan oleh Anda.</p>
                            </div>

                            <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                                <i class="bi bi-lightbulb" style="color: #0c5460; margin-right: 0.5rem;"></i>
                                <strong style="color: #0c5460;">Cara Menggunakan:</strong>
                                <ol style="margin: 0.75rem 0 0 1.5rem; padding: 0; color: #0c5460;">
                                    <li>Tunjukkan kartu member digital ini di kasir Ayu Mart</li>
                                    <li>Kasir akan scan QR Code atau Barcode Anda</li>
                                    <li>Dapatkan diskon {{ $membership->discount_percentage }}% dan poin otomatis!</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous"></script>
<!-- Barcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js" crossorigin="anonymous"></script>

<script>
    // Member Card / Kartu Member Section
    const memberCode = '{{ auth()->user()->membership ? "AYU-" . str_pad(auth()->user()->id_user, 6, "0", STR_PAD_LEFT) : "" }}';
    const memberName = '{{ auth()->user()->name }}';
    const memberTier = '{{ auth()->user()->membership ? strtoupper(auth()->user()->membership->tier) : "" }}';

    // Initialize member card on page load
    document.addEventListener('DOMContentLoaded', function() {
        @if(auth()->user()->membership && auth()->user()->membership->isValid())
        generateMemberCard();
        @endif
    });

    function generateMemberCard() {
        generateQRCode();
        generateBarcode();
    }

    function generateQRCode() {
        try {
            const qrcodeContainer = document.getElementById("member-qrcode");
            if (!qrcodeContainer) return;

            qrcodeContainer.innerHTML = '';
            qrcodeContainer.style.minHeight = 'auto';
            qrcodeContainer.style.display = 'block';
            qrcodeContainer.style.textAlign = 'center';

            if (typeof QRCode === 'undefined') {
                const img = document.createElement('img');
                img.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(memberCode)}`;
                img.alt = 'QR Code';
                img.style.maxWidth = '200px';
                img.style.height = 'auto';
                qrcodeContainer.appendChild(img);
                return;
            }

            new QRCode(qrcodeContainer, {
                text: memberCode,
                width: 200,
                height: 200,
                colorDark: "#333333",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch (error) {
            console.error('Error generating QR Code:', error);
        }
    }

    function generateBarcode() {
        try {
            const barcodeElement = document.getElementById("member-barcode");
            if (!barcodeElement) return;

            if (typeof JsBarcode === 'undefined') return;

            JsBarcode("#member-barcode", memberCode, {
                format: "CODE128",
                width: 2,
                height: 80,
                displayValue: true,
                fontSize: 16,
                margin: 10,
                background: "#ffffff",
                lineColor: "#000000"
            });
        } catch (error) {
            console.error('Error generating Barcode:', error);
        }
    }

    function switchMemberTab(tab) {
        document.querySelectorAll('.member-code-tab').forEach(t => t.style.borderBottomColor = 'transparent');
        document.querySelectorAll('.member-code-tab')[tab === 'qr' ? 0 : 1].style.borderBottomColor = '#015b1e';

        document.querySelectorAll('.member-code-content').forEach(c => c.style.display = 'none');

        if (tab === 'qr') {
            document.getElementById('member-qr-tab').style.display = 'block';
            document.querySelectorAll('.member-code-tab')[0].style.color = '#015b1e';
            document.querySelectorAll('.member-code-tab')[1].style.color = '#777';
        } else {
            document.getElementById('member-barcode-tab').style.display = 'block';
            document.querySelectorAll('.member-code-tab')[0].style.color = '#777';
            document.querySelectorAll('.member-code-tab')[1].style.color = '#015b1e';
        }
    }

    function downloadMemberQRCode() {
        const qrCanvas = document.querySelector('#member-qrcode canvas');
        if (!qrCanvas) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'QR Code belum di-generate!'
            });
            return;
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 400;
        canvas.height = 500;

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        const gradient = ctx.createLinearGradient(0, 0, canvas.width, 0);
        gradient.addColorStop(0, '#015b1e');
        gradient.addColorStop(1, '#027826');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, 80);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 24px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Ayu Mart Member Card', canvas.width / 2, 35);
        ctx.font = 'bold 16px Arial';
        ctx.fillText(memberTier, canvas.width / 2, 60);

        ctx.fillStyle = '#333333';
        ctx.font = 'bold 18px Arial';
        ctx.fillText(memberName, canvas.width / 2, 120);
        ctx.font = '16px Arial';
        ctx.fillText(memberCode, canvas.width / 2, 145);

        ctx.drawImage(qrCanvas, 100, 170, 200, 200);

        ctx.font = '12px Arial';
        ctx.fillStyle = '#666666';
        ctx.fillText('Tunjukkan kode ini di kasir', canvas.width / 2, 400);
        ctx.fillText('untuk mendapatkan poin & diskon', canvas.width / 2, 420);

        const link = document.createElement('a');
        link.download = `Ayu Mart_QR_${memberCode}.png`;
        link.href = canvas.toDataURL();
        link.click();

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'QR Code berhasil diunduh',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function downloadMemberBarcode() {
        const barcodeSvg = document.querySelector('#member-barcode');
        if (!barcodeSvg) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Barcode belum di-generate!'
            });
            return;
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 500;
        canvas.height = 400;

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        const gradient = ctx.createLinearGradient(0, 0, canvas.width, 0);
        gradient.addColorStop(0, '#015b1e');
        gradient.addColorStop(1, '#027826');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, 80);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 24px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Ayu Mart Member Card', canvas.width / 2, 35);
        ctx.font = 'bold 16px Arial';
        ctx.fillText(memberTier, canvas.width / 2, 60);

        ctx.fillStyle = '#333333';
        ctx.font = 'bold 18px Arial';
        ctx.fillText(memberName, canvas.width / 2, 120);

        const svgData = new XMLSerializer().serializeToString(barcodeSvg);
        const img = new Image();
        const svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
        const url = URL.createObjectURL(svgBlob);

        img.onload = function() {
            ctx.drawImage(img, 50, 150, 400, 150);

            ctx.font = '12px Arial';
            ctx.fillStyle = '#666666';
            ctx.fillText('Scan barcode ini di kasir', canvas.width / 2, 330);
            ctx.fillText('untuk mendapatkan poin & diskon', canvas.width / 2, 350);

            const link = document.createElement('a');
            link.download = `Ayu Mart_Barcode_${memberCode}.png`;
            link.href = canvas.toDataURL();
            link.click();

            URL.revokeObjectURL(url);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Barcode berhasil diunduh',
                timer: 2000,
                showConfirmButton: false
            });
        };

        img.src = url;
    }
</script>
@endpush
