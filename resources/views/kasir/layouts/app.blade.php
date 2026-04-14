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
        
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        
        .nav-sticky-wrapper {
            position: sticky;
            top: 0;
            z-index: 1050;
        }

        .top-header {
            background: linear-gradient(135deg, #FF8C42 0%, #FF6B35 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-menu {
            background: white;
            border-bottom: 3px solid #FF8C42;
            padding: 0 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .navbar-inner {
            display: flex;
            align-items: center;
            gap: 20px;
            min-height: 56px;
            flex-wrap: wrap;
        }

        .menu-label {
            color: #999;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

       
        .nav-custom {
            display: flex;
            align-items: center;
            gap: 5px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .nav-custom .nav-link-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            background: transparent;
            color: #FF8C42;
            border: 2px solid transparent;
            white-space: nowrap;
        }

        .nav-custom .nav-link-custom:hover {
            background: rgba(255,140,66,0.1);
            border-color: #FF8C42;
        }

        .nav-custom .nav-link-custom.active {
            background: #FF8C42;
            color: white;
            border-color: #FF8C42;
        }

        .nav-link-logout { color: #dc3545 !important; }
        .nav-link-logout:hover {
            background: rgba(220,53,69,0.1) !important;
            border-color: #dc3545 !important;
        }

       
        .btn-primary-custom,
        button.btn-primary-custom,
        a.btn-primary-custom {
            background: #FF8C42 !important;
            color: white !important;
            border: none !important;
            padding: 8px 20px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            text-decoration: none !important;
        }
        .btn-primary-custom:hover {
            background: #E67E22 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(255,140,66,0.3) !important;
        }

        .btn-outline-custom {
            background: transparent !important;
            border: 2px solid #FF8C42 !important;
            color: #FF8C42 !important;
            padding: 8px 20px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }
        .btn-outline-custom:hover {
            background: #FF8C42 !important;
            color: white !important;
        }

        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 6px;
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

        
        .main-content {
            padding: 25px 30px;
            min-height: calc(100vh - 170px);
        }

        .page-header {
            background: white;
            padding: 18px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

       
        .transaksi-wrapper {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            align-items: start;
        }

        .produk-panel {
            
        }

        .keranjang-panel {
           
        }

        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255,140,66,0.2);
            border-color: #FF8C42;
        }

        .product-img {
            width: 100%;
            height: 130px;
            object-fit: cover;
        }

        .product-info {
            padding: 10px;
        }

        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 0.93rem;
        }

        .product-price {
            color: #FF8C42;
            font-weight: 700;
            font-size: 0.97rem;
            margin-bottom: 6px;
        }

        .badge-custom {
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.78rem;
        }

        
        .cart-container {
            background: white;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            position: sticky;
            top: 170px; /* tinggi nav sticky */
            border-top: 4px solid #FF8C42;
            max-height: calc(100vh - 190px);
            overflow-y: auto;
        }

        .cart-item {
            background: #FFF9F5;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            border-left: 3px solid #FF8C42;
        }

        .cart-total {
            background: #FFE8DC;
            padding: 14px;
            border-radius: 8px;
            margin: 10px 0;
        }

       
        .mode-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .mode-tab {
            flex: 1;
            text-align: center;
            padding: 8px 6px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid #e0e0e0;
            color: #888;
            transition: all 0.2s;
        }

        .mode-tab.active {
            background: #FF8C42;
            color: white;
            border-color: #FF8C42;
        }

        
        .booking-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .booking-card:hover  { border-color: #FF8C42; background: #FFF9F5; }
        .booking-card.selected { border-color: #FF8C42; background: #FFF0E6; }

        .booking-name   { font-weight: 700; font-size: 14px; color: #333; }
        .booking-detail { font-size: 12px; color: #666; margin-top: 3px; }

        .dp-info-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 10px;
        }
        .dp-label { font-size: 12px; color: #155724; font-weight: 600; }
        .dp-value { font-size: 18px; font-weight: 700; color: #155724; }

        .catatan-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 10px;
        }

        
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
            padding: 13px 15px;
        }

        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        /* pagination brand color */
        .page-link { color: #FF8C42; }
        .page-item.active .page-link { background: #FF8C42; border-color: #FF8C42; }

       
        .form-control:focus,
        .form-select:focus {
            border-color: #FF8C42;
            box-shadow: 0 0 0 0.2rem rgba(255,140,66,0.2);
        }

        
        @media (max-width: 992px) {
            .transaksi-wrapper {
                grid-template-columns: 1fr;
            }
            .cart-container {
                position: relative;
                top: 0;
                max-height: none;
            }
        }

        @media (max-width: 768px) {
            .main-content { padding: 15px; }
            .navbar-inner { flex-direction: column; align-items: flex-start; padding: 8px 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="nav-sticky-wrapper">
        
        <div class="top-header">
            <div class="logo-section">
                <div class="logo-icon">
                    <img src="{{ asset('storage/gambar/logo.png') }}" alt="foodesia">
                </div>
                <div>
                    <h5 class="mb-0" style="font-weight:700;">FOODESIA - Kasir Panel</h5>
                    <small style="opacity:0.9;">Sistem Kasir Rumah Makan</small>
                </div>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:600;">{{ Auth::user()->nama ?? 'User' }}</div>
                    <small style="opacity:0.9;">{{ ucfirst(Auth::user()->role) }}</small>
                </div>
            </div>
        </div>

        
        <div class="navbar-menu">
            <div class="navbar-inner">
                <div class="menu-label">
                    <i class="bi bi-grid"></i> MENU KASIR:
                </div>
                <ul class="nav-custom">
                    <li class="nav-item">
                        
                        <a href="{{ route('kasir.transactions.dashboard') }}"
                           class="nav-link-custom {{ request()->routeIs('kasir.transactions.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-cart"></i> Transaksi Baru
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kasir.transactions.index') }}"
                           class="nav-link-custom {{ request()->routeIs('kasir.transactions.index') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i> Riwayat Transaksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kasir.tables.index') }}"
                           class="nav-link-custom {{ request()->routeIs('kasir.tables.*') ? 'active' : '' }}">
                            <i class="bi bi-table"></i> Status Meja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('kasir.logs.index') }}"
                        class="nav-link-custom {{ request()->routeIs('kasir.logs.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-text"></i> Log Aktivitas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link-custom nav-link-logout" onclick="confirmLogout(event)">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <!-- Toast Notification -->
    <div id="toastNotif" style="position:fixed;top:24px;right:24px;z-index:99999;background:white;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 8px 30px rgba(0,0,0,0.12);min-width:280px;transform:translateX(120%);transition:transform 0.3s ease;">
        <div id="toastIcon" style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;"></div>
        <span id="toastText" style="font-size:14px;font-weight:600;color:#333;"></span>
        <button onclick="closeToast()" style="margin-left:auto;background:none;border:none;color:#999;font-size:18px;cursor:pointer;">×</button>
    </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;">
        <div id="confirmBox" style="background:white;border-radius:16px;padding:30px;min-width:320px;max-width:400px;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,0.15);transform:scale(0.9);transition:transform 0.2s;">
            <div id="confirmIcon" style="width:70px;height:70px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:32px;"></div>
            <div id="confirmTitle" style="font-size:18px;font-weight:700;color:#333;margin-bottom:8px;"></div>
            <div id="confirmDesc" style="font-size:14px;color:#666;margin-bottom:24px;"></div>
            <div style="display:flex;gap:12px;justify-content:center;">
                <button onclick="closeConfirm()" class="btn-outline-custom">Tidak</button>
                <button id="confirmYesBtn" class="btn-primary-custom">Ya</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── TOAST ────────────────────────────────────────────
        let toastTimer;

        function showToast(type, message) {
            const colors = { success:'#28a745', warning:'#FF8C42', danger:'#dc3545' };
            const icons  = { success:'bi-check-circle-fill', warning:'bi-exclamation-circle-fill', danger:'bi-x-circle-fill' };
            const icon   = document.getElementById('toastIcon');
            icon.style.background = colors[type] + '22';
            icon.style.color      = colors[type];
            icon.innerHTML = `<i class="bi ${icons[type]}"></i>`;
            document.getElementById('toastText').textContent = message;
            document.getElementById('toastNotif').style.transform = 'translateX(0)';
            clearTimeout(toastTimer);
            toastTimer = setTimeout(closeToast, 3500);
        }

        function closeToast() {
            document.getElementById('toastNotif').style.transform = 'translateX(120%)';
        }

        // ── CONFIRM MODAL ────────────────────────────────────
        let confirmCallback = null;

        function showConfirm({ icon, iconType, title, desc, btnType, btnLabel, onYes }) {
            const colors = { success:'#28a745', warning:'#FF8C42', danger:'#dc3545' };
            const ci = document.getElementById('confirmIcon');
            ci.style.background = colors[iconType] + '22';
            ci.style.color      = colors[iconType];
            ci.innerHTML = icon;
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmDesc').textContent  = desc;
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

        // ── CONFIRM LOGOUT ───────────────────────────────────
        function confirmLogout(event) {
            event.preventDefault();
            showConfirm({
                icon: '<i class="bi bi-box-arrow-right"></i>',
                iconType: 'warning',
                title: 'Konfirmasi Logout',
                desc: 'Apakah Anda yakin ingin keluar dari sistem?',
                btnType: 'warning',
                btnLabel: 'Ya, Logout',
                onYes: () => document.getElementById('logout-form').submit()
            });
        }

        // ── LARAVEL SESSION MESSAGES ─────────────────────────
        @if(session('success'))
            showToast('success', '{{ session('success') }}');
        @endif
        @if(session('error'))
            showToast('danger', '{{ session('error') }}');
        @endif
        @if(session('warning'))
            showToast('warning', '{{ session('warning') }}');
        @endif
    </script>

    @stack('scripts')
</body>
</html>