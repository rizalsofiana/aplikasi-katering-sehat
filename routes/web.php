<?php

use App\Http\Controllers\AdminPackageController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
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

            $totalUser = User::where('role', 'customer')->count();

            $activeOrder = Delivery::where('status', ['cooking', 'on the way'])
                ->count();

            $totalDriver = User::where('role', 'driver')->count();

            $incomeMonthly = Order::whereMonth('created_at', Carbon::now('Asia/Jakarta')->month)
                ->whereYear('created_at', Carbon::now('Asia/Jakarta')->year)
                ->sum('total_amount');

            return view('admin.admin', compact('totalUser', 'activeOrder', 'totalDriver', 'incomeMonthly'));
        } elseif ($user->role === 'nutritionist') {
            return view('nutritionist.nutritionist', compact('profile'));
        } elseif ($user->role === 'driver') {
            return redirect()->route('deliveries.index');
        }

        $todayDeliveries = Delivery::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereDate('delivery_date', Carbon::today('Asia/Jakarta'))
            ->with('menu')
            ->where('status', '!=', 'delivered')
            ->latest()
            ->limit(1)
            ->get();

        $currentSubscription = Subscription::where('user_id', $user->id)
            ->with('package')
            ->latest()
            ->first();

        return view('customer.customer', compact('profile', 'todayDeliveries', 'currentSubscription'));
    })->name('dashboard');

    Route::post('/user-profile/store', [ProfileController::class, 'store'])->name('profile.store');

    Route::middleware(['role:admin'])->name('admin.')->group(function () {
        Route::get('/admin/kelola-menu', [MenuController::class, 'index'])->name('menu');
        Route::post('/admin/kelola-menu/store', [MenuController::class, 'store'])->name('menu.store');
        Route::put('/admin/kelola-menu/{id}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/admin/kelola-menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');

        Route::get('/admin/kelola-pengguna', [UserController::class, 'index'])->name('users');
        Route::post('/admin/kelola-pengguna/store', [UserController::class, 'store'])->name('users.store');
        Route::delete('/admin/kelola-pengguna/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/admin/kelola-pesanan', [OrderController::class, 'adminIndex'])->name('orders.index');
        Route::get('/admin/kelola-pesanan/{id}', [OrderController::class, 'adminShow'])->name('orders.show');
        Route::patch('/admin/kelola-pesanan/{id}/confirm', [OrderController::class, 'confirmPayment'])->name('orders.confirm');
        Route::post('/admin/kelola-pesanan/{id}/assign-driver', [OrderController::class, 'assignDriver'])->name('orders.assign_driver');

        Route::resource('packages', AdminPackageController::class)->except(['show']);
    });

    Route::middleware(['role:nutritionist'])->group(function () {
        Route::get('/nutritionist/konsultasi', function () {
            return view('nutritionist.consultation');
        })->name('nutritionist.consultation');
    });

    Route::middleware(['role:driver'])->group(function () {
        Route::get('/deliveries', [OrderController::class, 'driverIndex'])->name('deliveries.index');
        // Driver ambil orderan kosong
        Route::patch('/deliveries/{id}/take', [OrderController::class, 'takeOrder'])->name('deliveries.take');
        // Driver ubah status jadi otw
        Route::patch('/deliveries/{id}/otw', [OrderController::class, 'updateStatusToOnTheWay'])->name('deliveries.otw');
        Route::patch('/deliveries/{id}/delivered', [OrderController::class, 'updateStatusToDelivered'])->name('deliveries.delivered');
        Route::patch('/deliveries/{id}/failed', [OrderController::class, 'updateStatusToFailed'])->name('deliveries.failed');
    });

    Route::middleware(['role:customer'])->group(function () {
        Route::get('/order', [OrderController::class, 'index'])->name('customer.orders.index');
        Route::post('/order/checkout', [OrderController::class, 'store'])->name('customer.orders.store');
        Route::get('/order/history', [CustomerOrderController::class, 'index'])->name('customer.orders.history');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('customer.subscriptions.index');
        Route::get('/subscriptions/checkout/{package}', [SubscriptionController::class, 'checkout'])->name('customer.subscriptions.checkout');
        Route::post('/subscriptions/purchase', [SubscriptionController::class, 'store'])->name('customer.subscriptions.store');

        Route::get('/order/{order}/payment', [OrderController::class, 'payment'])->name('customer.orders.payment');

        // Route Webhook Callback dari Midtrans (Wajib di luar middleware auth/csrf jika bisa)
        Route::post('/midtrans/callback', [OrderController::class, 'callback'])->name('midtrans.callback');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
