<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Jalur Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Jalur Pengguna Terautentikasi (Sudah Login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/saving', [DashboardController::class, 'storeSaving'])->name('saving.store');
    
    // Route Penarikan Saldo
    Route::post('/withdraw', [DashboardController::class, 'storeWithdrawal'])->name('withdraw.store');
    
    // Route Ganti Nama
    Route::post('/update-pet-name', [DashboardController::class, 'updatePetName'])->name('pet.update-name');
    Route::post('/update-user-name', [DashboardController::class, 'updateUserName'])->name('user.update-name');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});