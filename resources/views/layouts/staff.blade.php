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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif !important;
            background-color: var(--light-color);
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* Sidebar styling - matching admin gradient */
        .sidebar {
            min-height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #115e59 0%, #134e4a 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.75rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            text-align: center;
        }

        .sidebar-brand {
            font-size: 1.45rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            letter-spacing: -0.5px;
        }

        .sidebar-brand i {
            font-size: 1.65rem;
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

        .sidebar-menu {
            padding: 1rem 0;
            overflow-y: auto;
            max-height: calc(100vh - 200px);
        }

        .menu-section-title {
            padding: 1rem 1.75rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            letter-spacing: 0.5px;
        }

        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.75rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-menu .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255,255,255,0.08);
            transform: translateX(4px);
        }

        .sidebar-menu .nav-link.active {
            color: #ffffff;
            background-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            font-weight: 600;
        }

        .sidebar-menu .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.05rem;
        }

        .sidebar-divider {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 0.75rem 1.25rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(0,0,0,0.02);
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
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 500;
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
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
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
            color: #6b7280;
        }

        /* Content Container */
        .content-container {
            padding: 1.5rem 2rem;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            letter-spacing: -0.5px;
        }

        .page-header h1 i {
            color: var(--primary-color);
        }

        /* Premium Cards */
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

        .card-body {
            padding: 1.25rem;
        }

        /* Premium Stat Cards */
        .stat-card {
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200%;
            height: 200%;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.25;
            position: absolute;
            right: 1.25rem;
            bottom: 0.75rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
            opacity: 0.4;
        }

        .stat-card .stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            position: relative;
            z-index: 1;
        }

        .bg-primary-gradient {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .bg-success-gradient {
            background: linear-gradient(135deg, #10b981, #047857);
        }

        .bg-info-gradient {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
        }

        .bg-warning-gradient {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .bg-danger-gradient {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .bg-secondary-gradient {
            background: linear-gradient(135deg, #6b7280, #4b5563);
        }

        .bg-dark-gradient {
            background: linear-gradient(135deg, #374151, #1f2937);
        }

        .bg-purple-gradient {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background-color: #14b8a6;
            border-color: #14b8a6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0f766e;
            border-color: #0f766e;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.2);
        }

        .btn-success {
            background-color: #10b981;
            border-color: #10b981;
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        /* Form Controls style */
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
            white-space: nowrap;
        }

        .table tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Badges */
        .badge {
            padding: 0.35rem 0.75rem;
            font-weight: 600;
            border-radius: 6px;
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
                <span>Ayu Mart</span>
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
