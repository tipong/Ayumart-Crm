@extends('layouts.staff')

@section('title', 'Ticketing System - Customer Service')

@push('styles')
<style>
    /* Modern Ticketing Styling */
    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header p {
        font-size: 0.95rem;
    }

    /* Stat Cards */
    .stat-card {
        border: none;
        border-radius: 0.75rem;
        padding: 1.5rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        min-height: 100px;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .stat-card.bg-primary-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card.bg-warning-gradient {
        background: linear-gradient(135deg, #f6c23e 0%, #daa520 100%);
    }

    .stat-card.bg-info-gradient {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .stat-card.bg-success-gradient {
        background: linear-gradient(135deg, #1cc88a 0%, #169b6b 100%);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
    }

    .stat-subtitle {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 0.25rem;
    }

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e9ecef;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
    }

    .filter-card .form-label {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-select, .form-control {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-select:focus, .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e9ecef;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }

    .table-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .table-card-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-card-body {
        padding: 0;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead {
        background-color: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }

    .table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem 1.25rem;
        border-bottom: none;
        font-size: 0.85rem;
    }

    .table tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.04);
        transform: translateX(2px);
    }

    .table tbody tr.table-unread {
        background-color: #f0f7ff !important;
        border-left: 4px solid #4e73df;
    }

    /* Badge Styling */
    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        font-size: 0.8rem;
        border-radius: 0.4rem;
    }

    /* Avatar */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
    }

    /* Button Group */
    .btn-group-sm .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .btn-group-sm .btn:hover {
        transform: translateY(-1px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-card {
            min-height: auto;
            flex-direction: column;
            text-align: center;
        }

        .stat-icon {
            font-size: 2rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .filter-card {
            padding: 1rem;
        }

        .table-responsive {
            font-size: 0.85rem;
        }

        .table thead th,
        .table tbody td {
            padding: 0.75rem 0.5rem;
        }
    }

    /* New Message Indicator */
    .unread-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #e74c3c;
        color: white;
        font-weight: 700;
        font-size: 0.75rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .ticket-row-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #e74c3c;
        color: white;
        font-weight: 700;
        font-size: 0.7rem;
        margin-left: 0.5rem;
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

    <a class="nav-link active" href="{{ route('cs.tickets.index') }}">
        <i class="bi bi-ticket-perforated"></i>
        <span>Ticketing</span>
        @php
            $unreadCount = \App\Models\Ticket::where('is_read', false)->count();
        @endphp
        @if($unreadCount > 0)
            <span class="badge bg-danger ms-auto" data-badge-tickets>{{ $unreadCount }}</span>
        @else
            <span class="badge bg-danger ms-auto" data-badge-tickets style="display: none;"></span>
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
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1>
                    <i class="fas fa-ticket-alt"></i> Ticketing System
                </h1>
                <p class="text-muted mb-0">Kelola keluhan dan pertanyaan pelanggan dengan mudah</p>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-left-success rounded-lg mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-left-danger rounded-lg mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-left-danger rounded-lg mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <strong>Terjadi Kesalahan</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-primary-gradient">
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Tickets</div>
                    <div class="stat-value">{{ $totalTickets ?? 0 }}</div>
                    <div class="stat-subtitle">Semua tickets</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-warning-gradient">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Open Tickets</div>
                    <div class="stat-value">{{ $openTickets ?? 0 }}</div>
                    <div class="stat-subtitle">Belum ditangani</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-info-gradient">
                <div class="stat-icon">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value">{{ $inProgressTickets ?? 0 }}</div>
                    <div class="stat-subtitle">Sedang ditangani</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-success-gradient">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Resolved</div>
                    <div class="stat-value">{{ $resolvedTickets ?? 0 }}</div>
                    <div class="stat-subtitle">Selesai</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form method="GET" action="{{ route('cs.tickets.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    <option value="">Semua Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Cari Ticket</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="ID, subject, atau nama pelanggan...">
                </div>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="table-card">
        <div class="table-card-header">
            <h6>
                <i class="fas fa-list"></i> Daftar Tickets
            </h6>
        </div>
        <div class="table-card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Subject</th>
                            <th style="width: 100px;">Priority</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 150px;">Tanggal</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets ?? [] as $ticket)
                        <tr data-ticket-id="{{ $ticket->id }}" @if(!$ticket->is_read) class="table-unread" @endif>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ substr($ticket->user->pelanggan->nama_pelanggan ?? $ticket->user->email ?? 'G', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">
                                            {{ $ticket->user->pelanggan->nama_pelanggan ?? $ticket->user->email ?? 'Guest' }}
                                            @if(!$ticket->is_read)
                                                <span class="ticket-row-badge" title="Belum dibaca">!</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $ticket->user->email ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium">{{ Str::limit($ticket->subject, 50) }}</div>
                                <small class="text-muted">{{ Str::limit($ticket->description, 60) }}</small>
                            </td>
                            <td>
                                @if($ticket->priority === 'high')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-arrow-up"></i> High
                                    </span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-minus"></i> Medium
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-arrow-down"></i> Low
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->status === 'open')
                                    <span class="badge bg-info text-white">
                                        <i class="fas fa-circle-dot"></i> Open
                                    </span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-spinner"></i> In Progress
                                    </span>
                                @elseif($ticket->status === 'resolved')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Resolved
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle"></i> Closed
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $ticket->created_at->format('d M Y') }}</small><br>
                                <small class="text-muted">{{ $ticket->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('cs.tickets.show', $ticket->id) }}" class="btn btn-outline-primary" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-success" title="Balas" onclick="replyTicket({{ $ticket->id }})" type="button">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Hapus" onclick="deleteTicket({{ $ticket->id }})" type="button">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-inbox empty-state-icon"></i>
                                    <p class="text-muted fw-bold mb-1">Belum ada tickets</p>
                                    <small class="text-muted">Tickets dari pelanggan akan muncul di sini</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($tickets) && method_exists($tickets, 'hasPages') && $tickets->hasPages())
            <div class="mt-3 ps-3">
                {{ $tickets->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white; display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-size: 1rem;
}
.stat-subtitle { font-size: 0.75rem; opacity: 0.9; margin-top: 0.25rem; }
.table tbody tr { transition: all 0.2s; }
.table tbody tr:hover { background-color: rgba(102, 126, 234, 0.05); transform: translateY(-1px); }
.btn-group-sm .btn { padding: 0.25rem 0.5rem; }
</style>
@endpush

@push('scripts')
<script>
// Auto-close modal after successful form submission
@if(session('success'))
    var createModal = bootstrap.Modal.getInstance(document.getElementById('createTicketModal'));
    if (createModal) {
        createModal.hide();
    }
@endif

function replyTicket(ticketId) {
    Swal.fire({
        title: 'Balas Ticket #' + ticketId,
        html: '<textarea id="reply-message" class="form-control" rows="4" placeholder="Tulis balasan Anda..."></textarea>',
        showCancelButton: true,
        confirmButtonText: 'Kirim Balasan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        preConfirm: () => {
            const message = document.getElementById('reply-message').value;
            if (!message) {
                Swal.showValidationMessage('Pesan balasan tidak boleh kosong');
            }
            return { message: message };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Berhasil!', 'Balasan telah dikirim ke pelanggan.', 'success');
        }
    });
}
function deleteTicket(ticketId) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus ticket #' + ticketId + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send DELETE request to server
            fetch('{{ route("cs.tickets.index") }}/' + ticketId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: data.message || 'Ticket berhasil dihapus.',
                        icon: 'success'
                    }).then(() => {
                        // Reload page to update list
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Gagal menghapus ticket.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus ticket.', 'error');
            });
        }
    });
}
</script>

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
@endpush
