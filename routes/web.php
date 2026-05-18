<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    // Ambil data profile beserta nutrisinya jika ada
    $profile = $user->profile;

    // Contoh data dummy untuk status pengiriman hari ini (Nanti bisa dihubungkan ke tabel deliveries)
    $todayDelivery = $profile ? $user->subscriptions()->where('status', 'active')->first() : null;

    return view('dashboard', compact('profile', 'todayDelivery'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
});

require __DIR__ . '/auth.php';
