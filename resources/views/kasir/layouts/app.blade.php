<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir Panel') - Rumah Makan Foodesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Header */
        .top-header {
            background: linear-gradient(135deg, #FF8C42 0%, #FF6B35 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            overflow: hidden;
            padding: 8px;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 25px;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FF8C42;
            font-weight: bold;
        }
        
        /* Main Content - Full width untuk kasir */
        .main-content {
            padding: 30px;
            min-height: calc(100vh - 110px);
        }
        
        .page-header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        
        .page-title i {
            color: #FF8C42;
            margin-right: 10px;
        }
        
        /* Custom Buttons */
        .btn-primary-custom {
            background: #FF8C42;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary-custom:hover {
            background: #FF6B35;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 140, 66, 0.3);
            color: white;
        }
        
        .btn-outline-custom {
            border: 2px solid #FF8C42;
            color: #FF8C42;
            background: transparent;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-outline-custom:hover {
            background: #FF8C42;
            color: white;
        }
        
        /* Product Grid untuk Kasir */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 140, 66, 0.2);
            border-color: #FF8C42;
        }
        
        .product-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 1rem;
        }
        
        .product-price {
            color: #FF8C42;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        
        .product-stock {
            font-size: 0.85rem;
            color: #666;
        }
        
        .badge-custom {
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .badge-custom.bg-success {
            background: #d4edda !important;
            color: #155724;
        }
        
        .badge-custom.bg-danger {
            background: #f8d7da !important;
            color: #721c24;
        }
        
        .badge-custom.bg-info {
            background: #FFE8DC !important;
            color: #FF8C42;
        }
        
        /* Filter Buttons */
        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .filter-btn {
            padding: 8px 20px;
            border-radius: 25px;
            border: 2px solid #FF8C42;
            background: transparent;
            color: #FF8C42;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: #FF8C42;
            color: white;
        }
        
        /* Cart Container */
        .cart-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            position: sticky;
            top: 30px;
            border-top: 4px solid #FF8C42;
        }
        
        .cart-header {
            border-bottom: 2px solid #FFE8DC;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .cart-header h5 {
            color: #FF8C42;
            font-weight: 700;
            margin: 0;
        }
        
        .cart-item {
            background: #FFF9F5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 3px solid #FF8C42;
        }
        
        .cart-total {
            background: #FFE8DC;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .cart-total .total-price {
            color: #FF8C42;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        /* Form Controls */
        .form-control:focus {
            border-color: #FF8C42;
            box-shadow: 0 0 0 0.25rem rgba(255, 140, 66, 0.25);
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .table thead {
            background: #FFE8DC;
        }
        
        .table thead th {
            color: #333;
            font-weight: 600;
            border: none;
            padding: 15px;
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        
        /* Modal */
        .modal-header.bg-success {
            background: #FF8C42 !important;
        }
        
        /* Quantity Controls */
        .qty-control {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 5px 10px;
            border-radius: 25px;
            border: 1px solid #FFE8DC;
        }
        
        .qty-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: #FFE8DC;
            color: #FF8C42;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .qty-btn:hover {
            background: #FF8C42;
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .main-content {
                padding: 15px;
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
            background: #FF8C42;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #FF6B35;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="logo-section">
            <div class="logo-icon">
                 <img src="{{ asset('storage/gambar/logo.png') }}" alt="foodesia">
            </div>
            <div>
                <h5 class="mb-0" style="font-weight: 700;">FOODESIA - Kasir Panel</h5>
                <small style="opacity: 0.9;">Sistem Kasir Rumah Makan</small>
            </div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->nama ?? 'U', 0, 1)) }}
            </div>
            <div>
                <div style="font-weight: 600;">{{ Auth::user()->name ?? Auth::user()->nama ?? 'User' }}</div>
                <small style="opacity: 0.9;">{{ ucfirst(Auth::user()->role) }}</small>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (diubah menjadi horizontal menu untuk kasir) -->
            <div class="col-12 px-0">
                <div class="bg-white" style="border-bottom: 3px solid #FF8C42; padding: 0 30px;">
                    <div class="d-flex align-items-center" style="min-height: 60px;">
                        <div class="me-4" style="color: #999; font-size: 13px; font-weight: 600;">
                            <i class="bi bi-grid me-1"></i>MENU KASIR:
                        </div>
                        
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a href="{{ route('kasir.transactions.dashboard') }}" class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}" style="color: #FF8C42; {{ request()->routeIs('kasir.dashboard') ? 'background: #FF8C42; color: white;' : '' }}">
                                    <i class="bi bi-cart me-1"></i>Transaksi Baru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('kasir.transactions.index') }}" class="nav-link {{ request()->routeIs('kasir.transactions.history') ? 'active' : '' }}" style="color: #FF8C42; {{ request()->routeIs('kasir.transactions.history') ? 'background: #FF8C42; color: white;' : '' }}">
                                    <i class="bi bi-clock-history me-1"></i>Riwayat Transaksi
                                </a>
                            </li>
                            <li class="nav-item ms-4">
                                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #dc3545;">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content - Full width -->
            <div class="col-12">
                <div class="main-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;">
       
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>