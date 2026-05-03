
@extends('layouts.pelanggan')
@section('title', 'Checkout')
@section('content')
<div class="container py-5">    
<div class="row">        
<div class="col-lg-8">            <!-- Shipping Method Card -->            
<div class="card shadow-sm mb-4">                
<div class="card-header bg-primary text-white">                    <h5 class="mb-0"><i class="bi bi-truck"></i> Metode Pengiriman</h5>                </div>
                
<div class="card-body">                    <form action="{{ route('order.place') }}" method="POST" id="checkoutForm">                        @csrf                        
<div class="mb-4">                            
<div class="row g-3">                                
<div class="col-md-6">                                    
<div class="card h-100 shipping-method-card" data-method="kurir">                                        
<div class="card-body text-center">                                            
<div class="form-check">                                                <input class="form-check-input" type="radio" name="metode_pengiriman"                                                        id="kurir" value="kurir" required>                                                <label class="form-check-label w-100" for="kurir">                                                    <i class="bi bi-truck fs-1 text-primary"></i>                                                    <h6 class="mt-2">Dikirim Kurir</h6>                                                    <p class="text-muted small mb-0">Produk akan dikirim ke alamat Anda</p>                                                    <span class="badge bg-info mt-2" id="shippingBadge">Pilih alamat untuk melihat biaya</span>                                                </label>                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
<div class="col-md-6">                                    
<div class="card h-100 shipping-method-card" data-method="ambil_sendiri">                                        
<div class="card-body text-center">                                            
<div class="form-check">                                                <input class="form-check-input" type="radio" name="metode_pengiriman"                                                        id="ambil_sendiri" value="ambil_sendiri">                                                <label class="form-check-label w-100" for="ambil_sendiri">                                                    <i class="bi bi-shop fs-1 text-success"></i>                                                    <h6 class="mt-2">Ambil Sendiri</h6>                                                    <p class="text-muted small mb-0">Ambil di toko terdekat</p>                                                    <span class="badge bg-success mt-2">Gratis</span>                                                </label>                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('metode_pengiriman')                                
<div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror                        </div>
                        <!-- Address Selection (shown when kurir is selected) -->                        
<div id="addressSection" style="display: none;">                            <h6 class="mb-3"><i class="bi bi-geo-alt"></i> Alamat Pengiriman</h6>                                                        
                                @if($addresses->count() > 0)                                
<div class="mb-3">                                    <label class="form-label">Pilih Alamat</label>                                    
                                    @foreach($addresses as $address)                                        
<div class="card mb-2 address-card" data-address-id="{{ $address->id }}">                                            
<div class="card-body">                                                
<div class="form-check">                                                    <input class="form-check-input" type="radio" name="address_id"                                                            id="address_{{ $address->id }}" value="{{ $address->id }}"                                                           {{ $address->is_default ? 'checked' : '' }}>                                                    <label class="form-check-label" for="address_{{ $address->id }}">                                                        
<div class="d-flex justify-content-between align-items-start">                                                            
<div>                                                                <strong>{{ $address->label }}</strong>                                                                
                                @if($address->is_default)                                                                    <span class="badge bg-success ms-2">Default</span>                                                                
                                @endif                                                                <p class="mb-1 mt-1">{{ $address->nama_penerima }} - {{ $address->no_telp_penerima }}</p>                                                                <p class="text-muted small mb-0">{{ $address->formatted_address }}</p>                                                            </div>
                                                        </div>
                                                    </label>                                                </div>
                                            </div>
                                        </div>
                                    
                                    @endforeach                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">                                    <i class="bi bi-plus-circle"></i> Tambah Alamat Baru                                </button>                            @else                                
<div class="alert alert-warning">                                    <i class="bi bi-exclamation-triangle"></i> Anda belum memiliki alamat pengiriman.                                    <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addAddressModal">                                        Tambah Alamat                                    </button>                                </div>
                            
                                @endif                            @error('address_id')                                
<div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror                        </div>
                        <!-- Pickup Info (shown when ambil_sendiri is selected) -->                        
