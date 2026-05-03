@extends('layouts.admin')

@section('title', 'Manajemen Staff')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users text-primary"></i> Manajemen Akun Staff
        </h1>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="fas fa-plus-circle"></i> Tambah Staff Baru
        </button>
    </div>

    <!-- Staff Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Staff
            </h6>
        </div>
        <div class="card-body">
            @if($staff->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                {{-- <th width="5%">ID</th> --}}
                                <th width="18%">Nama</th>
                                <th width="18%">Email</th>
                                <th width="12%">Telepon</th>
                                <th width="12%">Role</th>
                                <th width="15%">Profil</th>
                                <th width="10%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $user)
                                <tr>
                                    {{-- <td><strong>#{{ $user->id }}</strong></td> --}}
                                    <td>
                                        <i class="fas fa-user text-primary"></i>
                                        <strong>{{ $user->name }}</strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-envelope text-info"></i>
                                        {{ $user->email }}
                                    </td>                    <td>
                        <i class="fas fa-phone text-success"></i>
                        {{ $user->phone ?? '-' }}
                    </td>
                                    <td>
                                        @if($user->id_role == 1)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-crown"></i> Owner
                                            </span>
                                        @elseif($user->id_role == 2)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-user-shield"></i> Admin
                                            </span>
                                        @elseif($user->id_role == 3)
                                            <span class="badge bg-info">
                                                <i class="fas fa-headset"></i> CS
                                            </span>
                                        @elseif($user->id_role == 4)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-truck"></i> Kurir
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->staff && $user->staff->profil_staff ? Str::limit($user->staff->profil_staff, 50) : '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-ban"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                    class="btn btn-sm btn-warning"
                                                    data-id="{{ $user->id_user }}"
                                                    data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-phone="{{ $user->phone }}"
                                                    data-role-id="{{ $user->id_role }}"
                                                    data-is-active="{{ $user->is_active ? '1' : '0' }}"
                                                    data-profil-staff="{{ $user->staff && $user->staff->profil_staff ? $user->staff->profil_staff : '' }}"
                                                    onclick="editStaffFromData(this)"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteStaff({{ $user->id_user }}, '{{ $user->name }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Belum ada data staff.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addStaffModalLabel">
                    <i class="fas fa-user-plus"></i> Tambah Staff Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.staff.store') }}" method="POST" id="addStaffForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_name" class="form-label">
                                <i class="fas fa-user"></i> Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="add_name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_role_id" class="form-label">
                                <i class="fas fa-user-tag"></i> Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role_id') is-invalid @enderror"
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
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_email" class="form-label">
                                <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="add_email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_phone" class="form-label">
                                <i class="fas fa-phone"></i> Telepon <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="add_phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_profil_staff" class="form-label">
                            <i class="fas fa-id-card"></i> Profil / Biodata Staff (Opsional)
                        </label>
                        <textarea class="form-control @error('profil_staff') is-invalid @enderror"
                                  id="add_profil_staff" name="profil_staff" rows="3"
                                  placeholder="Contoh: Lulusan S1 Manajemen, pengalaman 3 tahun di bidang customer service">{{ old('profil_staff') }}</textarea>
                        @error('profil_staff')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="add_password" class="form-label">
                            <i class="fas fa-lock"></i> Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="add_password" name="password" required minlength="8">
                        <div class="form-text">Minimal 8 karakter</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editStaffModalLabel">
                    <i class="fas fa-edit"></i> Edit Staff
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStaffForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">
                                <i class="fas fa-user"></i> Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="edit_name" name="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_role_id" class="form-label">
                                <i class="fas fa-user-tag"></i> Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role_id') is-invalid @enderror"
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
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">
                                <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="edit_email" name="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone" class="form-label">
                                <i class="fas fa-phone"></i> Telepon <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="edit_phone" name="phone" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_profil_staff" class="form-label">
                            <i class="fas fa-id-card"></i> Profil / Biodata Staff (Opsional)
                        </label>
                        <textarea class="form-control @error('profil_staff') is-invalid @enderror"
                                  id="edit_profil_staff" name="profil_staff" rows="3"
                                  placeholder="Contoh: Lulusan S1 Manajemen, pengalaman 3 tahun di bidang customer service"></textarea>
                        @error('profil_staff')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_is_active" class="form-label">
                                <i class="fas fa-toggle-on"></i> Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('is_active') is-invalid @enderror"
                                    id="edit_is_active" name="is_active" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_password" class="form-label">
                                <i class="fas fa-lock"></i> Password Baru
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="edit_password" name="password" minlength="8">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="fas fa-save"></i> Update Staff
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
        // Set form action using Laravel route helper
        document.getElementById('editStaffForm').action = "{{ url('admin/staff') }}/" + id;

        // Fill form fields
        document.getElementById('edit_name').value = name || '';
        document.getElementById('edit_email').value = email || '';
        document.getElementById('edit_phone').value = phone || '';
        document.getElementById('edit_role_id').value = roleId || '';
        document.getElementById('edit_is_active').value = isActive ? '1' : '0';
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_profil_staff').value = profilStaff || '';

        // Clear any previous validation errors
        document.querySelectorAll('#editStaffModal .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('#editStaffModal .invalid-feedback').forEach(el => {
            el.style.display = 'none';
        });

        // Show modal
        const editModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
        editModal.show();
    }

    function deleteStaff(id, name) {
        Swal.fire({
            title: 'Hapus Staff?',
            text: `Anda akan menghapus staff "${name}". Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
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

    // Auto-open modal if validation errors exist
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any() && old('_method') === 'PUT')
            // Re-open edit modal with old values
            const editModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
            editModal.show();

            // Populate with old values
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
            // Re-open add modal
            const addModal = new bootstrap.Modal(document.getElementById('addStaffModal'));
            addModal.show();
        @endif
    });

    // Show success/error messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#4e73df',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#4e73df'
        });
    @endif
</script>
@endpush
