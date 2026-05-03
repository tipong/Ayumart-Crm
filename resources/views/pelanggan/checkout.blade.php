@extends('layouts.pelanggan')

@section('title', 'Checkout')

{{-- Add Leaflet CSS for interactive maps --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<style>
.custom-map-marker {
    background: none;
    border: none;
}

/* Map search results styling */
#searchResults .list-group-item {
    cursor: pointer;
    transition: all 0.2s;
}

#searchResults .list-group-item:hover {
    background-color: #f0f8ff;
    border-color: #0d6efd;
}
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Shipping Method Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-truck"></i> Metode Pengiriman</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('order.place') }}" method="POST" id="checkoutForm">
                        @csrf
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card h-100 shipping-method-card" data-method="kurir">
                                        <div class="card-body text-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="metode_pengiriman"
                                                        id="kurir" value="kurir" required>
                                                <label class="form-check-label w-100" for="kurir">
                                                    <i class="bi bi-truck fs-1 text-primary"></i>
                                                    <h6 class="mt-2">Dikirim Kurir</h6>
                                                    <p class="text-muted small mb-0">Produk akan dikirim ke alamat Anda</p>
                                                    <span class="badge bg-info mt-2" id="shippingBadge" style="font-size: 0.65rem;">Pilih alamat pengiriman untuk menghitung biaya</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 shipping-method-card" data-method="ambil_sendiri">
                                        <div class="card-body text-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="metode_pengiriman"
                                                        id="ambil_sendiri" value="ambil_sendiri">
                                                <label class="form-check-label w-100" for="ambil_sendiri">
                                                    <i class="bi bi-shop fs-1 text-success"></i>
                                                    <h6 class="mt-2">Ambil Sendiri</h6>
                                                    <p class="text-muted small mb-0">Ambil di toko terdekat</p>
                                                    <span class="badge bg-success mt-2">Gratis</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('metode_pengiriman')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address Selection (shown when kurir is selected) -->
                        <div id="addressSection" style="display: none;">
                            <h6 class="mb-3"><i class="bi bi-geo-alt"></i> Alamat Pengiriman</h6>

                            <!-- Display selected branch for kurir delivery -->
                            <div id="selectedBranchForKurirInfo" class="card mb-3" style="display: none;">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="bi bi-shop"></i> Cabang Pengiriman (Sesuai Pilihan Awal)
                                    </h6>
                                    <div id="selectedBranchForKurirDetails">
                                        <p class="mb-1"><strong id="selectedBranchKurirName">-</strong></p>
                                        <p class="text-muted small mb-2" id="selectedBranchKurirAddress">-</p>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            Stok produk akan diambil dari cabang ini untuk pengiriman ke alamat Anda
                                        </small>
                                    </div>
                                </div>
                            </div>

                            @if($addresses->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label">Pilih Alamat</label>
                                    @foreach($addresses as $address)
                                        <div class="card mb-2 address-card" data-address-id="{{ $address->id }}">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="address_id"
                                                            id="address_{{ $address->id }}" value="{{ $address->id }}"
                                                           {{ $address->is_default ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="address_{{ $address->id }}">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong>{{ $address->label }}</strong>
                                                                @if($address->is_default)
                                                                    <span class="badge bg-success ms-2">Default</span>
                                                                @endif
                                                                <p class="mb-1 mt-1">{{ $address->nama_penerima }} - {{ $address->no_telp_penerima }}</p>
                                                                <p class="text-muted small mb-0">{{ $address->formatted_address }}</p>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="bi bi-plus-circle"></i> Tambah Alamat Baru
                                </button>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Anda belum memiliki alamat pengiriman.
                                    <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                        Tambah Alamat
                                    </button>
                                </div>
                            @endif
                            @error('address_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pickup Info (shown when ambil_sendiri is selected) -->
                        <div id="pickupSection" style="display: none;">
                            <div class="alert alert-info mb-3">
                                <h6><i class="bi bi-info-circle"></i> Informasi Pengambilan</h6>
                                <p class="mb-2">Pesanan Anda akan dikemas dan siap diambil di cabang Ayu Mart setelah pembayaran berhasil dikonfirmasi.</p>
                            </div>

                            <!-- Display selected branch from session -->
                            <div id="selectedBranchInfo" class="card mb-3" style="display: none;">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="bi bi-shop"></i> Cabang yang Dipilih
                                    </h6>
                                    <div id="selectedBranchDetails">
                                        <p class="mb-1"><strong id="selectedBranchName">-</strong></p>
                                        <p class="text-muted small mb-2" id="selectedBranchAddress">-</p>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            Stok produk akan diambil dari cabang ini
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Optional: Detect different location button -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="detectPickupLocation()">
                                    <i class="bi bi-geo-alt-fill"></i> Gunakan Lokasi Saya untuk Cari Cabang Terdekat
                                </button>
                                <small class="text-muted d-block mt-1">
                                    Opsional: Deteksi lokasi untuk mencari cabang terdekat dari posisi Anda saat ini
                                </small>
                            </div>

                            <input type="hidden" name="pickup_latitude" id="pickup_latitude">
                            <input type="hidden" name="pickup_longitude" id="pickup_longitude">
                            <input type="hidden" name="id_cabang" id="id_cabang">
                        </div>

                        <!-- Hidden field for shipping cost -->
                        <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">

                        <!-- Order Notes -->
                        <div class="mb-3 mt-4">
                            <label for="catatan" class="form-label">Catatan Pesanan (Opsional)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"
                                       placeholder="Tambahkan catatan untuk pesanan Anda...">{{ old('catatan') }}</textarea>
                        </div>

                        <!-- Order Items -->
                        <h6 class="mb-3 mt-4"><i class="bi bi-basket"></i> Produk yang Dibeli</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center" width="100">Jumlah</th>
                                        <th class="text-end" width="150">Harga</th>
                                        <th class="text-end" width="150">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cartItems as $item)
                                        @if($item->product)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->product->nama_produk }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $item->qty }}</span>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($item->product->harga_produk, 0, ',', '.') }}</td>
                                            <td class="text-end"><strong>Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}</strong></td>
                                        </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-cart-x fs-1"></i>
                                                <p class="mt-2">Keranjang belanja Anda kosong</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('pelanggan.cart') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-credit-card"></i> Lanjut ke Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pesanan</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($membership && $membership->is_active && $membership->isValid() && $discount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>
                            <i class="bi bi-tag-fill"></i> Diskon Membership
                            <span class="badge bg-{{ $membership->tier == 'platinum' ? 'info' : ($membership->tier == 'gold' ? 'warning' : ($membership->tier == 'silver' ? 'secondary' : 'warning')) }}">
                                {{ ucfirst($membership->tier) }}
                            </span>
                        </span>
                        <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if($isFirstTransaction && !$hasMembership && $membershipFee > 0)
                    <div class="d-flex justify-content-between mb-2 text-info">
                        <span>
                            <i class="bi bi-award"></i> Biaya Pembuatan Member
                            <span class="badge bg-info">Transaksi Pertama</span>
                        </span>
                        <span>Rp {{ number_format($membershipFee, 0, ',', '.') }}</span>
                    </div>
                    <div class="alert alert-info small mb-2">
                        <i class="bi bi-info-circle"></i>
                        Ini adalah transaksi pertama Anda dan Anda belum terdaftar sebagai member. Biaya ini untuk pembuatan kartu member yang memberikan berbagai keuntungan seperti diskon hingga 20% dan poin reward!
                    </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2" id="shippingCostRow" style="display: none;">
                        <span><i class="bi bi-truck"></i> Ongkos Kirim</span>
                        <span id="shippingCostValue">Rp 0</span>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong class="text-primary" id="totalAmount">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                    </div>

                    @if($pointsToEarn > 0)
                    <div class="alert alert-info mb-0 small">
                        <i class="bi bi-star-fill"></i> Anda akan mendapatkan <strong id="pointsEarned">{{ $pointsToEarn }}</strong> poin dari transaksi ini!
                        <small class="d-block mt-1 text-muted">1 poin = Rp 20.000</small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Membership Info -->
            @if($membership && $membership->is_active && $membership->isValid())
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-award"></i> Membership Anda</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tier</span>
                        <span class="badge bg-{{ $membership->tier == 'platinum' ? 'info' : ($membership->tier == 'gold' ? 'warning' : ($membership->tier == 'silver' ? 'secondary' : 'warning')) }}">
                            {{ ucfirst($membership->tier) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Poin</span>
                        <strong>{{ number_format($membership->points) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Diskon</span>
                        <strong class="text-success">{{ $membership->discount_percentage }}%</strong>
                    </div>

                    @php
                        $nextTier = $membership->getNextTierInfo();
                    @endphp

                    @if($nextTier['next_tier'] != 'Maximum')
                    <hr>
                    <small class="text-muted">
                        Kumpulkan <strong>{{ $nextTier['points_needed'] }}</strong> poin lagi untuk naik ke tier <strong>{{ $nextTier['next_tier'] }}</strong>
                    </small>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Alamat Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addAddressForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Label Alamat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="label" placeholder="Rumah, Kantor, dll" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_penerima" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_telp_penerima" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kota <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kota" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" class="form-control" name="kecamatan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" name="kode_pos">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alamat_lengkap" rows="3" required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Lokasi (GPS/Maps)</label>
                            <div class="btn-group mb-2" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="getGPSLocation()">
                                    <i class="bi bi-geo-alt-fill"></i> Gunakan GPS Saya
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="pickLocationFromMapsAdd()">
                                    <i class="bi bi-map"></i> Pilih dari Maps
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="modal_latitude" name="latitude" placeholder="Latitude (contoh: -6.200000)" readonly>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="modal_longitude" name="longitude" placeholder="Longitude (contoh: 106.816666)" readonly>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1" id="gpsStatus"></small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_default" id="modal_is_default" value="1">
                                <label class="form-check-label" for="modal_is_default">
                                    Jadikan alamat utama
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitAddAddress()">
                    <i class="bi bi-save"></i> Simpan Alamat
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.shipping-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
}

.shipping-method-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.shipping-method-card.active {
    border-color: #0d6efd;
    background-color: #f0f8ff;
}

.address-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.address-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.address-card.active {
    border-color: #0d6efd;
    background-color: #f0f8ff;
}

.table th {
    background-color: #f8f9fa;
}
</style>

<script>
// Shipping method handling
let shippingMethods = null;
let addressSection = null;
let pickupSection = null;
let shippingCostRow = null;
let shippingCostValue = null;
let totalAmount = null;
let pointsEarned = null;

const subtotal = {{ $subtotal }};
const discount = {{ $discount }};
const membershipFee = {{ $membershipFee }};
let currentShippingCost = 0;

// Store checkout data globally
window.checkoutData = {
    subtotal: subtotal,
    discount: discount,
    membershipFee: membershipFee
};

// Initialize DOM elements when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOMContentLoaded: Initializing checkout form ===');

    // Get DOM elements
    shippingMethods = document.querySelectorAll('input[name="metode_pengiriman"]');
    addressSection = document.getElementById('addressSection');
    pickupSection = document.getElementById('pickupSection');
    shippingCostRow = document.getElementById('shippingCostRow');
    shippingCostValue = document.getElementById('shippingCostValue');
    totalAmount = document.getElementById('totalAmount');
    pointsEarned = document.getElementById('pointsEarned');

    console.log('✓ DOM elements loaded:', {
        shippingMethods: shippingMethods.length,
        addressSection: !!addressSection,
        pickupSection: !!pickupSection,
        shippingCostRow: !!shippingCostRow,
        totalAmount: !!totalAmount
    });

    // Attach event listeners to shipping methods
    shippingMethods.forEach((method, index) => {
        console.log('Attaching listener to shipping method ' + index + ':', method.value);

        method.addEventListener('change', function() {
            console.log('🎯 Shipping method changed to:', this.value);

            // Update card styling
            document.querySelectorAll('.shipping-method-card').forEach(card => {
                card.classList.remove('active');
            });
            const cardElement = this.closest('.shipping-method-card');
            if (cardElement) {
                cardElement.classList.add('active');
                console.log('✓ Card styling updated');
            }

            // Show/hide appropriate sections
            if (this.value === 'kurir') {
                console.log('→ Showing address section for kurir delivery');
                if (addressSection) {
                    addressSection.style.display = 'block';
                    console.log('✓ Address section displayed');
                }
                if (pickupSection) {
                    pickupSection.style.display = 'none';
                    console.log('✓ Pickup section hidden');
                }

                // Display branch info for kurir delivery (from session)
                displayBranchForKurirDelivery();

                // Check if address is already selected
                const selectedAddress = document.querySelector('input[name="address_id"]:checked');
                if (selectedAddress) {
                    const addressId = selectedAddress.value;
                    console.log('→ Calculating shipping cost for address:', addressId);
                    detectNearestBranchForDelivery(addressId);
                } else {
                    // Use default shipping cost if no address selected
                    console.log('→ No address selected yet, using default cost');
                    currentShippingCost = 15000;
                    updateShippingCost(currentShippingCost);
                }
            } else if (this.value === 'ambil_sendiri') {
                console.log('→ Showing pickup section for self pickup');
                if (addressSection) {
                    addressSection.style.display = 'none';
                    console.log('✓ Address section hidden');
                }
                if (pickupSection) {
                    pickupSection.style.display = 'block';
                    console.log('✓ Pickup section displayed');
                }

                // Set shipping cost to 0 for pickup
                currentShippingCost = 0;
                updateShippingCost(0);

                // Display selected branch from session (if any)
                displaySelectedBranchFromSession();
            }
        });
    });

    // Attach event listeners to address cards
    console.log('=== Attaching event listeners to address cards ===');
    document.querySelectorAll('.address-card').forEach((card, index) => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;

            document.querySelectorAll('.address-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            // Get address coordinates and calculate shipping cost
            const addressId = this.dataset.addressId;
            console.log('→ Address card selected:', addressId);
            detectNearestBranchForDelivery(addressId);
        });
    });

    console.log('✓ All event listeners attached');

    // Run initialization script
    initializeShippingCostOnLoad();
});