<div id="pickupSection" style="display: none;">                            
<div class="alert alert-info mb-3">                                <h6><i class="bi bi-info-circle"></i> Informasi Pengambilan</h6>                                <p class="mb-2">Silakan gunakan lokasi Anda untuk menemukan cabang terdekat, atau pilih cabang manual:</p>                                <button type="button" class="btn btn-primary btn-sm" onclick="detectNearestBranch()">                                    <i class="bi bi-geo-alt-fill"></i> Deteksi Cabang Terdekat                                </button>                                <small class="text-muted d-block mt-2" id="branchDetectionStatus"></small>                            </div>
                            <input type="hidden" name="pickup_latitude" id="pickup_latitude">                            <input type="hidden" name="pickup_longitude" id="pickup_longitude">                            
<div id="branchInfoCard" style="display: none;">                                
<div class="card border-success">                                    
<div class="card-header bg-success text-white">                                        <h6 class="mb-0"><i class="bi bi-shop"></i> Cabang AyuMart Terdekat</h6>                                    </div>
                                    
<div class="card-body">                                        <h5 id="branchName" class="text-success mb-3"></h5>                                        <p class="mb-2"><strong><i class="bi bi-geo-alt"></i> Alamat:</strong></p>                                        <p id="branchAddress" class="text-muted mb-3"></p>                                        <p class="mb-1"><strong><i class="bi bi-telephone"></i> Telepon:</strong> <span id="branchPhone"></span></p>                                        <p class="mb-1"><strong><i class="bi bi-clock"></i> Jam Operasional:</strong> <span id="branchHours"></span></p>                                        <p class="mb-1"><strong><i class="bi bi-pin-map"></i> Jarak:</strong> <span id="branchDistance" class="text-primary"></span></p>                                        <a id="branchMapsLink" href="#" target="_blank" class="btn btn-outline-primary btn-sm mt-2">                                            <i class="bi bi-map"></i> Lihat di Google Maps                                        </a>                                        <p class="text-muted small mt-3 mb-0">                                            <i class="bi bi-info-circle"></i> Setelah pembayaran berhasil, pesanan akan dikemas dan dapat diambil di cabang ini.                                        </p>                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Order Notes -->                        
<div class="mb-3 mt-4">                            <label for="catatan" class="form-label">Catatan Pesanan (Opsional)</label>                            <textarea class="form-control" id="catatan" name="catatan" rows="3"                                       placeholder="Tambahkan catatan untuk pesanan Anda...">{{ old('catatan') }}</textarea>                        </div>
                        <!-- Order Items -->                        <h6 class="mb-3 mt-4"><i class="bi bi-basket"></i> Produk yang Dibeli</h6>                        
<div class="table-responsive">                            <table class="table">                                <thead>                                    <tr>                                        <th>Produk</th>                                        <th class="text-center">Jumlah</th>                                        <th class="text-end">Harga</th>                                        <th class="text-end">Subtotal</th>                                    </tr>                                </thead>                                <tbody>                                    
                                    @foreach($cartItems as $item)                                        
                                @if($item->product)                                        <tr>                                            <td>{{ $item->product->nama_produk }}</td>                                            <td class="text-center">{{ $item->jumlah_produk }}</td>                                            <td class="text-end">Rp {{ number_format($item->product->harga_produk, 0, ',', '.') }}</td>                                            <td class="text-end">Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}</td>                                        </tr>                                        
                                @endif                                    
                                    @endforeach                                </tbody>                            </table>                        </div>
                        
<div class="d-flex justify-content-between mt-4">                            <a href="{{ route('pelanggan.cart') }}" class="btn btn-outline-secondary">                                <i class="bi bi-arrow-left"></i> Kembali ke Keranjang                            </a>                            <button type="submit" class="btn btn-primary btn-lg">                                <i class="bi bi-credit-card"></i> Lanjut ke Pembayaran                            </button>                        </div>
                    </form>                </div>
            </div>
        </div>
        
<div class="col-lg-4">            <!-- Order Summary -->            
<div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">                
<div class="card-header bg-light">                    <h6 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pesanan</h6>                </div>
                
