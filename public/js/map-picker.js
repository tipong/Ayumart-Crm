/**
 * Interactive Map Picker for Address Selection
 * Uses Leaflet.js for map rendering and OpenStreetMap tiles
 */

let mapPicker = null;
let currentMarker = null;
let searchTimeout = null;
let lastGeocodedAddress = null;

// Dynamically inject custom premium map styling
if (!document.getElementById('premium-map-picker-styles')) {
    const styleEl = document.createElement('style');
    styleEl.id = 'premium-map-picker-styles';
    styleEl.innerHTML = `
        #mapPickerModal .modal-content {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
            border: none;
        }
        .floating-search-panel {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .floating-search-panel:focus-within {
            box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
            transform: translateY(-2px);
        }
        #searchResults {
            max-height: 250px;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        #searchResults .list-group-item {
            border-color: #f1f3f5;
            padding: 12px 16px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        #searchResults .list-group-item:hover {
            background-color: #f1f3f5;
            cursor: pointer;
            color: #0d6efd;
        }
        .custom-map-marker {
            display: flex;
            justify-content: center;
            align-items: center;
            filter: drop-shadow(0px 4px 8px rgba(0,0,0,0.3));
            animation: bounceMarker 0.5s ease-out;
        }
        @keyframes bounceMarker {
            0% { transform: translateY(-20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        .bg-light-primary {
            background-color: rgba(13, 110, 253, 0.08) !important;
        }
        @media (max-width: 768px) {
            .floating-search-panel {
                position: static !important;
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-bottom: 1px solid #e9ecef !important;
            }
            .floating-details-card {
                position: static !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-top: 1px solid #e9ecef !important;
            }
            #mapPickerModal .modal-body {
                height: auto !important;
                display: flex;
                flex-direction: column;
            }
            #mapPickerContainer {
                height: 380px !important;
                order: 2;
            }
            .floating-search-panel {
                order: 1;
            }
            .floating-details-card {
                order: 3;
            }
        }
    `;
    document.head.appendChild(styleEl);
}

/**
 * Initialize Map Picker Modal
 * @param {string} targetLatId - ID of latitude input field
 * @param {string} targetLngId - ID of longitude input field
 * @param {string} modalType - 'add' or 'edit'
 */
function initMapPicker(targetLatId, targetLngId, modalType = 'add') {
    // Get current coordinates or use default (Jakarta center)
    const currentLat = document.getElementById(targetLatId)?.value || '-6.200000';
    const currentLng = document.getElementById(targetLngId)?.value || '106.816666';

    // Build and show the map picker modal
    showMapPickerModal(parseFloat(currentLat), parseFloat(currentLng), targetLatId, targetLngId, modalType);
}

/**
 * Show Map Picker Modal with interactive map
 */
