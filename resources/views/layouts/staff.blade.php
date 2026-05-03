<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - AyuMart</title>

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
        // Map role_id to role name
        $roleId = auth()->user()->id_role ?? 2;
        $roleMap = [
            1 => 'owner',
            2 => 'admin',
            3 => 'cs',
            4 => 'kurir',
            5 => 'pelanggan'
        ];
        $role = $roleMap[$roleId] ?? 'admin';

        $roleColors = [
            'admin' => ['primary' => '#10b981', 'secondary' => '#059669', 'accent' => '#34d399'],
            'owner' => ['primary' => '#10b981', 'secondary' => '#059669', 'accent' => '#34d399'],
            'cs' => ['primary' => '#10b981', 'secondary' => '#059669', 'accent' => '#34d399'],
            'kurir' => ['primary' => '#10b981', 'secondary' => '#059669', 'accent' => '#34d399'],
            'staff' => ['primary' => '#10b981', 'secondary' => '#059669', 'accent' => '#34d399']
        ];
        $colors = $roleColors[$role] ?? $roleColors['admin'];

        $roleLabels = [
            'admin' => 'Administrator',
            'owner' => 'Owner',
            'cs' => 'Customer Service',
            'kurir' => 'Kurir',
            'staff' => 'Staff'
        ];
        $roleLabel = $roleLabels[$role] ?? 'Staff';
    @endphp

    <style>
        :root {
            --primary-color: {{ $colors['primary'] }};
            --secondary-color: {{ $colors['secondary'] }};
            --accent-color: {{ $colors['accent'] }};
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--light-color);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .sidebar-brand i {
            font-size: 1.6rem;
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

        .sidebar-menu {
            padding: 1rem 0;
            overflow-y: auto;
            max-height: calc(100vh - 200px);
        }

        .menu-section-title {
            padding: 1rem 1.25rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            letter-spacing: 0.5px;
        }

        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            margin: 0.15rem 0;
        }

        .sidebar-menu .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: rgba(255,255,255,0.5);
        }

        .sidebar-menu .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
            font-weight: 600;
        }

        .sidebar-menu .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-divider {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 0.75rem 1.25rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        #sidebarToggle {
            background: none;
            border: none;
            color: var(--dark-color);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.35rem;
            transition: all 0.3s;
        }

        #sidebarToggle:hover {
            background: var(--light-color);
        }

        .topbar-date {
            color: var(--dark-color);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 0.35rem;
            transition: all 0.3s;
        }

        .topbar-user:hover {
            background: var(--light-color);
        }

        .topbar-user .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .topbar-user .user-info {
            display: flex;
            flex-direction: column;
        }

        .topbar-user .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .topbar-user .user-role {
            font-size: 0.75rem;
            color: var(--secondary-color);
        }

        /* Content Container */
        .content-container {
            padding: 1.5rem;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-header h1 i {
            color: var(--primary-color);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Stat Cards */
        .stat-card {
            border-radius: 0.5rem;
            padding: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
            position: absolute;
            right: 1rem;
            bottom: 1rem;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .bg-primary-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        }

        .bg-success-gradient {
            background: linear-gradient(135deg, #1cc88a, #13855c);
        }

        .bg-info-gradient {
            background: linear-gradient(135deg, #36b9cc, #258391);
        }

        .bg-warning-gradient {
            background: linear-gradient(135deg, #f6c23e, #dda20a);
        }

        .bg-danger-gradient {
            background: linear-gradient(135deg, #e74a3b, #be2617);
        }

        .bg-secondary-gradient {
            background: linear-gradient(135deg, #858796, #54596e);
        }

        .bg-dark-gradient {
            background: linear-gradient(135deg, #2e3338, #1a1d21);
        }

        .bg-purple-gradient {
            background: linear-gradient(135deg, #9b59b6, #7d3c98);
        }

        /* Buttons */
        .btn {
            border-radius: 0.35rem;
            padding: 0.5rem 1rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
            white-space: nowrap;
        }

        .table tbody tr {
            transition: all 0.3s;
        }

        .table tbody tr:hover {
            background: rgba(0,0,0,0.02);
        }

        /* Badges */
        .badge {
            padding: 0.35rem 0.75rem;
            font-weight: 600;
            border-radius: 0.25rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -260px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .topbar-date,
            .user-info {
                display: none;
            }

            #sidebarToggle {
                display: block !important;
            }
        }

        @media (min-width: 769px) {
            #sidebarToggle {
                display: none;
            }
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
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
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

        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 0.5rem;
        }

        .dropdown-item {
            padding: 0.75rem 1.25rem;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background: var(--light-color);
            color: var(--primary-color);
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 0.5rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="@if($role === 'admin') {{ route('admin.dashboard') }} @elseif($role === 'owner') {{ route('owner.dashboard') }} @elseif($role === 'cs') {{ route('cs.dashboard') }} @elseif($role === 'kurir') {{ route('kurir.dashboard') }} @else {{ route('dashboard') }} @endif" class="sidebar-brand">
                <i class="bi bi-shop"></i>
                <span>AyuMart</span>
            </a>
            <div class="role-badge">{{ $roleLabel }}</div>
        </div>

        <nav class="sidebar-menu">
            @yield('sidebar-menu')
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <button id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="topbar-right">
                <div class="topbar-date">
                    <i class="bi bi-calendar-event"></i>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>

                <div class="dropdown">
                    <div class="topbar-user" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ $roleLabel }}</div>
                        </div>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-container">
            @yield('content')
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

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

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const toggle = document.getElementById('sidebarToggle');

                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'desc']]
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
        function confirmDelete(formId, itemName = 'item ini') {
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

        // Success notification
        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Error notification
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message
            });
        }

        // Check for session messages
        @if(session('success'))
            showSuccess('{{ session('success') }}');
        @endif

        @if(session('error'))
            showError('{{ session('error') }}');
        @endif
    </script>

    @stack('scripts')
</body>
</html>