// Calculate shipping cost from selected branch to delivery address
// PENTING: Cabang pengiriman sudah ditentukan di awal website, tidak boleh berubah saat checkout
//
// FORMULA ONGKOS KIRIM (KURIR):
// ┌─────────────────────────────────────────────────────────────┐
// │ Biaya Pengiriman = Jarak (km) × Rp 10.000 per km             │
// │ Contoh: Jarak 5.5 km = Rp 55.000                             │
// │ Perhitungan dibulatkan ke atas (Math.ceil) untuk kemudahan   │
// └─────────────────────────────────────────────────────────────┘
//
// Metode Perhitungan Jarak: Haversine Formula
// (menghitung jarak terpendek antara dua titik GPS di permukaan bumi)

/**
 * Calculate distance between two GPS coordinates using Haversine formula
 * @param {number} lat1 - Latitude of point 1 (branch)
 * @param {number} lon1 - Longitude of point 1 (branch)
 * @param {number} lat2 - Latitude of point 2 (delivery address)
 * @param {number} lon2 - Longitude of point 2 (delivery address)
 * @returns {number} Distance in kilometers
 */
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c; // Distance in km

    return distance;
}

function detectNearestBranchForDelivery(addressId) {
    const shippingBadge = document.getElementById('shippingBadge');
    const SHIPPING_COST_PER_KM = 10000; // Rp 10.000 per km

    // Show loading state
    if (shippingBadge) {
        shippingBadge.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Menghitung biaya pengiriman...';
        shippingBadge.className = 'badge bg-secondary mt-2';
        shippingBadge.style.fontSize = '0.75rem';
    }

    // Get address coordinates
    fetch('/api/address/' + addressId, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.address && data.address.latitude && data.address.longitude) {
            // Get session branch info (cabang yang sudah dipilih di awal)
            fetch('/api/get-session-branch', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(branchSessionData => {
                if (branchSessionData.success && branchSessionData.branch) {
                    const sessionBranch = branchSessionData.branch;
                    const deliveryAddress = data.address;

                    console.log('📍 Branch from session (untuk pengiriman kurir):', sessionBranch.nama_cabang);
                    console.log('📍 Branch coordinates:', 'Lat=' + sessionBranch.latitude + ', Lon=' + sessionBranch.longitude);
                    console.log('📍 Delivery address:', deliveryAddress);
                    console.log('📍 Address coordinates:', 'Lat=' + deliveryAddress.latitude + ', Lon=' + deliveryAddress.longitude);

                    // Validate coordinates are valid numbers
                    const branchLat = parseFloat(sessionBranch.latitude);
                    const branchLon = parseFloat(sessionBranch.longitude);
                    const addressLat = parseFloat(deliveryAddress.latitude);
                    const addressLon = parseFloat(deliveryAddress.longitude);

                    // Check if all coordinates are valid numbers (not NaN)
                    if (isNaN(branchLat) || isNaN(branchLon) || isNaN(addressLat) || isNaN(addressLon)) {
                        console.error('❌ Invalid coordinates detected!');
                        console.error('Branch Lat: ' + branchLat + ' (NaN: ' + isNaN(branchLat) + ')');
                        console.error('Branch Lon: ' + branchLon + ' (NaN: ' + isNaN(branchLon) + ')');
                        console.error('Address Lat: ' + addressLat + ' (NaN: ' + isNaN(addressLat) + ')');
                        console.error('Address Lon: ' + addressLon + ' (NaN: ' + isNaN(addressLon) + ')');
                        console.warn('Menggunakan biaya estimasi fallback karena data lokasi tidak lengkap');
                        fallbackShippingCost();
                        return;
                    }

                    // Keep the branch that was selected at the beginning
                    document.getElementById('id_cabang').value = sessionBranch.id_cabang;

                    // Calculate distance between branch and delivery address using Haversine formula
                    const distance = calculateDistance(
                        branchLat,
                        branchLon,
                        addressLat,
                        addressLon
                    );

                    // Validate distance is a valid number
                    if (isNaN(distance)) {
                        console.error('❌ Distance calculation resulted in NaN!');
                        console.warn('Menggunakan biaya estimasi fallback karena perhitungan jarak gagal');
                        fallbackShippingCost();
                        return;
                    }

                    // Calculate shipping cost: distance × Rp 10.000 per km
                    const shippingCost = Math.ceil(distance * SHIPPING_COST_PER_KM);
                    currentShippingCost = shippingCost;

                    console.log('✅ Perhitungan Ongkos Kirim Berhasil:',
                                'Jarak: ' + distance.toFixed(2) + ' km',
                                'Tarif: Rp ' + formatNumber(SHIPPING_COST_PER_KM) + ' per km',
                                'Total: Rp ' + formatNumber(shippingCost));

                    // Update shipping badge
                    if (shippingBadge) {
                        shippingBadge.innerHTML = 'Biaya: Rp ' + formatNumber(shippingCost) + ' (' + distance.toFixed(1) + ' km dari ' + sessionBranch.nama_cabang + ')';
                        shippingBadge.className = 'badge bg-info mt-2';
                        shippingBadge.style.fontSize = '0.55rem';
                    }

                    // Update shipping cost in order summary
                    updateShippingCost(shippingCost);
                } else {
                    console.warn('⚠️ Tidak ada cabang dalam session, menggunakan biaya estimasi');
                    fallbackShippingCost();
                }
            })
            .catch(error => {
                console.error('❌ Error fetching session branch:', error);
                fallbackShippingCost();
            });
        } else {
            fallbackShippingCost();
        }
    })
    .catch(error => {
        console.error('❌ Error fetching address:', error);
        fallbackShippingCost();
    });
}

