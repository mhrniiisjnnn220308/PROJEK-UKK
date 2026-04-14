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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        
        .top-header {
            background: linear-gradient(135deg, #FF8640 0%, #FF6B35 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
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
            color: #FF8640;
            font-weight: bold;
        }

        
        .app-wrapper {
            display: flex;
            margin-top: 110px;
            min-height: calc(100vh - 110px);
        }

       
        .sidebar {
            background: white;
            width: 260px;
            position: fixed;
            top: 110px;
            left: 0;
            bottom: 0;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-y: auto;
        }

        .sidebar-label {
            padding: 0 15px 15px;
            color: #999;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
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

        /* ── Logout selalu di bawah ── */
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
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #FF6B35;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px 25px;
            width: calc(100% - 260px);
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

        /* Stat Cards */
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

        .card {
            border: none;
            border-radius: 12px;
        }

        .card-header {
            background: #FFE8DC;
            border-bottom: 2px solid #FF8640;
            font-weight: 600;
        }

        /* ── Pagination ── */
        .pagination {
            margin: 0;
            gap: 4px;
        }
        .pagination .page-item .page-link {
            padding: 5px 10px;
            font-size: 13px;
            border-radius: 6px !important;
            border: 1px solid #dee2e6;
            color: #FF8640;
            line-height: 1.4;
            min-width: 32px;
            text-align: center;
        }
        .pagination .page-item.active .page-link {
            background-color: #FF8640;
            border-color: #FF8640;
            color: white;
        }
        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            pointer-events: none;
        }
        .pagination .page-item .page-link:hover:not(.active) {
            background-color: #FFF5F0;
            border-color: #FF8640;
            color: #FF8640;
        }
        .pagination .page-link:focus {
            box-shadow: none;
        }

        /* Scrollbar sidebar tipis */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb {
            background: #FFD0B8;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                top: 0;
                min-height: auto;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
                width: 100%;
            }
            .top-header { position: relative; }
            .app-wrapper {
                margin-top: 0;
                flex-direction: column;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- ── Top Header ── -->
    <div class="top-header">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="{{ asset('storage/gambar/logo.png') }}" alt="Foodesia">
            </div>
            <div>
                <h5 class="mb-0" style="font-weight: 700;">FOODESIA - Owner Panel</h5>
                <small style="opacity: 0.9;">Sistem Laporan &amp; Monitoring</small>
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

    <div class="app-wrapper">

        <!-- ── Sidebar ── -->
        <div class="sidebar">
            <div>
                <div class="sidebar-label">Daftar Menu</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('owner.dashboard') }}"
                           class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('owner.reports.products') }}"
                           class="{{ request()->routeIs('owner.reports.products') ? 'active' : '' }}">
                            <i class="bi bi-box-seam me-2"></i>Data Produk
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('owner.reports.index') }}"
                           class="{{ request()->routeIs('owner.reports.index') ? 'active' : '' }}">
                            <i class="bi bi-graph-up me-2"></i>Laporan Transaksi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('owner.logs.index') }}"
                           class="{{ request()->routeIs('owner.logs.*') ? 'active' : '' }}">
                            <i class="bi bi-clock-history me-2"></i>Log Aktivitas
                        </a>
                    </li>
                </ul>
            </div>

            
            <div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
                <button class="logout-btn" onclick="confirmLogout(event)">
                    <i class="bi bi-box-arrow-left me-2"></i>Logout
                </button>
            </div>
        </div>

       
        <div class="main-content">
            @yield('content')
        </div>

    </div>

    
    <div id="confirmModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;">
        <div id="confirmBox" style="background:white;border-radius:16px;padding:30px;min-width:320px;max-width:400px;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,0.15);transform:scale(0.9);transition:transform 0.2s;">
            <div id="confirmIcon" style="width:70px;height:70px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:32px;"></div>
            <div id="confirmTitle" style="font-size:18px;font-weight:700;color:#333;margin-bottom:8px;"></div>
            <div id="confirmDesc" style="font-size:14px;color:#666;margin-bottom:24px;"></div>
            <div style="display:flex;gap:12px;justify-content:center;">
                <button onclick="closeConfirm()" style="background:transparent;border:2px solid #FF8640;color:#FF8640;padding:8px 20px;border-radius:8px;font-weight:600;cursor:pointer;">Tidak</button>
                <button id="confirmYesBtn" style="background:#FF8640;color:white;border:none;padding:8px 20px;border-radius:8px;font-weight:600;cursor:pointer;">Ya</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        
        let confirmCallback = null;

        function showConfirm({ icon, iconType, title, desc, btnType, btnLabel, onYes }) {
            const colors = { success: '#28a745', warning: '#FF8640', danger: '#dc3545' };
            const ci = document.getElementById('confirmIcon');

            ci.style.background = colors[iconType] + '22';
            ci.style.color = colors[iconType];
            ci.innerHTML = icon;
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmDesc').textContent = desc;

            const btn = document.getElementById('confirmYesBtn');
            btn.style.background = colors[btnType] || colors['warning'];
            btn.textContent = btnLabel;
            confirmCallback = onYes;

            const overlay = document.getElementById('confirmModal');
            overlay.style.display = 'flex';

            setTimeout(() => {
                overlay.style.opacity = '1';
                document.getElementById('confirmBox').style.transform = 'scale(1)';
            }, 10);
        }

        function closeConfirm() {
            const overlay = document.getElementById('confirmModal');
            overlay.style.opacity = '0';
            document.getElementById('confirmBox').style.transform = 'scale(0.9)';
            setTimeout(() => overlay.style.display = 'none', 200);
            confirmCallback = null;
        }

        document.getElementById('confirmYesBtn').addEventListener('click', () => {
            if (confirmCallback) confirmCallback();
            closeConfirm();
        });

        document.getElementById('confirmModal').addEventListener('click', e => {
            if (e.target.id === 'confirmModal') closeConfirm();
        });

        
        function confirmLogout(event) {
            event.preventDefault();

            showConfirm({
                icon: '<i class="bi bi-box-arrow-left"></i>',
                iconType: 'warning',
                title: 'Konfirmasi Logout',
                desc: 'Apakah Anda yakin ingin keluar dari sistem?',
                btnType: 'warning',
                btnLabel: 'Ya, Logout',
                onYes: () => {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>