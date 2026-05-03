@extends('layouts.admin')

@section('title', 'Manajemen Transaksi')

@push('styles')
<style>
    /* Custom Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        margin: 0 2px;
    }

    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        border-radius: 0.25rem;
    }

    .pagination .page-link:hover {
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }

    /* Custom button size for review */
    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    /* Hide ALL DataTables UI elements - AGGRESSIVE CSS */
    #transactionsTable thead th,
    #transactionsTable thead th.sorting,
    #transactionsTable thead th.sorting_asc,
    #transactionsTable thead th.sorting_desc,
    table.dataTable thead th.sorting,
    table.dataTable thead th.sorting_asc,
    table.dataTable thead th.sorting_desc {
        background-image: none !important;
        cursor: default !important;
        padding-right: 8px !important;
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
    }

    #transactionsTable_filter,
    #transactionsTable_wrapper .dataTables_filter,
    .dataTables_filter {
        display: none !important;
    }

    table.dataTable thead th {
        cursor: default !important;
    }

    /* Prevent any sorting decorations */
    .dataTables_wrapper .dataTables_header,
    .dataTables_wrapper .dataTables_footer {
        display: none !important;
    }

    /* Override all DataTables sorting styles */
    table.dataTable thead th:not(.dataTables_empty) {
        background: none !important;
        background-image: none !important;
    }

    /* Prevent pointer cursor on headers */
    table.dataTable thead th:hover {
        cursor: default !important;
    }

    /* Search Input Styling */
    #searchInput {
        border-radius: 0.25rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    #searchInput:focus {
        border-color: #4e73df !important;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
    }

    .input-group-text {
        border-radius: 0.25rem 0 0 0.25rem;
    }

    #searchResults {
        display: inline-block;
        padding: 0.375rem 0;
        color: #858796;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart text-primary"></i> Manajemen Transaksi
        </h1>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Belum Bayar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statusCounts['pending'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Sudah Bayar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statusCounts['completed'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Kadaluarsa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statusCounts['cancelled'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <!-- Search and Filter Row -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0"
                               id="searchInput"
                               placeholder="Cari pelanggan, kode transaksi, atau cabang...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted small" id="searchResults">Menampilkan semua transaksi</span>
                </div>
            </div>

            <!-- Status Filter Row -->
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> Daftar Transaksi
                </h6>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active" onclick="filterStatus('all')">
                        Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="filterStatus('belum_bayar')">
                        Belum Bayar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="filterStatus('sudah_bayar')">
                        Sudah Bayar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterStatus('kadaluarsa')">
                        Kadaluarsa
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover datatable" id="transactionsTable">
                        <thead>
                            <tr>
                                <th width="13%">Pelanggan</th>
                                <th width="10%">Cabang</th>
                                <th width="12%">Kode Transaksi</th>
                                <th width="11%">Total</th>
                                <th width="9%">Status</th>
                                <th width="11%">Tanggal Transaksi</th>
                                <th width="12%">Pembatalan</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                @php
                                    $tanggalTransaksi = $transaction->tanggal_transaksi;
                                    if (is_string($tanggalTransaksi)) {
                                        $tanggalTransaksi = \Carbon\Carbon::parse($tanggalTransaksi);
                                    }
                                    $sortDate = $tanggalTransaksi ? $tanggalTransaksi->format('d/m/Y') : '';
                                @endphp
                                <tr data-status="{{ $transaction->status_pembayaran }}" data-date="{{ $sortDate }}">
                                    <td>
                                        @if($transaction->pelanggan && $transaction->pelanggan->user)
                                            <i class="fas fa-user text-primary"></i>
                                            <strong>{{ $transaction->pelanggan->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $transaction->pelanggan->user->email }}</small>
                                        @elseif($transaction->pelanggan)
                                            <i class="fas fa-user text-warning"></i>
                                            <strong>{{ $transaction->pelanggan->nama_pelanggan }}</strong>
                                            <br>
                                            <small class="text-muted badge bg-warning">No User Account</small>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-user-slash"></i> Data Terhapus
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->cabang)
                                            <i class="fas fa-store text-success"></i>
                                            <strong class="d-block">{{ $transaction->cabang->nama_cabang }}</strong>
                                            <small class="text-muted">{{ $transaction->cabang->alamat_cabang ?? '-' }}</small>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-question-circle"></i> Tidak Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fas fa-hashtag"></i>
                                        <strong>{{ $transaction->kode_transaksi }}</strong>
                                    </td>
                                    <td class="font-weight-bold text-success">
                                        Rp {{ number_format($transaction->getTotalAmount(), 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @php
                                            // Map status_pembayaran to display
                                            $statusPembayaran = $transaction->status_pembayaran ?? 'unknown';
                                        @endphp

                                        @if($statusPembayaran === 'belum_bayar')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock"></i> Belum Bayar
                                            </span>
                                        @elseif($statusPembayaran === 'sudah_bayar')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Sudah Bayar
                                            </span>
                                        @elseif($statusPembayaran === 'kadaluarsa')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle"></i> Kadaluarsa
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-question"></i> {{ ucfirst($statusPembayaran) }}
                                            </span>
                                        @endif

                                        @if($transaction->status_pengiriman && $transaction->status_pengiriman !== 'pending')
                                            <br>
                                            <small class="badge bg-info mt-1">
                                                <i class="fas fa-truck"></i>
                                                {{ ucfirst(str_replace('_', ' ', $transaction->status_pengiriman)) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->tanggal_transaksi)
                                            @php
                                                $tanggalTransaksi = $transaction->tanggal_transaksi;
                                                // Convert to Carbon instance if it's a string
                                                if (is_string($tanggalTransaksi)) {
                                                    $tanggalTransaksi = \Carbon\Carbon::parse($tanggalTransaksi);
                                                }
                                            @endphp
                                            <i class="fas fa-calendar-alt text-info"></i>
                                            <strong>{{ $tanggalTransaksi->format('d/m/Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $tanggalTransaksi->format('H:i') }}</small>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-question-circle"></i> -
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->cancellation)
                                            @if($transaction->cancellation->status_pembatalan === 'diajukan')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-circle"></i> Perlu Review
                                                </span>
                                                <br>
                                                <a href="{{ route('admin.cancellations.show', $transaction->cancellation->id_pembatalan_transaksi) }}"
                                                   class="btn btn-xs btn-outline-warning mt-1"
                                                   title="Review Pembatalan">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            @elseif($transaction->cancellation->status_pembatalan === 'disetujui')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Disetujui
                                                </span>
                                                @if($transaction->cancellation->updated_at)
                                                <br>
                                                <small class="text-muted">
                                                    {{ $transaction->cancellation->updated_at->format('d/m/Y') }}
                                                </small>
                                                @endif
                                            @elseif($transaction->cancellation->status_pembatalan === 'ditolak')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> Ditolak
                                                </span>
                                                @if($transaction->cancellation->updated_at)
                                                <br>
                                                <small class="text-muted">
                                                    {{ $transaction->cancellation->updated_at->format('d/m/Y') }}
                                                </small>
                                                @endif
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-minus-circle"></i> Tidak Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.transactions.show', $transaction->id_transaksi) }}"
                                               class="btn btn-sm btn-info"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- @if($transaction->status_pembayaran !== 'kadaluarsa' && $transaction->status_pembayaran !== 'sudah_bayar')
                                                <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="cancelTransaction({{ $transaction->id_transaksi }})"
                                                        title="Batalkan">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @endif --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Total: <strong>{{ $transactions->total() }}</strong> transaksi
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Belum ada transaksi.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Transaction Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fas fa-ban"></i> Batalkan Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian!</strong> Pembatalan transaksi akan mengembalikan stok produk.
                    </div>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">
                            <i class="fas fa-comment"></i> Alasan Pembatalan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="cancellation_reason"
                                  name="cancellation_reason"
                                  rows="4"
                                  placeholder="Masukkan alasan pembatalan transaksi..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Batalkan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Variabel global untuk state filter
    var currentStatusFilter = 'all';
    var transactionsTable = null;

    $(document).ready(function() {
        // Destroy DataTable jika sudah ada
        if ($.fn.DataTable.isDataTable('#transactionsTable')) {
            $('#transactionsTable').DataTable().destroy();
        }

        // Inisialisasi DataTables
        transactionsTable = $('#transactionsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "pageLength": 15,
            "lengthMenu": [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Semua"]],
            "dom": '<"row"<"col-sm-12 col-md-6"l>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "responsive": true,
            "autoWidth": false,
            "paging": true,
            "ordering": false,
            "columnDefs": [
                { "orderable": false, "targets": "_all" }
            ]
        });

        // Disable DataTables default search dan sorting UI
        $(document).off('init.dt.dtr').off('init.dt');
        $('#transactionsTable thead').off('click.DT');
        $('#transactionsTable').off('click.DT');
        $('#transactionsTable thead th').removeClass('sorting sorting_asc sorting_desc');

        // Hapus filter elements
        function removeFiltersUI() {
            $('#transactionsTable_filter').remove();
            $('.dataTables_filter').remove();
            $('#transactionsTable thead th').css('background-image', 'none');
            $('#transactionsTable thead').off('click.DT');
        }

        removeFiltersUI();
        setTimeout(removeFiltersUI, 100);
        setTimeout(removeFiltersUI, 500);

        // Prevent re-initialization
        $(document).on('DOMNodeInserted', function() {
            removeFiltersUI();
        });

        // Event listener untuk search input
        $('#searchInput').on('keyup', function() {
            applyFilters();
            updateSearchResults();
        });

        // Store instance untuk global use
        window.transactionsTable = transactionsTable;
    });

    function applyFilters() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const rows = $('#transactionsTable tbody tr');
        let visibleCount = 0;

        rows.each(function() {
            const row = $(this);
            const rowStatus = row.data('status');

            // Ambil text dari 3 kolom utama
            const pelangganText = row.find('td').eq(0).text().toLowerCase();
            const cabangText = row.find('td').eq(1).text().toLowerCase();
            const kodeText = row.find('td').eq(2).text().toLowerCase();

            // Check search match
            const matchesSearch = searchTerm === '' ||
                                 pelangganText.includes(searchTerm) ||
                                 cabangText.includes(searchTerm) ||
                                 kodeText.includes(searchTerm);

            // Check status match
            const matchesStatus = currentStatusFilter === 'all' || rowStatus === currentStatusFilter;

            // Tampilkan jika kedua filter cocok
            if (matchesSearch && matchesStatus) {
                row.show();
                visibleCount++;
            } else {
                row.hide();
            }
        });

        // Update visible count di bawah tabel
        const totalFiltered = $('#transactionsTable tbody tr:visible').length;
        console.log('Filter diterapkan - Status: ' + currentStatusFilter + ', Search: ' + searchTerm + ', Visible: ' + totalFiltered);

        // Redraw pagination
        if (transactionsTable) {
            transactionsTable.draw(false);
        }

        return visibleCount;
    }

    function updateSearchResults() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const visibleCount = applyFilters();

        if (searchTerm === '') {
            $('#searchResults').text('Menampilkan semua transaksi');
        } else {
            $('#searchResults').text(`Menemukan ${visibleCount} hasil untuk "${searchTerm}"`);
        }
    }

    function filterStatus(status) {
        // Update tombol status filter
        document.querySelectorAll('.btn-group button').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');

        // Update current status filter
        currentStatusFilter = status;

        // Terapkan filter
        applyFilters();
        updateSearchResults();
    }

    function cancelTransaction(transactionId) {
        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Anda akan membatalkan transaksi ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set form action
                const form = document.getElementById('cancelForm');
                form.action = `/admin/transactions/${transactionId}/cancel`;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
                modal.show();
            }
        });
    }

    // Handle cancel form submission
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
        const reason = document.getElementById('cancellation_reason').value;
        if (!reason.trim()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Mohon masukkan alasan pembatalan!',
            });
            return false;
        }
    });
</script>
@endsection