function fallbackShippingCost() {
    const shippingBadge = document.getElementById('shippingBadge');
    currentShippingCost = 15000;
    if (shippingBadge) {
        shippingBadge.innerHTML = 'Biaya: Rp 15.000 <small class="d-block mt-1 text-white-50">(estimasi - data lokasi tidak lengkap)</small>';
        shippingBadge.className = 'badge bg-warning mt-2';
        shippingBadge.style.fontSize = '0.75rem';
    }
    updateShippingCost(currentShippingCost);
}

function updateShippingCost(cost) {
    shippingCostRow.style.display = 'flex';
    shippingCostValue.textContent = 'Rp ' + formatNumber(cost);

    // Save shipping cost to hidden field
    document.getElementById('shipping_cost').value = cost;

    // Update total
    const newTotal = subtotal - discount + membershipFee + cost;
    totalAmount.textContent = 'Rp ' + formatNumber(newTotal);

    // Update points if element exists
    if (pointsEarned) {
        const newPoints = Math.floor(newTotal / 20000);
        pointsEarned.textContent = newPoints;
    }
}

// Get GPS location
function getGPSLocation() {
    const gpsStatus = document.getElementById('gpsStatus');

    if (navigator.geolocation) {
        gpsStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise"></i> Mengambil lokasi GPS...</span>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('modal_latitude').value = position.coords.latitude;
                document.getElementById('modal_longitude').value = position.coords.longitude;
                gpsStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Lokasi GPS berhasil diambil! (Lat: ' +
                    position.coords.latitude.toFixed(6) + ', Long: ' + position.coords.longitude.toFixed(6) + ')</span>';
            },
            function(error) {
                gpsStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Gagal mengambil lokasi GPS: ' + error.message + '</span>';
                console.error('GPS Error:', error);
            }
        );
    } else {
        gpsStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Browser tidak mendukung GPS</span>';
    }
}

