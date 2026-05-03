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
                            <label class="form-label">Lokasi (GPS/Maps)</label>
                            <div class="btn-group mb-2" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="getGPSLocationEdit()">
                                    <i class="bi bi-geo-alt-fill"></i> Gunakan GPS Saya
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="pickLocationFromMapsEdit()">
                                    <i class="bi bi-map"></i> Pilih dari Maps
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="edit_latitude" name="latitude" placeholder="Latitude (contoh: -6.200000)" readonly>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="edit_longitude" name="longitude" placeholder="Longitude (contoh: 106.816666)" readonly>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1" id="editGpsStatus"></small>
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
