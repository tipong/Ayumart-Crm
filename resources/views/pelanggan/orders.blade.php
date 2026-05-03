@extends('layouts.pelanggan')

@section('title', 'Pesanan Saya')

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
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle"></i> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Check for cancellation status updates --}}
    @php
        $approvedCancellations = $orders->filter(function($order) {
            return isset($order->cancellation) && $order->cancellation->status_pembatalan === 'disetujui';
        });

        $rejectedCancellations = $orders->filter(function($order) {
            return isset($order->cancellation) && $order->cancellation->status_pembatalan === 'ditolak';
        });
    @endphp

    @if($approvedCancellations->count() > 0)
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <strong>Pembatalan Disetujui!</strong>
        <p class="mb-0 mt-2">
            @foreach($approvedCancellations as $order)
                Pesanan <strong>{{ $order->kode_transaksi }}</strong> telah dibatalkan.
                @if($order->status_pembayaran === 'sudah_bayar')
                    Dana akan dikembalikan dalam 3-5 hari kerja.
                @endif
                <br>
            @endforeach
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($rejectedCancellations->count() > 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle-fill"></i>
        <strong>Pembatalan Ditolak</strong>
        <p class="mb-0 mt-2">
            @foreach($rejectedCancellations as $order)
                Pembatalan pesanan <strong>{{ $order->kode_transaksi }}</strong> ditolak oleh admin.
                @if($order->cancellation->catatan_admin)
                    <br><small>Catatan: {{ $order->cancellation->catatan_admin }}</small>
                @endif
                <br>
            @endforeach
            Silakan lanjutkan pembayaran atau hubungi customer service untuk bantuan.
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Pesanan Saya</h5>
                    <a href="{{ route('pelanggan.reviews.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-star-fill text-warning"></i> Review Saya
                    </a>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Transaksi</th>
                                        {{-- <th>Produk</th>
                                        <th class="text-center">Jumlah</th> --}}
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Status Pembayaran</th>
                                        <th class="text-center">Status Pengiriman</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>{{ $order->kode_transaksi }}</strong>
                                            @if($order->shipment && $order->shipment->no_resi)
                                                <br><small class="text-muted"><i class="bi bi-box"></i> Resi: {{ $order->shipment->no_resi }}</small>
                                            @endif
                                        </td>
                                        {{-- <td>
                                            @if($order->product)
                                                {{ $order->product->nama_produk }}
                                            @else
                                                <em class="text-muted">Produk tidak tersedia</em>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $order->jumlah_produk }}</td> --}}
                                        <td class="text-end">
                                            Rp {{ number_format($order->total_harga - $order->total_diskon + ($order->biaya_membership ?? 0) + $order->ongkir, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            @if($order->status_pembayaran == 'sudah_bayar')
                                                <span class="badge bg-success">Sudah Bayar</span>
                                            @elseif($order->status_pembayaran == 'kadaluarsa')
                                                <span class="badge bg-danger">Kadaluarsa</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Belum Bayar</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                // Prioritas: gunakan shipping_status dari tb_pengiriman jika ada, fallback ke status_pengiriman di tb_transaksi
                                                $displayStatus = $order->shipping_status ?? $order->status_pengiriman;
                                            @endphp

                                            @if(in_array($displayStatus, ['sampai', 'terkirim', 'selesai']))
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Terkirim
                                                </span>
                                            @elseif(in_array($displayStatus, ['dikirim', 'dalam_pengiriman']))
                                                <span class="badge bg-info">
                                                    <i class="bi bi-truck"></i> Dalam Pengiriman
                                                </span>
                                            @elseif($displayStatus == 'dikemas')
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-box-seam"></i> Dikemas
                                                </span>
                                            @elseif($displayStatus == 'siap_diambil')
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-bag-check"></i> Siap Diambil
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-clock"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <small>{{ $order->kode_transaksi }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group-vertical btn-group-sm" role="group">
                                                <a href="{{ route('pelanggan.orders.detail', $order->id_transaksi) }}"
                                                   class="btn btn-sm btn-info mb-1"
                                                   title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>

                                                {{-- Tombol Konfirmasi Penerimaan - tampil jika sudah terkirim tapi belum dikonfirmasi --}}
                                                @php
                                                    $displayStatus = $order->shipping_status ?? $order->status_pengiriman;

                                                    // For kurir delivery
                                                    $canConfirmReceived = in_array($displayStatus, ['sampai', 'terkirim']) &&
                                                                         $order->status_pembayaran == 'sudah_bayar' &&
                                                                         $order->metode_pengiriman == 'kurir';

                                                    // For ambil_sendiri pickup
                                                    $canConfirmPickup = $displayStatus === 'siap_diambil' &&
                                                                       $order->status_pembayaran == 'sudah_bayar' &&
                                                                       $order->metode_pengiriman == 'ambil_sendiri';

                                                    $isCompleted = $displayStatus === 'selesai';

                                                    // Get products from this order for review (JOIN ke database integrasi)
                                                    $orderProducts = DB::table('tb_detail_transaksi')
                                                        ->join('db_integrasi_ayu_mart.tb_produk', 'tb_detail_transaksi.id_produk', '=', 'db_integrasi_ayu_mart.tb_produk.id_produk')
                                                        ->where('tb_detail_transaksi.id_transaksi', $order->id_transaksi)
                                                        ->select('db_integrasi_ayu_mart.tb_produk.id_produk', 'db_integrasi_ayu_mart.tb_produk.nama_produk')
                                                        ->get();
                                                @endphp

                                                {{-- Button to confirm order received (for kurir) --}}
                                                @if($canConfirmReceived)
                                                    <button type="button"
                                                            class="btn btn-sm btn-primary mb-1"
                                                            onclick="confirmOrderReceived({{ $order->id_transaksi }}, '{{ $order->kode_transaksi }}')"
                                                            title="Konfirmasi Barang Diterima">
                                                        <i class="bi bi-check-circle"></i> Barang Diterima
                                                    </button>
                                                @endif

                                                {{-- Button to confirm order picked up (for ambil_sendiri) --}}
                                                @if($canConfirmPickup)
                                                    <button type="button"
                                                            class="btn btn-sm btn-success mb-1"
                                                            onclick="confirmPickup({{ $order->id_transaksi }}, '{{ $order->kode_transaksi }}')"
                                                            title="Konfirmasi Barang Diambil">
                                                        <i class="bi bi-bag-check"></i> Sudah Diambil
                                                    </button>
                                                @endif

                                                {{-- Tombol Review - tampil jika sudah selesai (customer confirmed) --}}
                                                @if($isCompleted && $order->status_pembayaran == 'sudah_bayar' && $orderProducts->count() > 0)
                                                    @foreach($orderProducts as $product)
                                                        @php
                                                            // Check if product already reviewed
                                                            $existingReview = \App\Models\Review::where('id_pelanggan', $order->id_pelanggan)
                                                                ->where('id_produk', $product->id_produk)
                                                                ->where('id_transaksi', $order->id_transaksi)
                                                                ->first();
                                                        @endphp

                                                        @if(!$existingReview)
                                                            <a href="{{ route('pelanggan.review.create', [$order->id_transaksi, $product->id_produk]) }}"
                                                               class="btn btn-sm btn-success mb-1"
                                                               title="Review: {{ $product->nama_produk }}">
                                                                <i class="bi bi-star"></i> Review
                                                            </a>
                                                        @else
                                                            <span class="badge bg-success mb-1" title="Sudah direview">
                                                                <i class="bi bi-check-circle"></i> Sudah Review
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                @endif

                                                @if($order->status_pembayaran == 'belum_bayar')
                                                    <a href="{{ route('pelanggan.orders.continue-payment', $order->id_transaksi) }}"
                                                       class="btn btn-sm btn-warning mb-1"
                                                       title="Lanjutkan Pembayaran">
                                                        <i class="bi bi-credit-card"></i> Bayar
                                                    </a>

                                                    @php
                                                        $hasCancellation = isset($order->cancellation) && $order->cancellation;
                                                    @endphp

                                                    @if(!$hasCancellation)
                                                        <button type="button"
                                                                class="btn btn-sm btn-danger"
                                                                onclick="showCancelModal({{ $order->id_transaksi }}, '{{ $order->kode_transaksi }}')"
                                                                title="Batalkan Pesanan">
                                                            <i class="bi bi-x-circle"></i> Batalkan
                                                        </button>
                                                    @else
                                                        @php
                                                            $cancelStatus = $order->cancellation->status_pembatalan;
                                                        @endphp

                                                        @if($cancelStatus === 'diajukan')
                                                            <span class="badge bg-warning text-dark d-block py-2 mb-1" title="Menunggu konfirmasi admin">
                                                                <i class="bi bi-hourglass-split"></i> Pembatalan Diajukan
                                                            </span>
                                                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                                Menunggu konfirmasi
                                                            </small>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary mt-1 w-100"
                                                                    onclick="showCancellationDetail({{ $order->cancellation->id_pembatalan_transaksi }}, '{{ $order->kode_transaksi }}', '{{ $order->cancellation->alasan_pembatalan }}', '{{ $cancelStatus }}', '{{ $order->cancellation->created_at ? $order->cancellation->created_at->format('d/m/Y H:i') : '-' }}', '{{ $order->cancellation->catatan_admin ?? '' }}')"
                                                                    style="font-size: 0.7rem; padding: 2px 5px;">
                                                                <i class="bi bi-info-circle"></i> Detail
                                                            </button>
                                                        @elseif($cancelStatus === 'disetujui')
                                                            <span class="badge bg-success d-block py-2 mb-1" title="Pembatalan disetujui">
                                                                <i class="bi bi-check-circle"></i> Pembatalan Disetujui
                                                            </span>
                                                            <small class="text-success d-block" style="font-size: 0.7rem;">
                                                                <i class="bi bi-info-circle"></i> Transaksi dibatalkan
                                                            </small>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-success mt-1 w-100"
                                                                    onclick="showCancellationDetail({{ $order->cancellation->id_pembatalan_transaksi }}, '{{ $order->kode_transaksi }}', '{{ $order->cancellation->alasan_pembatalan }}', '{{ $cancelStatus }}', '{{ $order->cancellation->created_at ? $order->cancellation->created_at->format('d/m/Y H:i') : '-' }}', '{{ $order->cancellation->catatan_admin ?? '' }}')"
                                                                    style="font-size: 0.7rem; padding: 2px 5px;">
                                                                <i class="bi bi-eye"></i> Detail
                                                            </button>
                                                        @elseif($cancelStatus === 'ditolak')
                                                            <span class="badge bg-danger d-block py-2 mb-1" title="Pembatalan ditolak">
                                                                <i class="bi bi-x-circle"></i> Pembatalan Ditolak
                                                            </span>
                                                            <small class="text-danger d-block" style="font-size: 0.7rem;">
                                                                <i class="bi bi-exclamation-circle"></i> Mohon lakukan pembayaran
                                                            </small>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger mt-1 w-100"
                                                                    onclick="showCancellationDetail({{ $order->cancellation->id_pembatalan_transaksi }}, '{{ $order->kode_transaksi }}', '{{ $order->cancellation->alasan_pembatalan }}', '{{ $cancelStatus }}', '{{ $order->cancellation->created_at ? $order->cancellation->created_at->format('d/m/Y H:i') : '-' }}', '{{ $order->cancellation->catatan_admin ?? '' }}')"
                                                                    style="font-size: 0.7rem; padding: 2px 5px;">
                                                                <i class="bi bi-eye"></i> Lihat Alasan
                                                            </button>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan
                            </div>
                            <div>
                                {{ $orders->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ddd;"></i>
                            <p class="text-muted mt-3">Anda belum memiliki pesanan</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="bi bi-shop"></i> Mulai Belanja
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <!-- Membership Info -->
            @if($membership && $membership->is_active && $membership->isValid())
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-award"></i> Membership</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="badge bg-{{ $membership->tier == 'platinum' ? 'info' : ($membership->tier == 'gold' ? 'warning' : ($membership->tier == 'silver' ? 'secondary' : 'warning')) }} mb-2" style="font-size: 1.2rem;">
                            {{ ucfirst($membership->tier) }}
                        </div>
                        <div>
                            <i class="bi bi-star-fill text-warning"></i>
                            <strong style="font-size: 1.5rem;">{{ number_format($membership->points) }}</strong>
                            <small class="text-muted d-block">Total Poin</small>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <small class="text-muted">Diskon Aktif</small>
                        <div class="h5 text-success mb-0">{{ $membership->discount_percentage }}%</div>
                    </div>

                    @php
                        $nextTier = $membership->getNextTierInfo();
                    @endphp

                    @if($nextTier['next_tier'] != 'Maximum')
                    <hr>
                    <div class="alert alert-info mb-0 small">
                        <i class="bi bi-info-circle"></i>
                        Kumpulkan <strong>{{ $nextTier['points_needed'] }}</strong> poin lagi untuk naik ke tier <strong>{{ $nextTier['next_tier'] }}</strong>
                    </div>
                    @else
                    <hr>
                    <div class="alert alert-success mb-0 small">
                        <i class="bi bi-trophy-fill"></i>
                        Selamat! Anda sudah mencapai tier tertinggi
                    </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('membership') }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-info-circle"></i> Info Poin</h6>
                    <small class="text-muted">
                        <ul class="mb-0 ps-3">
                            <li>1 poin = Rp 20.000 belanja</li>
                            <li>Bronze: 0-100 poin (5%)</li>
                            <li>Silver: 101-250 poin (10%)</li>
                            <li>Gold: 251-400 poin (15%)</li>
                            <li>Platinum: 401+ poin (20%)</li>
                        </ul>
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancellation Detail Modal -->
<div class="modal fade" id="cancellationDetailModal" tabindex="-1" aria-labelledby="cancellationDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="cancelDetailHeader">
                <h5 class="modal-title" id="cancellationDetailModalLabel">
                    <i class="bi bi-file-text"></i> Detail Pembatalan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="text-muted small">Kode Transaksi</label>
                    <div class="fw-bold" id="cancelDetailOrderCode"></div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Tanggal Pengajuan</label>
                    <div id="cancelDetailDate"></div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Alasan Pembatalan Anda</label>
                    <div class="p-3 bg-light rounded" id="cancelDetailReason"></div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Status Pembatalan</label>
                    <div id="cancelDetailStatus"></div>
                </div>

                <div class="mb-3" id="adminNoteSection" style="display: none;">
                    <label class="text-muted small">Catatan dari Admin</label>
                    <div class="alert alert-info mb-0" id="cancelDetailAdminNote"></div>
                </div>

                <div class="alert alert-warning mb-0" id="pendingInfo" style="display: none;">
                    <i class="bi bi-info-circle"></i>
                    <small>Permintaan pembatalan Anda sedang dalam proses review oleh admin. Kami akan menginformasikan hasilnya segera.</small>
                </div>

                <div class="alert alert-success mb-0" id="approvedInfo" style="display: none;">
                    <i class="bi bi-check-circle"></i>
                    <small>Pembatalan Anda telah disetujui. Transaksi ini telah dibatalkan.</small>
                </div>

                <div class="alert alert-danger mb-0" id="rejectedInfo" style="display: none;">
                    <i class="bi bi-x-circle"></i>
                    <small>Pembatalan Anda ditolak. Silakan lanjutkan pembayaran atau hubungi customer service untuk bantuan lebih lanjut.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelOrderModalLabel">
                    <i class="bi bi-x-circle"></i> Batalkan Pesanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelOrderForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Perhatian!</strong>
                        <p class="mb-0 mt-2">Anda akan membatalkan pesanan dengan kode: <strong id="orderCodeDisplay"></strong></p>
                        <p class="mb-0">Pembatalan memerlukan konfirmasi dari admin.</p>
                    </div>

                    <div class="mb-3">
                        <label for="alasan_pembatalan" class="form-label">
                            Alasan Pembatalan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="alasan_pembatalan"
                                  name="alasan_pembatalan"
                                  rows="4"
                                  required
                                  placeholder="Mohon jelaskan alasan pembatalan pesanan Anda..."></textarea>
                        <div class="form-text">Minimal 10 karakter</div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i>
                        <small>
                            Setelah Anda mengajukan pembatalan, admin akan melakukan review.
                            Anda akan mendapat notifikasi setelah pembatalan diproses.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger" id="submitCancelBtn">
                        <i class="bi bi-check-circle"></i> Ya, Batalkan Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">
                    <i class="bi bi-star"></i> Berikan Review untuk Pesanan <span id="reviewModalOrderCode"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reviewProductsList" class="mb-3">
                    <!-- Produk akan dimuat di sini -->
                </div>

                <div class="mb-3">
                    <label class="form-label">Rating Anda</label>
                    <div class="d-flex align-items-center">
                        <div class="rating" id="reviewRating" data-rating="0">
                            <i class="bi bi-star-fill" data-value="1"></i>
                            <i class="bi bi-star-fill" data-value="2"></i>
                            <i class="bi bi-star-fill" data-value="3"></i>
                            <i class="bi bi-star-fill" data-value="4"></i>
                            <i class="bi bi-star-fill" data-value="5"></i>
                        </div>
                        <input type="hidden" id="reviewRatingValue" name="rating" value="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reviewComment" class="form-label">Komentar</label>
                    <textarea class="form-control" id="reviewComment" rows="3" placeholder="Tulis komentar Anda di sini..."></textarea>
                </div>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i>
                    <strong>Catatan:</strong> Komentar Anda akan ditampilkan di halaman produk setelah disetujui.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" id="submitReviewBtn">
                    <i class="bi bi-check-circle"></i> Kirim Review
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentOrderId = null;

function confirmOrderReceived(orderId, orderCode) {
    if (!confirm(`Apakah Anda yakin sudah menerima pesanan ${orderCode}?\n\nSetelah dikonfirmasi, status akan berubah menjadi "Selesai" dan Anda dapat memberikan review untuk produk.`)) {
        return;
    }

    // Show loading state
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

    fetch(`/orders/${orderId}/confirm-received`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Check if response is OK (status 200-299)
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Terjadi kesalahan saat mengkonfirmasi pesanan');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.row'));

            // Reload page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            alert(data.message || 'Terjadi kesalahan saat mengkonfirmasi pesanan');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function confirmPickup(orderId, orderCode) {
    if (!confirm(`Apakah Anda yakin sudah mengambil pesanan ${orderCode}?\n\nSetelah dikonfirmasi, status akan berubah menjadi "Selesai" dan Anda dapat memberikan review untuk produk.`)) {
        return;
    }

    // Show loading state
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

    fetch(`/orders/${orderId}/confirm-pickup`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Check if response is OK (status 200-299)
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Terjadi kesalahan saat mengkonfirmasi pengambilan');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.row'));

            // Reload page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            alert(data.message || 'Terjadi kesalahan saat mengkonfirmasi pengambilan');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function showCancelModal(orderId, orderCode) {
    currentOrderId = orderId;
    document.getElementById('orderCodeDisplay').textContent = orderCode;
    document.getElementById('cancelOrderForm').action = `/orders/${orderId}/cancel`;
    document.getElementById('alasan_pembatalan').value = '';

    const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    modal.show();
}

function showCancellationDetail(cancellationId, orderCode, reason, status, date, adminNote) {
    // Set order code and date
    document.getElementById('cancelDetailOrderCode').textContent = orderCode;
    document.getElementById('cancelDetailDate').textContent = date;
    document.getElementById('cancelDetailReason').textContent = reason;

    // Set status badge
    const statusContainer = document.getElementById('cancelDetailStatus');
    let statusBadge = '';
    let headerClass = '';

    if (status === 'diajukan') {
        statusBadge = '<span class="badge bg-warning text-dark px-3 py-2"><i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi</span>';
        headerClass = 'bg-warning text-dark';
        document.getElementById('pendingInfo').style.display = 'block';
        document.getElementById('approvedInfo').style.display = 'none';
        document.getElementById('rejectedInfo').style.display = 'none';
    } else if (status === 'disetujui') {
        statusBadge = '<span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle"></i> Disetujui</span>';
        headerClass = 'bg-success text-white';
        document.getElementById('pendingInfo').style.display = 'none';
        document.getElementById('approvedInfo').style.display = 'block';
        document.getElementById('rejectedInfo').style.display = 'none';
    } else if (status === 'ditolak') {
        statusBadge = '<span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle"></i> Ditolak</span>';
        headerClass = 'bg-danger text-white';
        document.getElementById('pendingInfo').style.display = 'none';
        document.getElementById('approvedInfo').style.display = 'none';
        document.getElementById('rejectedInfo').style.display = 'block';
    }

    statusContainer.innerHTML = statusBadge;

    // Update header color
    const header = document.getElementById('cancelDetailHeader');
    header.className = 'modal-header ' + headerClass;

    // Show admin note if exists
    if (adminNote && adminNote.trim() !== '') {
        document.getElementById('adminNoteSection').style.display = 'block';
        document.getElementById('cancelDetailAdminNote').innerHTML = '<i class="bi bi-person-badge"></i> ' + adminNote;
    } else {
        document.getElementById('adminNoteSection').style.display = 'none';
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('cancellationDetailModal'));
    modal.show();
}

// Form validation and submission
document.getElementById('cancelOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const alasan = document.getElementById('alasan_pembatalan').value.trim();

    if (alasan.length < 10) {
        alert('Alasan pembatalan minimal 10 karakter');
        return;
    }

    const submitBtn = document.getElementById('submitCancelBtn');
    const originalText = submitBtn.innerHTML;

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

    // Submit via AJAX
    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            alasan_pembatalan: alasan
        })
    })
    .then(response => {
        // Check if response is OK before parsing
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Terjadi kesalahan saat membatalkan pesanan');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal'));
            modal.hide();

            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.row'));

            // Reload page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            alert(data.message || 'Terjadi kesalahan saat membatalkan pesanan');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endpush
