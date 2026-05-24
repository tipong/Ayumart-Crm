@extends('layouts.admin')

@section('title', 'Manajemen Staff')

@push('styles')
<style>
    .custom-input {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 0.6rem 1rem;
        font-weight: 500;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }
    .custom-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        background-color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <!-- Page Heading -->
    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
        <div>
            <h1 class="fw-extrabold text-success-emphasis mb-1">
                <i class="bi bi-people-fill text-success"></i> Manajemen Akun Staff
            </h1>
            <p class="text-muted mb-0">Kelola informasi staff, hak akses role, serta status keaktifan akun staff.</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <button type="button" class="btn btn-success text-white fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Staff Baru
            </button>
        </div>
    </div>

    <!-- Staff Table Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 ps-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="m-0 fw-bold text-success-emphasis">
                <i class="bi bi-list-ul text-success me-1"></i> Daftar Staff Terdaftar
            </h5>
            
            <form action="{{ route('admin.staff.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama, email, telepon..." value="{{ request('search') }}">
                </div>
                
                <select name="role" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    @foreach($roles as $roleId => $roleName)
                        <option value="{{ $roleId }}" {{ request('role') == $roleId ? 'selected' : '' }}>
                            {{ $roleName }}
                        </option>
                    @endforeach
                </select>
                
                <select name="status" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                
                <button type="submit" class="btn btn-sm btn-success text-white">Filter</button>
                @if(request()->filled('search') || request()->filled('role') || request()->filled('status'))
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
                <span class="text-muted small ms-md-2">Total Staff: <strong>{{ $staff->count() }}</strong></span>
            </form>
        </div>
        <div class="card-body p-0">
            @if($staff->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th scope="col" class="ps-4 py-3" width="22%">Nama</th>
                                <th scope="col" class="py-3" width="22%">Email</th>
                                <th scope="col" class="py-3" width="15%">Telepon</th>
                                <th scope="col" class="py-3" width="12%">Role</th>
                                <th scope="col" class="py-3" width="15%">Profil / Keterangan</th>
                                <th scope="col" class="py-3" width="8%">Status</th>
                                <th scope="col" class="pe-4 py-3 text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2.5 me-3">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block">{{ $user->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark"><i class="bi bi-envelope text-muted me-1"></i> {{ $user->email }}</span>
                                    </td>
                                    <td>
                                        <span class="text-dark"><i class="bi bi-telephone text-muted me-1"></i> {{ $user->phone ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if($user->id_role == 1)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-2.5 py-1 fw-bold">
                                                <i class="bi bi-award-fill me-1"></i> Owner
                                            </span>
                                        @elseif($user->id_role == 2)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2.5 py-1 fw-bold">
                                                <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                            </span>
                                        @elseif($user->id_role == 3)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-2.5 py-1 fw-bold">
                                                <i class="bi bi-chat-left-dots-fill me-1"></i> CS
                                            </span>
                                        @elseif($user->id_role == 4)
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 px-2.5 py-1 fw-bold">
                                                <i class="bi bi-truck me-1"></i> Kurir
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted d-block text-wrap" style="max-width: 250px;">
                                            {{ $user->staff && $user->staff->profil_staff ? Str::limit($user->staff->profil_staff, 60) : '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2.5 py-1 fw-bold">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2.5 py-1 fw-bold">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-warning rounded-pill py-1 px-3"
                                                    data-id="{{ $user->id_user }}"
                                                    data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-phone="{{ $user->phone }}"
                                                    data-role-id="{{ $user->id_role }}"
                                                    data-is-active="{{ $user->is_active ? '1' : '0' }}"
                                                    data-profil-staff="{{ $user->staff && $user->staff->profil_staff ? $user->staff->profil_staff : '' }}"
                                                    onclick="editStaffFromData(this)"
                                                    title="Edit Staff">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger rounded-pill py-1 px-3"
                                                    onclick="deleteStaff({{ $user->id_user }}, '{{ $user->name }}')"
                                                    title="Hapus Staff">
                                                <i class="bi bi-trash"></i> Haps
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
                    Belum ada data staff terdaftar.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0 py-3 px-4">
                <h5 class="modal-title fw-bold" id="addStaffModalLabel">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Staff Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.staff.store') }}" method="POST" id="addStaffForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="add_name" class="form-label fw-bold text-secondary">
                                <i class="bi bi-person me-1 text-success"></i> Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control custom-input @error('name') is-invalid @enderror"
                                   id="add_name" name="name" value="{{ old('name') }}" placeholder="Nama staff" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="add_role_id" class="form-label fw-bold text-secondary">
                                <i class="bi bi-shield-check me-1 text-success"></i> Hak Akses Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select custom-input @error('role_id') is-invalid @enderror"
                                    id="add_role_id" name="role_id" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $roleId => $roleName)
                                    <option value="{{ $roleId }}" {{ old('role_id') == $roleId ? 'selected' : '' }}>
                                        {{ $roleName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="add_email" class="form-label fw-bold text-secondary">
                                <i class="bi bi-envelope me-1 text-success"></i> Email Login <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control custom-input @error('email') is-invalid @enderror"
                                   id="add_email" name="email" value="{{ old('email') }}" placeholder="name@domain.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="add_phone" class="form-label fw-bold text-secondary">
                                <i class="bi bi-telephone me-1 text-success"></i> Nomor Telepon <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control custom-input @error('phone') is-invalid @enderror"
                                   id="add_phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxx" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="add_profil_staff" class="form-label fw-bold text-secondary">
                                <i class="bi bi-card-text me-1 text-success"></i> Profil / Ringkasan Staff (Opsional)
                            </label>
                            <textarea class="form-control custom-input @error('profil_staff') is-invalid @enderror"
                                      id="add_profil_staff" name="profil_staff" rows="3"
                                      placeholder="Contoh: Pengalaman 2 tahun di administrasi toko ritel, jujur dan teliti">{{ old('profil_staff') }}</textarea>
                            @error('profil_staff')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="add_password" class="form-label fw-bold text-secondary">
                                <i class="bi bi-lock me-1 text-success"></i> Kata Sandi Akun <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control custom-input @error('password') is-invalid @enderror"
                                   id="add_password" name="password" placeholder="Kata sandi baru" required minlength="8">
                            <div class="form-text small text-muted">Panjang sandi minimal 8 karakter.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success text-white fw-bold rounded-pill px-4">
                        Simpan Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0 py-3 px-4">
                <h5 class="modal-title fw-bold" id="editStaffModalLabel">
                    <i class="bi bi-pencil-square me-1"></i> Edit Data Staff
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStaffForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label fw-bold text-secondary">
                                <i class="bi bi-person me-1 text-success"></i> Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control custom-input @error('name') is-invalid @enderror"
                                   id="edit_name" name="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_role_id" class="form-label fw-bold text-secondary">
                                <i class="bi bi-shield-check me-1 text-success"></i> Hak Akses Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select custom-input @error('role_id') is-invalid @enderror"
                                    id="edit_role_id" name="role_id" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $roleId => $roleName)
                                    <option value="{{ $roleId }}">{{ $roleName }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label fw-bold text-secondary">
                                <i class="bi bi-envelope me-1 text-success"></i> Email Login <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control custom-input @error('email') is-invalid @enderror"
                                   id="edit_email" name="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label fw-bold text-secondary">
                                <i class="bi bi-telephone me-1 text-success"></i> Nomor Telepon <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control custom-input @error('phone') is-invalid @enderror"
                                   id="edit_phone" name="phone" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="edit_profil_staff" class="form-label fw-bold text-secondary">
                                <i class="bi bi-card-text me-1 text-success"></i> Profil / Ringkasan Staff (Opsional)
                            </label>
                            <textarea class="form-control custom-input @error('profil_staff') is-invalid @enderror"
                                      id="edit_profil_staff" name="profil_staff" rows="3"
                                      placeholder="Contoh: Pengalaman 2 tahun di administrasi toko ritel, jujur dan teliti"></textarea>
                            @error('profil_staff')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_is_active" class="form-label fw-bold text-secondary">
                                <i class="bi bi-toggle-on me-1 text-success"></i> Status Akun <span class="text-danger">*</span>
                            </label>
                            <select class="form-select custom-input @error('is_active') is-invalid @enderror"
                                    id="edit_is_active" name="is_active" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label fw-bold text-secondary">
                                <i class="bi bi-lock me-1 text-success"></i> Kata Sandi Baru
                            </label>
                            <input type="password" class="form-control custom-input @error('password') is-invalid @enderror"
                                   id="edit_password" name="password" placeholder="Biarkan kosong jika tidak diubah" minlength="8">
                            <div class="form-text small text-muted">Kosongkan jika tidak ingin merubah sandi.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4">
                        Update Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteStaffForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function editStaffFromData(button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const email = button.getAttribute('data-email');
        const phone = button.getAttribute('data-phone');
        const roleId = button.getAttribute('data-role-id');
        const isActive = button.getAttribute('data-is-active') === '1';
        const profilStaff = button.getAttribute('data-profil-staff') || '';

        editStaff(id, name, email, phone, roleId, isActive, profilStaff);
    }

    function editStaff(id, name, email, phone, roleId, isActive, profilStaff = '') {
        document.getElementById('editStaffForm').action = "{{ url('admin/staff') }}/" + id;

        document.getElementById('edit_name').value = name || '';
        document.getElementById('edit_email').value = email || '';
        document.getElementById('edit_phone').value = phone || '';
        document.getElementById('edit_role_id').value = roleId || '';
        document.getElementById('edit_is_active').value = isActive ? '1' : '0';
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_profil_staff').value = profilStaff || '';

        document.querySelectorAll('#editStaffModal .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('#editStaffModal .invalid-feedback').forEach(el => {
            el.style.display = 'none';
        });

        const editModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
        editModal.show();
    }

    function deleteStaff(id, name) {
        Swal.fire({
            title: 'Hapus Staff?',
            text: `Anda akan menghapus staff "${name}". Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteStaffForm');
                form.action = "{{ url('admin/staff') }}/" + id;
                form.submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any() && old('_method') === 'PUT')
            const editModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
            editModal.show();

            @if(old('name'))
                document.getElementById('edit_name').value = "{{ old('name') }}";
            @endif
            @if(old('email'))
                document.getElementById('edit_email').value = "{{ old('email') }}";
            @endif
            @if(old('phone'))
                document.getElementById('edit_phone').value = "{{ old('phone') }}";
            @endif
            @if(old('role_id'))
                document.getElementById('edit_role_id').value = "{{ old('role_id') }}";
            @endif
            @if(old('is_active'))
                document.getElementById('edit_is_active').value = "{{ old('is_active') }}";
            @endif
            @if(old('profil_staff'))
                document.getElementById('edit_profil_staff').value = "{{ old('profil_staff') }}";
            @endif
        @elseif($errors->any())
            const addModal = new bootstrap.Modal(document.getElementById('addStaffModal'));
            addModal.show();
        @endif
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#10b981',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#10b981'
        });
    @endif
</script>
@endpush
