/**
 * Interactive Map Picker for Address Selection
 * Uses Leaflet.js for map rendering and OpenStreetMap tiles
 */

let mapPicker = null;
let currentMarker = null;
let searchTimeout = null;

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
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-pin-map-fill"></i> Pilih Lokasi dari Peta
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <!-- Search Bar -->
                        <div class="p-3 bg-light border-bottom">
                            <div class="row g-2">
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control"
                                               id="mapSearchInput"
                                               placeholder="Cari alamat, nama tempat, atau landmark (contoh: Monas, Jakarta Pusat)">
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Ketik nama tempat atau alamat, lalu klik hasil pencarian
                                    </small>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-primary w-100" id="useMyLocationBtn">
                                        <i class="bi bi-geo-alt-fill"></i> Lokasi Saya
                                    </button>
                                </div>
                            </div>
                            <div id="searchResults" class="mt-2" style="display: none;">
                                <!-- Search results will appear here -->
                            </div>
                        </div>

                        <!-- Map Container -->
                        <div id="mapPickerContainer" style="height: 500px; width: 100%;"></div>

                        <!-- Selected Location Info -->
                        <div class="p-3 bg-light border-top">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1">
                                        <i class="bi bi-pin-map"></i> Lokasi Terpilih:
                                    </h6>
                                    <p class="mb-1 text-muted small" id="selectedAddress">
                                        Klik pada peta atau cari lokasi untuk memilih
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        <strong>Koordinat:</strong>
                                        <span id="selectedCoords">-</span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="button" class="btn btn-success" id="confirmLocationBtn" disabled>
                                        <i class="bi bi-check-circle"></i> Gunakan Lokasi Ini
                                    </button>
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
        }
        this.remove();
    });
}

/**
 * Initialize Leaflet Map
 */
function initializeMap(lat, lng, targetLatId, targetLngId) {
    // Initialize map
    mapPicker = L.map('mapPickerContainer').setView([lat, lng], 15);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(mapPicker);

    // Add initial marker
    const customIcon = L.divIcon({
        className: 'custom-map-marker',
        html: '<i class="bi bi-geo-alt-fill text-danger" style="font-size: 2.5rem;"></i>',
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
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mendeteksi...';
            this.disabled = true;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    // Move map and marker to user location
                    mapPicker.setView([userLat, userLng], 17);
                    currentMarker.setLatLng([userLat, userLng]);
                    updateLocationInfo(userLat, userLng);

                    this.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Lokasi Saya';
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

                    this.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Lokasi Saya';
                    this.disabled = false;
                }
            );
        }
    });

    // Confirm location button
    document.getElementById('confirmLocationBtn').addEventListener('click', function() {
        const position = currentMarker.getLatLng();

        // Set values to target inputs
        document.getElementById(targetLatId).value = position.lat.toFixed(6);
        document.getElementById(targetLngId).value = position.lng.toFixed(6);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Lokasi Berhasil Dipilih!',
            text: 'Koordinat telah disimpan ke form.',
            timer: 2000,
            showConfirmButton: false
        });

        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('mapPickerModal')).hide();
    });

    // Initial location info update
    updateLocationInfo(lat, lng);
}

/**
 * Search location using Nominatim API
 */
function searchLocation(query) {
    const resultsDiv = document.getElementById('searchResults');
    resultsDiv.innerHTML = '<div class="p-2 text-center"><span class="spinner-border spinner-border-sm me-2"></span>Mencari...</div>';
    resultsDiv.style.display = 'block';

    // Use Nominatim API for geocoding
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                resultsDiv.innerHTML = `
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Lokasi tidak ditemukan. Coba kata kunci lain.
                    </div>
                `;
                return;
            }

            // Display results
            let html = '<div class="list-group">';
            data.forEach((item, index) => {
                html += `
                    <button type="button"
                            class="list-group-item list-group-item-action"
                            onclick="selectSearchResult(${item.lat}, ${item.lon}, '${item.display_name.replace(/'/g, "\\'")}')">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="bi bi-pin-map"></i> ${item.display_name.split(',').slice(0, 2).join(',')}
                            </h6>
                        </div>
                        <small class="text-muted">${item.display_name}</small>
                    </button>
                `;
            });
            html += '</div>';

            resultsDiv.innerHTML = html;
        })
        .catch(error => {
            console.error('Search error:', error);
            resultsDiv.innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-x-circle"></i> Terjadi kesalahan saat mencari lokasi.
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

    // Update coordinates
    coordsEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

    // Enable confirm button
    confirmBtn.disabled = false;

    // If address name is provided, use it
    if (addressName) {
        addressEl.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${addressName}`;
        return;
    }

    // Otherwise, do reverse geocoding
    addressEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengambil informasi alamat...';

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            if (data.display_name) {
                addressEl.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${data.display_name}`;
            } else {
                addressEl.textContent = 'Lokasi terpilih (alamat tidak tersedia)';
            }
        })
        .catch(error => {
            console.error('Reverse geocoding error:', error);
            addressEl.textContent = 'Lokasi terpilih (alamat tidak dapat diambil)';
        });
}

/**
 * Format number with thousand separators
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
