<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        $profile = $user->profile;

        if ($user->role === 'admin') {
            return view('admin.admin');
        } elseif ($user->role === 'nutritionist') {
            return view('nutritionist.nutritionist', compact('profile'));
        } elseif ($user->role === 'driver') {
            return view('driver.driver', compact('profile'));
        }

        return view('customer.customer', compact('profile'));
    })->name('dashboard');

    Route::post('/user-profile/store', [ProfileController::class, 'store'])->name('profile.store');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/kelola-menu', [MenuController::class, 'index'])->name('admin.menu');
        Route::post('/admin/kelola-menu/store', [MenuController::class, 'store'])->name('admin.menu.store');
        Route::put('/admin/kelola-menu/{id}', [MenuController::class, 'update'])->name('admin.menu.update');
        Route::delete('/admin/kelola-menu/{id}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');

        Route::get('/admin/kelola-pengguna', [UserController::class, 'index'])->name('admin.users');
        Route::post('/admin/kelola-pengguna/store', [UserController::class, 'store'])->name('admin.users.store');
        Route::delete('/admin/kelola-pengguna/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    Route::middleware(['role:nutritionist'])->group(function () {
        Route::get('/nutritionist/konsultasi', function () {
            return view('nutritionist.consultation');
        })->name('nutritionist.consultation');
    });

    Route::middleware(['role:driver'])->group(function () {
        Route::get('/driver/antaran', function () {
            return view('driver.delivery');
        })->name('driver.delivery');
    });

    Route::middleware(['role:customer'])->group(function () {
        Route::get('/order', [OrderController::class, 'index'])->name('customer.orders.index');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
