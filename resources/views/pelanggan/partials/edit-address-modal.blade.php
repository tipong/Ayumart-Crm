<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAddressForm">
                    <input type="hidden" id="edit_address_id" name="address_id">
                    <input type="hidden" name="_method" value="PUT">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Label Alamat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_label" name="label" placeholder="Rumah, Kantor, dll" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_penerima" name="nama_penerima" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_no_telp_penerima" name="no_telp_penerima" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kota <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_kota" name="kota" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" class="form-control" id="edit_kecamatan" name="kecamatan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" id="edit_kode_pos" name="kode_pos">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_alamat_lengkap" name="alamat_lengkap" rows="3" required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label d-block fw-bold">Lokasi Peta (GPS/Maps) <span class="text-danger">*</span></label>
                            
                            <!-- Hidden inputs for coordinates -->
                            <input type="hidden" id="edit_latitude" name="latitude">
                            <input type="hidden" id="edit_longitude" name="longitude">

                            <!-- Visual status card inside modal -->
                            <div id="modalEditLocationStatusCard" class="card border-1 mb-2" style="border-radius: 10px; border: 1px dashed #ffc107; background-color: rgba(255, 193, 7, 0.05);">
                                <div class="card-body p-3 d-flex align-items-center gap-3">
                                    <div class="modal-status-icon bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <i class="bi bi-geo-alt fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold text-warning-emphasis modal-status-title" style="font-size: 0.9rem;">Titik Koordinat Belum Disematkan</h6>
                                        <p class="mb-0 text-muted small modal-status-desc">Silakan pilih lokasi dari peta atau gunakan GPS untuk menghitung ongkir kurir.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="btn-group d-flex gap-2" role="group" style="max-width: 450px;">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-3 py-2" onclick="getGPSLocationEdit()">
                                    <i class="bi bi-geo-alt-fill"></i> Deteksi GPS Saya
                                </button>
                                <button type="button" class="btn btn-success text-white btn-sm rounded-3 py-2" onclick="pickLocationFromMapsEdit()">
                                    <i class="bi bi-map-fill"></i> Pilih Titik di Maps
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2" id="editGpsStatus"></small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_default" name="is_default" value="1">
                                <label class="form-check-label" for="edit_is_default">
                                    Jadikan alamat utama
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitEditAddress()">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function updateModalEditLocationStatusCard() {
    const lat = document.getElementById('edit_latitude').value;
    const lng = document.getElementById('edit_longitude').value;
    const statusCard = document.getElementById('modalEditLocationStatusCard');
    
    if (statusCard) {
        if (lat && lng && parseFloat(lat) !== 0 && parseFloat(lng) !== 0) {
            statusCard.style.border = '1px solid #198754';
            statusCard.style.backgroundColor = 'rgba(25, 135, 84, 0.05)';
            statusCard.querySelector('.modal-status-icon').className = 'modal-status-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center';
            statusCard.querySelector('.modal-status-icon i').className = 'bi bi-check-circle-fill fs-5';
            statusCard.querySelector('.modal-status-title').className = 'mb-0 fw-bold text-success modal-status-title';
            statusCard.querySelector('.modal-status-title').textContent = 'Lokasi Tersemat!';
            statusCard.querySelector('.modal-status-desc').textContent = 'Titik peta telah berhasil disimpan (' + parseFloat(lat).toFixed(5) + ', ' + parseFloat(lng).toFixed(5) + ').';
        } else {
            statusCard.style.border = '1px dashed #ffc107';
            statusCard.style.backgroundColor = 'rgba(255, 193, 7, 0.05)';
            statusCard.querySelector('.modal-status-icon').className = 'modal-status-icon bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center';
            statusCard.querySelector('.modal-status-icon i').className = 'bi bi-geo-alt fs-5';
            statusCard.querySelector('.modal-status-title').className = 'mb-0 fw-bold text-warning-emphasis modal-status-title';
            statusCard.querySelector('.modal-status-title').textContent = 'Titik Koordinat Belum Disematkan';
            statusCard.querySelector('.modal-status-desc').textContent = 'Silakan pilih lokasi dari peta atau gunakan GPS untuk menghitung ongkir kurir.';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('edit_latitude');
    const lngInput = document.getElementById('edit_longitude');
    if (latInput && lngInput) {
        latInput.addEventListener('change', updateModalEditLocationStatusCard);
        lngInput.addEventListener('change', updateModalEditLocationStatusCard);
        
        // Also watch when Bootstrap Modal is shown, to check the initial coordinate values loaded by editAddress()
        const editModalEl = document.getElementById('editAddressModal');
        if (editModalEl) {
            editModalEl.addEventListener('shown.bs.modal', function() {
                // Add a slight delay to ensure editAddress() has finished setting the inputs
                setTimeout(updateModalEditLocationStatusCard, 150);
            });
        }
    }
});
</script>