// Submit new address
function submitAddAddress() {
    const form = document.getElementById('addAddressForm');
    const formData = new FormData(form);
    const submitBtn = event?.target || document.querySelector('#addAddressModal button[onclick="submitAddAddress()"]');

    // Validate required fields
    const label = formData.get('label');
    const namaPenerima = formData.get('nama_penerima');
    const noTelp = formData.get('no_telp_penerima');
    const kota = formData.get('kota');
    const alamatLengkap = formData.get('alamat_lengkap');

    if (!label || !namaPenerima || !noTelp || !kota || !alamatLengkap) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Tidak Lengkap',
            text: 'Mohon lengkapi semua field yang wajib diisi',
            confirmButtonColor: '#3085d6'
        });
        return;
    }

    // Disable button
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
    }

    // Show loading
    Swal.fire({
        title: 'Menyimpan Alamat...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Add CSRF token to FormData
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("pelanggan.addresses.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Alamat berhasil ditambahkan',
                confirmButtonColor: '#3085d6',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                // Close modal
                const addModal = bootstrap.Modal.getInstance(document.getElementById('addAddressModal'));
                if (addModal) {
                    addModal.hide();
                }
                // Reload page to reflect changes
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Gagal menambahkan alamat');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';

        if (error.message) {
            errorMsg = error.message;
        } else if (error.errors) {
            const errors = Object.values(error.errors).flat();
            errorMsg = errors.join('\n');
        }

        Swal.fire({
            icon: 'error',
            title: 'Gagal Menyimpan',
            text: errorMsg,
            confirmButtonColor: '#3085d6'
        });

        // Re-enable button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Alamat';
        }
    });
}