function showMapPickerModal(lat, lng, targetLatId, targetLngId, modalType) {
    // Create modal HTML
    const modalHtml = `
        <div class="modal fade" id="mapPickerModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 bg-dark text-white px-4 py-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;">
                        <h5 class="modal-title d-flex align-items-center gap-2" style="font-family: 'Outfit', 'Inter', sans-serif; font-weight: 600; letter-spacing: -0.5px;">
                            <i class="bi bi-pin-map-fill text-warning fs-4"></i>
                            <span>Tentukan Koordinat Pengiriman</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0 position-relative" style="height: 600px;">
                        
                        <!-- Leaflet Map Container -->
                        <div id="mapPickerContainer" style="height: 100%; width: 100%;"></div>

                        <!-- Floating Search Panel -->
                        <div class="floating-search-panel shadow" style="position: absolute; top: 15px; left: 15px; z-index: 1050; width: 380px; max-width: calc(100% - 30px); border-radius: 12px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.08); overflow: hidden;">
                            <div class="p-3">
                                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control border-0 ps-1"
                                           id="mapSearchInput"
                                           placeholder="Cari jalan, kelurahan, kota..."
                                           style="box-shadow: none; font-size: 0.9rem; height: 38px;">
                                    <button type="button" class="btn btn-primary" id="useMyLocationBtn" title="Gunakan Lokasi Saya" style="border-radius: 0;">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </button>
                                </div>
                                <div id="searchResults" class="mt-2 list-group list-group-flush" style="display: none;">
                                    <!-- Search results will appear here -->
                                </div>
                            </div>
                        </div>

                        <!-- Floating Location Details Card -->
                        <div class="floating-details-card shadow-lg" style="position: absolute; bottom: 15px; left: 15px; right: 15px; z-index: 1050; border-radius: 12px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.08);">
                            <div class="p-3">
                                <div class="row align-items-center g-3">
                                    <div class="col-lg-8">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="p-2 bg-light-primary rounded-3 text-primary mt-1">
                                                <i class="bi bi-geo-alt-fill fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-dark fw-bold" style="font-size: 0.95rem;">Titik Alamat Terpilih</h6>
                                                <p class="mb-1 text-secondary small" id="selectedAddress" style="line-height: 1.4; max-height: 4.2em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                                    Menentukan titik pin...
                                                </p>
                                                <div class="d-flex align-items-center gap-2 small text-muted">
                                                    <i class="bi bi-crosshair"></i>
                                                    <span id="selectedCoords">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-lg-end d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500; font-size: 0.9rem;">
                                            Batal
                                        </button>
                                        <button type="button" class="btn btn-success px-4" id="confirmLocationBtn" disabled style="border-radius: 8px; font-weight: 600; font-size: 0.9rem; background-color: #198754; border-color: #198754;">
                                            <i class="bi bi-check2-circle me-1"></i> Konfirmasi Lokasi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if any
    const existingModal = document.getElementById('mapPickerModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('mapPickerModal'));
    modal.show();

    // Initialize map after modal is shown
    document.getElementById('mapPickerModal').addEventListener('shown.bs.modal', function() {
        initializeMap(lat, lng, targetLatId, targetLngId);
    });

    // Cleanup map when modal is hidden
    document.getElementById('mapPickerModal').addEventListener('hidden.bs.modal', function() {
        if (mapPicker) {
            mapPicker.remove();
            mapPicker = null;
            currentMarker = null;
            lastGeocodedAddress = null;
        }
        this.remove();
    });
}

/**
 * Initialize Leaflet Map
 */
function initializeMap(lat, lng, targetLatId, targetLngId) {
    // Initialize map with zoomControl disabled so we can place it on bottomright
    mapPicker = L.map('mapPickerContainer', {
        zoomControl: false
    }).setView([lat, lng], 15);

    // Position Zoom control at bottomright to prevent overlap with floating panels
    L.control.zoom({
        position: 'bottomright'
    }).addTo(mapPicker);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(mapPicker);

    // Add initial marker
    const customIcon = L.divIcon({
        className: 'custom-map-marker',
        html: '<i class="bi bi-geo-alt-fill text-danger" style="font-size: 2.5rem; -webkit-text-stroke: 1px white;"></i>',
        iconSize: [40, 40],
        iconAnchor: [20, 40]
    });

    currentMarker = L.marker([lat, lng], {
        icon: customIcon,
        draggable: true
    }).addTo(mapPicker);

    // Update info on marker drag
    currentMarker.on('dragend', function(e) {
        const position = e.target.getLatLng();
        updateLocationInfo(position.lat, position.lng);
    });

    // Click on map to set marker
    mapPicker.on('click', function(e) {
        const { lat, lng } = e.latlng;

        // Move marker to clicked position
        currentMarker.setLatLng([lat, lng]);

        // Update info
        updateLocationInfo(lat, lng);
    });

    // Search functionality
    const searchInput = document.getElementById('mapSearchInput');
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 3) {
            document.getElementById('searchResults').style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            searchLocation(query);
        }, 500);
    });

    // Use my location button
    document.getElementById('useMyLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            const originalHtml = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            this.disabled = true;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    // Move map and marker to user location
                    mapPicker.setView([userLat, userLng], 17);
                    currentMarker.setLatLng([userLat, userLng]);
                    updateLocationInfo(userLat, userLng);

                    this.innerHTML = originalHtml;
                    this.disabled = false;
                },
                (error) => {
                    console.error('Geolocation error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mendeteksi Lokasi',
                        text: 'Pastikan Anda telah mengizinkan akses lokasi pada browser.',
                        confirmButtonColor: '#3085d6'
                    });

                    this.innerHTML = originalHtml;
                    this.disabled = false;
                }
            );
        }
    });

    // Confirm location button
    document.getElementById('confirmLocationBtn').addEventListener('click', function() {
        const position = currentMarker.getLatLng();

        // Get target fields
        const latInput = document.getElementById(targetLatId);
        const lngInput = document.getElementById(targetLngId);

        if (latInput && lngInput) {
            latInput.value = position.lat.toFixed(6);
            lngInput.value = position.lng.toFixed(6);

            // Trigger change events so observer scripts can react
            latInput.dispatchEvent(new Event('change', { bubbles: true }));
            lngInput.dispatchEvent(new Event('change', { bubbles: true }));

            // Autofill other address fields in the form if we have geocoded data
            if (lastGeocodedAddress) {
                const parentForm = latInput.closest('form');
                if (parentForm) {
                    const kotaField = parentForm.querySelector('input[name="kota"]');
                    const kecField = parentForm.querySelector('input[name="kecamatan"]');
                    const posField = parentForm.querySelector('input[name="kode_pos"]');
                    const alamatField = parentForm.querySelector('textarea[name="alamat_lengkap"]') || parentForm.querySelector('input[name="alamat_lengkap"]');

                    if (kotaField && lastGeocodedAddress.kota) {
                        kotaField.value = lastGeocodedAddress.kota;
                        kotaField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    if (kecField && lastGeocodedAddress.kecamatan) {
                        kecField.value = lastGeocodedAddress.kecamatan;
                        kecField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    if (posField && lastGeocodedAddress.kode_pos) {
                        posField.value = lastGeocodedAddress.kode_pos;
                        posField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    if (alamatField && lastGeocodedAddress.alamat_lengkap) {
                        alamatField.value = lastGeocodedAddress.alamat_lengkap;
                        alamatField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            }

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Lokasi Berhasil Disematkan!',
                text: 'Koordinat & detail alamat otomatis terisi.',
                timer: 2000,
                showConfirmButton: false
            });

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('mapPickerModal')).hide();
        }
    });

    // Initial location info update
    updateLocationInfo(lat, lng);
}

/**
 * Search location using Nominatim API
 */
function searchLocation(query) {
    const resultsDiv = document.getElementById('searchResults');
    resultsDiv.innerHTML = '<div class="p-3 text-center text-muted"><span class="spinner-border spinner-border-sm me-2 text-primary"></span>Mencari alamat...</div>';
    resultsDiv.style.display = 'block';

    // Use Nominatim API for geocoding
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                resultsDiv.innerHTML = `
                    <div class="p-3 text-center text-warning small">
                        <i class="bi bi-exclamation-triangle-fill"></i> Lokasi tidak ditemukan. Coba keyword lain.
                    </div>
                `;
                return;
            }

            // Display results
            let html = '<div class="list-group list-group-flush">';
            data.forEach((item, index) => {
                const displayNameClean = item.display_name.replace(/'/g, "\\'");
                html += `
                    <button type="button"
                            class="list-group-item list-group-item-action border-0"
                            onclick="selectSearchResult(${item.lat}, ${item.lon}, '${displayNameClean}')">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt-fill text-danger mt-1"></i>
                            <div>
                                <div class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                                    ${item.display_name.split(',').slice(0, 2).join(',')}
                                </div>
                                <div class="text-muted small text-truncate" style="max-width: 300px;">
                                    ${item.display_name}
                                </div>
                            </div>
                        </div>
                    </button>
                `;
            });
            html += '</div>';

            resultsDiv.innerHTML = html;
        })
        .catch(error => {
            console.error('Search error:', error);
            resultsDiv.innerHTML = `
                <div class="p-3 text-center text-danger small">
                    <i class="bi bi-x-circle-fill"></i> Terjadi kesalahan pencarian.
                </div>
            `;
        });
}

/**
 * Select a search result
 */
function selectSearchResult(lat, lng, displayName) {
    // Move map and marker to selected location
    mapPicker.setView([lat, lng], 17);
    currentMarker.setLatLng([lat, lng]);

    // Update location info
    updateLocationInfo(lat, lng, displayName);

    // Hide search results
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('mapSearchInput').value = displayName;
}

/**
 * Update location information display
 */
function updateLocationInfo(lat, lng, addressName = null) {
    const coordsEl = document.getElementById('selectedCoords');
    const addressEl = document.getElementById('selectedAddress');
    const confirmBtn = document.getElementById('confirmLocationBtn');

    // Update coordinates text
    coordsEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

    // Enable confirm button
    confirmBtn.disabled = false;

    // Show loading state
    addressEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2 text-primary"></span>Mencari detail alamat...';

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            if (data.display_name) {
                const finalAddr = addressName || data.display_name;
                addressEl.innerHTML = `<span class="text-success"><i class="bi bi-patch-check-fill me-1"></i></span>${finalAddr}`;
                
                // Parse structured address fields
                const addr = data.address || {};
                
                // Kota/Kabupaten
                let kota = addr.city || addr.town || addr.municipality || addr.city_district || addr.county || '';
                // Kecamatan/Distrik
                let kecamatan = addr.suburb || addr.village || addr.neighbourhood || addr.city_district || addr.state_district || '';
                
                // Clean up prefixes if necessary
                kota = kota.replace(/Kabupaten\s+/g, '').replace(/Kota\s+/g, '').trim();
                kecamatan = kecamatan.replace(/Kecamatan\s+/g, '').trim();

                lastGeocodedAddress = {
                    kota: kota,
                    kecamatan: kecamatan,
                    kode_pos: addr.postcode || '',
                    alamat_lengkap: data.display_name || ''
                };
            } else {
                addressEl.textContent = addressName || 'Lokasi terpilih (alamat tidak tersedia)';
                lastGeocodedAddress = null;
            }
        })
        .catch(error => {
            console.error('Reverse geocoding error:', error);
            addressEl.textContent = addressName || 'Lokasi terpilih (alamat tidak dapat diambil)';
            lastGeocodedAddress = null;
        });
}

/**
 * Format number with thousand separators
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
