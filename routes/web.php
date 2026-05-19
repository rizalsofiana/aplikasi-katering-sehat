<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- ROUTE GROUP UNTUK USER YANG SUDAH LOGIN ---
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD UTAMA (Mengarahkan atau memuat view sesuai role)
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $profile = $user->profile;

        // Contoh data dummy langganan (bisa disesuaikan dengan relasi tabel Anda nantinya)
        $todayDelivery = $profile ? null : null;

        // Cek role untuk menentukan view dashboard yang dimuat
        if ($user->role === 'admin') {
            return view('dashboard.admin');
        } elseif ($user->role === 'nutritionist') {
            return view('dashboard.nutritionist', compact('profile'));
        } elseif ($user->role === 'driver') {
            return view('dashboard.driver', compact('profile'));
        }

        // Default: Halaman Dashboard Customer (yang ada Pop-up melengkapi data fisik)
        return view('dashboard.customer', compact('profile', 'todayDelivery'));
    })->name('dashboard');

    // Route untuk simpan profile fisik customer dari Pop-up
    Route::post('/user-profile/store', [ProfileController::class, 'store'])->name('profile.store');

    // 2. KELOMPOK RUTE KHUSUS ADMIN (Hanya bisa diakses jika role = admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/kelola-menu', function () {
            return view('admin.menu');
        })->name('admin.menu');
        Route::get('/admin/kelola-user', function () {
            return view('admin.users');
        })->name('admin.users');
    });

    // 3. KELOMPOK RUTE KHUSUS NUTRISIONIS (ROLE: nutritionist)
    Route::middleware(['role:nutritionist'])->group(function () {
        Route::get('/nutritionist/konsultasi', function () {
            return view('nutritionist.consultation');
        })->name('nutritionist.consultation');
    });

    // 4. KELOMPOK RUTE KHUSUS DRIVER (ROLE: driver)
    Route::middleware(['role:driver'])->group(function () {
        Route::get('/driver/antaran', function () {
            return view('driver.delivery');
        })->name('driver.delivery');
    });

    // Rute Profile bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
