<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Kasir\KasirDashboardController;
use App\Http\Controllers\Kasir\TransactionController;

// Login Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/redirect-dashboard', [AuthController::class, 'redirectDashboard'])->name('redirect.dashboard');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::get('/products/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('admin.products.toggle');
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::get('/categories/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle');
    
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::get('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');
});

Route::prefix('kasir')->middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('kasir.transactions.dashboard');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('kasir.transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('kasir.transactions.store');
    Route::get('/transactions/print/{nomorUnik}', [TransactionController::class, 'print'])->name('kasir.transactions.print');
});

// Owner Routes
Route::prefix('owner')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('owner.dashboard');
    
    // Reports
    Route::get('/reports', [\App\Http\Controllers\Owner\ReportController::class, 'index'])->name('owner.reports.index');
    Route::get('/reports/pdf', [\App\Http\Controllers\Owner\ReportController::class, 'printPdf'])->name('owner.reports.pdf');
    Route::get('/reports/products', [\App\Http\Controllers\Owner\ReportController::class, 'products'])->name('owner.reports.products');
    Route::get('/reports/products/pdf', [\App\Http\Controllers\Owner\ReportController::class, 'productsPdf'])->name('owner.reports.products.pdf');
    
    // Logs
    Route::get('/logs', [\App\Http\Controllers\Owner\LogController::class, 'index'])->name('owner.logs.index');
});