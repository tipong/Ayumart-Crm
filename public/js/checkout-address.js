// Fungsi Manajemen Alamat untuk Checkout

/**
 * Edit Alamat
 * Fungsi untuk menampilkan modal edit alamat dan memuat data alamat
 * @param {number} addressId - ID alamat yang akan diedit
 */
function editAddress(addressId) {
    // Tampilkan loading
    Swal.fire({
        title: 'Memuat Data...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Ambil detail alamat dari server
    fetch(`/api/address/${addressId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Gagal memuat data alamat');
        }
        return response.json();
    })
    .then(data => {
        Swal.close();

        if (data.success && data.address) {
            const address = data.address;

            // Isi form modal edit dengan data alamat
            document.getElementById('edit_address_id').value = address.id;
            document.getElementById('edit_label').value = address.label;
            document.getElementById('edit_nama_penerima').value = address.nama_penerima;
            document.getElementById('edit_no_telp_penerima').value = address.no_telp_penerima;
            document.getElementById('edit_kota').value = address.kota;
            document.getElementById('edit_kecamatan').value = address.kecamatan || '';
            document.getElementById('edit_kode_pos').value = address.kode_pos || '';
            document.getElementById('edit_alamat_lengkap').value = address.alamat_lengkap;
            document.getElementById('edit_latitude').value = address.latitude || '';
            document.getElementById('edit_longitude').value = address.longitude || '';
            document.getElementById('edit_is_default').checked = address.is_default == 1 || address.is_default === true;

            // Tampilkan modal
            const editModal = new bootstrap.Modal(document.getElementById('editAddressModal'));
            editModal.show();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: data.message || 'Gagal memuat data alamat',
                confirmButtonColor: '#3085d6'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: error.message || 'Terjadi kesalahan saat memuat data alamat',
            confirmButtonColor: '#3085d6'
        });
    });
}

/**
 * Submit Edit Alamat
 * Fungsi untuk menyimpan perubahan data alamat
 */
function submitEditAddress() {
    const addressId = document.getElementById('edit_address_id').value;
    const form = document.getElementById('editAddressForm');
    const formData = new FormData(form);
    const submitBtn = event?.target || document.querySelector('#editAddressForm button[type="submit"]');

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
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
    }

    // Show loading
    Swal.fire({
        title: 'Menyimpan Perubahan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Add _method and _token fields for Laravel
    formData.append('_method', 'PUT');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

    fetch(`/addresses/${addressId}`, {
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
                text: 'Alamat berhasil diperbarui',
                confirmButtonColor: '#3085d6',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                // Close modal
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editAddressModal'));
                if (editModal) {
                    editModal.hide();
                }
                // Reload page to reflect changes
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Gagal memperbarui alamat');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';

        if (error.message) {
            errorMsg = error.message;
        } else if (error.errors) {
            // Handle Laravel validation errors
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
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Perubahan';
        }
    });
}

/**
 * Delete Address
 */
function deleteAddress(addressId) {
    // Show confirmation dialog
    Swal.fire({
        title: 'Hapus Alamat?',
        text: 'Apakah Anda yakin ingin menghapus alamat ini? Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
        cancelButtonText: '<i class="bi bi-x-circle"></i> Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus Alamat...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Perform delete with _method field for Laravel
            const deleteData = new FormData();
            deleteData.append('_method', 'DELETE');
            deleteData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

            fetch(`/addresses/${addressId}`, {
                method: 'POST',
                body: deleteData,
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
                        title: 'Berhasil Dihapus!',
                        text: 'Alamat berhasil dihapus dari daftar Anda',
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Reload page to reflect changes
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Gagal menghapus alamat');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMsg = 'Terjadi kesalahan saat menghapus alamat';

                if (error.message) {
                    errorMsg = error.message;
                } else if (error.errors) {
                    const errors = Object.values(error.errors).flat();
                    errorMsg = errors.join('\n');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menghapus',
                    text: errorMsg,
                    confirmButtonColor: '#3085d6'
                });
            });
        }
    });
}

/**
 * Get GPS Location for Add Modal
 */
function getGPSLocationAdd() {
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
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        gpsStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Browser tidak mendukung GPS</span>';
    }
}

/**
 * Get GPS Location for Edit Modal
 */
function getGPSLocationEdit() {
    const gpsStatus = document.getElementById('editGpsStatus');

    if (navigator.geolocation) {
        gpsStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise"></i> Mengambil lokasi GPS...</span>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('edit_latitude').value = position.coords.latitude;
                document.getElementById('edit_longitude').value = position.coords.longitude;
                gpsStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Lokasi GPS berhasil diambil! (Lat: ' +
                    position.coords.latitude.toFixed(6) + ', Long: ' + position.coords.longitude.toFixed(6) + ')</span>';
            },
            function(error) {
                gpsStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Gagal mengambil lokasi GPS: ' + error.message + '</span>';
                console.error('GPS Error:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        gpsStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Browser tidak mendukung GPS</span>';
    }
}

/**
 * Pick Location from Maps (Add Modal)
 * Opens interactive map picker for selecting location
 */
function pickLocationFromMapsAdd() {
    // Use the new interactive map picker
    if (typeof initMapPicker === 'function') {
        initMapPicker('modal_latitude', 'modal_longitude', 'add');
    } else {
        console.error('Map picker not loaded. Please ensure map-picker.js is included.');
        Swal.fire({
            icon: 'error',
            title: 'Fitur Tidak Tersedia',
            text: 'Map picker belum dimuat. Silakan refresh halaman.',
            confirmButtonColor: '#3085d6'
        });
    }
}

/**
 * Pick Location from Maps (Edit Modal)
 * Opens interactive map picker for selecting location
 */
function pickLocationFromMapsEdit() {
    // Use the new interactive map picker
    if (typeof initMapPicker === 'function') {
        initMapPicker('edit_latitude', 'edit_longitude', 'edit');
    } else {
        console.error('Map picker not loaded. Please ensure map-picker.js is included.');
        Swal.fire({
            icon: 'error',
            title: 'Fitur Tidak Tersedia',
            text: 'Map picker belum dimuat. Silakan refresh halaman.',
            confirmButtonColor: '#3085d6'
        });
    }
}
