<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Owner Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Header */
        .top-header {
            background: linear-gradient(135deg, #FF8640 0%, #FF6B35 100%);
            color: white;
            padding: 12px 30px;
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
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 3px 12px rgba(0,0,0,0.2);
            padding: 12px;
            flex-shrink: 0;
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
            color: #FF8640;
            font-weight: bold;
        }
        
        /* Sidebar */
        .sidebar {
            background: white;
            min-height: calc(100vh - 76px);
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
            border-right: 3px solid #FFE8DC;
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
            color: #FF8640;
        }
        
        .sidebar-menu a.active {
            background: #FF8640;
            color: white;
        }
        
        .logout-btn {
            background: #FF8640;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            margin: 20px 15px;
            width: calc(100% - 30px);
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
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
            background: #FF8640;
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
            box-shadow: 0 5px 15px rgba(255, 134, 64, 0.3);
            color: white;
        }
        
        /* Stats Card */
        .stat-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card-1 { background: linear-gradient(135deg, #FF8640 0%, #FF6B35 100%); }
        .stat-card-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        
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
        
        .badge-custom {
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .pagination svg {
            width: 16px;
            height: 16px;
        }

        /* Card */
        .card {
            border: none;
            border-radius: 12px;
        }

        .card-header {
            background: #FFE8DC;
            border-bottom: 2px solid #FF8640;
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="{{ asset('storage/gambar/logo.png') }}" alt="Foodesia">
            </div>
            <div>
                <h5 class="mb-0" style="font-weight: 700;">FOODESIA - Owner Panel</h5>
                <small style="opacity: 0.9;">Sistem Laporan & Monitoring</small>
            </div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
            </div>
            <div>
                <div style="font-weight: 600;">{{ Auth::user()->nama }}</div>
                <small style="opacity: 0.9;">Owner</small>
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
                            <a href="{{ route('owner.dashboard') }}" class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('owner.reports.products') }}" class="{{ request()->routeIs('owner.reports.products') ? 'active' : '' }}">
                                <i class="bi bi-box-seam me-2"></i>Data Produk
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('owner.reports.index') }}" class="{{ request()->routeIs('owner.reports.index') ? 'active' : '' }}">
                                <i class="bi bi-graph-up me-2"></i>Laporan Transaksi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('owner.logs.index') }}" class="{{ request()->routeIs('owner.logs.*') ? 'active' : '' }}">
                                <i class="bi bi-clock-history me-2"></i>Log Aktivitas
                            </a>
                        </li>
                    </ul>
                    
                    <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-left me-2"></i>Logout
                    </button>
                    
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