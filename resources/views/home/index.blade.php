<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Home - Supermarket CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary-color: #3F4F44;
            --secondary-color: #64748b;
            --success-color: #3F4F44;
            --danger-color: #ef4444;
            --green: #3F4F44;
            --light-green: #f6ffed;
            --dark-green: #556B58;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        /* Navbar Style */
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

        .btn-outline-light {
            border: 2px solid rgba(255,255,255,0.8);
            font-weight: 600;
        }

        .btn-outline-light:hover {
            background-color: white;
            color: #3F4F44;
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

        /* Branch Location Section */
        .branch-location-section {
            background: #fff3cd;
            border-bottom: 1px solid #ffd966;
            padding: 10px 0;
            margin-bottom: 0;
        }

        .branch-info-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .branch-current {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .branch-current i.bi-geo-alt-fill {
            font-size: 1.2rem;
        }

        .branch-selector {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #branchSelector {
            min-width: 200px;
            border: 1px solid #ffc107;
            background: white;
        }

        #detectLocationBtn {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .branch-info-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .branch-current {
                justify-content: center;
            }

            .branch-selector {
                justify-content: center;
            }

            #branchSelector {
                flex-grow: 1;
            }
        }

        /* Notification Styles */
        .notification-item {
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa !important;
        }

        .unread-notification {
            background-color: #fff5f3;
            border-left: 3px solid #3F4F44;
        }

        .unread-notification:hover {
            background-color: #ffe8e3 !important;
        }

        #notificationDropdownMenu {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        #notificationDropdownMenu .dropdown-header {
            background-color: #f8f9fa;
            padding: 12px 20px;
            font-size: 0.95rem;
        }

        #markAllReadBtn {
            color: #3F4F44;
            font-size: 0.8rem;
        }

        #markAllReadBtn:hover {
            color: #d73211;
        }

        /* Slideshow Section */
        .slideshow-section {
            background: white;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        .main-carousel {
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.12);
        }

        .carousel-item {
            height: 380px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .carousel-item img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }

        .carousel-caption {
            background: rgba(0,0,0,0.4);
            padding: 20px;
            border-radius: 8px;
            bottom: 40px;
        }

        .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* Promo Sidebar */
        .promo-sidebar {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .promo-card {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.12);
            transition: all 0.3s;
            height: 185px;
        }

        .promo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .promo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Categories Section - Shopee Style */

        /* Categories Section - Shopee Style */
        .categories-section {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .categories-title {
            font-size: 1rem;
            font-weight: 400;
            color: rgba(0,0,0,.54);
            text-transform: uppercase;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5f5f5;
        }

        .category-card {
            text-align: center;
            padding: 15px 10px;
            transition: all 0.2s;
            cursor: pointer;
            border-radius: 4px;
            background: transparent;
        }

        .category-card:hover {
            background: #fafafa;
            transform: translateY(-2px);
        }

        .category-card.active-category {
            background: #fff5f3;
            border: 2px solid #3F4F44;
        }

        .category-card.active-category i {
            color: #3F4F44;
        }

        .category-card.active-category h6 {
            color: #3F4F44;
            font-weight: 600;
        }

        .category-card i {
            font-size: 2.5rem;
            color: #3F4F44;
            margin-bottom: 8px;
            display: block;
        }

        .category-card h6 {
            font-size: 0.875rem;
            color: rgba(0,0,0,.87);
            margin: 0;
            font-weight: 400;
        }

        /* Flash Sale Section */
        .flash-sale-section {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .flash-sale-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5f5f5;
        }

        .flash-sale-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-sale-title h3 {
            color: #3F4F44;
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
        }

        .flash-sale-timer {
            display: flex;
            gap: 5px;
        }

        .timer-box {
            background: #000;
            color: white;
            padding: 5px 8px;
            border-radius: 2px;
            font-weight: 600;
            font-size: 0.875rem;
            min-width: 30px;
            text-align: center;
        }

        .timer-separator {
            color: #000;
            font-weight: 600;
        }

        .timer-separator {
            color: #000;
            font-weight: 600;
        }

        /* Products Section - Shopee Style */
        .products-section {
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 1rem;
            font-weight: 400;
            color: rgba(0,0,0,.54);
            text-transform: uppercase;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5f5f5;
            text-align: left;
        }

        .product-card {
            background: white;
            border-radius: 2px;
            overflow: hidden;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            height: 100%;
            border: 1px solid rgba(0,0,0,0.09);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            border-color: rgba(238,77,45,0.3);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image i {
            font-size: 3.5rem;
            color: #cbd5e1;
        }

        .product-body {
            padding: 10px;
        }

        .product-title {
            font-size: 0.875rem;
            color: rgba(0,0,0,.87);
            margin-bottom: 8px;
            min-height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            font-weight: 400;
            line-height: 1.2rem;
        }

        .product-price {
            font-size: 1rem;
            font-weight: 500;
            color: #3F4F44;
            margin-bottom: 5px;
        }

        .product-price del {
            font-size: 0.75rem;
            color: rgba(0,0,0,.26);
            margin-left: 5px;
            font-weight: 400;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 0.75rem;
            color: rgba(0,0,0,.54);
        }

        .product-sold {
            font-size: 0.75rem;
            color: rgba(0,0,0,.54);
        }

        .discount-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255,212,36,.9);
            color: #3F4F44;
            padding: 2px 4px;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 0 0 0 2px;
        }

        .stock-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 2px;
            font-size: 0.75rem;
            font-weight: 400;
        }

        .stock-available {
            background: rgba(16, 249, 155, 0.1);
            color: #00bf56;
        }

        .stock-low {
            background: rgba(255, 212, 36, 0.1);
            color: #ff9800;
        }

        .stock-out {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .btn-primary {
            background: #3F4F44;
            border: none;
            border-radius: 2px;
            padding: 6px 12px;
            font-weight: 400;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-primary:hover {
            background: #d73d1f;
            box-shadow: 0 1px 4px rgba(238, 77, 45, 0.4);
        }

        /* Footer Style */
        footer {
            background: white;
            color: rgba(0,0,0,.65);
            padding: 40px 0 20px;
            margin-top: 40px;
            border-top: 4px solid #3F4F44;
        }

        footer h5, footer h6 {
            color: rgba(0,0,0,.87);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        footer p, footer a {
            color: rgba(0,0,0,.65);
            font-size: 0.875rem;
        }

        footer a:hover {
            color: #3F4F44;
            text-decoration: none;
        }

        /* Branch Location Section */
        .branch-location-section {
            background: #fff3cd;
            border-bottom: 1px solid #ffd966;
            padding: 10px 0;
            margin-bottom: 0;
        }

        .branch-info-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .branch-current {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .branch-current i.bi-geo-alt-fill {
            font-size: 1.2rem;
        }

        .branch-selector {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #branchSelector {
            min-width: 200px;
            border: 1px solid #ffc107;
            background: white;
        }

        #detectLocationBtn {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .branch-info-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .branch-current {
                justify-content: center;
            }

            .branch-selector {
                justify-content: center;
            }

            #branchSelector {
                flex-grow: 1;
            }
        }

        /* Notification Styles */
        .notification-item {
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa !important;
        }

        .unread-notification {
            background-color: #fff5f3;
            border-left: 3px solid #3F4F44;
        }

        .unread-notification:hover {
            background-color: #ffe8e3 !important;
        }

        #notificationDropdownMenu {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        #notificationDropdownMenu .dropdown-header {
            background-color: #f8f9fa;
            padding: 12px 20px;
            font-size: 0.95rem;
        }

        #markAllReadBtn {
            color: #3F4F44;
            font-size: 0.8rem;
        }

        #markAllReadBtn:hover {
            color: #d73211;
        }

        /* Slideshow Section */
        .slideshow-section {
            background: white;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        .main-carousel {
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.12);
        }

        .carousel-item {
            height: 380px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .carousel-item img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }

        .carousel-caption {
            background: rgba(0,0,0,0.4);
            padding: 20px;
            border-radius: 8px;
            bottom: 40px;
        }

        .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* Promo Sidebar */
        .promo-sidebar {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .promo-card {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.12);
            transition: all 0.3s;
            height: 185px;
        }

        .promo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .promo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Categories Section - Shopee Style */

        /* Categories Section - Shopee Style */
        .categories-section {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .categories-title {
            font-size: 1rem;
            font-weight: 400;
            color: rgba(0,0,0,.54);
            text-transform: uppercase;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5f5f5;
        }

        .category-card {
            text-align: center;
            padding: 15px 10px;
            transition: all 0.2s;
            cursor: pointer;
            border-radius: 4px;
            background: transparent;
        }

        .category-card:hover {
            background: #fafafa;
            transform: translateY(-2px);
        }

        .category-card.active-category {
            background: #fff5f3;
            border: 2px solid #3F4F44;
        }

        .category-card.active-category i {
            color: #3F4F44;
        }

        .category-card.active-category h6 {
            color: #3F4F44;
            font-weight: 600;
        }

        .category-card i {
            font-size: 2.5rem;
            color: #3F4F44;
            margin-bottom: 8px;
            display: block;
        }

        .category-card h6 {
            font-size: 0.875rem;
            color: rgba(0,0,0,.87);
            margin: 0;
            font-weight: 400;
        }

        /* Flash Sale Section */
        .flash-sale-section {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .flash-sale-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5f5f5;
        }

        .flash-sale-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-sale-title h3 {
            color: #3F4F44;
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
        }

        .flash-sale-timer {
            display: flex;
            gap: 5px;
        }

        .timer-box {
            background: #000;
            color: white;
            padding: 5px 8px;
            border-radius: 2px;
            font-weight: 600;
            font-size: 0.875rem;
            min-width: 30px;
            text-align: center;
        }

        .timer-separator {
            color: #000;
            font-weight: 600;
        }

        .timer-separator {
            color: #000;
            font-weight: 600;
        }

        /* Products Section - Shopee Style */
        .products-section {
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 1rem;
            font-weight: 400;
            color: rgba(0,0,0,.54);
            text-transform: uppercase;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5f5f5;
            text-align: left;
        }

        .product-card {
            background: white;
            border-radius: 2px;
            overflow: hidden;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            height: 100%;
            border: 1px solid rgba(0,0,0,0.09);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            border-color: rgba(238,77,45,0.3);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image i {
            font-size: 3.5rem;
            color: #cbd5e1;
        }

        .product-body {
            padding: 10px;
        }

        .product-title {
            font-size: 0.875rem;
            color: rgba(0,0,0,.87);
            margin-bottom: 8px;
            min-height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            font-weight: 400;
            line-height: 1.2rem;
        }

        .product-price {
            font-size: 1rem;
            font-weight: 500;
            color: #3F4F44;
            margin-bottom: 5px;
        }

        .product-price del {
            font-size: 0.75rem;
            color: rgba(0,0,0,.26);
            margin-left: 5px;
            font-weight: 400;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 0.75rem;
            color: rgba(0,0,0,.54);
        }

        .product-sold {
            font-size: 0.75rem;
            color: rgba(0,0,0,.54);
        }

        .discount-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255,212,36,.9);
            color: #3F4F44;
            padding: 2px 4px;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 0 0 0 2px;
        }

        .stock-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 2px;
            font-size: 0.75rem;
            font-weight: 400;
        }

        .stock-available {
            background: rgba(16, 249, 155, 0.1);
            color: #00bf56;
        }

        .stock-low {
            background: rgba(255, 212, 36, 0.1);
            color: #ff9800;
        }

        .stock-out {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .btn-primary {
            background: #3F4F44;
            border: none;
            border-radius: 2px;
            padding: 6px 12px;
            font-weight: 400;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-primary:hover {
            background: #d73d1f;
            box-shadow: 0 1px 4px rgba(238, 77, 45, 0.4);
        }

        /* Footer Style */
        footer {
            background: white;
            color: rgba(0,0,0,.65);
            padding: 40px 0 20px;
            margin-top: 40px;
            border-top: 4px solid #3F4F44;
        }

        footer h5, footer h6 {
            color: rgba(0,0,0,.87);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        footer p, footer a {
            color: rgba(0,0,0,.65);
            font-size: 0.875rem;
        }

        footer a:hover {
            color: #3F4F44;
            text-decoration: none;
        }

        /* Branch Location Section */
        .branch-location-section {
            background: #fff3cd;
            border-bottom: 1px solid #ffd966;
            padding: 10px 0;
            margin-bottom: 0;
        }

        .branch-info-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .branch-current {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .branch-current i.bi-geo-alt-fill {
            font-size: 1.2rem;
        }

        .branch-selector {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #branchSelector {
            min-width: 200px;
            border: 1px solid #ffc107;
            background: white;
        }

        #detectLocationBtn {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .branch-info-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .branch-current {
                justify-content: center;
            }

            .branch-selector {
                justify-content: center;
            }

            #branchSelector {
                flex-grow: 1;
            }
        }
    </style>
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
                <div class="navbar-search mx-lg-3 my-2 my-lg-0">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari Barang disini..." id="searchInput">
                        <button class="btn" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Right Menu -->
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> Daftar
                            </a>
                        </li>
                    @else
                        <!-- Notifikasi -->
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link nav-icon dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" title="Notifikasi" id="notificationDropdown">
                                <i class="bi bi-bell"></i>
                                <span class="badge bg-danger" id="notification-count" style="display: none;">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 500px; overflow-y: auto;" id="notificationDropdownMenu">
                                <li class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span><strong>Notifikasi</strong></span>
                                    <button class="btn btn-sm btn-link text-decoration-none p-0" onclick="markAllAsRead()" id="markAllReadBtn">
                                        <small>Tandai Semua Dibaca</small>
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li id="notificationList">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mb-0 mt-2 small text-muted">Memuat notifikasi...</p>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="text-center">
                                    <a class="dropdown-item small" href="{{ route('pelanggan.orders') }}">
                                        <i class="bi bi-eye"></i> Lihat Semua Pesanan
                                    </a>
                                </li>
                            </ul>
                        </li> -->

                        <!-- Ticketing -->
                        <li class="nav-item">
                            <a class="nav-link nav-icon" href="{{ route('pelanggan.tickets.index') }}" title="Ticketing">
                                <i class="bi bi-headset"></i>
                                <span class="badge bg-info" id="ticket-count">0</span>
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
                            <a class="nav-link dropdown-toggle user-dropdown" href="#" role="button" data-bs-toggle="dropdown">
                                <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
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
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Branch Location Section -->
    <section class="branch-location-section">
        <div class="container">
            <div class="branch-info-bar">
                <div class="branch-current">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span id="branch-info">
                        @if(session('nearest_branch'))
                            <strong>{{ session('nearest_branch')['nama_cabang'] }}</strong>
                            <small class="text-muted ms-2">
                                <i class="bi bi-pin-map"></i>
                                {{ number_format(session('nearest_branch')['distance'], 1) }} km
                            </small>
                        @else
                            <strong>Pilih Cabang Terdekat</strong>
                        @endif
                    </span>
                </div>
                <div class="branch-selector">
                    <select id="branchSelector" class="form-select form-select-sm">
                        <option value="">Ganti Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id_cabang }}"
                                {{ session('nearest_branch') && session('nearest_branch')['id_cabang'] == $branch->id_cabang ? 'selected' : '' }}>
                                {{ $branch->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                    <button id="detectLocationBtn" class="btn btn-sm btn-outline-primary ms-2" title="Deteksi Lokasi Saya">
                        <i class="bi bi-crosshair"></i> Deteksi Lokasi
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Slideshow Section -->
    <section class="slideshow-section">
        <div class="container">
            <div class="row">
                <!-- Main Carousel -->
                <div class="col-lg-12 col-md-12 mb-3 mb-lg-0">
                    <div id="mainCarousel" class="carousel slide main-carousel" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 380px; display: flex; align-items: center; justify-content: center;">
                                    <div class="text-center text-white p-4">
                                        <h2 class="display-4 fw-bold mb-3">Selamat Datang!</h2>
                                        <p class="fs-5 mb-4">Belanja kebutuhan sehari-hari jadi lebih mudah</p>
                                        {{-- <a href="#products" class="btn btn-light btn-lg px-5">Belanja Sekarang</a> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">                            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); height: 380px; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-white p-4">
                                    <h2 class="display-4 fw-bold mb-3">Diskon Hingga 50%!</h2>
                                    <p class="fs-5 mb-4">Promo spesial untuk produk pilihan</p>
                                    <a href="#promo" class="btn btn-light btn-lg px-5">Lihat Promo</a>
                                </div>
                            </div>
                            </div>
                            <div class="carousel-item">
                                <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); height: 380px; display: flex; align-items: center; justify-content: center;">
                                    <div class="text-center text-white p-4">
                                        <h2 class="display-4 fw-bold mb-3">Member Baru</h2>
                                        <p class="fs-5 mb-4">Daftar & Dapat Voucher!</p>
                                        <a href="#products" class="btn btn-light btn-lg px-5">Belanja Yuk</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <!-- Promo Sidebar -->
                {{-- <div class="col-lg-4 col-md-4 d-none d-md-block">
                    <div class="promo-sidebar">
                        <div class="promo-card">
                            <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
                                <div class="text-center text-white">
                                    <h5 class="fw-bold">Member Baru</h5>
                                    <p class="mb-0">Daftar & Dapat Voucher!</p>
                                </div>
                            </div>
                        </div>
                        <div class="promo-card">
                            <div style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
                                <div class="text-center text-dark">
                                    <h5 class="fw-bold">Flash Sale</h5>
                                    <p class="mb-0">Setiap Hari Pukul 12.00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    @if($categories && count($categories) > 0)
    <section class="container mb-3">
        <div class="categories-section">
            <div class="categories-title">KATEGORI</div>
            <div class="row g-2">
                <!-- All Products Category -->
                <div class="col-lg-1 col-md-2 col-4">
                    <a href="{{ route('home') }}" style="text-decoration: none;">
                        <div class="category-card {{ !request('category') ? 'active-category' : '' }}">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            <h6>Semua</h6>
                        </div>
                    </a>
                </div>

                @foreach($categories as $category)
                <div class="col-lg-1 col-md-2 col-4">
                    <a href="{{ route('home', ['category' => $category->id_jenis]) }}" style="text-decoration: none;">
                        <div class="category-card {{ request('category') == $category->id_jenis ? 'active-category' : '' }}">
                            @php
                                // Map kategori ke icon yang sesuai
                                $categoryIcons = [
                                    'Makanan Pokok' => 'basket3-fill',
                                    'Minuman' => 'cup-straw',
                                    'Snack & Makanan Ringan' => 'cookie',
                                    'Susu & Produk Olahan' => 'droplet-fill',
                                    'Buah & Sayur' => 'apple',
                                    'Daging & Seafood' => 'egg-fried',
                                    'Bumbu & Penyedap' => 'egg',
                                    'Frozen Food' => 'snow',
                                    'Perawatan Pribadi' => 'heart-pulse',
                                    'Peralatan Rumah Tangga' => 'house-door',
                                    'Ibu & Bayi' => 'heart-fill',
                                    'Kesehatan' => 'shield-plus',
                                ];
                                $icon = $categoryIcons[$category->nama_jenis] ?? 'box-seam';
                            @endphp
                            <i class="bi bi-{{ $icon }}"></i>
                            <h6>{{ Str::limit($category->nama_jenis, 15) }}</h6>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Promo Section -->
    <section class="container mb-3" id="promo">
        <div class="flash-sale-section">
            <div class="flash-sale-header">
                <div class="flash-sale-title">
                    <i class="bi bi-percent text-danger fs-3"></i>
                    <h3>DISKON &amp; PROMO
                        @if(isset($categoryId) && $categoryId)
                            @php
                                $selectedCategory = $categories->firstWhere('id_jenis', $categoryId);
                            @endphp
                            <!-- @if($selectedCategory)
                                <span class="text-muted" style="font-size: 0.8rem; font-weight: 400; text-transform: none;">
                                    - {{ $selectedCategory->nama_jenis }}
                                </span>
                            @endif -->
                        @endif
                    </h3>
                </div>
                <div>
                    @if(isset($customerTier) && $customerTier)
                        @php
                            $tierIcons = ['bronze'=>'🥉','silver'=>'🥈','gold'=>'🥇','platinum'=>'💎'];
                            $tierColors = ['bronze'=>'warning','silver'=>'secondary','gold'=>'info','platinum'=>'danger'];
                        @endphp
                        <span class="badge bg-{{ $tierColors[$customerTier] ?? 'secondary' }} me-2">
                            {{ $tierIcons[$customerTier] ?? '' }} Tier {{ ucfirst($customerTier) }} – Diskon Eksklusif Anda
                        </span>
                    @endif
                </div>
            </div>

            @if(isset($promoProducts) && count($promoProducts) > 0)
            <div class="row g-2">
                @foreach($promoProducts as $product)
                @php
                    // Kalkulasi harga diskon untuk tampilan
                    $isTierDiscount = ($product->discount_target === 'tier');
                    $tier = $product->customer_tier ?? null;
                    $tierDiscountData = null;
                    $hargaSetelahDiskon = $product->harga_produk;
                    $pctDiskon = 0;
                    $badgeLabel = '';

                    if ($isTierDiscount && $tier) {
                        $tierDiscountData = \App\Models\ProductMemberDiscount::findByProductAndTier($product->id_produk, $tier);
                        if ($tierDiscountData) {
                            $pctDiskon = $tierDiscountData->discount_percentage;
                            $hargaSetelahDiskon = $product->harga_produk - ($product->harga_produk * ($pctDiskon / 100));
                            $badgeLabel = '-' . number_format($pctDiskon, 0) . '%';
                        }
                    } elseif (!$isTierDiscount && $product->hasActiveDiscount()) {
                        $hargaSetelahDiskon = $product->harga_diskon ?? $product->harga_produk;
                        $pctDiskon = $product->persentase_diskon;
                        $badgeLabel = '-' . number_format($pctDiskon, 0) . '%';
                    }
                    $adaDiskon = $pctDiskon > 0;
                @endphp
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="product-card position-relative">
                        @if($adaDiskon)
                            <span class="discount-badge">{{ $badgeLabel }}</span>
                        @endif

                        <a href="{{ route('product.show', $product->id_produk) }}" style="text-decoration: none; color: inherit;">
                            <div class="product-image">
                                @if($product->foto_produk)
                                    <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 200, 200) }}" alt="{{ $product->nama_produk }}">
                                @else
                                    <i class="bi bi-box-seam"></i>
                                @endif
                            </div>

                            <div class="product-body">
                                <h5 class="product-title">{{ $product->nama_produk }}</h5>

                                <div class="product-price">
                                    @if($adaDiskon)
                                        <span class="current-price">Rp {{ number_format($hargaSetelahDiskon, 0, ',', '.') }}</span>
                                        <del class="original-price">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</del>
                                    @else
                                        <span class="current-price">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</span>
                                    @endif
                                </div>

                                <div class="product-footer">
                                    @if($adaDiskon)
                                        <span class="stock-badge stock-low">
                                            <i class="bi bi-tag-fill"></i> Hemat {{ number_format($pctDiskon, 0) }}%
                                            @if($isTierDiscount && $tier)
                                                <small>({{ ucfirst($tier) }})</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="stock-badge stock-available">Promo</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4">
                <i class="bi bi-tag" style="font-size: 3rem; color: #cbd5e1;"></i>
                @if(isset($categoryId) && $categoryId)
                    @php
                        $selectedCategory = $categories->firstWhere('id_jenis', $categoryId);
                    @endphp
                    <p class="text-muted mt-2">Belum ada produk diskon untuk kategori <strong>{{ $selectedCategory->nama_jenis ?? 'ini' }}</strong></p>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="bi bi-arrow-clockwise"></i> Lihat Semua Diskon
                    </a>
                @else
                    <p class="text-muted mt-2">Belum ada produk diskon saat ini</p>
                @endif
            </div>
            @endif
        </div>
    </section>

    <!-- Products Section -->
    <section class="container mb-3" id="products">
        <div class="products-section">
            <div class="section-title">PRODUK UNTUK ANDA</div>

            <!-- Search Result Info -->
            <div id="searchResultInfo" class="alert alert-info" style="display: none;">
                <i class="bi bi-info-circle"></i>
                <span id="searchResultText"></span>
            </div>

            <!-- No Product Found Message -->
            <div id="noProductFound" class="text-center py-5" style="display: none;">
                <i class="bi bi-search" style="font-size: 4rem; color: #cbd5e1;"></i>
                <h4 class="mt-3 text-muted">Produk Tidak Ditemukan</h4>
                <p class="text-muted">Coba gunakan kata kunci lain atau lihat semua produk</p>
                <button class="btn btn-primary" onclick="clearSearch()">
                    <i class="bi bi-arrow-clockwise"></i> Lihat Semua Produk
                </button>
            </div>

            @if($products && count($products) > 0)
            <div class="row g-2" id="productContainer">
                @foreach($products as $product)
                @php
                    // Kalkulasi harga untuk setiap produk (general atau tier)
                    $isProdukTier = ($product->discount_target === 'tier');
                    $custTier = $product->customer_tier ?? null;
                    $prodHargaDiskon = $product->harga_produk;
                    $prodPctDiskon = 0;
                    $prodAdaDiskon = false;
                    $prodBadge = '';

                    if ($isProdukTier && $custTier) {
                        // Cari diskon spesifik untuk tier pelanggan ini
                        $td = \App\Models\ProductMemberDiscount::findByProductAndTier($product->id_produk, $custTier);
                        if ($td && $product->hasActiveDiscount()) {
                            $prodPctDiskon = $td->discount_percentage;
                            $prodHargaDiskon = $product->harga_produk - ($product->harga_produk * ($prodPctDiskon / 100));
                            $prodAdaDiskon = true;
                            $prodBadge = '-' . number_format($prodPctDiskon, 0) . '%';
                        }
                    } elseif (!$isProdukTier && $product->hasActiveDiscount()) {
                        $prodHargaDiskon = $product->harga_diskon ?? $product->harga_produk;
                        $prodPctDiskon = $product->persentase_diskon;
                        $prodAdaDiskon = true;
                        $prodBadge = '-' . number_format($prodPctDiskon, 0) . '%';
                    }
                @endphp
                <div class="col-lg-2 col-md-3 col-6 product-item">
                    <div class="product-card position-relative">
                        @if($prodAdaDiskon)
                            <span class="discount-badge">{{ $prodBadge }}</span>
                        @endif

                        <a href="{{ route('product.show', $product->id_produk) }}" style="text-decoration: none; color: inherit;">
                            <div class="product-image">
                                @if($product->foto_produk)
                                    <img src="{{ \App\Helpers\ImageHelper::getProductThumbnail($product->foto_produk, 200, 200) }}" alt="{{ $product->nama_produk }}">
                                @else
                                    <i class="bi bi-box-seam"></i>
                                @endif
                            </div>

                            <div class="product-body">
                                <h5 class="product-title">{{ $product->nama_produk }}</h5>

                                <div class="product-price">
                                    @if($prodAdaDiskon)
                                        <span class="current-price">Rp {{ number_format($prodHargaDiskon, 0, ',', '.') }}</span>
                                        <del class="original-price">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</del>
                                    @else
                                        <span class="current-price">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</span>
                                    @endif
                                </div>

                                <div class="product-footer">
                                    <div>
                                        @php
                                            $stok = $product->stok_cabang ?? 0;
                                        @endphp
                                        @if($stok > 10)
                                            <span class="stock-badge stock-available">Stok: {{ $stok }}</span>
                                        @elseif($stok > 0)
                                            <span class="stock-badge stock-low">Stok: {{ $stok }}</span>
                                        @else
                                            <span class="stock-badge stock-out">Habis</span>
                                        @endif
                                    </div>
                                    <div class="product-sold">
                                        <i class="bi bi-star-fill" style="color: #ffce3d;"></i> 4.8
                                    </div>
                                </div>

                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-primary w-100 mt-2">
                                        <i class="bi bi-cart-plus"></i> Beli
                                    </a>
                                @else
                                    @php
                                        $stok = $product->stok_cabang ?? 0;
                                    @endphp
                                    @if($stok > 0)
                                        <button class="btn btn-primary w-100 mt-2" onclick="addToCart({{ $product->id_produk }}, event)">
                                            <i class="bi bi-cart-plus"></i> Tambah
                                        </button>
                                    @else
                                        <button class="btn btn-secondary w-100 mt-2" disabled>
                                            Habis
                                        </button>
                                    @endif
                                @endguest
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #cbd5e1;"></i>
                <h4 class="mt-3 text-muted">Belum ada produk tersedia</h4>
            </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h6>LAYANAN PELANGGAN</h6>
                    <ul class="list-unstyled">
                        {{-- <li class="mb-2"><a href="#">Pusat Bantuan</a></li> --}}
                        <li class="mb-2"><a href="{{ route('pelanggan.tickets.index') }}">Pusat Bantuan</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>IKUTI KAMI</h6>
                    <div class="d-flex gap-3 mt-3">
                        <a href="https://www.facebook.com/3aayumart"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="https://www.instagram.com/3aayumart12/?igshid=YmMyMTA2M2Y%3D"><i class="bi bi-instagram fs-4"></i></a>
                    </div>
                    <div class="mt-3">
                        <p class="mb-1"><i class="bi bi-telephone"></i> +62 85 955 202 267</p>
                        <p><i class="bi bi-envelope"></i> tigaayumart@gmail.com</p>
                    </div>
                </div>
            </div>
            <hr style="border-color: rgba(0,0,0,.12);">
            <div class="text-center" style="color: rgba(0,0,0,.54);">
                <p class="mb-0">&copy; 2025 SuperMarket. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // SweetAlert2 untuk success messages
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

        // Search functionality with auto-scroll and not found message
        function performSearch() {
            const searchInput = document.getElementById('searchInput');

            if (!searchInput) {
                console.error('Search input not found!');
                return;
            }

            const searchTerm = searchInput.value.toLowerCase().trim();
            const productItems = document.querySelectorAll('.product-item');
            const productContainer = document.getElementById('productContainer');
            const noProductFound = document.getElementById('noProductFound');
            const searchResultInfo = document.getElementById('searchResultInfo');
            const searchResultText = document.getElementById('searchResultText');

            let visibleCount = 0;
            let totalCount = productItems.length;

            console.log('=== SEARCH DEBUG ===');
            console.log('Search term:', searchTerm);
            console.log('Total product items found:', totalCount);
            console.log('Elements check:', {
                searchInput: !!searchInput,
                productContainer: !!productContainer,
                noProductFound: !!noProductFound,
                searchResultInfo: !!searchResultInfo,
                searchResultText: !!searchResultText
            });

            // Filter products
            productItems.forEach((item, index) => {
                const titleElement = item.querySelector('.product-title');
                const title = titleElement?.textContent.toLowerCase().trim() || '';

                if (index === 0) {
                    console.log('First product title element:', titleElement);
                    console.log('First product title text:', title);
                }

                const isMatch = searchTerm === '' || title.includes(searchTerm);

                if (isMatch) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            console.log('Visible products after filter:', visibleCount);

            // Show/hide messages based on search results
            if (searchTerm !== '') {
                // Scroll to products section when searching
                setTimeout(() => {
                    const productsSection = document.getElementById('products');
                    if (productsSection) {
                        console.log('Scrolling to products section');
                        productsSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    } else {
                        console.error('Products section not found!');
                    }
                }, 100);

                if (visibleCount === 0) {
                    // No products found
                    console.log('No products found - showing message');
                    if (productContainer) productContainer.style.display = 'none';
                    if (noProductFound) noProductFound.style.display = 'block';
                    if (searchResultInfo) searchResultInfo.style.display = 'none';
                } else {
                    // Products found
                    console.log('Products found - showing results');
                    if (productContainer) productContainer.style.display = '';
                    if (noProductFound) noProductFound.style.display = 'none';
                    if (searchResultInfo) searchResultInfo.style.display = 'block';
                    if (searchResultText) {
                        searchResultText.textContent = `Menampilkan ${visibleCount} dari ${totalCount} produk untuk "${searchTerm}"`;
                    }
                }
            } else {
                // Reset to show all
                console.log('Empty search - showing all products');
                if (productContainer) productContainer.style.display = '';
                if (noProductFound) noProductFound.style.display = 'none';
                if (searchResultInfo) searchResultInfo.style.display = 'none';
            }
            console.log('=== END SEARCH DEBUG ===');
        }

        // Initialize search functionality when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - initializing search');

            const searchInput = document.getElementById('searchInput');
            const searchButton = document.querySelector('.navbar-search button');

            console.log('Search elements:', {
                searchInput: !!searchInput,
                searchButton: !!searchButton
            });

            // Event listener untuk input search (real-time)
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    console.log('Keyup event:', e.key, 'Value:', this.value);

                    // Jika Enter ditekan, lakukan pencarian dan scroll
                    if (e.key === 'Enter') {
                        console.log('Enter pressed - performing search');
                        performSearch();
                    } else {
                        // Untuk typing biasa, filter dengan debounce
                        clearTimeout(window.searchTimeout);
                        window.searchTimeout = setTimeout(() => {
                            console.log('Debounced search triggered');
                            performSearch();
                        }, 300);
                    }
                });
                console.log('Search input event listener attached');
            } else {
                console.error('Search input not found - event listener NOT attached');
            }

            // Event listener untuk tombol search
            if (searchButton) {
                searchButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Search button clicked');
                    performSearch();
                });
                console.log('Search button event listener attached');
            } else {
                console.error('Search button not found - event listener NOT attached');
            }
        });

        // Clear search function
        function clearSearch() {
            const searchInput = document.getElementById('searchInput');
            const productItems = document.querySelectorAll('.product-item');
            const productContainer = document.getElementById('productContainer');
            const noProductFound = document.getElementById('noProductFound');
            const searchResultInfo = document.getElementById('searchResultInfo');

            // Clear search input
            searchInput.value = '';

            // Show all products
            productItems.forEach(item => {
                item.style.display = '';
            });

            // Reset displays
            if (productContainer) productContainer.style.display = '';
            noProductFound.style.display = 'none';
            searchResultInfo.style.display = 'none';

            // Scroll back to top of products
            document.getElementById('products').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Add to cart function with SweetAlert
        function addToCart(productId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            Swal.fire({
                title: 'Tambah ke Keranjang?',
                text: "Produk akan ditambahkan ke keranjang belanja Anda",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3F4F44',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tambahkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to add to cart
                    fetch(`/cart/add/${productId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ quantity: 1 })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Ditambahkan!',
                            text: 'Produk berhasil ditambahkan ke keranjang',
                            confirmButtonColor: '#3F4F44',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menambahkan produk',
                            confirmButtonColor: '#3F4F44'
                        });
                    });
                }
            });
        }

        // Auto-play carousel
        const carousel = new bootstrap.Carousel(document.getElementById('mainCarousel'), {
            interval: 4000,
            ride: 'carousel'
        });

        // Load cart and wishlist count for logged-in users
        @auth
        function loadCartCount() {
            fetch('/api/cart/count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('cart-count');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else if (badge) {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error loading cart count:', error));
        }

        function loadWishlistCount() {
            fetch('/api/wishlist/count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('wishlist-count');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else if (badge) {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error loading wishlist count:', error));
        }

        function loadTicketCount() {
            fetch('/api/tickets/count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('ticket-count');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else if (badge) {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error loading ticket count:', error));
        }

        function loadNotificationCount() {
            fetch('/api/notifications/count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-count');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else if (badge) {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error loading notification count:', error));
        }

        function loadNotifications() {
            const notificationList = document.getElementById('notificationList');

            console.log('Loading notifications...');

            fetch('/api/notifications', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Notification response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Notification data received:', data);

                // Check if response is successful
                if (!data.success) {
                    throw new Error(data.message || 'Failed to load notifications');
                }

                // Check if we have notifications
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

        function markNotificationAsRead(notificationId) {
            fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
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

        function markAllAsRead() {
            fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotificationCount();
                    loadNotifications();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Semua notifikasi ditandai sebagai dibaca',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        }

        // Load notifications when dropdown is opened
        document.getElementById('notificationDropdown')?.addEventListener('click', function(e) {
            loadNotifications();
        });

        // Load counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCartCount();
            loadWishlistCount();
            loadTicketCount();
            loadNotificationCount();

            // Refresh counts every 30 seconds
            setInterval(function() {
                loadCartCount();
                loadWishlistCount();
                loadTicketCount();
                loadNotificationCount();
            }, 30000);
        });
        @endauth

        // Branch location detection and selection
        document.getElementById('detectLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Send AJAX request to get nearest branch
                    fetch('/api/set-user-location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ latitude: lat, longitude: lon })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.branch) {
                            // Show success message and reload
                            Swal.fire({
                                icon: 'success',
                                title: 'Lokasi Ditemukan',
                                text: `Cabang terdekat: ${data.branch.nama_cabang} (${data.branch.distance} km)`,
                                confirmButtonColor: '#3F4F44',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload page to show new stock data
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Informasi',
                                text: 'Cabang terdekat tidak ditemukan',
                                confirmButtonColor: '#3F4F44'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error detecting location:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mendeteksi lokasi',
                            confirmButtonColor: '#3F4F44'
                        });
                    });
                }, function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perijinan Diperlukan',
                        text: 'Izinkan akses lokasi untuk mendeteksi cabang terdekat',
                        confirmButtonColor: '#3F4F44'
                    });
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Browser Tidak Mendukung',
                    text: 'Browser Anda tidak mendukung fitur deteksi lokasi',
                    confirmButtonColor: '#3F4F44'
                });
            }
        });

        document.getElementById('branchSelector').addEventListener('change', function() {
            const branchId = this.value;

            if (branchId) {
                // Send AJAX request to switch branch
                fetch('/api/change-branch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ id_cabang: branchId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message and reload page
                        Swal.fire({
                            icon: 'success',
                            title: 'Cabang Berhasil Diubah',
                            text: `Anda sekarang memilih cabang: ${data.branch.nama_cabang}`,
                            confirmButtonColor: '#3F4F44',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload page to show new stock data
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengubah cabang',
                            confirmButtonColor: '#3F4F44'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error switching branch:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengubah cabang',
                        confirmButtonColor: '#3F4F44'
                    });
                });
            }
        });
    </script>
</body>
</html>