// Detect nearest branch for pickup
function detectNearestBranch() {
    console.log('=== detectNearestBranch called ===');
    const branchDetectionStatus = document.getElementById('branchDetectionStatus');
    const branchInfoCard = document.getElementById('branchInfoCard');

    if (!branchDetectionStatus) {
        console.error('ERROR: branchDetectionStatus element not found');
        alert('Error: Element status tidak ditemukan');
        return;
    }

    if (!navigator.geolocation) {
        const errorMsg = 'Browser tidak mendukung GPS';
        branchDetectionStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> ' + errorMsg + '</span>';
        console.error('ERROR:', errorMsg);
        alert(errorMsg + '. Silakan gunakan browser modern seperti Chrome atau Firefox.');
        return;
    }

    branchDetectionStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise spinner-border spinner-border-sm"></i> Mendeteksi lokasi Anda...</span>';
    console.log('Requesting geolocation with high accuracy...');

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            console.log('✓ GPS Location obtained:', {
                latitude: latitude,
                longitude: longitude,
                accuracy: accuracy + 'm'
            });

            // Save coordinates
            document.getElementById('pickup_latitude').value = latitude;
            document.getElementById('pickup_longitude').value = longitude;

            branchDetectionStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise spinner-border spinner-border-sm"></i> Mencari cabang terdekat...</span>';

            const url = '/api/nearest-branch?latitude=' + latitude + '&longitude=' + longitude;
            console.log('Fetching nearest branch from:', url);

            // Call API to get nearest branch
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                console.log('API Response status:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error('HTTP error ' + response.status + ': ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('✓ API Response data:', data);

                if (data.success && data.branch) {
                    const branch = data.branch;
                    console.log('✓ Nearest branch found:', branch.nama_cabang, '(' + branch.distance + ' km)');

                    // Display branch info
                    document.getElementById('branchName').textContent = branch.nama_cabang;
                    document.getElementById('branchAddress').textContent = branch.formatted_address;
                    document.getElementById('branchPhone').textContent = branch.no_telepon || 'Tidak tersedia';
                    document.getElementById('branchHours').textContent = branch.jam_buka.substring(0, 5) + ' - ' + branch.jam_tutup.substring(0, 5) + ' WIB';
                    document.getElementById('branchDistance').textContent = branch.distance.toFixed(2) + ' km';

                    const mapsLink = document.getElementById('branchMapsLink');
                    if (branch.google_maps_url) {
                        mapsLink.href = branch.google_maps_url;
                        mapsLink.style.display = 'inline-block';
                    } else {
                        mapsLink.style.display = 'none';
                    }

                    branchInfoCard.style.display = 'block';
                    branchDetectionStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Cabang terdekat berhasil ditemukan!</span>';

                    console.log('✓ Branch info displayed successfully');
                } else {
                    console.warn('⚠ No branch found in response:', data);
                    branchDetectionStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Tidak dapat menemukan cabang terdekat. ' + (data.message || '') + '</span>';
                    branchInfoCard.style.display = 'none';
                    alert('Tidak dapat menemukan cabang terdekat. ' + (data.message || 'Silakan coba lagi.'));
                }
            })
            .catch(error => {
                console.error('✗ Error fetching nearest branch:', error);
                branchDetectionStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Gagal mengambil data cabang: ' + error.message + '</span>';
                branchInfoCard.style.display = 'none';
                alert('Gagal mengambil data cabang: ' + error.message + '. Silakan coba lagi.');
            });
        },
        function(error) {
            console.error('✗ GPS Error:', error);
            let errorMessage = 'Unknown error';
            let userMessage = '';

            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'Izin lokasi ditolak';
                    userMessage = 'Anda harus mengizinkan akses lokasi untuk menggunakan fitur ini. Silakan ubah pengaturan browser Anda.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Lokasi tidak tersedia';
                    userMessage = 'Lokasi GPS tidak dapat ditentukan. Pastikan GPS aktif dan Anda berada di tempat terbuka.';
                    break;
                case error.TIMEOUT:
                    errorMessage = 'Waktu habis saat mengambil lokasi';
                    userMessage = 'Waktu habis saat mendeteksi lokasi. Silakan coba lagi.';
                    break;
            }

            branchDetectionStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> ' + errorMessage + '</span>';
            alert(userMessage || errorMessage);
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    );

    console.log('=== Geolocation request sent ===');
}

