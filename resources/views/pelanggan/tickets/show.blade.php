@extends('layouts.pelanggan')

@section('title', 'Detail Tiket #' . $ticket->ticket_number)

@section('content')
<div class="container-fluid py-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('pelanggan.tickets.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Tiket
        </a>

        @if($ticket->status === 'resolved' && $ticket->status !== 'closed')
        <form action="{{ route('pelanggan.tickets.close', $ticket->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-success" onclick="return confirm('Tutup tiket ini? Anda tidak akan bisa mengirim pesan lagi.')">
                <i class="bi bi-check-circle"></i> Tutup Tiket
            </button>
        </form>
        @endif
    </div>

    <div class="row g-4">
        <!-- LEFT SIDE: Chat/Messages -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100" style="min-height: 600px;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots"></i> Percakapan dengan Customer Service
                        </h5>
                        <div>
                            @if($ticket->status === 'open')
                                <span class="badge bg-light text-primary">Open</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="badge bg-warning text-dark">In Progress</span>
                            @elseif($ticket->status === 'resolved')
                                <span class="badge bg-success">Resolved</span>
                            @else
                                <span class="badge bg-dark">Closed</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body bg-light" style="height: 450px; overflow-y: auto;" id="chatContainer">
                    @forelse($ticket->messages as $message)
                    <div class="message-item mb-3 {{ $message->user_id === auth()->id() ? 'text-end' : '' }}">
                        <div class="d-inline-block" style="max-width: 70%;">
                            <div class="card shadow-sm {{ $message->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-white' }}">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <strong class="small">
                                            @if($message->user_id === auth()->id())
                                                <i class="bi bi-person-circle"></i> Anda
                                            @else
                                                <i class="bi bi-headset"></i> Customer Service
                                            @endif
                                        </strong>
                                        <small class="{{ $message->user_id === auth()->id() ? 'text-white-50' : 'text-muted' }} ms-2">
                                            {{ $message->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                                    <div class="text-end mt-1">
                                        <small class="{{ $message->user_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-text" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="mt-3 mb-1"><strong>Belum ada percakapan</strong></p>
                        <small>Mulai percakapan dengan mengirim pesan di bawah</small>
                    </div>
                    @endforelse
                </div>

                @if($ticket->status !== 'closed' && $ticket->status !== 'resolved')
                <div class="card-footer bg-white">
                    <form action="{{ route('pelanggan.tickets.reply', $ticket->id) }}" method="POST" id="replyForm">
                        @csrf
                        <div class="row g-2">
                            <div class="col">
                                <textarea class="form-control" name="message" rows="2"
                                    placeholder="Tulis pesan Anda di sini..."
                                    required
                                    style="resize: none;"></textarea>
                            </div>
                            <div class="col-auto d-flex align-items-end">
                                <button class="btn btn-primary btn-lg" type="submit">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="bi bi-info-circle"></i> Tekan Enter untuk baris baru, klik tombol kirim untuk mengirim pesan
                        </small>
                    </form>
                </div>
                @elseif($ticket->status === 'resolved')
                <div class="card-footer bg-light text-center">
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle-fill"></i> Tiket ini sudah diselesaikan. Jika masalah Anda sudah teratasi, silakan tutup tiket.
                    </div>
                </div>
                @else
                <div class="card-footer bg-light text-center">
                    <div class="alert alert-secondary mb-0">
                        <i class="bi bi-lock-fill"></i> Tiket ini sudah ditutup. Tidak dapat mengirim pesan baru.
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- RIGHT SIDE: Ticket Details -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Detail Tiket
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Nomor Tiket</small>
                        <h6 class="mb-0">
                            <span class="badge bg-dark fs-6">#{{ $ticket->ticket_number }}</span>
                        </h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Subject</small>
                        <h5 class="mb-0">{{ $ticket->subject }}</h5>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-tag"></i> Kategori
                        </small>
                        <span class="badge bg-secondary">{{ $ticket->category ?? 'Umum' }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-exclamation-triangle"></i> Priority
                        </small>
                        @if($ticket->priority === 'high')
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-up-circle"></i> High Priority
                            </span>
                        @elseif($ticket->priority === 'medium')
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-dash-circle"></i> Medium Priority
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-arrow-down-circle"></i> Low Priority
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-flag"></i> Status
                        </small>
                        @if($ticket->status === 'open')
                            <span class="badge bg-info fs-6">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Open
                            </span>
                        @elseif($ticket->status === 'in_progress')
                            <span class="badge bg-primary fs-6">
                                <i class="bi bi-arrow-clockwise"></i> In Progress
                            </span>
                        @elseif($ticket->status === 'resolved')
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle"></i> Resolved
                            </span>
                        @else
                            <span class="badge bg-dark fs-6">
                                <i class="bi bi-x-circle"></i> Closed
                            </span>
                        @endif
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-calendar-plus"></i> Dibuat
                        </small>
                        <strong>{{ $ticket->created_at->format('d M Y, H:i') }}</strong>
                        <br>
                        <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-calendar-check"></i> Terakhir Update
                        </small>
                        <strong>{{ $ticket->updated_at->format('d M Y, H:i') }}</strong>
                        <br>
                        <small class="text-muted">{{ $ticket->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Masalah Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-file-text"></i> Deskripsi Masalah
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->description }}</p>
                </div>
            </div>

            <!-- Status Actions -->
            @if($ticket->status !== 'closed')
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="bi bi-gear"></i> Aksi
                    </h6>

                    @if($ticket->status === 'resolved')
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Tiket ini sudah diselesaikan!
                        <br>
                        <small>Jika masalah sudah selesai, Anda bisa menutup tiket ini.</small>
                    </div>
                    @elseif($ticket->status === 'in_progress')
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-hourglass-split"></i> Customer Service sedang menangani tiket Anda.
                        <br>
                        <small>Harap tunggu respon dari CS.</small>
                    </div>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-clock"></i> Tiket Anda menunggu respon dari CS.
                        <br>
                        <small>Kami akan segera membantu Anda.</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

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

                // Show notification if new message
                if (newMessageCount > lastMessageCount) {
                    // Could add sound notification here
                    console.log('New message received!');
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
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
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

<style>
/* Smooth scrollbar */
#chatContainer::-webkit-scrollbar {
    width: 8px;
}

#chatContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#chatContainer::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

#chatContainer::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Message animations */
.message-item {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Typing indicator space */
.typing-indicator {
    padding: 10px;
    font-style: italic;
    color: #6c757d;
}

/* Better textarea */
textarea[name="message"] {
    border: 2px solid #dee2e6;
    transition: border-color 0.3s;
}

textarea[name="message"]:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
}

/* Status badge animations */
.badge {
    transition: all 0.3s ease;
}

/* Card hover effects */
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}
</style>
@endpush
@endsection
