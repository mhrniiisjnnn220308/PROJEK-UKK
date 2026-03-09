<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Rumah Makan Foodesia</title>
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
            overflow: hidden; /* Penting agar gambar tidak keluar dari lingkaran */
            padding: 8px; /* Beri sedikit padding */
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Agar logo tidak terdistorsi */
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
        
        /* Sidebar */
        .sidebar {
            background: white;
            min-height: calc(100vh - 80px);
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin: 5px 15px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .sidebar-menu a:hover {
            background: #FFF5F0;
            color: #FF8C42;
        }
        
        .sidebar-menu a.active {
            background: #FF8C42;
            color: white;
        }
        
        .logout-btn {
            background: #FF8C42;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            margin: 20px 15px;
            width: calc(100% - 30px);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: #FF6B35;
        }
        
        /* Main Content */
        .main-content {
            padding: 30px;
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
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .table {
            margin: 0;
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
        
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .badge-custom {
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .btn-action {
            width: 35px;
            height: 35px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin: 0 3px;
        }
        
        /* Stats Card */
        .stat-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
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
                <h5 class="mb-0" style="font-weight: 700;">FOODESIA - Admin Panel</h5>
                <small style="opacity: 0.9;">Sistem Manajemen Rumah Makan</small>
            </div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
            </div>
            <div>
                <div style="font-weight: 600;">{{ Auth::user()->nama }}</div>
                <small style="opacity: 0.9;">{{ ucfirst(Auth::user()->role) }}</small>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 px-0">
                <div class="sidebar">
                    <div style="padding: 0 15px 15px; color: #999; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                        Daftar Menu
                    </div>
                    
                    <ul class="sidebar-menu">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <i class="bi bi-box-seam me-2"></i>Kelola Produk
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="bi bi-grid me-2"></i>Daftar Kategori
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="bi bi-people me-2"></i>Kelola Users
                            </a>
                        </li>
                    </ul>
                    
                    <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-left me-2"></i>Logout
                    </button>
                    
                    <!-- Di layout, pastikan form logout menggunakan POST dengan CSRF -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10">
                <div class="main-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>