<div class="card-body">                    
<div class="d-flex justify-content-between mb-2">                        <span>Subtotal</span>                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>                    </div>


                    
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

                    
                                @if($isFirstTransaction && $membershipFee > 0)
                    
<div class="d-flex justify-content-between mb-2 text-info">
                        <span>
                            <i class="bi bi-award"></i> Biaya Pembuatan Member
                            <span class="badge bg-info">Baru</span>
                        </span>
                        <span>Rp {{ number_format($membershipFee, 0, ',', '.') }}</span>
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
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="getGPSLocation()">
                                <i class="bi bi-geo-alt-fill"></i> Gunakan Lokasi GPS Saya
                            </button>
                            <input type="hidden" name="latitude" id="modal_latitude">
                            <input type="hidden" name="longitude" id="modal_longitude">
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
const shippingMethods = document.querySelectorAll('input[name="metode_pengiriman"]');
const addressSection = document.getElementById('addressSection');
const pickupSection = document.getElementById('pickupSection');
const shippingCostRow = document.getElementById('shippingCostRow');
const shippingCostValue = document.getElementById('shippingCostValue');
const totalAmount = document.getElementById('totalAmount');
const pointsEarned = document.getElementById('pointsEarned');

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

shippingMethods.forEach(method => {
    method.addEventListener('change', function() {
        // Update card styling
        document.querySelectorAll('.shipping-method-card').forEach(card => {
            card.classList.remove('active');
        });
        this.closest('.shipping-method-card').classList.add('active');

        // Show/hide appropriate sections
        if (this.value === 'kurir') {
            addressSection.style.display = 'block';
            pickupSection.style.display = 'none';

            // Check if address is already selected
            const selectedAddress = document.querySelector('input[name="address_id"]:checked');
            if (selectedAddress) {
                const addressId = selectedAddress.value;
                detectNearestBranchForDelivery(addressId);
            } else {
                // Use default shipping cost if no address selected
                currentShippingCost = 15000;
                updateShippingCost(currentShippingCost);
            }
        } else if (this.value === 'ambil_sendiri') {
            addressSection.style.display = 'none';
            pickupSection.style.display = 'block';
            shippingCostRow.style.display = 'none';

            // Update total without shipping
            const newTotal = subtotal - discount + membershipFee;
            totalAmount.textContent = 'Rp ' + formatNumber(newTotal);

            // Update points if element exists
            if (pointsEarned) {
                const newPoints = Math.floor(newTotal / 20000);
                pointsEarned.textContent = newPoints;
            }
        }
    });
});

// Address card selection with shipping cost calculation
document.querySelectorAll('.address-card').forEach(card => {
    card.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;

        document.querySelectorAll('.address-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');

        // Get address coordinates and calculate shipping cost
        const addressId = this.dataset.addressId;
        detectNearestBranchForDelivery(addressId);
    });
});

// Detect nearest branch for delivery based on selected address
function detectNearestBranchForDelivery(addressId) {
    const shippingBadge = document.getElementById('shippingBadge');

    // Show loading state
    if (shippingBadge) {
        shippingBadge.innerHTML = '<i class="spinner-border spinner-border-sm"></i> Menghitung...';
        shippingBadge.className = 'badge bg-secondary mt-2';
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
            // Find nearest branch and get shipping cost
            fetch('/api/nearest-branch?latitude=' + data.address.latitude + '&longitude=' + data.address.longitude, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(branchData => {
                if (branchData.success && branchData.branch) {
                    const branch = branchData.branch;
                    currentShippingCost = branch.shipping_cost || 15000;

                    console.log('Nearest branch:', branch.nama_cabang,
                                '(' + branch.distance + ' km)',
                                'Ongkir: Rp ' + formatNumber(currentShippingCost));

                    // Update shipping badge
                    if (shippingBadge) {
                        shippingBadge.innerHTML = 'Biaya: Rp ' + formatNumber(currentShippingCost) +
                                                  ' <small>(' + branch.distance + ' km)</small>';
                        shippingBadge.className = 'badge bg-info mt-2';
                    }

                    // Update shipping cost in order summary
                    updateShippingCost(currentShippingCost);
                } else {
                    fallbackShippingCost();
                }
            })
            .catch(error => {
                console.error('Error fetching branch:', error);
                fallbackShippingCost();
            });
        } else {
            fallbackShippingCost();
        }
    })
    .catch(error => {
        console.error('Error fetching address:', error);
        fallbackShippingCost();
    });
}

