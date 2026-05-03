@extends('layouts.staff')

@section('title', 'Detail Tiket #' . $ticket->ticket_number)

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
            <span class="badge bg-danger ms-auto unread-badge">{{ $unreadCount }}</span>
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
            <div class="d-flex align-items-center gap-3 mb-1">
                <a href="{{ route('cs.tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0 text-gray-900">
                    <i class="bi bi-chat-dots"></i> #{{ $ticket->ticket_number }}
                </h1>
                @if($ticket->status === 'open')
                    <span class="badge bg-info">
                        <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Open
                    </span>
                @elseif($ticket->status === 'in_progress')
                    <span class="badge bg-primary">
                        <i class="bi bi-arrow-clockwise"></i> In Progress
                    </span>
                @elseif($ticket->status === 'resolved')
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i> Resolved
                    </span>
                @else
                    <span class="badge bg-dark">
                        <i class="bi bi-x-circle"></i> Closed
                    </span>
                @endif
            </div>
            <p class="text-muted mb-0">{{ $ticket->subject }}</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-left-success rounded-lg mb-4" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-left-danger rounded-lg mb-4" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- LEFT SIDE: Chat/Messages -->
        <div class="col-lg-8">
            <!-- Chat Container Card -->
            <div class="card shadow-sm border-0 rounded-3 h-100" style="min-height: 600px;">
                <div class="card-header bg-transparent border-bottom p-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-chat-dots me-2"></i> Percakapan dengan Pelanggan
                        </h6>
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> {{ $ticket->messages->count() }} pesan
                        </small>
                    </div>
                </div>

                <div class="card-body bg-light" style="height: 450px; overflow-y: auto;" id="chatContainer">
                    @forelse($ticket->messages as $message)
                    <div class="message-item mb-3 {{ $message->user_id !== $ticket->user_id ? 'text-end' : '' }}">
                        <div class="d-inline-block" style="max-width: 75%;">
                            <div class="message-bubble {{ $message->user_id !== $ticket->user_id ? 'bg-primary text-white' : 'bg-white border' }}">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-opacity-25">
                                    <strong class="small">
                                        @if($message->user_id !== $ticket->user_id)
                                            <i class="bi bi-person-badge"></i> CS
                                        @else
                                            <i class="bi bi-person-circle"></i> Pelanggan
                                        @endif
                                    </strong>
                                    <small class="opacity-75 ms-2">
                                        {{ $message->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                                <small class="opacity-75 d-block mt-2 text-end">
                                    {{ $message->created_at->format('H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-text" style="font-size: 3.5rem; opacity: 0.2;"></i>
                        <p class="mt-3 mb-1 fw-bold">Belum ada percakapan</p>
                        <small>Kirim pesan pertama untuk memulai percakapan dengan pelanggan</small>
                    </div>
                    @endforelse
                </div>

                @if($ticket->status !== 'closed')
                <div class="card-footer bg-white border-top p-4">
                    <form action="{{ route('cs.tickets.reply', $ticket->id) }}" method="POST" id="replyForm">
                        @csrf
                        <div class="row g-2">
                            <div class="col">
                                <textarea class="form-control form-control-lg" name="message" rows="2"
                                    placeholder="Tulis pesan balasan untuk pelanggan..."
                                    required
                                    style="resize: none; border-radius: 0.5rem;"></textarea>
                            </div>
                            <div class="col-auto d-flex align-items-end gap-2">
                                <button class="btn btn-primary btn-lg rounded-2" type="submit" id="submitBtn">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-info-circle"></i> Gunakan Ctrl+Enter untuk mengirim pesan dengan cepat
                        </small>
                    </form>
                </div>
                @else
                <div class="card-footer bg-light border-top p-4 text-center">
                    <div class="alert alert-secondary mb-0 py-2">
                        <i class="bi bi-lock-fill"></i> Tiket ini sudah ditutup. Tidak dapat mengirim pesan baru.
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- RIGHT SIDE: Ticket Details Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Info Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-header bg-transparent border-bottom p-4 pb-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-person"></i> Pelanggan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="avatar-circle">
                            {{ substr($ticket->user->pelanggan->nama_pelanggan ?? $ticket->user->email ?? 'G', 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $ticket->user->pelanggan->nama_pelanggan ?? $ticket->user->email ?? 'Guest' }}</h6>
                            <small class="text-muted">{{ $ticket->user->email ?? '-' }}</small>
                        </div>
                    </div>

                    @php
                        $pelanggan = $ticket->user->pelanggan ?? null;
                    @endphp

                    @if($pelanggan)
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-telephone"></i> Telepon
                        </small>
                        <strong>{{ $pelanggan->no_tlp_pelanggan ?? '-' }}</strong>
                    </div>
                    @endif

                    <small class="text-muted d-block">
                        <i class="bi bi-calendar"></i> Member sejak {{ $ticket->user->created_at ? $ticket->user->created_at->format('d M Y') : '-' }}
                    </small>
                </div>
            </div>

            <!-- Ticket Details Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-header bg-transparent border-bottom p-4 pb-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle"></i> Detail Tiket
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-hash"></i> Nomor Tiket
                        </small>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark fs-6">#{{ $ticket->ticket_number }}</span>
                            <small class="text-muted">{{ $ticket->created_at ? $ticket->created_at->format('d M Y') : '-' }}</small>
                        </div>
                    </div>

                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-tag"></i> Kategori
                        </small>
                        <span class="badge bg-secondary rounded-pill">{{ $ticket->category ?? 'Umum' }}</span>
                    </div>

                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-exclamation-triangle"></i> Prioritas
                        </small>
                        @if($ticket->priority === 'high')
                            <span class="badge bg-danger rounded-pill">
                                <i class="bi bi-arrow-up-circle"></i> High
                            </span>
                        @elseif($ticket->priority === 'medium')
                            <span class="badge bg-warning text-dark rounded-pill">
                                <i class="bi bi-dash-circle"></i> Medium
                            </span>
                        @else
                            <span class="badge bg-success rounded-pill">
                                <i class="bi bi-arrow-down-circle"></i> Low
                            </span>
                        @endif
                    </div>

                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-flag"></i> Status
                        </small>
                        @if($ticket->status === 'open')
                            <span class="badge bg-info rounded-pill">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Open
                            </span>
                        @elseif($ticket->status === 'in_progress')
                            <span class="badge bg-primary rounded-pill">
                                <i class="bi bi-arrow-clockwise"></i> In Progress
                            </span>
                        @elseif($ticket->status === 'resolved')
                            <span class="badge bg-success rounded-pill">
                                <i class="bi bi-check-circle"></i> Resolved
                            </span>
                        @else
                            <span class="badge bg-dark rounded-pill">
                                <i class="bi bi-x-circle"></i> Closed
                            </span>
                        @endif
                    </div>

                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-arrow-repeat"></i> Terakhir Update
                        </small>
                        <strong class="d-block mb-1">{{ $ticket->updated_at ? $ticket->updated_at->format('d M Y, H:i') : '-' }}</strong>
                        <small class="text-muted">{{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : '-' }}</small>
                    </div>

                    @if($ticket->assigned_to)
                    <div>
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-person-badge"></i> Ditangani oleh
                        </small>
                        <strong>{{ $ticket->assignedTo->staff->nama_staff ?? $ticket->assignedTo->email ?? '-' }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Deskripsi Masalah Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-header bg-transparent border-bottom p-4 pb-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-file-text"></i> Deskripsi Masalah
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0 text-muted" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">{{ $ticket->description }}</p>
                </div>
            </div>

            <!-- Quick Actions Card -->
            @if($ticket->status !== 'closed')
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-transparent border-bottom p-4 pb-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-lightning-fill"></i> Aksi Cepat
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        @if($ticket->status === 'open')
                        <form action="{{ route('cs.tickets.update', $ticket->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-primary w-100 rounded-2">
                                <i class="bi bi-play-circle"></i> Mulai Tangani
                            </button>
                        </form>
                        @endif

                        @if($ticket->status === 'in_progress')
                        <form action="{{ route('cs.tickets.update', $ticket->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="resolved">
                            <button type="submit" class="btn btn-success w-100 rounded-2">
                                <i class="bi bi-check-circle"></i> Tandai Selesai
                            </button>
                        </form>
                        @endif

                        @if($ticket->status === 'resolved')
                        <div class="alert alert-success mb-0 rounded-2">
                            <small><i class="bi bi-check-circle"></i> Tiket sudah diselesaikan!</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-repeat"></i> Update Status Tiket
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cs.tickets.update', $ticket->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-2">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-lg rounded-2" required>
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Prioritas</label>
                        <select name="priority" class="form-select form-select-lg rounded-2">
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div class="alert alert-info rounded-2 mb-0">
                        <i class="bi bi-info-circle"></i>
                        <small>Perubahan status akan terlihat oleh pelanggan.</small>
                    </div>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-2">
                        <i class="bi bi-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Modern Ticket Show Styling */

    /* Avatar Circle */
    .avatar-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    /* Message Bubble Styling */
    .message-bubble {
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
    }

    .message-bubble.bg-white {
        border: 1px solid #e9ecef;
        color: #212529;
    }

    .message-bubble.bg-primary {
        border-radius: 0.75rem 0.75rem 0 0.75rem;
        box-shadow: 0 2px 8px rgba(78, 115, 223, 0.2);
    }

    .message-item:not(.text-end) .message-bubble.bg-white {
        border-radius: 0.75rem 0.75rem 0.75rem 0;
    }

    /* Chat Container Scrollbar */
    #chatContainer::-webkit-scrollbar {
        width: 6px;
    }

    #chatContainer::-webkit-scrollbar-track {
        background: transparent;
    }

    #chatContainer::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }

    #chatContainer::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }

    /* Message Animation */
    .message-item {
        animation: slideInMessage 0.3s ease-out;
    }

    @keyframes slideInMessage {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Textarea Styling */
    .form-control-lg[name="message"] {
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        max-height: 120px;
    }

    .form-control-lg[name="message"]:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    /* Card Styling */
    .card {
        transition: all 0.3s ease;
        border-color: #e9ecef;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    /* Rounded Buttons */
    .btn-primary.rounded-2, .btn-success.rounded-2, .btn-light.rounded-2 {
        border-radius: 0.75rem;
        padding: 0.625rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-primary.rounded-2:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-success.rounded-2:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(28, 200, 138, 0.3);
    }

    /* Badge Styling */
    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        font-size: 0.8rem;
    }

    /* Responsive Chat */
    @media (max-width: 992px) {
        #chatContainer {
            min-height: 400px;
        }
    }

    @media (max-width: 768px) {
        #chatContainer {
            min-height: 300px;
        }

        .message-bubble {
            max-width: 85% !important;
        }
    }

    /* Alert Styling */
    .alert {
        border: none;
        border-left: 4px solid;
    }

    .alert-info {
        border-left-color: #4e73df;
        background-color: #f8f9fa;
        color: #4e73df;
    }

    .alert-success {
        border-left-color: #1cc88a;
        background-color: #f8f9fa;
        color: #1cc88a;
    }

    .alert-danger {
        border-left-color: #e74c3c;
        background-color: #f8f9fa;
        color: #e74c3c;
    }

    /* Unread Badge Animation */
    .unread-badge {
        animation: badgePulse 2s infinite;
    }

    @keyframes badgePulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Auto scroll to bottom of chat
const chatContainer = document.getElementById('chatContainer');
if (chatContainer) {
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

// Smooth scroll animation
function smoothScrollToBottom() {
    if (chatContainer) {
        chatContainer.scrollTo({
            top: chatContainer.scrollHeight,
            behavior: 'smooth'
        });
    }
}

// Auto refresh chat every 10 seconds
let lastMessageCount = {{ $ticket->messages->count() }};

setInterval(function() {
    if (document.hidden) return; // Don't refresh if tab is not active

    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newChat = doc.getElementById('chatContainer');

            if (newChat) {
                const currentScroll = chatContainer.scrollTop;
                const currentHeight = chatContainer.scrollHeight;
                const newMessageCount = newChat.querySelectorAll('.message-item').length;

                // Update chat content
                chatContainer.innerHTML = newChat.innerHTML;

                // Auto scroll only if user was at bottom or new message arrived
                if (currentScroll + chatContainer.clientHeight >= currentHeight - 100 || newMessageCount > lastMessageCount) {
                    smoothScrollToBottom();
                }

                lastMessageCount = newMessageCount;

                // Show notification if new message from customer
                if (newMessageCount > lastMessageCount) {
                    console.log('New message from customer!');
                    // Could add desktop notification here
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing chat:', error);
        });
}, 10000); // 10 seconds

// Form submit with loading state
document.getElementById('replyForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const textarea = form.querySelector('textarea[name="message"]');
    const originalBtnText = submitBtn.innerHTML;

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';
    textarea.disabled = true;

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.reset();

            // Immediately trigger polling to update other tabs/windows
            // Wait a short moment to ensure DB is updated
            setTimeout(() => {
                if (window.ticketNotificationManager) {
                    console.log('Triggering immediate polling after reply...');
                    window.ticketNotificationManager.checkForNewNotifications()
                        .then(() => {
                            console.log('Polling completed, reloading page...');
                            window.location.reload();
                        })
                        .catch(error => {
                            console.error('Polling error, still reloading:', error);
                            window.location.reload();
                        });
                } else {
                    // Fallback if notification manager not available
                    console.log('Notification manager not available, reloading directly...');
                    window.location.reload();
                }
            }, 200); // Small delay to ensure DB writes are complete
        } else {
            throw new Error(data.message || 'Gagal mengirim pesan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');

        // Re-enable form
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        textarea.disabled = false;
    });
});

// Keyboard shortcuts
document.querySelector('textarea[name="message"]')?.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to send
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('replyForm')?.requestSubmit();
    }
});

// Focus on textarea when page loads
window.addEventListener('load', function() {
    const textarea = document.querySelector('textarea[name="message"]');
    if (textarea && !document.body.classList.contains('modal-open')) {
        textarea.focus();
    }
});
</script>
@endpush
@endsection
