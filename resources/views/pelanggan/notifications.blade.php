@extends('layouts.pelanggan')

@section('title', 'Notifikasi — AyuMart')

@push('styles')
<style>
    /* ─── PAGE LAYOUT ─────────────────────────────────────── */
    .notif-page-hero {
        background: linear-gradient(135deg, var(--primary) 0%, #027826 100%);
        color: #fff;
        padding: 28px 0 36px;
    }
    .notif-page-hero h1 {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 800;
        font-size: 2rem;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .notif-page-hero p { color: rgba(255,255,255,0.78); font-size: 0.9rem; }
    .hero-icon-lg {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
        backdrop-filter: blur(8px);
        flex-shrink: 0;
    }

    /* ─── FILTER TABS ─────────────────────────────────────── */
    .notif-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .notif-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 100px;
        border: 1.5px solid var(--border);
        background: #fff;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
    }
    .notif-tab:hover { border-color: var(--primary); color: var(--primary); }
    .notif-tab.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        box-shadow: 0 3px 12px rgba(1,91,30,0.25);
    }
    .notif-tab .tab-count {
        background: rgba(255,255,255,0.3);
        color: inherit;
        font-size: 11px;
        min-width: 18px;
        padding: 1px 5px;
        border-radius: 100px;
    }
    .notif-tab:not(.active) .tab-count { background: var(--primary-light); color: var(--primary); }

    /* ─── NOTIFICATION CARDS ──────────────────────────────── */
    .notif-card-list { display: flex; flex-direction: column; gap: 10px; }

    .notif-card {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 20px;
        background: #fff;
        border-radius: 14px;
        border: 1.5px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: var(--transition);
        text-decoration: none !important;
        color: var(--text-dark);
        position: relative;
        overflow: hidden;
    }
    .notif-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        border-radius: 2px 0 0 2px;
    }
    .notif-card.type-shipping::before   { background: #3b82f6; }
    .notif-card.type-payment::before    { background: var(--primary); }
    .notif-card.type-expired::before    { background: #ef4444; }
    .notif-card.type-cancellation::before { background: #f59e0b; }
    .notif-card.type-ticket::before     { background: #0ea5e9; }
    .notif-card.type-order::before      { background: var(--primary); }

    .notif-card:hover {
        border-color: var(--primary);
        box-shadow: 0 6px 22px rgba(1,91,30,0.12);
        transform: translateY(-2px);
    }

    .notif-card-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .notif-card-icon.type-shipping   { background: #dbeafe; color: #1d4ed8; }
    .notif-card-icon.type-payment    { background: #dcfce7; color: var(--primary); }
    .notif-card-icon.type-expired    { background: #fee2e2; color: #dc2626; }
    .notif-card-icon.type-cancellation { background: #fef3c7; color: #b45309; }
    .notif-card-icon.type-ticket     { background: #e0f2fe; color: #0369a1; }
    .notif-card-icon.type-order      { background: var(--primary-light); color: var(--primary); }

    .notif-card-body { flex: 1; min-width: 0; }
    .notif-card-title {
        font-weight: 800;
        font-size: 15px;
        margin-bottom: 4px;
        color: var(--text-dark);
    }
    .notif-card-msg {
        font-size: 13.5px;
        color: var(--text-mid);
        line-height: 1.5;
        margin-bottom: 8px;
    }
    .notif-card-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .notif-card-time {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .notif-badge-type {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 100px;
    }
    .notif-badge-type.type-shipping   { background: #dbeafe; color: #1d4ed8; }
    .notif-badge-type.type-payment    { background: #dcfce7; color: var(--primary); }
    .notif-badge-type.type-expired    { background: #fee2e2; color: #dc2626; }
    .notif-badge-type.type-cancellation { background: #fef3c7; color: #b45309; }
    .notif-badge-type.type-ticket     { background: #e0f2fe; color: #0369a1; }
    .notif-badge-type.type-order      { background: var(--primary-light); color: var(--primary); }

    .notif-card-action {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-left: auto;
        padding-left: 8px;
        transition: var(--transition);
    }
    .notif-card:hover .notif-card-action { color: var(--primary); transform: translateX(4px); }

    /* ─── EMPTY STATE ─────────────────────────────────────── */
    .notif-empty-state {
        text-align: center;
        padding: 64px 20px;
    }
    .notif-empty-state .empty-icon {
        width: 100px; height: 100px;
        background: var(--primary-light);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.5rem;
        color: var(--primary);
    }
    .notif-empty-state h5 {
        font-weight: 800;
        color: var(--text-dark);
        font-size: 1.15rem;
        margin-bottom: 8px;
    }
    .notif-empty-state p { color: var(--text-muted); font-size: 14px; margin-bottom: 24px; }

    /* ─── ACTIONS BAR ─────────────────────────────────────── */
    .notif-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 12px;
        flex-wrap: wrap;
    }
    .notif-count-text {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
    }
    .notif-count-text span { color: var(--primary); font-weight: 800; }

    /* ─── LOADING SKELETON ────────────────────────────────── */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 8px;
    }
    @keyframes shimmer {
        from { background-position: 200% 0; }
        to   { background-position: -200% 0; }
    }
    .skeleton-card {
        display: flex; gap: 16px; padding: 18px 20px;
        background: #fff; border-radius: 14px;
        border: 1.5px solid var(--border);
        margin-bottom: 10px;
    }

    /* ─── RESPONSIVE ──────────────────────────────────────── */
    @media (max-width: 576px) {
        .notif-card { padding: 14px 16px; gap: 12px; }
        .notif-card-icon { width: 42px; height: 42px; font-size: 1.1rem; }
        .notif-card-title { font-size: 14px; }
        .notif-card-action { display: none; }
    }
</style>
@endpush

@section('content')
<!-- Hero -->
<div class="notif-page-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-icon-lg">
                <i class="bi bi-bell-fill"></i>
            </div>
            <div>
                <h1>Notifikasi</h1>
                <p>Semua pembaruan aktivitas pesanan dan tiket Anda</p>
            </div>
        </div>
    </div>
</div>

<div class="container" style="margin-top:-20px; position:relative; z-index:10;">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Filter Tabs -->
            <div class="notif-tabs" id="notifTabs">
                <button class="notif-tab active" data-filter="all" onclick="filterNotifs('all', this)">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    Semua
                    <span class="tab-count" id="cnt-all">0</span>
                </button>
                <button class="notif-tab" data-filter="shipping" onclick="filterNotifs('shipping', this)">
                    <i class="bi bi-truck"></i>
                    Pengiriman
                    <span class="tab-count" id="cnt-shipping">0</span>
                </button>
                <button class="notif-tab" data-filter="payment" onclick="filterNotifs('payment', this)">
                    <i class="bi bi-credit-card-2-front"></i>
                    Pembayaran
                    <span class="tab-count" id="cnt-payment">0</span>
                </button>
                <button class="notif-tab" data-filter="cancellation" onclick="filterNotifs('cancellation', this)">
                    <i class="bi bi-x-circle"></i>
                    Pembatalan
                    <span class="tab-count" id="cnt-cancellation">0</span>
                </button>
                <button class="notif-tab" data-filter="ticket" onclick="filterNotifs('ticket', this)">
                    <i class="bi bi-headset"></i>
                    Tiket
                    <span class="tab-count" id="cnt-ticket">0</span>
                </button>
            </div>

            <!-- Actions Bar -->
            <div class="notif-actions">
                <div class="notif-count-text">
                    Menampilkan <span id="showing-count">0</span> notifikasi
                </div>
                <button class="btn btn-outline-primary btn-sm" onclick="markAllRead()">
                    <i class="bi bi-check2-all me-1"></i> Tandai Semua Dibaca
                </button>
            </div>

            <!-- Skeleton Loading -->
            <div id="skeletonLoader">
                @for ($i = 0; $i < 3; $i++)
                <div class="skeleton-card">
                    <div class="skeleton" style="width:50px;height:50px;border-radius:14px;flex-shrink:0;"></div>
                    <div style="flex:1;">
                        <div class="skeleton mb-2" style="height:16px;width:60%;"></div>
                        <div class="skeleton mb-1" style="height:12px;width:90%;"></div>
                        <div class="skeleton" style="height:12px;width:40%;"></div>
                    </div>
                </div>
                @endfor
            </div>

            <!-- Notifications List -->
            <div class="notif-card-list" id="notifPageList" style="display:none;"></div>

            <!-- Empty State -->
            <div class="notif-empty-state" id="notifEmptyState" style="display:none;">
                <div class="empty-icon">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h5>Tidak Ada Notifikasi</h5>
                <p>Belum ada aktivitas yang perlu Anda ketahui saat ini.<br>Notifikasi akan muncul saat ada pembaruan pesanan atau tiket Anda.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="bi bi-shop me-2"></i>Mulai Belanja
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let allNotifications = [];
    let currentFilter = 'all';

    const TYPE_LABELS = {
        shipping:     { label: 'Pengiriman', icon: 'bi-truck' },
        payment:      { label: 'Pembayaran', icon: 'bi-credit-card-2-front' },
        expired:      { label: 'Kadaluarsa', icon: 'bi-clock-history' },
        cancellation: { label: 'Pembatalan', icon: 'bi-x-circle' },
        ticket:       { label: 'Tiket CS',   icon: 'bi-headset' },
        order:        { label: 'Pesanan',    icon: 'bi-bag-check' },
    };

    function fetchNotifications() {
        const h = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
        };

        fetch('/api/notifications', { headers: h })
            .then(r => r.json())
            .then(data => {
                document.getElementById('skeletonLoader').style.display = 'none';
                document.getElementById('notifPageList').style.display = 'flex';

                if (!data.success || !data.notifications) {
                    showEmpty();
                    return;
                }

                allNotifications = data.notifications;
                renderNotifications(allNotifications);
                updateTabCounts(allNotifications);
            })
            .catch(() => {
                document.getElementById('skeletonLoader').style.display = 'none';
                showEmpty();
            });
    }

    function updateTabCounts(notifs) {
        const counts = { all: notifs.length, shipping: 0, payment: 0, cancellation: 0, ticket: 0 };
        notifs.forEach(n => {
            if (n.type === 'shipping')     counts.shipping++;
            if (n.type === 'payment')      counts.payment++;
            if (n.type === 'expired')      counts.payment++; // group expired under payment tab
            if (n.type === 'cancellation') counts.cancellation++;
            if (n.type === 'ticket')       counts.ticket++;
        });
        document.getElementById('cnt-all').textContent          = counts.all;
        document.getElementById('cnt-shipping').textContent      = counts.shipping;
        document.getElementById('cnt-payment').textContent       = counts.payment;
        document.getElementById('cnt-cancellation').textContent  = counts.cancellation;
        document.getElementById('cnt-ticket').textContent        = counts.ticket;
    }

    function filterNotifs(filter, btn) {
        currentFilter = filter;
        document.querySelectorAll('.notif-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');

        let filtered = allNotifications;
        if (filter === 'payment') {
            filtered = allNotifications.filter(n => n.type === 'payment' || n.type === 'expired');
        } else if (filter !== 'all') {
            filtered = allNotifications.filter(n => n.type === filter);
        }
        renderNotifications(filtered);
    }

    function renderNotifications(notifs) {
        const list = document.getElementById('notifPageList');
        const emptyState = document.getElementById('notifEmptyState');
        document.getElementById('showing-count').textContent = notifs.length;

        if (notifs.length === 0) {
            list.innerHTML = '';
            emptyState.style.display = 'block';
            return;
        }

        emptyState.style.display = 'none';
        list.innerHTML = notifs.map(n => {
            const typeInfo = TYPE_LABELS[n.type] || { label: 'Info', icon: 'bi-bell' };
            return `
            <a href="${n.url}" class="notif-card type-${n.type}" onclick="markRead('${n.id}')">
                <div class="notif-card-icon type-${n.type}">
                    <i class="bi ${n.icon || typeInfo.icon}"></i>
                </div>
                <div class="notif-card-body">
                    <div class="notif-card-title">${n.title}</div>
                    <div class="notif-card-msg">${n.message}</div>
                    <div class="notif-card-meta">
                        <span class="notif-card-time">
                            <i class="bi bi-clock"></i> ${n.time}
                        </span>
                        <span class="notif-badge-type type-${n.type}">${n.label || typeInfo.label}</span>
                    </div>
                </div>
                <div class="notif-card-action">
                    <i class="bi bi-chevron-right"></i>
                </div>
            </a>`;
        }).join('');
    }

    function showEmpty() {
        document.getElementById('notifPageList').innerHTML = '';
        document.getElementById('notifEmptyState').style.display = 'block';
        document.getElementById('showing-count').textContent = '0';
        ['cnt-all','cnt-shipping','cnt-payment','cnt-cancellation','cnt-ticket']
            .forEach(id => document.getElementById(id).textContent = '0');
    }

    function markRead(id) {
        const h = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json'
        };
        fetch(`/api/notifications/${id}/read`, { method: 'POST', headers: h }).catch(() => {});
    }

    function markAllRead() {
        const h = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json'
        };
        fetch('/api/notifications/read-all', { method: 'POST', headers: h })
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Semua notifikasi telah ditandai dibaca',
                    confirmButtonColor: '#015b1e',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            })
            .catch(() => {});
    }

    document.addEventListener('DOMContentLoaded', fetchNotifications);
</script>
@endpush