function fallbackShippingCost() {
    const shippingBadge = document.getElementById('shippingBadge');
    currentShippingCost = 15000;
    if (shippingBadge) {
        shippingBadge.innerHTML = 'Biaya: Rp 15.000 (estimasi)';
        shippingBadge.className = 'badge bg-warning mt-2';
    }
    updateShippingCost(currentShippingCost);
}

function updateShippingCost(cost) {
    shippingCostRow.style.display = 'flex';
    shippingCostValue.textContent = 'Rp ' + formatNumber(cost);

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
    const submitBtn = event.target;

    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

    fetch('{{ route("pelanggan.addresses.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
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
            alert('Alamat berhasil ditambahkan!');
            location.reload();
        } else {
            alert('Gagal menambahkan alamat: ' + (data.message || 'Silakan coba lagi.'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Alamat';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
        if (error.message) {
            errorMsg = error.message;
        } else if (error.errors) {
            errorMsg = Object.values(error.errors).flat().join(', ');
        }
        alert(errorMsg);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Alamat';
    });
}

// Detect nearest branch for pickup
function detectNearestBranch() {
    const branchDetectionStatus = document.getElementById('branchDetectionStatus');
    const branchInfoCard = document.getElementById('branchInfoCard');

    if (navigator.geolocation) {
        branchDetectionStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise spinner-border spinner-border-sm"></i> Mendeteksi lokasi Anda...</span>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Save coordinates
                document.getElementById('pickup_latitude').value = latitude;
                document.getElementById('pickup_longitude').value = longitude;

                branchDetectionStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise spinner-border spinner-border-sm"></i> Mencari cabang terdekat...</span>';

                // Call API to get nearest branch
                fetch('/api/nearest-branch?latitude=' + latitude + '&longitude=' + longitude, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.branch) {
                        const branch = data.branch;

                        // Display branch info
                        document.getElementById('branchName').textContent = branch.nama_cabang;
                        document.getElementById('branchAddress').textContent = branch.formatted_address;
                        document.getElementById('branchPhone').textContent = branch.no_telepon || 'Tidak tersedia';
                        document.getElementById('branchHours').textContent = branch.jam_buka.substring(0, 5) + ' - ' + branch.jam_tutup.substring(0, 5) + ' WIB';
                        document.getElementById('branchDistance').textContent = branch.distance.toFixed(2) + ' km';
                        document.getElementById('branchMapsLink').href = branch.google_maps_url || '#';

                        branchInfoCard.style.display = 'block';
                        branchDetectionStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Cabang terdekat berhasil ditemukan!</span>';
                    } else {
                        branchDetectionStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Tidak dapat menemukan cabang terdekat</span>';
                        branchInfoCard.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching nearest branch:', error);
                    branchDetectionStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Gagal mengambil data cabang</span>';
                    branchInfoCard.style.display = 'none';
                });
            },
            function(error) {
                branchDetectionStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Gagal mengambil lokasi GPS: ' + error.message + '</span>';
                console.error('GPS Error:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        branchDetectionStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Browser tidak mendukung GPS</span>';
    }
}

// Format number helper
function formatNumber(num) {
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Form validation
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const metode = document.querySelector('input[name="metode_pengiriman"]:checked');

    if (!metode) {
        e.preventDefault();
        alert('Silakan pilih metode pengiriman terlebih dahulu!');
        return false;
    }

    if (metode.value === 'kurir') {
        const address = document.querySelector('input[name="address_id"]:checked');
        if (!address) {
            e.preventDefault();
            alert('Silakan pilih alamat pengiriman terlebih dahulu!');
            return false;
        }
    }
});
</script>

@endsection
