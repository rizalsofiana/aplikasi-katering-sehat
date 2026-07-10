<?php

use App\Http\Controllers\AdminPackageController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Models\Consultation;
use App\Models\Delivery;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [function () {
    $featuredMenu = Menu::with('nutrition')->inRandomOrder()->first();

    // Atau jika ingin mengambil menu terbaru:
    // $featuredMenu = Menu::with('nutrition')->latest()->first();

    return view('welcome', compact('featuredMenu'));
}]);

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
            $antreanKonsultasi = Consultation::where('status', 'open')
                ->whereNull('nutritionist_id')->count();

            $konsultasiBerlangsung = Consultation::where('status', 'on_going')
                ->whereNotNull('nutritionist_id')->count();

            $menuTerkompilasiAI = Menu::count();
            return view('nutritionist.nutritionist', compact('antreanKonsultasi', 'konsultasiBerlangsung', 'menuTerkompilasiAI', 'profile'));
        } elseif ($user->role === 'driver') {
            return redirect()->route('deliveries.index');
        }

        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        // 2. Kalkulasi total kalori dari makanan yang dikirim hari ini
        $kaloriTerpakaiHariIni = OrderItem::whereHas('order', function ($query) use ($user, $today) {
            $query->where('user_id', $user->id)
                // Opsional: Pastikan hanya menghitung pesanan yang sudah dibayar
                // ->where('status', 'settlement') 
                ->whereHas('deliveries', function ($deliveryQuery) use ($today) {
                    $deliveryQuery->whereDate('delivery_date', $today);
                });
        })
            // Eager load relasi untuk mencegah N+1 Query problem
            ->with('menu.nutrition')
            ->get()
            ->sum(function ($item) {
                // Kalikan jumlah porsi (quantity) dengan jumlah kalori per porsi
                $kaloriPerPorsi = $item->menu->nutrition->calories ?? 0;
                return $kaloriPerPorsi * $item->quantity;
            });

        // 3. Kurangi target harian dengan kalori yang sudah terpakai
        $targetAwal = $profile->daily_calorie_target ?? 0;
        $sisaKalori = $targetAwal - $kaloriTerpakaiHariIni;

        // Pastikan angka sisa kalori tidak menjadi minus jika user makan berlebih
        $sisaKalori = $sisaKalori < 0 ? 0 : $sisaKalori;

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

        return view('customer.customer', compact('profile', 'todayDeliveries', 'currentSubscription', 'sisaKalori'));
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
        Route::post('/admin/kelola-pesanan/{id}/assign-driver', [OrderController::class, 'assignDriver'])->name('orders.assign_driver');
        Route::post('/admin/kelola-pesanan/{id}/reassign-driver', [OrderController::class, 'reassignDriver'])->name('orders.reassign_driver');

        Route::resource('packages', AdminPackageController::class)->except(['show']);

        Route::get('/admin/sales-report', [ReportController::class, 'salesReport'])->name('reports.sales');
    });

    Route::middleware(['role:nutritionist'])->group(function () {
        Route::get('/nutritionist/konsultasi', function () {
            return view('nutritionist.consultation');
        })->name('nutritionist.consultation');

        // Tambahkan ini di dalam group middleware nutritionist Anda
        Route::get('/nutritionist/consultation', [ConsultationController::class, 'nutritionistIndex'])->name('nutritionist.consultation.index');
        Route::get('/nutritionist/consultation/{id}', [ConsultationController::class, 'show'])->name('nutritionist.consultation.show');
        Route::post('/nutritionist/consultation/{id}/reply', [ConsultationController::class, 'reply'])->name('nutritionist.consultation.reply');
        Route::post('/nutritionist/consultation/{id}/close', [ConsultationController::class, 'close'])->name('nutritionist.consultation.close');
    });

    Route::middleware(['role:driver'])->group(function () {
        Route::get('/deliveries', [OrderController::class, 'driverIndex'])->name('deliveries.index');
        Route::get('/delivery/histories', [OrderController::class, 'history'])->name('deliveries.histories');
        Route::patch('/deliveries/{id}/take', [OrderController::class, 'takeOrder'])->name('deliveries.take');
        Route::patch('/deliveries/{id}/otw', [OrderController::class, 'updateStatusToOnTheWay'])->name('deliveries.otw');
        Route::patch('/deliveries/{id}/delivered', [OrderController::class, 'updateStatusToDelivered'])->name('deliveries.delivered');
        Route::patch('/deliveries/{id}/failed', [OrderController::class, 'updateStatusToFailed'])->name('deliveries.failed');
    });

    Route::middleware(['role:customer'])->group(function () {
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('customer.profile.update');

        Route::get('/order', [OrderController::class, 'index'])->name('customer.orders.index');
        Route::post('/order/checkout', [OrderController::class, 'store'])->name('customer.orders.store');
        Route::get('/order/history', [CustomerOrderController::class, 'index'])->name('customer.orders.history');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('customer.subscriptions.index');
        Route::get('/subscriptions/checkout/{package}', [SubscriptionController::class, 'checkout'])->name('customer.subscriptions.checkout');
        Route::post('/subscriptions/purchase', [SubscriptionController::class, 'store'])->name('customer.subscriptions.store');

        Route::get('/order/{order}/payment', [OrderController::class, 'payment'])->name('customer.orders.payment');
        Route::post('/order/ai-recommendation', [OrderController::class, 'getAiRecommendation'])->name('customer.orders.ai');

        // Route Webhook Callback dari Midtrans (Wajib di luar middleware auth/csrf jika bisa)
        Route::post('/midtrans/callback', [OrderController::class, 'callback'])->name('midtrans.callback');

        // Tambahkan ini di dalam group middleware customer
        Route::get('/customer/consultation', [ConsultationController::class, 'index'])->name('customer.consultation.index');

        Route::post('/customer/consultation', [ConsultationController::class, 'store'])->name('customer.consultation.store');
        // Melihat obrolan
        Route::get('/customer/consultation/{id}', [ConsultationController::class, 'show'])->name('customer.consultation.show');
        // Mengirim balasan
        Route::post('/customer/consultation/{id}/reply', [ConsultationController::class, 'reply'])->name('customer.consultation.reply');
        // Menutup konsultasi
        Route::post('/customer/consultation/{id}/close', [ConsultationController::class, 'close'])->name('customer.consultation.close');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
