@extends('layouts.staff')

@section('title', 'Dashboard Customer Service')

@section('sidebar-menu')
    <a class="nav-link active" href="{{ route('cs.dashboard') }}">
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

    <a class="nav-link" href="{{ route('cs.newsletters.index') }}">
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
                <i class="fas fa-chart-line"></i> Dashboard
            </h1>
            <p class="text-muted mb-0">Selamat datang kembali, {{ auth()->user()->name }}! Lihat ringkasan aktivitas Anda.</p>
        </div>
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

    <!-- Statistics Overview Section -->
    <!-- Charts Section -->
    <div class="row g-4 mb-5">
            <!-- Ticketing Statistics Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header border-0 bg-transparent p-4 pb-2">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-chart-bar text-primary me-2"></i> Statistik Ticketing (12 Bulan)
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-3">
                        <canvas id="ticketingChart" height="80"></canvas>
                        <div class="row g-3 mt-4 pt-3 border-top">
                            <div class="col-4">
                                <div class="text-center">
                                    <p class="text-muted small mb-1">Total Tickets</p>
                                    <p class="mb-0 fw-bold h5 text-primary">{{ $totalTickets ?? 0 }}</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <p class="text-muted small mb-1">Pesan</p>
                                    <p class="mb-0 fw-bold h5 text-warning">{{ $totalMessages ?? 0 }}</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <p class="text-muted small mb-1">Selesai</p>
                                    <p class="mb-0 fw-bold h5 text-success">{{ $resolvedTickets ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Statistics Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header border-0 bg-transparent p-4 pb-2">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-rocket text-success me-2"></i> Statistik Campaign (12 Bulan)
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-3">
                        <canvas id="campaignChart" height="80"></canvas>
                        <div class="row g-3 mt-4 pt-3 border-top">
                            <div class="col-6">
                                <div class="text-center">
                                    <p class="text-muted small mb-1">Campaign Dibuat</p>
                                    <p class="mb-0 fw-bold h5 text-warning">{{ $totalNewsletters ?? $totalNewslettersSent ?? 0 }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <p class="text-muted small mb-1">Campaign Dikirim</p>
                                    <p class="mb-0 fw-bold h5 text-success">{{ $totalNewslettersSent ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    <!-- Latest Tickets -->
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header border-0 bg-transparent p-4 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-ticket-alt text-primary me-2"></i> Tickets Terbaru
                    </h6>
                    <a href="{{ route('cs.tickets.index') }}" class="btn btn-sm btn-primary rounded-pill">
                        <i class="fas fa-eye me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-4 pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="table-light">
                                    <th class="fw-bold">Pelanggan</th>
                                    <th class="fw-bold">Subject</th>
                                    <th class="fw-bold">Status</th>
                                    <th class="fw-bold">Tanggal</th>
                                    <th class="fw-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestTickets ?? [] as $ticket)
                                <tr class="border-bottom small">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle bg-light me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <span class="text-dark fw-500">{{ $ticket->user->name ?? 'Guest' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span title="{{ $ticket->subject }}">{{ Str::limit($ticket->subject, 35) }}</span>
                                    </td>
                                    <td>
                                        @if($ticket->status === 'open')
                                            <span class="badge bg-warning-light text-warning px-2 py-1">
                                                <i class="fas fa-circle-notch me-1"></i> Open
                                            </span>
                                        @elseif($ticket->status === 'in_progress')
                                            <span class="badge bg-info-light text-info px-2 py-1">
                                                <i class="fas fa-hourglass-half me-1"></i> In Progress
                                            </span>
                                        @elseif($ticket->status === 'resolved')
                                            <span class="badge bg-success-light text-success px-2 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Resolved
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-light text-secondary px-2 py-1">
                                                <i class="fas fa-times-circle me-1"></i> Closed
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $ticket->created_at->format('d M Y H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('cs.tickets.show', $ticket->id) }}" class="btn btn-sm btn-light rounded-circle">
                                            <i class="fas fa-arrow-right text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                        <p class="mb-0 mt-3">Belum ada tickets</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Subscribers - Mailchimp & Fonnte -->
    <div class="row g-4 mb-5" id="subscribers">
        <!-- Mailchimp Subscribers -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header border-0 bg-transparent p-4 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-envelope text-primary me-2"></i> Email Subscribers
                    </h6>
                    <span class="badge bg-light text-primary px-3 py-2">
                        <i class="fas fa-users me-1"></i> {{ $subscribersCount ?? 0 }}
                    </span>
                </div>
                <div class="card-body p-4 pt-3">
                    <div class="d-flex gap-2 mb-4">
                        <button type="button" class="btn btn-sm btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                        <a href="{{ route('cs.newsletters.index') }}" class="btn btn-sm btn-primary rounded-pill">
                            <i class="fas fa-paper-plane me-1"></i> Buat Newsletter
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-sm">
                            <thead>
                                <tr class="table-light">
                                    <th class="fw-bold">Email</th>
                                    <th class="fw-bold">Nama</th>
                                    <th class="fw-bold">Tanggal Daftar</th>
                                    <th class="fw-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscribers ?? [] as $subscriber)
                                <tr class="border-bottom small">
                                    <td>
                                        <i class="fas fa-envelope text-muted me-1"></i>
                                        <span class="text-dark fw-500">{{ $subscriber->email }}</span>
                                    </td>
                                    <td>
                                        @if(!empty($subscriber->first_name) || !empty($subscriber->last_name))
                                            {{ trim($subscriber->first_name . ' ' . $subscriber->last_name) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscriber->created_at)
                                            <i class="fas fa-calendar text-muted me-1"></i>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($subscriber->created_at)->format('d M Y') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light rounded-circle" onclick="unsubscribe('{{ $subscriber->email }}')">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox" style="font-size: 1.5rem; opacity: 0.5;"></i>
                                        <p class="mb-0 mt-2 small">Belum ada subscribers</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(($subscribers ?? collect())->count() > 0 && $subscribersCount > ($subscribers ?? collect())->count())
                    <div class="alert alert-info alert-sm mt-3 mb-0" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Menampilkan {{ ($subscribers ?? collect())->count() }} dari {{ $subscribersCount ?? 0 }} subscribers</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Fonnte Subscribers -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header border-0 bg-transparent p-4 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fab fa-whatsapp text-success me-2"></i> WhatsApp Subscribers
                    </h6>
                    <span class="badge bg-light text-success px-3 py-2">
                        <i class="fas fa-users me-1"></i> {{ $fonnteSubscribersCount ?? 0 }}
                    </span>
                </div>
                <div class="card-body p-4 pt-3">
                    @if(($fonnteSubscribers ?? collect())->count() > 0)
                        <div class="d-flex gap-2 mb-4">
                            <button class="btn btn-sm btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#addFonnteSubscriberModal">
                                <i class="fas fa-plus me-1"></i> Tambah
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-sm">
                                <thead>
                                    <tr class="table-light">
                                        <th class="fw-bold">Nama</th>
                                        <th class="fw-bold">No. Telepon</th>
                                        <th class="fw-bold text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($fonnteSubscribers as $subscriber)
                                    <tr class="border-bottom small">
                                        <td>
                                            <i class="fas fa-user text-muted me-1"></i>
                                            <span class="text-dark fw-500">{{ $subscriber->nome ?? $subscriber->name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($subscriber->phone)
                                                <i class="fas fa-phone text-muted me-1"></i>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $subscriber->phone) }}" target="_blank" class="text-decoration-none">
                                                    {{ $subscriber->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($subscriber->phone)
                                                <button class="btn btn-sm btn-light rounded-circle" onclick="sendWhatsApp('{{ $subscriber->phone }}', '{{ $subscriber->nome ?? $subscriber->name }}')">
                                                    <i class="fab fa-whatsapp text-success"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox" style="font-size: 1.5rem; opacity: 0.5;"></i>
                                            <p class="mb-0 mt-2 small">Belum ada data</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="d-flex gap-2 mb-4">
                            <button class="btn btn-sm btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#addFonnteSubscriberModal">
                                <i class="fas fa-plus me-1"></i> Tambah
                            </button>
                        </div>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p class="mb-0 mt-3 text-muted">Belum ada WhatsApp Subscribers</p>
                            <small class="text-muted d-block mt-2">Tambahkan pelanggan dengan nomor telepon untuk memulai</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Send Newsletter Modal -->
    <div class="modal fade" id="sendNewsletterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-send"></i>
                        Kirim Newsletter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Newsletter akan dikirim ke <strong>{{ $subscribersCount ?? 0 }}</strong> subscribers.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Subscriber Modal -->
    <div class="modal fade" id="addSubscriberModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-3">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-plus me-2"></i> Tambah Email Subscriber
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('cs.subscribers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="subscriber_email" class="form-label fw-bold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   class="form-control form-control-lg rounded-2"
                                   id="subscriber_email"
                                   name="email"
                                   placeholder="contoh@email.com"
                                   required>
                            <small class="text-muted">Email harus valid dan belum terdaftar</small>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="subscriber_first_name" class="form-label fw-bold">Nama Depan</label>
                                <input type="text"
                                       class="form-control form-control-lg rounded-2"
                                       id="subscriber_first_name"
                                       name="first_name"
                                       placeholder="John">
                            </div>
                            <div class="col-md-6">
                                <label for="subscriber_last_name" class="form-label fw-bold">Nama Belakang</label>
                                <input type="text"
                                       class="form-control form-control-lg rounded-2"
                                       id="subscriber_last_name"
                                       name="last_name"
                                       placeholder="Doe">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 bg-light">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success rounded-pill">
                            <i class="fas fa-check me-1"></i> Tambah Subscriber
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<script>
    // ===== TICKETING CHART (Bar Chart with Monthly Data) =====
    const ticketingCtx = document.getElementById('ticketingChart');
    if (ticketingCtx) {
        new Chart(ticketingCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($ticketChartData['labels'] ?? []) !!},
                datasets: [
                    {
                        label: 'Total Tickets',
                        data: {!! json_encode($ticketChartData['total'] ?? []) !!},
                        backgroundColor: 'rgba(63, 79, 68, 0.8)',
                        borderColor: 'rgb(63, 79, 68)',
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(63, 79, 68, 1)',
                        order: 1
                    },
                    {
                        label: 'Pesan Ditangani',
                        data: {!! json_encode($ticketChartData['messages'] ?? []) !!},
                        backgroundColor: 'rgba(246, 194, 62, 0.8)',
                        borderColor: 'rgb(246, 194, 62)',
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(246, 194, 62, 1)',
                        order: 2
                    },
                    {
                        label: 'Tickets Selesai',
                        data: {!! json_encode($ticketChartData['resolved'] ?? []) !!},
                        backgroundColor: 'rgba(28, 200, 138, 0.8)',
                        borderColor: 'rgb(28, 200, 138)',
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(28, 200, 138, 1)',
                        order: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.raw.toLocaleString('id-ID');
                                return label + ': ' + value;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        stacked: false
                    }
                }
            }
        });
    }

    // ===== CAMPAIGN CHART (Line Chart with Monthly Data) =====
    const campaignCtx = document.getElementById('campaignChart');
    if (campaignCtx) {
        new Chart(campaignCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($campaignChartData['labels'] ?? []) !!},
                datasets: [
                    {
                        label: 'Campaign Dibuat',
                        data: {!! json_encode($campaignChartData['created'] ?? []) !!},
                        backgroundColor: 'rgba(246, 194, 62, 0.8)',
                        borderColor: 'rgb(246, 194, 62)',
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(246, 194, 62, 1)',
                        order: 1
                    },
                    {
                        label: 'Campaign Dikirim',
                        data: {!! json_encode($campaignChartData['sent'] ?? []) !!},
                        backgroundColor: 'rgba(28, 200, 138, 0.8)',
                        borderColor: 'rgb(28, 200, 138)',
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(28, 200, 138, 1)',
                        order: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.raw.toLocaleString('id-ID');
                                return label + ': ' + value + ' campaign';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        stacked: false
                    }
                }
            }
        });
    }

    function unsubscribe(email) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Hapus subscriber ' + email + ' dari Mailchimp?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("cs.subscribers.destroy") }}';

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                var methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                var emailField = document.createElement('input');
                emailField.type = 'hidden';
                emailField.name = 'email';
                emailField.value = email;
                form.appendChild(emailField);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function sendWhatsApp(phone, name) {
        // Remove non-numeric characters from phone
        const cleanPhone = phone.replace(/[^0-9]/g, '');

        // Ensure it starts with country code (62 for Indonesia)
        let fullPhone = cleanPhone;
        if (!cleanPhone.startsWith('62')) {
            // If it starts with 0, replace with 62
            if (cleanPhone.startsWith('0')) {
                fullPhone = '62' + cleanPhone.substring(1);
            } else {
                fullPhone = '62' + cleanPhone;
            }
        }

        // WhatsApp Web URL
        const whatsappUrl = `https://web.whatsapp.com/send?phone=${fullPhone}&text=Halo ${encodeURIComponent(name || 'Pelanggan')}, `;

        // Open WhatsApp Web
        window.open(whatsappUrl, '_blank');
    }
</script>
@endpush

<!-- Add Fonnte Subscriber Modal -->
<div class="modal fade" id="addFonnteSubscriberModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-0 bg-light p-4">
                <h5 class="modal-title fw-bold">
                    <i class="fab fa-whatsapp me-2 text-success"></i> Tambah WhatsApp Subscriber
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cs.fonnte-subscribers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="fonnteSubscriberName" class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control form-control-lg rounded-2"
                               id="fonnteSubscriberName"
                               name="name"
                               required
                               placeholder="Masukkan nama">
                    </div>
                    <div class="mb-3">
                        <label for="fonnteSubscriberPhone" class="form-label fw-bold">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="tel"
                               class="form-control form-control-lg rounded-2"
                               id="fonnteSubscriberPhone"
                               name="phone"
                               required
                               placeholder="Contoh: 0812345678">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill">
                        <i class="fas fa-check me-1"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .container-fluid {
        background-color: #f8f9fa;
    }

    .card {
        border: none !important;
        border-radius: 0.75rem !important;
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    .card-header {
        background: transparent !important;
        border-bottom: 1px solid #e3e6f0 !important;
    }

    .rounded-3 {
        border-radius: 0.75rem !important;
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }

    .table-light {
        background-color: #f8f9fa !important;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa !important;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.3rem;
    }

    .btn-light {
        background-color: #f0f0f0;
        border: 1px solid #e0e0e0;
        transition: all 0.2s ease;
    }

    .btn-light:hover {
        background-color: #e8e8e8;
        border-color: #d0d0d0;
    }

    .modal-content {
        border: none !important;
    }

    .form-control-lg {
        border-radius: 0.5rem;
        padding: 0.85rem 1rem;
    }

    .form-control-lg:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
    }

    .text-gray-900 {
        color: #2e3338;
    }

    .bg-warning-light {
        background: #fff3cd;
    }

    .bg-info-light {
        background: #d1ecf1;
    }

    .bg-success-light {
        background: #d4edda;
    }

    .bg-secondary-light {
        background: #e2e3e5;
    }

    .text-warning {
        color: #ffc107;
    }

    .text-info {
        color: #17a2b8;
    }

    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }

    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }

        .table {
            font-size: 0.875rem;
        }

        .btn-sm {
            padding: 0.35rem 0.6rem;
            font-size: 0.8rem;
        }
    }

</style>
@endpush
