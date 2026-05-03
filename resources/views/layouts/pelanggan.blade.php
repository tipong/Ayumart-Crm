<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AyuMart - Supermarket Online')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- Allow child views to add additional styles --}}
    @stack('styles')

    <style>
        :root {
            --primary-color: #3F4F44;
            --primary-hover: #d73211;
            --secondary-color: #3F4F44;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --text-dark: #333;
            --text-muted: #666;
            --border-color: #e5e5e5;
            --bg-light: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #3F4F44 0%, #2E3A31 100%);
            box-shadow: 0 2px 8px 0 rgba(82,196,26,.15);
            padding: 0.8rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: white !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .nav-link {
            color: rgba(255,255,255,0.95) !important;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 1rem !important;
        }

        .nav-link:hover {
            color: white !important;
            opacity: 0.8;
        }

        .navbar-toggler {
            border-color: rgba(255,255,255,0.5);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.95%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Search Bar in Navbar */
        .navbar-search {
            max-width: 500px;
            flex-grow: 1;
        }

        .navbar-search input {
            border: none;
            border-radius: 2px;
            padding: 8px 15px;
            background: rgba(255,255,255,0.9);
        }

        .navbar-search input:focus {
            background: white;
            outline: none;
            box-shadow: 0 0 4px rgba(0,0,0,0.15);
        }

        .navbar-search button {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 2px;
            transition: all 0.3s;
        }

        .navbar-search button:hover {
            background: rgba(255,255,255,0.3);
        }

        /* User Menu Icons */
        .nav-icon {
            position: relative;
            padding: 8px 12px;
            color: rgba(255,255,255,0.95) !important;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-icon:hover {
            color: white !important;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .nav-icon i {
            font-size: 1.3rem;
        }

        .nav-icon .badge {
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 0.65rem;
            padding: 2px 5px;
            min-width: 18px;
        }

        .user-dropdown {
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .user-dropdown:hover {
            background: rgba(255,255,255,0.2);
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: white;
            color: #3F4F44;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.75rem;
            margin-right: 8px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border-radius: 4px;
            margin-top: 8px;
        }

        .dropdown-item {
            padding: 10px 20px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: #fff5f3;
            color: #3F4F44;
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            min-height: calc(100vh - 200px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            transition: all 0.3s;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
            transform: translateY(-4px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 700;
            padding: 1.25rem 1.5rem;
            border: none;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-weight: 700;
            border-bottom: 2px solid var(--border-color);
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.3s;
        }

        .table tbody tr:hover {
            background-color: #fff5f2;
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(238, 77, 45, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(238, 77, 45, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(238, 77, 45, 0.3);
        }

        /* Footer */
        footer {
            background: white;
            color: var(--text-muted);
            padding: 2rem 0;
            margin-top: 3rem;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        }

        footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s;
        }

        footer a:hover {
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-search {
                max-width: 100%;
                margin: 10px 0;
            }

            .navbar-icons {
                gap: 1rem;
            }

            .nav-icon {
                font-size: 1.2rem;
                width: 35px;
                height: 35px;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .card {
                margin-bottom: 1.5rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-hover);
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        /* Notification Dropdown Styles */
        .notification-dropdown {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .notification-item {
            transition: all 0.2s;
            cursor: pointer;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .unread-notification {
            background-color: #fff5f3;
        }

        .notification-item:active {
            background-color: #e9ecef;
        }

        .dropdown-header {
            padding: 0.75rem 1rem;
            background-color: #f8f9fa;
            font-size: 0.9rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="AyuMart" height="36" class="d-inline-block align-text-top me-2">
                Ayu Mart
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar -->
                {{-- <div class="navbar-search mx-lg-3 my-2 my-lg-0">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari Barang disini..." id="searchInput">
                        <button class="btn" type="button" onclick="searchProducts()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div> --}}

                <!-- Right Menu -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>

                    <!-- Notifikasi -->
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-icon dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                            <i class="bi bi-bell"></i>
                            <span class="badge bg-danger" id="notification-count" style="display: none;">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span><strong>Notifikasi</strong></span>
                                <button class="btn btn-sm btn-link text-decoration-none" id="markAllReadBtn" style="font-size: 0.75rem;">Tandai Semua Dibaca</button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <div id="notificationList">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mb-0 mt-2 small">Memuat notifikasi...</p>
                                </div>
                            </div>
                        </ul>
                    </li>

                    <!-- Ticketing -->
                    <li class="nav-item">
                        <a class="nav-link nav-icon" href="{{ route('pelanggan.tickets.index') }}" title="Ticketing">
                            <i class="bi bi-headset"></i>
                            <span class="badge bg-danger" id="ticket-count">0</span>
                        </a>
                    </li>

                    <!-- Wishlist -->
                    <li class="nav-item">
                        <a class="nav-link nav-icon" href="{{ route('pelanggan.wishlist') }}" title="Wishlist">
                            <i class="bi bi-heart"></i>
                            <span class="badge bg-danger" id="wishlist-count">0</span>
                        </a>
                    </li>

                    <!-- Keranjang -->
                    <li class="nav-item">
                        <a class="nav-link nav-icon" href="{{ route('pelanggan.cart') }}" title="Keranjang">
                            <i class="bi bi-cart3"></i>
                            <span class="badge bg-danger" id="cart-count">0</span>
                        </a>
                    </li>

                    <!-- User Menu Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                           <li>
                                <a class="dropdown-item" href="{{ route('pelanggan.profile') }}">
                                    <i class="bi bi-person-circle"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('pelanggan.orders') }}">
                                    <i class="bi bi-bag-check"></i> Pesanan Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('pelanggan.reviews.index') }}">
                                    <i class="bi bi-star-fill text-warning"></i> Review Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Footer -->

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // SweetAlert2 for success messages
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3F4F44',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#3F4F44'
            });
        @endif

        // Search functionality
        function searchProducts() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value.trim()) {
                window.location.href = '{{ route("home") }}?search=' + encodeURIComponent(searchInput.value.trim());
            }
        }

        // Enter key to search
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        searchProducts();
                    }
                });
            }
        });

        // Load cart and wishlist counts
        function loadCounts() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.content;
            }

            fetch('/api/cart/count', { headers })
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('cart-count');
                    if (badge) {
                        badge.textContent = data.count || 0;
                        if (data.count > 0) {
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error loading cart count:', error));

            fetch('/api/wishlist/count', { headers })
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('wishlist-count');
                    if (badge) {
                        badge.textContent = data.count || 0;
                        if (data.count > 0) {
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error loading wishlist count:', error));

            fetch('/api/tickets/count', { headers })
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('ticket-count');
                    if (badge) {
                        badge.textContent = data.count || 0;
                        if (data.count > 0) {
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error loading ticket count:', error));
        }

        // Load counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCounts();
            loadNotificationCount();

            // Load notifications when dropdown is opened
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.addEventListener('click', function() {
                    loadNotifications();
                });
            }

            // Mark all as read handler
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    markAllNotificationsAsRead();
                });
            }

            // Auto-refresh notification count every 30 seconds
            setInterval(loadNotificationCount, 30000);
        });

        // Load notification count
        function loadNotificationCount() {
            fetch('/api/notifications/count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-count');
                if (badge && data.success) {
                    const count = data.count || 0;
                    badge.textContent = count;
                    if (count > 0) {
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error loading notification count:', error));
        }

        // Load notifications list
        function loadNotifications() {
            const notificationList = document.getElementById('notificationList');

            fetch('/api/notifications', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Failed to load notifications');
                }

                if (data.notifications && Array.isArray(data.notifications) && data.notifications.length > 0) {
                    notificationList.innerHTML = '';

                    data.notifications.forEach(notification => {
                        const item = document.createElement('a');
                        item.className = 'dropdown-item notification-item' + (notification.read ? '' : ' unread-notification');
                        item.href = notification.url;
                        item.style.cssText = 'white-space: normal; padding: 12px 20px; border-bottom: 1px solid #f0f0f0;';

                        item.innerHTML = `
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="bi ${notification.icon} text-${notification.color}" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong class="d-block mb-1" style="font-size: 0.9rem;">${notification.title}</strong>
                                    <p class="mb-1 small text-muted">${notification.message}</p>
                                    <small class="text-muted"><i class="bi bi-clock"></i> ${notification.time}</small>
                                </div>
                            </div>
                        `;

                        // Mark as read when clicked
                        item.addEventListener('click', function(e) {
                            markNotificationAsRead(notification.id);
                        });

                        notificationList.appendChild(item);
                    });
                } else {
                    notificationList.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-bell-slash" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                            <p class="text-muted mb-0 mt-2 small">Tidak ada notifikasi</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <p class="text-muted mb-0 small">Gagal memuat notifikasi</p>
                        <small class="text-muted d-block">${error.message}</small>
                    </div>
                `;
            });
        }

        // Mark single notification as read
        function markNotificationAsRead(notificationId) {
            fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotificationCount();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        // Mark all notifications as read
        function markAllNotificationsAsRead() {
            fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotificationCount();
                    loadNotifications();

                    // Show success message
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Semua notifikasi ditandai sudah dibaca'
                    });
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        }
    </script>

    @stack('scripts')
</body>
</html>
