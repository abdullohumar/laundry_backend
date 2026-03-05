<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) { return $request->user()->load('roles'); });

    // Fitur Buka/Tutup Kasir (Shift)
    Route::get('/shifts/current', [ShiftController::class, 'current']);
    Route::post('/shifts/start', [ShiftController::class, 'start']);
    Route::post('/shifts/close', [ShiftController::class, 'close']);

    // Kasir & Karyawan Area (Operasional POS)
    Route::apiResource('customers', CustomerController::class)->only(['index', 'store']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Kasir Butuh Lihat Produk dan Mesin
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/machines', [MachineController::class, 'index']);
    Route::patch('/machines/{machine}/status', [MachineController::class, 'updateStatus']); // Ubah status mesin

    // ==========================================
    // AREA KHUSUS SUPER ADMIN
    // ==========================================
    Route::middleware('can:manage_users')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    Route::middleware('can:manage_products')->group(function () {
        // Super admin kelola harga, tambah koin, tambah addon
        Route::apiResource('products', ProductController::class)->except(['index']);
        
        // Super admin kelola data fisik mesin
        Route::apiResource('machines', MachineController::class)->except(['index']);
    });
});
