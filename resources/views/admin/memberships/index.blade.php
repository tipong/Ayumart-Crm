@extends('layouts.admin')

@section('title', 'Manajemen Membership')

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
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-crown text-success"></i> Manajemen Membership
        </h1>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMembershipModal">
            <i class="fas fa-plus-circle"></i> Tambah Membership Baru
        </button>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Bronze
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $tierCounts['bronze'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-medal fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Silver
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $tierCounts['silver'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-medal fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: gold;">
                                Gold
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $tierCounts['gold'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-medal fa-2x" style="color: gold;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Platinum
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $tierCounts['platinum'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-medal fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memberships Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Membership
            </h6>
        </div>
        <div class="card-body">
            @if($memberships->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                {{-- <th width="5%">ID</th> --}}
                                <th width="20%">Pelanggan</th>
                                <th width="12%">Tier</th>
                                <th width="10%">Points</th>
                                <th width="10%">Diskon</th>
                                <th width="15%">Masa Aktif</th>
                                <th width="10%">Status</th>
                                <th width="18%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberships as $membership)
                                <tr>
                                    {{-- <td><strong>#{{ $membership->id }}</strong></td> --}}
                                    <td>
                                        <i class="fas fa-user text-primary"></i>
                                        <strong>{{ $membership->user->pelanggan->nama_pelanggan ?? $membership->user->email ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $membership->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($membership->tier === 'bronze')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-medal"></i> Bronze
                                            </span>
                                        @elseif($membership->tier === 'silver')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-medal"></i> Silver
                                            </span>
                                        @elseif($membership->tier === 'gold')
                                            <span class="badge" style="background-color: gold; color: #333;">
                                                <i class="fas fa-medal"></i> Gold
                                            </span>
                                        @elseif($membership->tier === 'platinum')
                                            <span class="badge bg-info">
                                                <i class="fas fa-medal"></i> Platinum
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ number_format($membership->points) }} pts</span>
                                    </td>
                                    <td class="font-weight-bold text-success">
                                        {{ $membership->discount_percentage }}%
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-calendar"></i>
                                            {{ $membership->valid_from->format('d/m/Y') }} - <br>
                                            {{ $membership->valid_until->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($membership->isValid())
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                    class="btn btn-sm btn-warning"
                                                    onclick="editMembership({{ $membership->id }}, {{ $membership->user_id }}, '{{ $membership->tier }}', {{ $membership->points }}, {{ $membership->discount_percentage }}, '{{ $membership->valid_from->format('Y-m-d') }}', '{{ $membership->valid_until->format('Y-m-d') }}', {{ $membership->is_active ? 'true' : 'false' }})"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteMembership({{ $membership->id }}, '{{ $membership->user->pelanggan->nama_pelanggan ?? $membership->user->email ?? 'N/A' }}')"
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

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Menampilkan {{ $memberships->firstItem() ?? 0 }} - {{ $memberships->lastItem() ?? 0 }} dari {{ $memberships->total() }} membership
                    </div>
                    <div>
                        {{ $memberships->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Belum ada data membership.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Membership Modal -->
<div class="modal fade" id="addMembershipModal" tabindex="-1" aria-labelledby="addMembershipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addMembershipModalLabel">
                    <i class="fas fa-crown"></i> Tambah Membership Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.memberships.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="add_user_id" class="form-label">
                                <i class="fas fa-user"></i> Pelanggan <span class="text-danger">*</span>
                            </label>                                            <select class="form-select" id="add_user_id" name="user_id" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($customersWithoutMembership as $customer)
                                    <option value="{{ $customer->id_user }}">{{ $customer->pelanggan->nama_pelanggan ?? $customer->email }} - {{ $customer->email }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pelanggan yang belum memiliki membership</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_points" class="form-label">
                                <i class="fas fa-star"></i> Points Awal <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="add_points" name="points" value="0" min="0" required>
                            <small class="text-muted">Masukkan poin awal member (akan bertambah saat transaksi)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-medal"></i> Tier (Preview)
                            </label>
                            <input type="text" class="form-control" id="add_tier_display" value="Bronze" readonly>
                            <small class="text-muted">Tier akan otomatis disesuaikan berdasarkan poin</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-percent"></i> Diskon (Preview)
                            </label>
                            <input type="text" class="form-control" id="add_discount_display" value="5%" readonly>
                            <small class="text-muted">Diskon otomatis: Bronze=5%, Silver=10%, Gold=15%, Platinum=20%</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_valid_from" class="form-label">
                                <i class="fas fa-calendar-check"></i> Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="add_valid_from" name="valid_from" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_valid_until" class="form-label">
                                <i class="fas fa-calendar-times"></i> Berakhir <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="add_valid_until" name="valid_until" required>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info m-3">
                    <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Info Tier System</h6>
                    <ul class="mb-0 small">
                        <li><strong>Bronze:</strong> 0-100 points (Diskon 5%)</li>
                        <li><strong>Silver:</strong> 101-250 points (Diskon 10%)</li>
                        <li><strong>Gold:</strong> 251-400 points (Diskon 15%)</li>
                        <li><strong>Platinum:</strong> 401+ points (Diskon 20%)</li>
                    </ul>
                    <hr>
                    <small><i class="fas fa-calculator"></i> Poin dihitung otomatis: 1 poin = Rp 20.000 transaksi</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Membership
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Membership Modal -->
<div class="modal fade" id="editMembershipModal" tabindex="-1" aria-labelledby="editMembershipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editMembershipModalLabel">
                    <i class="fas fa-edit"></i> Edit Membership
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMembershipForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian:</strong>
                        Tier dan diskon akan otomatis diupdate berdasarkan poin yang dimasukkan.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_points" class="form-label">
                                <i class="fas fa-star"></i> Points <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="edit_points" name="points" min="0" required>
                            <small class="text-muted">Tier akan auto-update: 0-100=Bronze, 101-250=Silver, 251-400=Gold, 401+=Platinum</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-medal"></i> Tier Saat Ini
                            </label>
                            <input type="text" class="form-control" id="edit_tier_display" readonly>
                            <small class="text-muted">Tier otomatis berdasarkan poin</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-percent"></i> Diskon (Auto)
                            </label>
                            <input type="text" class="form-control" id="edit_discount_display" readonly>
                            <small class="text-muted">Diskon otomatis: Bronze=5%, Silver=10%, Gold=15%, Platinum=20%</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_is_active" class="form-label">
                                <i class="fas fa-toggle-on"></i> Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="edit_is_active" name="is_active" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_valid_from" class="form-label">
                                <i class="fas fa-calendar-check"></i> Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="edit_valid_from" name="valid_from" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_valid_until" class="form-label">
                                <i class="fas fa-calendar-times"></i> Berakhir <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="edit_valid_until" name="valid_until" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Membership
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteMembershipForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // Tier calculation helper
    function calculateTier(points) {
        if (points >= 401) return { name: 'Platinum', discount: 20, class: 'info' };
        if (points >= 251) return { name: 'Gold', discount: 15, class: 'warning', color: 'gold' };
        if (points >= 101) return { name: 'Silver', discount: 10, class: 'secondary' };
        return { name: 'Bronze', discount: 5, class: 'warning' };
    }

    // Make functions global
    window.calculateTier = calculateTier;

    window.editMembership = function(id, userId, tier, points, discount, validFrom, validUntil, isActive) {
        // Set form action
        document.getElementById('editMembershipForm').action = '/admin/memberships/' + id;

        // Fill form fields
        document.getElementById('edit_points').value = points;
        document.getElementById('edit_valid_from').value = validFrom;
        document.getElementById('edit_valid_until').value = validUntil;
        document.getElementById('edit_is_active').value = isActive ? '1' : '0';

        // Calculate and display tier
        var tierInfo = calculateTier(points);
        document.getElementById('edit_tier_display').value = tierInfo.name + ' (Current)';
        document.getElementById('edit_discount_display').value = tierInfo.discount + '%';

        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('editMembershipModal'));
        modal.show();
    };

    window.deleteMembership = function(id, pelangganName) {
        if (typeof Swal === 'undefined') {
            if (confirm('Anda akan menghapus membership untuk "' + pelangganName + '". Lanjutkan?')) {
                var form = document.getElementById('deleteMembershipForm');
                form.action = '/admin/memberships/' + id;
                form.submit();
            }
            return;
        }

        Swal.fire({
            title: 'Hapus Membership?',
            text: 'Anda akan menghapus membership untuk "' + pelangganName + '". Tindakan ini tidak dapat dibatalkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                var form = document.getElementById('deleteMembershipForm');
                form.action = '/admin/memberships/' + id;
                form.submit();
            }
        });
    };

    // Update tier display when points change in edit modal
    document.addEventListener('DOMContentLoaded', function() {
        // Edit modal - update tier preview
        var editPointsInput = document.getElementById('edit_points');
        if (editPointsInput) {
            editPointsInput.addEventListener('input', function() {
                var points = parseInt(this.value) || 0;
                var tier = calculateTier(points);

                document.getElementById('edit_tier_display').value = tier.name + ' (Auto)';
                document.getElementById('edit_discount_display').value = tier.discount + '% (Auto)';
            });
        }

        // Add modal - update tier preview when points change
        var addPointsInput = document.getElementById('add_points');
        if (addPointsInput) {
            addPointsInput.addEventListener('input', function() {
                var points = parseInt(this.value) || 0;
                var tier = calculateTier(points);

                document.getElementById('add_tier_display').value = tier.name;
                document.getElementById('add_discount_display').value = tier.discount + '%';
            });
        }

        // Set default dates (today and 1 year from now)
        var today = new Date().toISOString().split('T')[0];
        var oneYearLater = new Date();
        oneYearLater.setFullYear(oneYearLater.getFullYear() + 1);
        var oneYearLaterStr = oneYearLater.toISOString().split('T')[0];

        var addValidFrom = document.getElementById('add_valid_from');
        var addValidUntil = document.getElementById('add_valid_until');

        if (addValidFrom) addValidFrom.value = today;
        if (addValidUntil) addValidUntil.value = oneYearLaterStr;
    });
})();
</script>
@endpush
