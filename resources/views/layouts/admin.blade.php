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
        :root {
            --primary-color: #10b981;
            --secondary-color: #059669;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
        }

        /* Sidebar */
        #sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        #sidebar .sidebar-brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        #sidebar .sidebar-brand i {
            font-size: 1.6rem;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.35rem;
            transition: all 0.3s;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }

        #sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        #content {
            width: 100%;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        /* Top Navigation */
        .topbar {
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15);
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
        }

        .topbar .nav-link {
            color: var(--dark-color);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
        }

        .border-left-primary {
            border-left: 0.25rem solid var(--primary-color)!important;
        }

        .border-left-success {
            border-left: 0.25rem solid var(--success-color)!important;
        }

        .border-left-info {
            border-left: 0.25rem solid var(--info-color)!important;
        }

        .border-left-warning {
            border-left: 0.25rem solid var(--warning-color)!important;
        }

        .border-left-danger {
            border-left: 0.25rem solid var(--danger-color)!important;
        }

        /* Buttons */
        .btn {
            border-radius: 0.35rem;
        }

        /* Tables */
        .table {
            color: var(--dark-color);
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: var(--primary-color);
        }

        /* Badges */
        .badge {
            padding: 0.35rem 0.65rem;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                width: 100%;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #34d399;
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

        .role-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
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
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>

                <hr class="sidebar-divider my-2" style="border-color: rgba(255,255,255,0.1);">

                <div class="px-3 mb-1" style="color: rgba(255,255,255,0.5); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">
                    Manajemen
                </div>

                <a class="nav-link {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}"
                   href="{{ route('admin.discounts.index') }}">
                    <i class="fas fa-tags"></i> Diskon
                </a>

                <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}"
                   href="{{ route('admin.transactions.index') }}">
                    <i class="fas fa-shopping-cart"></i> Transaksi
                </a>

                <a class="nav-link {{ request()->routeIs('admin.cancellations.*') ? 'active' : '' }}"
                   href="{{ route('admin.cancellations.index') }}">
                    <i class="fas fa-ban"></i> Pembatalan Transaksi
                </a>

                <a class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}"
                   href="{{ route('admin.staff.index') }}">
                    <i class="fas fa-users"></i> Staff
                </a>

                <a class="nav-link {{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}"
                   href="{{ route('admin.memberships.index') }}">
                    <i class="fas fa-crown"></i> Membership
                </a>

                <hr class="sidebar-divider my-2" style="border-color: rgba(255,255,255,0.1);">

                <div class="px-3 mb-1" style="color: rgba(255,255,255,0.5); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">
                    Akun
                </div>

                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
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
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center">
                    <span class="me-3 d-none d-md-inline text-muted">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </span>

                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg"></i>
                            <span class="ms-2 d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Logout
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