// Display branch info for kurir delivery (from session)
function displayBranchForKurirDelivery() {
    console.log('=== displayBranchForKurirDelivery called ===');

    // Fetch session branch info from backend
    fetch('/api/get-session-branch', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Session branch data for kurir:', data);

        if (data.success && data.branch) {
            const branch = data.branch;
            const branchInfo = document.getElementById('selectedBranchForKurirInfo');
            const branchName = document.getElementById('selectedBranchKurirName');
            const branchAddress = document.getElementById('selectedBranchKurirAddress');

            // SAVE branch ID to form
            document.getElementById('id_cabang').value = branch.id_cabang || '';

            if (branchName) branchName.textContent = branch.nama_cabang || '-';
            if (branchAddress) branchAddress.textContent = branch.alamat || '-';

            if (branchInfo) {
                branchInfo.style.display = 'block';
                console.log('✓ Branch for kurir delivery displayed:', branch.nama_cabang, '(ID:', branch.id_cabang + ')');
            }
        } else {
            console.log('⚠ No branch in session for kurir');
            const branchInfo = document.getElementById('selectedBranchForKurirInfo');
            if (branchInfo) branchInfo.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error fetching session branch for kurir:', error);
        const branchInfo = document.getElementById('selectedBranchForKurirInfo');
        if (branchInfo) branchInfo.style.display = 'none';
    });
}

