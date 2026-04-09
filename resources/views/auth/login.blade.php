<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rumah Makan Foodesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            display: flex;
            max-width: 1000px;
            width: 90%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            min-height: 600px;
        }
        
        .login-left {
            background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
            color: white;
            padding: 50px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        
        .login-left::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ff9c5c' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.3;
        }
        
        .login-right {
            background: white;
            padding: 50px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .logo-subtext {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            color: #333;
        }
        
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #ff8c42;
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 66, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
            border: none;
            padding: 14px;
            font-weight: 600;
            border-radius: 10px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 15px rgba(255, 107, 53, 0.3);
        }
        
        .login-info {
            background-color: #fff9f5;
            border-left: 4px solid #ff8c42;
            padding: 15px;
            border-radius: 8px;
            margin-top: 25px;
            font-size: 0.9rem;
        }
        
        .welcome-text {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 20px;
            line-height: 1.4;
        }
        
        .description-text {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #888;
            font-size: 0.9rem;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .divider span {
            padding: 0 15px;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 95%;
            }
            
            .login-left, .login-right {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo-container">
                <div class="logo">
                   <img src="{{ asset('storage/gambar/foodesia.png') }}" alt="foodesia" style="width: 300px; height: 200px; object-fit: contain;">
                </div>
                <div class="logo-text">Foodesia</div>
                <div class="logo-subtext">Sistem Kasir & Manajemen</div>
            </div>
            
            <div class="welcome-text">
                Selamat Datang di Sistem Manajemen Restoran
            </div>
            
            <div class="description-text">
                Gunakan sistem ini untuk mengelola transaksi kasir, inventaris stok, laporan penjualan, dan manajemen karyawan dengan mudah dan efisien.
            </div>
        </div>
        
        <!-- Bagian Kanan dengan Form Login -->
        <div class="login-right">
            <h5 class="login-title">Silakan Login</h5>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-person-fill me-2"></i>Username
                    </label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                           name="username" placeholder="Masukkan username" value="admin" required autofocus>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-lock-fill me-2"></i>Password
                    </label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           name="password" placeholder="Masukkan password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>