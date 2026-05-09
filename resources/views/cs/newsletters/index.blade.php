@extends('layouts.staff')

@section('title', 'Manajemen Newsletter')

@push('styles')
<style>
    /* Modern Pagination Styling */
    .pagination {
        margin-bottom: 0;
        gap: 0.25rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }

    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        color: #4e73df;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
        min-width: 2.5rem;
        text-align: center;
        font-weight: 500;
    }

    .pagination .page-link:hover {
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
        box-shadow: 0 2px 4px rgba(78, 115, 223, 0.25);
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
        box-shadow: 0 2px 8px rgba(78, 115, 223, 0.35);
    }

    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        border-color: #dee2e6;
        background-color: #f8f9fa;
        cursor: not-allowed;
        opacity: 0.5;
    }

    /* Pagination Info Container */
    .pagination-info {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }

    .pagination-info strong {
        color: #4e73df;
    }

    /* Pagination Container */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .pagination-container {
            flex-direction: column;
            align-items: flex-start;
        }

        .pagination {
            justify-content: flex-start;
        }

        .pagination-info {
            order: -1;
        }
    }

    /* Responsive Pagination */
    @media (max-width: 576px) {
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
            min-width: 2rem;
        }

        .pagination .page-item:nth-child(n+4):nth-child(-n+6) {
            display: none;
        }
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

    <a class="nav-link" href="{{ route('cs.tickets.index') }}">
        <i class="bi bi-ticket-perforated"></i>
        <span>Ticketing</span>
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
                <i class="fas fa-envelope-open-text"></i> Newsletter
            </h1>
            <p class="text-muted mb-0">Kelola dan pantau semua kampanye newsletter Anda</p>
        </div>
        <a href="{{ route('cs.newsletters.create') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-2"></i> Buat Newsletter Baru
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

    <!-- Newsletters Table -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header border-0 bg-transparent p-4 pb-2">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list text-primary me-2"></i> Daftar Newsletter
            </h6>
        </div>
        <div class="card-body p-4 pt-3">
            @if($newsletters->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="table-light">
                                <th class="fw-bold">No</th>
                                <th class="fw-bold">Judul</th>
                                <th class="fw-bold">Subjek Email</th>
                                <th class="fw-bold">Metode</th>
                                <th class="fw-bold">Status</th>
                                <th class="fw-bold">Tanggal Kirim</th>
                                <th class="fw-bold">Penerima</th>
                                <th class="fw-bold">Dibuat Oleh</th>
                                <th class="fw-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($newsletters as $index => $newsletter)
                            <tr class="border-bottom small">
                                <td>
                                    <span class="text-muted">{{ $newsletters->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-500">{{ $newsletter->judul }}</span>
                                </td>
                                <td>
                                    <span title="{{ $newsletter->subjek_email }}">{{ Str::limit($newsletter->subjek_email, 30) }}</span>
                                </td>
                                <td>
                                    @if($newsletter->metode_pengiriman === 'mailchimp')
                                        <span class="badge bg-primary-light text-primary px-2 py-1">
                                            <i class="fas fa-envelope me-1"></i> Mailchimp
                                        </span>
                                    @elseif($newsletter->metode_pengiriman === 'fonnte')
                                        <span class="badge bg-warning-light text-warning px-2 py-1">
                                            <i class="fab fa-whatsapp me-1"></i> Fonnte
                                        </span>
                                    @elseif($newsletter->metode_pengiriman === 'keduanya')
                                        <span class="badge bg-success-light text-success px-2 py-1">
                                            <i class="fas fa-layer-group me-1"></i> Keduanya
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-light text-secondary px-2 py-1">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($newsletter->status === 'draft')
                                        <span class="badge bg-secondary-light text-secondary px-2 py-1">
                                            <i class="fas fa-file me-1"></i> Draft
                                        </span>
                                    @elseif($newsletter->status === 'mengirim')
                                        <span class="badge bg-info-light text-info px-2 py-1">
                                            <i class="fas fa-spinner fa-spin me-1"></i> Mengirim
                                        </span>
                                    @elseif($newsletter->status === 'terkirim')
                                        <span class="badge bg-success-light text-success px-2 py-1">
                                            <i class="fas fa-check-circle me-1"></i> Terkirim
                                        </span>
                                    @else
                                        <span class="badge bg-danger-light text-danger px-2 py-1">
                                            <i class="fas fa-times-circle me-1"></i> Gagal
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($newsletter->tanggal_kirim)
                                        <small class="text-muted">{{ $newsletter->tanggal_kirim->format('d M Y H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($newsletter->total_penerima > 0)
                                        <div>
                                            <small class="text-dark fw-500">{{ number_format($newsletter->total_penerima) }}</small>
                                        </div>
                                        <small class="text-muted d-block">
                                            ✓ {{ $newsletter->total_terkirim }} | ✗ {{ $newsletter->total_gagal }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $newsletter->creator->name ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('cs.newsletters.show', $newsletter->id_newsletter) }}"
                                           class="btn btn-light rounded-circle"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>

                                        @if($newsletter->isDraft())
                                            <a href="{{ route('cs.newsletters.edit', $newsletter->id_newsletter) }}"
                                               class="btn btn-light rounded-circle"
                                               title="Edit">
                                                <i class="fas fa-edit text-warning"></i>
                                            </a>

                                            <button type="button"
                                                    class="btn btn-light rounded-circle"
                                                    title="Hapus"
                                                    onclick="deleteNewsletter('{{ $newsletter->id_newsletter }}')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4 pt-3 border-top">
                    <div class="pagination-info">
                        <small class="text-muted">
                            Menampilkan <strong class="text-primary">{{ $newsletters->firstItem() ?? 0 }}</strong> -
                            <strong class="text-primary">{{ $newsletters->lastItem() ?? 0 }}</strong> dari
                            <strong class="text-primary">{{ $newsletters->total() }}</strong> newsletter
                        </small>
                    </div>
                    @if($newsletters->hasPages())
                    <nav aria-label="Page navigation">
                        {{ $newsletters->links('pagination::bootstrap-5') }}
                    </nav>
                    @endif
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-envelope fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                    <p class="text-muted mb-3">Belum ada newsletter yang dibuat</p>
                    <a href="{{ route('cs.newsletters.create') }}" class="btn btn-primary rounded-pill">
                        <i class="fas fa-plus me-1"></i> Buat Newsletter Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Newsletter Modal -->
<form id="deleteNewsletterForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteNewsletter(id) {
        Swal.fire({
            title: 'Hapus Newsletter?',
            text: "Newsletter ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteNewsletterForm');
                // Construct the route dynamically
                form.action = "{{ url('cs/newsletters') }}/" + id;
                form.submit();
            }
        });
    }
</script>
@endpush
