<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Kasir\TransactionController;
use App\Http\Controllers\Kasir\KasirTableController;

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
    Route::put('products/toggle/{id}', [ProductController::class, 'toggle'])->name('admin.products.toggle');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::put('/categories/toggle/{id}', [CategoryController::class, 'toggle'])->name('admin.categories.toggle');

    // Tables
    Route::get('/tables', [TableController::class, 'index'])->name('admin.tables.index');
    Route::post('/tables', [TableController::class, 'store'])->name('admin.tables.store');
    Route::put('/tables/{id}', [TableController::class, 'update'])->name('admin.tables.update');
    Route::delete('/tables/{id}', [TableController::class, 'destroy'])->name('admin.tables.destroy');
    Route::put('/tables/{id}/status', [TableController::class, 'toggleStatus'])->name('admin.tables.status');
    Route::put('/tables/{id}/tersedia', [TableController::class, 'setTersedia'])->name('admin.tables.tersedia');
    Route::put('/tables/{id}/reserve', [TableController::class, 'reserve'])->name('admin.tables.reserve');

    // Bookings
    Route::prefix('bookings')->name('admin.bookings.')->group(function () {
    Route::get('/',         [App\Http\Controllers\Admin\BookingController::class, 'index'])   ->name('index');
    Route::post('/',        [App\Http\Controllers\Admin\BookingController::class, 'store'])   ->name('store');
    Route::put('/{id}',     [App\Http\Controllers\Admin\BookingController::class, 'update'])  ->name('update');   // ← ini yang penting untuk edit
    Route::delete('/{id}',  [App\Http\Controllers\Admin\BookingController::class, 'destroy']) ->name('destroy');
    Route::put('/{id}/konfirmasi', [App\Http\Controllers\Admin\BookingController::class, 'konfirmasi'])->name('konfirmasi');
    Route::put('/{id}/batal',      [App\Http\Controllers\Admin\BookingController::class, 'batal'])     ->name('batal');
});
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::get('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');
});

// Kasir Routes
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/dashboard',                  [TransactionController::class, 'dashboard'])->name('transactions.dashboard');
    Route::get('/transactions',               [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions',              [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/from-booking', [TransactionController::class, 'storeFromBooking'])->name('transactions.storeFromBooking');
    Route::get('/transactions/print/{nomor}', [TransactionController::class, 'print'])->name('transactions.print');
    Route::get('/tables',                     [KasirTableController::class, 'index'])->name('tables.index');
    Route::patch('/tables/{table}/bebaskan',  [KasirTableController::class, 'bebaskan'])->name('tables.bebaskan');
    Route::patch('/tables/{table}/selesai',   [KasirTableController::class, 'selesai'])->name('tables.selesai');
});

// Owner Routes
Route::prefix('owner')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('owner.dashboard');
    Route::get('/reports', [\App\Http\Controllers\Owner\ReportController::class, 'index'])->name('owner.reports.index');
    Route::get('/reports/pdf', [\App\Http\Controllers\Owner\ReportController::class, 'printPdf'])->name('owner.reports.pdf');
    Route::get('/reports/products', [\App\Http\Controllers\Owner\ReportController::class, 'products'])->name('owner.reports.products');
    Route::get('/reports/products/pdf', [\App\Http\Controllers\Owner\ReportController::class, 'productsPdf'])->name('owner.reports.products.pdf');
    Route::get('/logs', [\App\Http\Controllers\Owner\LogController::class, 'index'])->name('owner.logs.index');
    Route::get('/logs/pdf', [\App\Http\Controllers\Owner\LogController::class, 'printPdf'])->name('owner.logs.pdf');
});