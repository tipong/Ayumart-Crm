<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - AyuMart</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    @php
        // Get role from authenticated user
        $user = Auth::user();
        $role = $user->getRoleName(); // Use the method from User model

        // Map role to label
        $roleLabels = [
            'admin' => 'Administrator',
            'owner' => 'Owner',
            'cs' => 'Customer Service',
            'kurir' => 'Kurir',
            'pelanggan' => 'Pelanggan'
        ];
        $roleLabel = $roleLabels[$role] ?? ucfirst($role);
    @endphp

    <style>
        /* Premium Typography & Variable Tokens */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary-color: #10b981;
            --secondary-color: #059669;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #374151;
        }

        body {
            font-family: 'Inter', sans-serif !important;
            background-color: var(--light-color);
            color: var(--dark-color);
        }

        /* Sidebar Styling */
        #sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #115e59 0%, #134e4a 100%);
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            width: 250px;
            z-index: 1000;
        }

        #sidebar .sidebar-brand {
            font-size: 1.45rem;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            letter-spacing: -0.5px;
        }

        #sidebar .sidebar-brand i {
            font-size: 1.65rem;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            font-weight: 500;
        }

        #sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255,255,255,0.08);
            transform: translateX(4px);
        }

        #sidebar .nav-link.active {
            color: #ffffff;
            background-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            font-weight: 600;
        }

        #sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
            font-size: 1.05rem;
        }

        #content {
            width: 100%;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.75rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            text-align: center;
        }

        /* Topbar Header */
        .topbar {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.02);
        }

        .topbar .nav-link {
            color: var(--dark-color);
            font-weight: 500;
        }

        /* Custom Cards style */
        .card {
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            font-weight: 700;
            padding: 1.25rem;
            color: #1f2937;
        }

        /* Custom buttons style */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-success {
            background-color: #10b981;
            border-color: #10b981;
        }

        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
        }

        .btn-primary {
            background-color: #14b8a6;
            border-color: #14b8a6;
        }

        .btn-primary:hover {
            background-color: #0f766e;
            border-color: #0f766e;
        }

        /* Border highlights */
        .border-left-primary { border-left: 0.25rem solid var(--primary-color)!important; }
        .border-left-success { border-left: 0.25rem solid var(--success-color)!important; }
        .border-left-info { border-left: 0.25rem solid var(--info-color)!important; }
        .border-left-warning { border-left: 0.25rem solid var(--warning-color)!important; }
        .border-left-danger { border-left: 0.25rem solid var(--danger-color)!important; }

        /* Tables style */
        .table {
            color: var(--dark-color);
        }

        .table thead th {
            border-bottom: 2px solid #e5e7eb;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: #0f766e;
            letter-spacing: 0.5px;
            background: #f9fafb;
            padding: 0.75rem 1rem;
        }

        .table tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Custom inputs */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0.55rem 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        /* Badges */
        .badge {
            padding: 0.35rem 0.75rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .role-badge {
            display: inline-block;
            background: rgba(255,255,255,0.18);
            color: white;
            padding: 0.25rem 0.85rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #0d9488;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0f766e;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-overlay.active {
            display: flex;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar">
            <div class="sidebar-header">
                <a href="@if($role === 'admin') {{ route('admin.dashboard') }} @elseif($role === 'owner') {{ route('owner.dashboard') }} @elseif($role === 'cs') {{ route('cs.dashboard') }} @elseif($role === 'kurir') {{ route('kurir.dashboard') }} @else {{ route('dashboard') }} @endif" class="sidebar-brand">
                    <i class="bi bi-shop"></i>
                    <span>Ayu Mart</span>
                </a>
                <div class="role-badge">{{ $roleLabel }}</div>
            </div>

            <nav class="nav flex-column py-3">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>

                <hr class="sidebar-divider my-2" style="border-color: rgba(255,255,255,0.08);">

                <div class="px-4 mb-2" style="color: rgba(255,255,255,0.4); font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                    Manajemen
                </div>

                <a class="nav-link {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}"
                   href="{{ route('admin.discounts.index') }}">
                    <i class="bi bi-tags-fill"></i> Diskon Produk
                </a>

                <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}"
                   href="{{ route('admin.transactions.index') }}">
                    <i class="bi bi-cart-fill"></i> Transaksi
                </a>

                <a class="nav-link {{ request()->routeIs('admin.cancellations.*') ? 'active' : '' }}"
                   href="{{ route('admin.cancellations.index') }}">
                    <i class="bi bi-x-octagon-fill"></i> Pembatalan
                </a>

                <a class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}"
                   href="{{ route('admin.staff.index') }}">
                    <i class="bi bi-people-fill"></i> Data Staff
                </a>

                <a class="nav-link {{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}"
                   href="{{ route('admin.memberships.index') }}">
                    <i class="bi bi-award-fill"></i> Membership
                </a>

                <hr class="sidebar-divider my-2" style="border-color: rgba(255,255,255,0.08);">

                <div class="px-4 mb-2" style="color: rgba(255,255,255,0.4); font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                    Akun
                </div>

                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </nav>
        </div>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation Bar -->
            <nav class="topbar d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-light d-md-none" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center">
                    <span class="me-3 d-none d-md-inline text-muted small fw-semibold">
                        <i class="bi bi-calendar3 text-success me-1"></i>
                        {{ \Carbon\Carbon::now()->translatedFormat('dddd, D F Y') }}
                    </span>

                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 700; font-size: 0.9rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline font-medium text-dark">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li>
                                <a class="dropdown-item" href="#"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2 text-danger"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid px-4 pb-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Initialize DataTables (skip custom initialized tables like #transactionsTable)
        $(document).ready(function() {
            $('.datatable').not('#transactionsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                pageLength: 10,
                responsive: true
            });
        });

        // Show loading overlay
        function showLoading() {
            document.querySelector('.loading-overlay').classList.add('active');
        }

        // Hide loading overlay
        function hideLoading() {
            document.querySelector('.loading-overlay').classList.remove('active');
        }

        // Confirm delete
        function confirmDelete(formId, itemName) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus ${itemName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
