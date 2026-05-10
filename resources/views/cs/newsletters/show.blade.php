@extends('layouts.staff')

@section('title', 'Detail Newsletter')

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

@push('styles')
<style>
    /* Modern Detail View Styling */
    .detail-section {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .detail-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e9ecef;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .detail-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .detail-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .detail-card-header.info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .detail-card-header.success {
        background: linear-gradient(135deg, #1cc88a 0%, #169b6b 100%);
    }

    .detail-card-header.danger {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    }

    .detail-card-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-card-body {
        padding: 1.5rem;
    }

    .detail-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .detail-row:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        flex: 0 0 150px;
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
    }

    .detail-value {
        flex: 1;
        color: #212529;
        word-break: break-word;
    }

    .detail-value small {
        display: block;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .badge-lg {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }

    .content-preview {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem;
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 0.875rem;
        max-height: 300px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-item {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 0.75rem;
        text-align: center;
    }

    .stat-item.success {
        background: linear-gradient(135deg, #1cc88a 0%, #169b6b 100%);
    }

    .stat-item.info {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .stat-item.warning {
        background: linear-gradient(135deg, #f6c23e 0%, #daa520 100%);
    }

    .stat-item h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .stat-item small {
        display: block;
        opacity: 0.9;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .action-buttons .btn {
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        min-height: 44px;
    }

    .alert-modern {
        background: white;
        border: 1px solid #e9ecef;
        border-left: 4px solid;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
    }

    .alert-modern.success {
        border-left-color: #1cc88a;
        background: #f0fdf4;
    }

    .alert-modern.danger {
        border-left-color: #e74c3c;
        background: #fef2f2;
    }

    .alert-modern.info {
        border-left-color: #4e73df;
        background: #f0f7ff;
    }

    .alert-modern.warning {
        border-left-color: #f6c23e;
        background: #fffbf0;
    }

    .alert-modern i {
        margin-right: 0.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .detail-label {
            flex: 0 0 120px;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        }

        .stat-item h3 {
            font-size: 1.25rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 mb-1 text-gray-900">
                <i class="fas fa-envelope-open-text"></i> Detail Newsletter
            </h1>
            <p class="text-muted mb-0">Lihat informasi lengkap dan statistik newsletter Anda</p>
        </div>
        <a href="{{ route('cs.newsletters.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-left-success rounded-lg mb-4" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-left-danger rounded-lg mb-4" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Content -->
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Newsletter Details Card -->
            <div class="detail-card">
                <div class="detail-card-header info">
                    <h6>
                        <i class="fas fa-info-circle"></i> Informasi Newsletter
                    </h6>
                    @if($newsletter->status === 'draft')
                        <span class="badge bg-warning text-dark">Draft</span>
                    @elseif($newsletter->status === 'terkirim')
                        <span class="badge bg-success">Terkirim</span>
                    @elseif($newsletter->status === 'gagal')
                        <span class="badge bg-danger">Gagal</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($newsletter->status) }}</span>
                    @endif
                </div>
                <div class="detail-card-body">
                    <div class="detail-row">
                        <div class="detail-label">Judul</div>
                        <div class="detail-value">{{ $newsletter->judul }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Subjek Email</div>
                        <div class="detail-value">{{ $newsletter->subjek_email }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Metode Pengiriman</div>
                        <div class="detail-value">
                            @if($newsletter->metode_pengiriman === 'mailchimp')
                                <span class="badge bg-primary badge-lg">
                                    <i class="fas fa-envelope me-1"></i> Mailchimp
                                </span>
                            @elseif($newsletter->metode_pengiriman === 'fonnte')
                                <span class="badge bg-warning badge-lg">
                                    <i class="fab fa-whatsapp me-1"></i> Fonnte
                                </span>
                            @elseif($newsletter->metode_pengiriman === 'keduanya')
                                <span class="badge bg-success badge-lg">
                                    <i class="fas fa-layer-group me-1"></i> Keduanya
                                </span>
                            @else
                                <span class="badge bg-secondary badge-lg">Unknown</span>
                            @endif
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            @if($newsletter->status === 'draft')
                                <span class="badge bg-secondary text-white">
                                    <i class="fas fa-file me-1"></i> Draft
                                </span>
                                <small>Belum dikirim</small>
                            @elseif($newsletter->status === 'terkirim')
                                <span class="badge bg-success text-white">
                                    <i class="fas fa-check-circle me-1"></i> Terkirim
                                </span>
                                <small>Berhasil dikirim ke semua penerima</small>
                            @elseif($newsletter->status === 'mengirim')
                                <span class="badge bg-info text-white">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Mengirim
                                </span>
                                <small>Sedang dikirim...</small>
                            @else
                                <span class="badge bg-danger text-white">
                                    <i class="fas fa-times-circle me-1"></i> Gagal
                                </span>
                                <small>Terjadi kesalahan saat pengiriman</small>
                            @endif
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Dibuat Oleh</div>
                        <div class="detail-value">{{ $newsletter->creator->name ?? 'Unknown' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Tanggal Dibuat</div>
                        <div class="detail-value">{{ $newsletter->created_at->format('d M Y H:i') }}</div>
                    </div>

                    @if($newsletter->tanggal_kirim)
                    <div class="detail-row">
                        <div class="detail-label">Tanggal Kirim</div>
                        <div class="detail-value">{{ $newsletter->tanggal_kirim->format('d M Y H:i') }}</div>
                    </div>
                    @endif

                    @if($newsletter->mailchimp_campaign_id)
                    <div class="detail-row">
                        <div class="detail-label">Campaign ID</div>
                        <div class="detail-value">
                            <code class="bg-light p-2 rounded">{{ $newsletter->mailchimp_campaign_id }}</code>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Content Preview Card -->
            <div class="detail-card">
                <div class="detail-card-header info">
                    <h6>
                        <i class="fas fa-eye"></i> Preview Konten Email
                    </h6>
                </div>
                <div class="detail-card-body">
                    <div class="mb-3">
                        <strong class="d-block mb-2">Subjek:</strong>
                        <span class="text-muted">{{ $newsletter->subjek_email }}</span>
                    </div>
                    <hr>
                    <div>
                        <strong class="d-block mb-2">Isi Email:</strong>
                        <div class="content-preview">{{ $newsletter->konten_email }}</div>
                    </div>
                </div>
            </div>

            @if($newsletter->konten_html)
            <!-- HTML Content Card -->
            <div class="detail-card">
                <div class="detail-card-header info">
                    <h6>
                        <i class="fas fa-code"></i> Kode HTML
                    </h6>
                </div>
                <div class="detail-card-body">
                    <details class="w-100">
                        <summary class="cursor-pointer text-primary fw-bold mb-2">
                            <i class="fas fa-chevron-right me-1"></i> Tampilkan Kode HTML
                        </summary>
                        <div class="content-preview mt-3">{{ $newsletter->konten_html }}</div>
                    </details>
                </div>
            </div>
            @endif

            <!-- Tracking Statistics -->
            @if($newsletter->status === 'terkirim' && $newsletter->metode_pengiriman !== 'fonnte')
            <div class="detail-card">
                <div class="detail-card-header success">
                    <h6>
                        <i class="fas fa-chart-bar"></i> Statistik Pengiriman
                    </h6>
                </div>
                <div class="detail-card-body">
                    @if($mailchimpStats)
                        <!-- Mailchimp Statistics -->
                        <div class="stats-grid">
                            <div class="stat-item success">
                                <h3>{{ $mailchimpStats['emails_sent'] ?? 0 }}</h3>
                                <small>Terkirim</small>
                            </div>
                            <div class="stat-item info">
                                <h3>{{ $mailchimpStats['unique_opens'] ?? 0 }}</h3>
                                <small>Dibuka</small>
                            </div>
                            <div class="stat-item warning">
                                <h3>{{ number_format($mailchimpStats['open_rate'] ?? 0, 1) }}%</h3>
                                <small>Open Rate</small>
                            </div>
                            <div class="stat-item">
                                <h3>{{ $mailchimpStats['unique_clicks'] ?? 0 }}</h3>
                                <small>Diklik</small>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-lg-6">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="newsletterEngagementChart"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="newsletterRateChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="alert-modern info mt-3 mb-0">
                            <i class="fas fa-info-circle text-info"></i>
                            <strong>Info:</strong> Data statistik diambil langsung dari Mailchimp dan diperbarui real-time.
                        </div>
                    @else
                        <!-- Local Database Statistics -->
                        <div class="stats-grid">
                            <div class="stat-item success">
                                <h3>{{ $totalSent ?? 0 }}</h3>
                                <small>Terkirim</small>
                            </div>
                            <div class="stat-item info">
                                <h3>{{ $totalOpened ?? 0 }}</h3>
                                <small>Dibuka</small>
                            </div>
                            <div class="stat-item warning">
                                <h3>{{ number_format($openRate ?? 0, 1) }}%</h3>
                                <small>Open Rate</small>
                            </div>
                            <div class="stat-item">
                                <h3>{{ $totalClicked ?? 0 }}</h3>
                                <small>Diklik</small>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-lg-6">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="newsletterEngagementChartLocal"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="newsletterRateChartLocal"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="alert-modern warning mt-3 mb-0">
                            <i class="fas fa-database text-warning"></i>
                            <strong>Info:</strong> Data statistik dari database lokal (Mailchimp tidak tersedia).
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Table -->
            @if($mailchimpActivity && $mailchimpActivity->count() > 0)
                <div class="detail-card">
                    <div class="detail-card-header info">
                        <h6>
                            <i class="fas fa-list"></i> Detail Penerima
                        </h6>
                    </div>
                    <div class="detail-card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-bold">Email</th>
                                        <th class="fw-bold">Status</th>
                                        <th class="fw-bold">Dibuka</th>
                                        <th class="fw-bold">Diklik</th>
                                        <th class="fw-bold text-center">Total Buka</th>
                                        <th class="fw-bold text-center">Total Klik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mailchimpActivity as $activity)
                                    <tr>
                                        <td><small>{{ $activity['email'] }}</small></td>
                                        <td>
                                            @if($activity['status'] === 'sent')
                                                <span class="badge bg-success text-white">Terkirim</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activity['first_open'])
                                                <i class="fas fa-check text-success me-1"></i>
                                                <small>{{ \Carbon\Carbon::parse($activity['first_open'])->format('d/m/y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activity['first_click'])
                                                <i class="fas fa-check text-primary me-1"></i>
                                                <small>{{ \Carbon\Carbon::parse($activity['first_click'])->format('d/m/y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($activity['open_count'] > 0)
                                                <span class="badge bg-info">{{ $activity['open_count'] }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($activity['click_count'] > 0)
                                                <span class="badge bg-primary">{{ $activity['click_count'] }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($newsletter->trackings->count() > 0)
                <div class="detail-card">
                    <div class="detail-card-header info">
                        <h6>
                            <i class="fas fa-list"></i> Detail Penerima
                        </h6>
                    </div>
                    <div class="detail-card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-bold">Pelanggan</th>
                                        <th class="fw-bold">Kontak (Email/WA)</th>
                                        <th class="fw-bold">Status</th>
                                        <th class="fw-bold">Dibuka</th>
                                        <th class="fw-bold">Diklik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($newsletter->trackings as $tracking)
                                    <tr>
                                        <td><small>{{ $tracking->pelanggan->nama_pelanggan ?? 'Unknown' }}</small></td>
                                        <td><small>{{ $tracking->email_tujuan ?? 'N/A' }}</small></td>
                                        <td>
                                            @if($tracking->status_kirim === 'terkirim')
                                                <span class="badge bg-success text-white">Terkirim</span>
                                            @else
                                                <span class="badge bg-danger text-white">Gagal</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tracking->waktu_dibuka)
                                                <i class="fas fa-check text-success me-1"></i>
                                                <small>{{ \Carbon\Carbon::parse($tracking->waktu_dibuka)->format('d/m/y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tracking->waktu_klik)
                                                <i class="fas fa-check text-primary me-1"></i>
                                                <small>{{ \Carbon\Carbon::parse($tracking->waktu_klik)->format('d/m/y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="detail-card mb-4">
                <div class="detail-card-header info">
                    <h6>
                        <i class="fas fa-cogs"></i> Aksi
                    </h6>
                </div>
                <div class="detail-card-body">
                    <div class="action-buttons">
                        @if($newsletter->status === 'draft')
                            <a href="{{ route('cs.newsletters.edit', $newsletter->id_newsletter) }}"
                               class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i> Edit Newsletter
                            </a>

                            <form action="{{ route('cs.newsletters.send', $newsletter->id_newsletter) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin mengirim newsletter ini?');">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Newsletter
                                </button>
                            </form>

                            <form action="{{ route('cs.newsletters.destroy', $newsletter->id_newsletter) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus newsletter ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-2"></i> Hapus Newsletter
                                </button>
                            </form>
                        @elseif($newsletter->status === 'terkirim')
                            <div class="alert-modern success mb-3">
                                <i class="fas fa-check-circle"></i>
                                <strong>Terkirim</strong>
                                <p class="mb-0 mt-1 small">Newsletter berhasil dikirim ke semua subscribers.</p>
                            </div>

                            @if($newsletter->mailchimp_campaign_id)
                            <a href="https://us21.admin.mailchimp.com/campaigns/show/?id={{ $newsletter->mailchimp_campaign_id }}"
                               target="_blank"
                               class="btn btn-info w-100">
                                <i class="fas fa-external-link-alt me-2"></i> Lihat di Mailchimp
                            </a>
                            @endif
                        @elseif($newsletter->status === 'gagal')
                            <div class="alert-modern danger mb-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Pengiriman Gagal</strong>
                                <p class="mb-0 mt-1 small">Terjadi kesalahan saat mengirim newsletter.</p>
                            </div>

                            <form action="{{ route('cs.newsletters.send', $newsletter->id_newsletter) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin mencoba mengirim ulang?');">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-redo me-2"></i> Coba Kirim Ulang
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delivery Method Card -->
            <div class="detail-card mb-4">
                <div class="detail-card-header info">
                    <h6>
                        <i class="fas fa-paper-plane"></i> Metode Pengiriman
                    </h6>
                </div>
                <div class="detail-card-body">
                    @if($newsletter->metode_pengiriman === 'mailchimp')
                        <div class="text-center mb-3">
                            <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                            <h6 class="mb-0">Mailchimp</h6>
                        </div>
                        <p class="text-muted small text-center mb-3">
                            Newsletter dikirim melalui Mailchimp API ke semua subscribers.
                        </p>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Open rate tracking</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Click rate tracking</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Bounce handling</li>
                            <li><i class="fas fa-check text-success me-2"></i> Template responsive</li>
                        </ul>
                    @elseif($newsletter->metode_pengiriman === 'fonnte')
                        <div class="text-center mb-3">
                            <i class="fab fa-whatsapp fa-2x text-warning mb-2"></i>
                            <h6 class="mb-0">Fonnte</h6>
                        </div>
                        <p class="text-muted small text-center mb-3">
                            Newsletter dikirim melalui Fonnte API ke semua subscribers.
                        </p>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Real-time tracking</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Webhook support</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Advanced analytics</li>
                            <li><i class="fas fa-check text-success me-2"></i> High reliability</li>
                        </ul>
                    @elseif($newsletter->metode_pengiriman === 'keduanya')
                        <div class="text-center mb-3">
                            <i class="fas fa-layer-group fa-2x text-success mb-2"></i>
                            <h6 class="mb-0">Mailchimp + Fonnte</h6>
                        </div>
                        <p class="text-muted small text-center mb-3">
                            Newsletter dikirim ke kedua platform untuk jangkauan maksimal.
                        </p>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Double coverage</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Tracking lengkap</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> High reliability</li>
                            <li><i class="fas fa-check text-success me-2"></i> Maksimal jangkauan</li>
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Status Info Card -->
            <div class="detail-card">
                <div class="detail-card-header info">
                    <h6>
                        <i class="fas fa-info-circle"></i> Status Newsletter
                    </h6>
                </div>
                <div class="detail-card-body">
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            @if($newsletter->status === 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @elseif($newsletter->status === 'terkirim')
                                <span class="badge bg-success">Terkirim</span>
                            @elseif($newsletter->status === 'mengirim')
                                <span class="badge bg-info">Mengirim</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($newsletter->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Jenis</div>
                        <div class="detail-value">
                            @if($newsletter->jenis_newsletter === 'mailchimp')
                                <span class="badge bg-primary">Mailchimp</span>
                            @elseif($newsletter->jenis_newsletter === 'fonnte')
                                <span class="badge bg-warning">Fonnte</span>
                            @elseif($newsletter->jenis_newsletter === 'keduanya')
                                <span class="badge bg-success">Keduanya</span>
                            @endif
                        </div>
                    </div>

                    @if($newsletter->total_penerima)
                    <div class="detail-row">
                        <div class="detail-label">Penerima</div>
                        <div class="detail-value">
                            {{ number_format($newsletter->total_penerima) }} orang
                            @if($newsletter->total_terkirim || $newsletter->total_gagal)
                                <small class="d-block">✓ {{ $newsletter->total_terkirim ?? 0 }} | ✗ {{ $newsletter->total_gagal ?? 0 }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => alert.style.display = 'none', 300);
        });
    }, 5000);

    // Chart Color Palette
    const chartColors = {
        success: '#1cc88a',
        info: '#4e73df',
        primary: '#0099ff',
        warning: '#f6c23e',
        danger: '#e74c3c',
        secondary: '#6c757d',
        light: '#f8f9fa',
        dark: '#343a40'
    };

    // Chart Options
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: { size: 12, weight: '500' }
                }
            }
        }
    };

    // Initialize Newsletter Engagement Chart (Mailchimp)
    @if($mailchimpStats)
    const engagementCtx = document.getElementById('newsletterEngagementChart');
    if (engagementCtx) {
        new Chart(engagementCtx, {
            type: 'bar',
            data: {
                labels: ['Terkirim', 'Dibuka', 'Diklik'],
                datasets: [{
                    label: 'Jumlah',
                    data: [
                        {{ $mailchimpStats['emails_sent'] ?? 0 }},
                        {{ $mailchimpStats['unique_opens'] ?? 0 }},
                        {{ $mailchimpStats['unique_clicks'] ?? 0 }}
                    ],
                    backgroundColor: [
                        chartColors.success,
                        chartColors.info,
                        chartColors.primary
                    ],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Initialize Newsletter Rate Chart (Mailchimp)
    const rateCtx = document.getElementById('newsletterRateChart');
    if (rateCtx) {
        const openRate = {{ number_format($mailchimpStats['open_rate'] ?? 0, 2, '.', '') }};
        const clickRate = {{ number_format($mailchimpStats['click_rate'] ?? 0, 2, '.', '') }};
        const otherRate = Math.max(0, 100 - openRate - clickRate);

        new Chart(rateCtx, {
            type: 'doughnut',
            data: {
                labels: ['Open Rate', 'Click Rate', 'Lainnya'],
                datasets: [{
                    data: [openRate, clickRate, otherRate],
                    backgroundColor: [
                        chartColors.info,
                        chartColors.primary,
                        chartColors.light
                    ],
                    borderColor: ['white', 'white', 'white'],
                    borderWidth: 3
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    @else
    // Initialize Newsletter Engagement Chart (Local Database)
    const engagementCtxLocal = document.getElementById('newsletterEngagementChartLocal');
    if (engagementCtxLocal) {
        new Chart(engagementCtxLocal, {
            type: 'bar',
            data: {
                labels: ['Terkirim', 'Dibuka', 'Diklik'],
                datasets: [{
                    label: 'Jumlah',
                    data: [
                        {{ $totalSent ?? 0 }},
                        {{ $totalOpened ?? 0 }},
                        {{ $totalClicked ?? 0 }}
                    ],
                    backgroundColor: [
                        chartColors.success,
                        chartColors.info,
                        chartColors.primary
                    ],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Initialize Newsletter Rate Chart (Local Database)
    const rateCtxLocal = document.getElementById('newsletterRateChartLocal');
    if (rateCtxLocal) {
        const openRateLocal = {{ number_format($openRate ?? 0, 2, '.', '') }};
        const clickRateLocal = {{ number_format($clickRate ?? 0, 2, '.', '') }};
        const otherRateLocal = Math.max(0, 100 - openRateLocal - clickRateLocal);

        new Chart(rateCtxLocal, {
            type: 'doughnut',
            data: {
                labels: ['Open Rate', 'Click Rate', 'Lainnya'],
                datasets: [{
                    data: [openRateLocal, clickRateLocal, otherRateLocal],
                    backgroundColor: [
                        chartColors.info,
                        chartColors.primary,
                        chartColors.light
                    ],
                    borderColor: ['white', 'white', 'white'],
                    borderWidth: 3
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
</script>
@endpush
