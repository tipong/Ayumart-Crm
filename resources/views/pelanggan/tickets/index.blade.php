@extends('layouts.pelanggan')

@section('title', 'Tiket Bantuan Saya')

@section('content')
<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <div>
                    <h1>Pusat Bantuan</h1>
                    <p>Kelola pertanyaan, keluhan, dan tiket bantuan Anda</p>
                </div>
            </div>
            <button class="btn" style="background:rgba(255,255,255,0.2);color:#fff;border-radius:100px;font-weight:700;font-size:14px;padding:8px 20px;" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="bi bi-plus-lg me-1"></i> Tiket Baru
            </button>
        </div>
    </div>
</div>

<div class="container py-3 pb-5">
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Pusat Bantuan</li>
        </ol>
    </nav>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Tickets List -->
            <div class="ay-card">
                <div class="ay-card-header">
                    <i class="bi bi-list-stars"></i> Riwayat Tiket Bantuan
                </div>
                <div class="ay-card-body">
                    @if($tickets && count($tickets) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Tiket</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr data-ticket-id="{{ $ticket->id }}" @if(!$ticket->is_read) class="table-light table-unread" style="background-color: #e3f2fd; border-left: 4px solid #1976d2;" @endif>
                                    <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                    <td>
                                        <div class="fw-medium">
                                            {{ Str::limit($ticket->subject, 50) }}
                                        </div>
                                        <small class="text-muted">{{ Str::limit($ticket->description, 60) }}</small>
                                    </td>
                                    <td>
                                        @if($ticket->priority === 'high')
                                            <span class="badge bg-danger">High</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span class="badge bg-warning">Medium</span>
                                        @else
                                            <span class="badge bg-secondary">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->status === 'open')
                                            <span class="badge bg-info">Open</span>
                                        @elseif($ticket->status === 'in_progress')
                                            <span class="badge bg-primary">In Progress</span>
                                        @elseif($ticket->status === 'resolved')
                                            <span class="badge bg-success">Resolved</span>
                                        @else
                                            <span class="badge bg-dark">Closed</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $ticket->created_at->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $ticket->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('pelanggan.tickets.show', $ticket->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($tickets, 'links'))
                        <div class="mt-3">{{ $tickets->links() }}</div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #cbd5e1;"></i>
                        <h5 class="mt-3 text-muted">Belum ada tiket</h5>
                        <p class="text-muted">Buat tiket baru jika Anda memiliki pertanyaan atau keluhan</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                            <i class="bi bi-plus-circle"></i> Buat Tiket Pertama
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header text-white" style="background: var(--primary); border: none;">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Buat Tiket Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pelanggan.tickets.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Produk">Pertanyaan Produk</option>
                            <option value="Pesanan">Masalah Pesanan</option>
                            <option value="Pembayaran">Masalah Pembayaran</option>
                            <option value="Pengiriman">Masalah Pengiriman</option>
                            <option value="Akun">Masalah Akun</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            <option value="low">Low - Pertanyaan umum</option>
                            <option value="medium" selected>Medium - Perlu bantuan</option>
                            <option value="high">High - Masalah mendesak</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject" placeholder="Ringkasan masalah Anda" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" rows="5" placeholder="Jelaskan masalah Anda dengan detail..." required></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <small>Tim CS kami akan merespon tiket Anda dalam waktu 1x24 jam.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal" style="border-radius:100px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:100px;">
                        <i class="bi bi-send"></i> Kirim Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Load Ticket Notification Manager -->
<script src="{{ asset('js/ticket-notifications.js') }}"></script>
<script>
    // Initialize notification manager when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TicketNotificationManager !== 'undefined') {
            window.ticketNotificationManager = new TicketNotificationManager({
                pollInterval: 15000, // 15 seconds
                badgeSelector: '[data-badge-tickets]',
                ticketRowSelector: '[data-ticket-id]',
                enableDesktopNotifications: true,
                apiUrl: '/api'
            });
        }
    });
</script>
@endsection