// NEW: Display selected branch from session
function displaySelectedBranchFromSession() {
    console.log('=== displaySelectedBranchFromSession called ===');

    // Fetch session branch info from backend
    fetch('/api/get-session-branch', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Session branch data:', data);

        if (data.success && data.branch) {
            const branch = data.branch;
            const branchInfo = document.getElementById('selectedBranchInfo');
            const branchName = document.getElementById('selectedBranchName');
            const branchAddress = document.getElementById('selectedBranchAddress');

            // SAVE branch ID to form
            document.getElementById('id_cabang').value = branch.id_cabang || '';

            if (branchName) branchName.textContent = branch.nama_cabang || '-';
            if (branchAddress) branchAddress.textContent = branch.alamat || '-';

            if (branchInfo) {
                branchInfo.style.display = 'block';
                console.log('✓ Branch from session displayed:', branch.nama_cabang, '(ID:', branch.id_cabang + ')');
            }
        } else {
            console.log('⚠ No branch in session');
            const branchInfo = document.getElementById('selectedBranchInfo');
            if (branchInfo) branchInfo.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error fetching session branch:', error);
        const branchInfo = document.getElementById('selectedBranchInfo');
        if (branchInfo) branchInfo.style.display = 'none';
    });
}

// NEW: Detect pickup location (optional - for checkout page)
function detectPickupLocation() {
    console.log('=== detectPickupLocation called ===');

    if (!navigator.geolocation) {
        alert('Browser tidak mendukung GPS. Silakan gunakan browser modern.');
        return;
    }

    // Show loading with SweetAlert2 if available
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Mendeteksi Lokasi...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            console.log('✓ GPS Location obtained:', latitude, longitude);

            // Save coordinates
            document.getElementById('pickup_latitude').value = latitude;
            document.getElementById('pickup_longitude').value = longitude;

            // Find nearest branch
            fetch('/api/nearest-branch?latitude=' + latitude + '&longitude=' + longitude, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.branch) {
                    const branch = data.branch;
                    console.log('✓ Nearest branch found:', branch.nama_cabang);

                    // SAVE branch ID to form
                    document.getElementById('id_cabang').value = branch.id_cabang;

                    // Update display
                    const branchInfo = document.getElementById('selectedBranchInfo');
                    const branchName = document.getElementById('selectedBranchName');
                    const branchAddress = document.getElementById('selectedBranchAddress');

                    if (branchName) branchName.textContent = branch.nama_cabang;
                    if (branchAddress) branchAddress.textContent = branch.formatted_address;
                    if (branchInfo) branchInfo.style.display = 'block';

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cabang Terdekat Ditemukan!',
                            html: '<strong>' + branch.nama_cabang + '</strong><br>' +
                                  '<small class="text-muted">' + branch.distance + ' km dari lokasi Anda</small>',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        alert('Cabang terdekat: ' + branch.nama_cabang + ' (' + branch.distance + ' km)');
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cabang Tidak Ditemukan',
                            text: 'Tidak dapat menemukan cabang terdekat. Sistem akan menggunakan cabang yang Anda pilih sebelumnya.',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        alert('Tidak dapat menemukan cabang terdekat.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengambil data cabang: ' + error.message,
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    alert('Gagal mengambil data cabang.');
                }
            });
        },
        function(error) {
            console.error('GPS Error:', error);
            let errorMessage = 'Gagal mendeteksi lokasi';

            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'Izin lokasi ditolak. Silakan izinkan akses lokasi.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
                    break;
                case error.TIMEOUT:
                    errorMessage = 'Waktu habis saat mendeteksi lokasi.';
                    break;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mendeteksi Lokasi',
                    text: errorMessage,
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert(errorMessage);
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    );
}

