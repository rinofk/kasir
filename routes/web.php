<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Auth;

// Public Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    
    // POS (Point of Sale) - Accessible by both Admin and Kasir
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::post('/pos', [PosController::class, 'store']);
    Route::get('/pos/receipt/{transaction}', [PosController::class, 'printReceipt'])->name('pos.receipt');

    // Dashboard - Accessible to Admin or anyone with 'view reports' permission
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('role_or_permission:admin|view reports');
    
    // Master CRUD Resource Routes protected by specific Spatie permissions
    Route::resource('categories', CategoryController::class)->middleware('permission:manage categories');
    Route::resource('products', ProductController::class)->middleware('permission:manage products');
    Route::resource('users', UserController::class)->middleware('permission:manage users');
    Route::resource('roles', RoleController::class)->middleware('permission:manage users');
    
    // Sales Analysis Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:view reports');
    Route::get('/reports/{transaction}', [ReportController::class, 'show'])->name('reports.show')->middleware('permission:view reports');

    // Store Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index')->middleware('role_or_permission:admin|manage settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('role_or_permission:admin|manage settings');
});
