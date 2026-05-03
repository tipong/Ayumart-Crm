@extends('layouts.pelanggan')

@section('title', 'Detail Membership')

@push('styles')
<style>
    :root {
        --primary-color: #3F4F44;
        --primary-hover: #2E3A31;
        --secondary-color: #556B58;
        --text-dark: #333;
        --text-muted: #666;
        --border-color: #e5e5e5;
        --bg-light: #f8f9fa;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --info-color: #3b82f6;
    }

    body {
        background-color: var(--bg-light);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 2rem;
        transition: all 0.2s;
    }

    .back-link:hover {
        color: var(--primary-hover);
        transform: translateX(-4px);
    }

    .membership-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .membership-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .membership-tier-badge {
        display: inline-block;
        padding: 0.5rem 1.2rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .tier-bronze {
        background-color: rgba(205, 127, 50, 0.9);
    }

    .tier-silver {
        background-color: rgba(192, 192, 192, 0.9);
    }

    .tier-gold {
        background-color: rgba(255, 215, 0, 0.9);
        color: #333 !important;
    }

    .tier-platinum {
        background-color: rgba(229, 228, 226, 0.9);
        color: #333 !important;
    }

    .membership-header h2 {
        margin: 1rem 0 0.5rem 0;
        font-size: 1.8rem;
        font-weight: 600;
    }

    .membership-header p {
        font-size: 0.95rem;
        opacity: 0.95;
        margin: 0;
    }

    .membership-body {
        padding: 2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        background: var(--bg-light);
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
        border-left: 4px solid var(--primary-color);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .tier-structure {
        background: var(--bg-light);
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .tier-structure h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .tier-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .tier-item:last-child {
        border-bottom: none;
    }

    .tier-item-name {
        font-weight: 500;
    }

    .tier-item-range {
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .progress-section {
        background: var(--bg-light);
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .progress-section h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .progress-bar {
        height: 24px;
        border-radius: 12px;
        background-color: white;
        overflow: hidden;
        border: 1px solid var(--border-color);
        margin-top: 0.75rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 12px;
        transition: width 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        padding-right: 0.75rem;
    }

    .progress-text {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-top: 0.75rem;
    }

    .inactive-alert {
        background: #fffbeb;
        border: 1px solid #fde68a;
        padding: 1.5rem;
        border-radius: 8px;
        color: #92400e;
        margin-bottom: 2rem;
    }

    .inactive-alert i {
        margin-right: 0.5rem;
    }

    .no-membership-alert {
        background: #dbeafe;
        border: 1px solid #93c5fd;
        padding: 1.5rem;
        border-radius: 8px;
        color: #1e40af;
        text-align: center;
    }

    .success-message {
        background: #dcfce7;
        border: 1px solid #86efac;
        padding: 1.5rem;
        border-radius: 8px;
        color: #166534;
        text-align: center;
    }

    .success-message i {
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <!-- Back Button -->
    <a href="{{ route('pelanggan.orders') }}" class="back-link">
        <i class="bi bi-chevron-left"></i> Kembali ke Pesanan
    </a>

    @if(!$membership || !$membership->is_active)
    <!-- No Active Membership Alert -->
    <div class="no-membership-alert">
        <i class="bi bi-info-circle-fill"></i>
        <strong>Belum Ada Membership Aktif</strong>
        <p class="mb-0 mt-1">Belanja sekarang untuk mengaktifkan membership dan nikmati berbagai keuntungan eksklusif!</p>
    </div>
    @else
    <!-- Membership Card -->
    <div class="membership-card">
        <!-- Header -->
        <div class="membership-header">
            <div class="membership-tier-badge tier-{{ strtolower($membership->tier) }}">
                {{ ucfirst($membership->tier) }}
            </div>
            <h2>Tier {{ ucfirst($membership->tier) }}</h2>
            <p>Aktif sejak {{ $membership->created_at->format('d M Y') }}</p>
        </div>

        <!-- Body -->
        <div class="membership-body">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">Poin Terkumpul</div>
                    <div class="stat-value">{{ number_format($membership->points) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Diskon Aktif</div>
                    <div class="stat-value">{{ $membership->discount_percentage }}%</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Total Pembelian</div>
                    <div class="stat-value">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Tier Structure -->
            <div class="tier-structure">
                <h5><i class="bi bi-diagram-3"></i> Struktur Tier Membership</h5>
                <div class="tier-item">
                    <span class="tier-item-name"><span class="badge tier-bronze" style="font-size: 0.85rem;">Bronze</span></span>
                    <span class="tier-item-range">0 - 100 poin (5%)</span>
                </div>
                <div class="tier-item">
                    <span class="tier-item-name"><span class="badge tier-silver" style="font-size: 0.85rem; color: black;">Silver</span></span>
                    <span class="tier-item-range">101 - 250 poin (10%)</span>
                </div>
                <div class="tier-item">
                    <span class="tier-item-name"><span class="badge tier-gold" style="font-size: 0.85rem;">Gold</span></span>
                    <span class="tier-item-range">251 - 400 poin (15%)</span>
                </div>
                <div class="tier-item">
                    <span class="tier-item-name"><span class="badge tier-platinum" style="font-size: 0.85rem;">Platinum</span></span>
                    <span class="tier-item-range">401+ poin (20%)</span>
                </div>
            </div>

            <!-- Progress to Next Tier -->
            @if($nextTier && $nextTier !== 'Maximum')
            <div class="progress-section">
                <h5><i class="bi bi-graph-up"></i> Perjalanan Menuju Tier {{ ucfirst($nextTier) }}</h5>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min($progressToNext, 100) }}%;">
                        @if(min($progressToNext, 100) > 15)
                            {{ round(min($progressToNext, 100), 0) }}%
                        @endif
                    </div>
                </div>
                <p class="progress-text">
                    Kumpulkan lebih banyak poin untuk naik ke tier berikutnya dan nikmati keuntungan yang lebih besar!
                </p>
            </div>
            @else
            <div class="success-message">
                <i class="bi bi-trophy-fill"></i>
                <strong>Selamat!</strong>
                <p class="mb-0 mt-1">Anda sudah mencapai tier tertinggi dalam program membership kami.</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