// Format number helper
function formatNumber(num) {
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Initialize shipping cost on page load
function initializeShippingCostOnLoad() {
    console.log('=== Initializing shipping cost on page load ===');

    // Check if shipping method is already selected
    const selectedShippingMethod = document.querySelector('input[name="metode_pengiriman"]:checked');

    if (selectedShippingMethod) {
        console.log('Selected shipping method found:', selectedShippingMethod.value);

        if (selectedShippingMethod.value === 'kurir') {
            // Show address section
            if (addressSection) addressSection.style.display = 'block';
            if (pickupSection) pickupSection.style.display = 'none';

            // Display branch info for kurir delivery (from session) - ALWAYS SHOW SESSION BRANCH
            displayBranchForKurirDelivery();

            // Check if address is already selected
            const selectedAddress = document.querySelector('input[name="address_id"]:checked');

            if (selectedAddress) {
                const addressId = selectedAddress.value;
                console.log('Selected address found:', addressId);
                console.log('Calculating shipping cost from session branch to delivery address...');

                // Calculate shipping cost for selected address (from session branch, NOT nearest branch)
                detectNearestBranchForDelivery(addressId);
            } else {
                console.log('No address selected yet, using default shipping cost');
                // Use default shipping cost
                currentShippingCost = 15000;
                updateShippingCost(currentShippingCost);
            }
        } else if (selectedShippingMethod.value === 'ambil_sendiri') {
            console.log('Pickup method selected, shipping cost = 0');

            // Show pickup section
            if (addressSection) addressSection.style.display = 'none';
            if (pickupSection) pickupSection.style.display = 'block';

            // Set shipping cost to 0 for pickup
            currentShippingCost = 0;
            updateShippingCost(0);
        }
    } else {
        console.log('No shipping method selected yet');
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            console.log('=== Form submit event fired ===');

            const metode = document.querySelector('input[name="metode_pengiriman"]:checked');
            console.log('Selected shipping method:', metode?.value || 'NONE');

            if (!metode) {
                e.preventDefault();
                console.warn('⚠ Validation failed: No shipping method selected');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Metode Pengiriman Belum Dipilih',
                        text: 'Silakan pilih metode pengiriman terlebih dahulu!',
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    alert('Silakan pilih metode pengiriman terlebih dahulu!');
                }
                return false;
            }

            if (metode.value === 'kurir') {
                const address = document.querySelector('input[name="address_id"]:checked');
                console.log('Selected address:', address?.value || 'NONE');

                if (!address) {
                    e.preventDefault();
                    console.warn('⚠ Validation failed: No address selected for kurir delivery');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alamat Pengiriman Belum Dipilih',
                            text: 'Silakan pilih alamat pengiriman terlebih dahulu!',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        alert('Silakan pilih alamat pengiriman terlebih dahulu!');
                    }
                    return false;
                }

                // Check if shipping cost has been calculated
                const shippingCostField = document.getElementById('shipping_cost');
                const shippingCostValue = shippingCostField ? parseInt(shippingCostField.value) : 0;

                console.log('Shipping cost in form:', shippingCostValue);

                if (shippingCostValue <= 0) {
                    e.preventDefault();
                    console.warn('⚠️  Validation failed: Biaya pengiriman belum terhitung (nilai: ' + shippingCostValue + ')');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Biaya Pengiriman Belum Terhitung',
                            html: '<p>Biaya pengiriman belum berhasil dihitung.</p>' +
                                  '<p class="small text-muted mt-2">💡 Coba: Pilih ulang alamat pengiriman atau periksa apakah data lokasi lengkap.</p>',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        alert('Mohon pilih ulang alamat pengiriman agar biaya pengiriman terhitung!');
                    }
                    return false;
                }

                console.log('✓ Kurir delivery validated - Address ID:', address.value, '- Shipping Cost: Rp ' + shippingCostValue);
            } else if (metode.value === 'ambil_sendiri') {
                const pickupLat = document.getElementById('pickup_latitude').value;
                const pickupLng = document.getElementById('pickup_longitude').value;

                console.log('Pickup coordinates:', {
                    latitude: pickupLat || 'NOT SET',
                    longitude: pickupLng || 'NOT SET'
                });

                if (!pickupLat || !pickupLng) {
                    console.warn('⚠ Warning: Pickup location not detected, will use default branch');
                } else {
                    console.log('✓ Pickup location validated');
                }
            }

            console.log('✓ Form validation passed, submitting...');

            // Show loading indicator
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Memproses Pesanan...',
                    html: 'Mohon tunggu, pesanan Anda sedang diproses',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            return true;
        });
    }
});
</script>
@endsection

{{-- Include Edit Address Modal --}}
@include('pelanggan.partials.edit-address-modal')

{{-- Load Leaflet.js for Interactive Maps --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

{{-- Load Custom Map Picker --}}
<script src="{{ asset('js/map-picker.js') }}"></script>

{{-- Load Checkout Address Management JS --}}
<script src="{{ asset('js/checkout-address.js') }}"></script>

<style>
.address-actions {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>

<script>
// Add edit and delete buttons to address cards
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Initializing address card actions ===');

    document.querySelectorAll('.address-card').forEach(card => {
        const addressId = card.dataset.addressId;

        // Create action buttons
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'address-actions btn-group btn-group-sm';
        actionsDiv.innerHTML = `
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="editAddress(${addressId})" title="Edit Alamat">
                <i class="bi bi-pencil"></i>
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteAddress(${addressId})" title="Hapus Alamat">
                <i class="bi bi-trash"></i>
            </button>
        `;

        // Add to card
        const cardBody = card.querySelector('.card-body');
        if (cardBody) {
            cardBody.style.position = 'relative';
            cardBody.appendChild(actionsDiv);
        }
    });
});
</script>

