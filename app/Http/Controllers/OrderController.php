<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function index()
    {
        $menus = Menu::with('nutrition')->where('is_available', true)->get();
        $userProfile = Auth::user()->profile;

        return view('customer.menu', compact('menus', 'userProfile'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delivery_date' => 'required|date|after_or_equal:today',
            'delivery_address' => 'required|string|max:255',
            'meal_time' => 'required|in:breakfast,lunch,dinner',
            'cart_data' => 'required|json'
        ]);

        $cartItems = json_decode($request->cart_data, true);
        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda kosong!');
        }

        // =========================================================================
        // 🟢 TAHAP 1: SIAPKAN TARGET HARIAN (PROFILE USER)
        // =========================================================================
        $user = Auth::user();
        $profile = $user->profile;

        $dailyCalorieTarget = $profile ? $profile->daily_calorie_target : 2000;
        $dailyProteinTarget = ($profile && $profile->weight_kg) ? ($profile->weight_kg * 1.5) : 60;

        // =========================================================================
        // 🟢 TAHAP 2: HITUNG NUTRISI KERANJANG BARU & VALIDASI STOK
        // =========================================================================
        $menuIds = array_column($cartItems, 'id');
        $menus = Menu::with('nutrition')->whereIn('id', $menuIds)->get()->keyBy('id');

        $cartCalories = 0;
        $cartProtein = 0;
        $totalAmount = 0;

        foreach ($cartItems as $item) {
            $menu = $menus->get($item['id']);
            if ($menu) {
                // 💡 BARU: Validasi ketersediaan stok sebelum diproses
                if ($menu->stock < $item['qty']) {
                    return response()->json([
                        'message' => "Maaf, stok untuk menu '{$menu->name}' tidak mencukupi. Sisa stok: {$menu->stock} porsi."
                    ], 400);
                }

                $totalAmount += ($menu->price * $item['qty']);

                if ($menu->nutrition) {
                    $cartCalories += ($menu->nutrition->calories * $item['qty']);
                    $cartProtein += ($menu->nutrition->protein_g * $item['qty']);
                }
            }
        }

        // =========================================================================
        // 🟢 TAHAP 3: HITUNG HISTORI MAKANAN DI TANGGAL TERSEBUT
        // =========================================================================
        $existingDeliveries = Delivery::with('menu.nutrition')
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDate('delivery_date', $request->delivery_date)
            ->whereIn('status', ['cooking', 'on_the_way', 'delivered'])
            ->get();

        $existingCalories = 0;
        $existingProtein = 0;

        foreach ($existingDeliveries as $delivery) {
            if ($delivery->menu && $delivery->menu->nutrition) {
                $existingCalories += $delivery->menu->nutrition->calories;
                $existingProtein += $delivery->menu->nutrition->protein_g;
            }
        }

        // =========================================================================
        // 🟢 TAHAP 4: VALIDASI GABUNGAN & GENERATE CATATAN
        // =========================================================================
        $totalDayCalories = $existingCalories + $cartCalories;
        $totalDayProtein = $existingProtein + $cartProtein;

        if ($totalDayCalories > $dailyCalorieTarget) {
            return response()->json([
                'message' => "TIDAK SEHAT! 🛑 Pesanan ini membuat Anda kelebihan kalori. Total konsumsi Anda pada tanggal tersebut akan menjadi {$totalDayCalories} Kkal (Melebihi target batas {$dailyCalorieTarget} Kkal). Silakan kurangi porsi."
            ], 400);
        }

        $nutritionNotes = "Gizi Harian: Kalori {$totalDayCalories}/{$dailyCalorieTarget} Kkal | Protein {$totalDayProtein}/{$dailyProteinTarget}g";

        // =========================================================================
        // 🟢 TAHAP 5: SIMPAN DATA KE DATABASE & POTONG STOK
        // =========================================================================
        // 💡 BARU: Tambahkan variabel $menus ke dalam 'use' agar bisa diakses di dalam transaction
        $order = DB::transaction(function () use ($request, $cartItems, $totalAmount, $nutritionNotes, $menus) {

            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => 'INV-' . strtoupper(Str::random(5)) . '-' . now()->format('YmdHis'),
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'menu_id' => $item['id'],
                    'quantity' => $item['qty']
                ]);

                // 💡 BARU: Kurangi stok menu secara langsung di database menggunakan decrement()
                $menuToUpdate = $menus->get($item['id']);
                if ($menuToUpdate) {
                    $menuToUpdate->decrement('stock', $item['qty']);

                    // Opsional: Jika stok habis (0), otomatis ubah is_available jadi false
                    if ($menuToUpdate->stock <= 0) {
                        $menuToUpdate->update(['is_available' => false]);
                    }
                }

                for ($i = 0; $i < $item['qty']; $i++) {
                    Delivery::create([
                        'order_id'         => $order->id,
                        'menu_id'          => $item['id'],
                        'driver_id'        => null,
                        'delivery_date'    => $request->delivery_date,
                        'delivery_address' => $request->delivery_address,
                        'latitude'         => $request->latitude ?? null,
                        'longitude'        => $request->longitude ?? null,
                        'meal_time'        => $request->meal_time,
                        'status'           => 'cooking',
                        'notes'            => $nutritionNotes,
                    ]);
                }
            }

            $order->update(['status' => 'paid']);

            return $order;
        });

        $profile->update([
            'daily_calorie_target' => $dailyCalorieTarget - $cartCalories,
        ]);

        // =========================================================================
        // 🟢 TAHAP 6: INTEGRASI MIDTRANS SNAP
        // =========================================================================
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $order->invoice_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $order->update(['snap_token' => $snapToken]);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal terhubung ke gerbang pembayaran: ' . $e->getMessage()], 500);
        }
    }

    public function payment(Order $order)
    {
        // Pastikan hanya pemilik order yang bisa melihat halaman ini
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.orders.payment', compact('order'));
    }

    public function adminIndex()
    {
        // Ambil semua order, urutkan dari yang terbaru
        $orders = Order::with('user')->latest()->get();

        return view('admin.order', compact('orders'));
    }

    public function adminShow(int $id)
    {
        $order = Order::with(['user', 'items.menu'])->findOrFail($id);

        // Ambil antrean delivery berdasarkan menu dari order ini yang BELUM punya driver
        $deliveries = Delivery::with('menu')
            ->whereIn('menu_id', $order->items->pluck('menu_id'))
            ->where('order_id', $id)
            ->get();

        $drivers = User::where('role', 'driver')->get();

        return view('admin.order_detail', compact('order', 'deliveries', 'drivers'));
    }

    // ==========================================
    //          FITUR KHUSUS DRIVER
    // ==========================================

    // 2. Halaman Dashboard Driver (Melihat Orderan Kosong & Orderan Milik Dia)
    public function driverIndex()
    {
        $driverId = Auth::id();

        // A. List pesanan yang BELUM memiliki driver
        $availableDeliveries = Delivery::with(['menu', 'order.user', 'order'])
            ->whereNull('driver_id')
            ->whereIn('status', ['cooking']) // Sesuaikan status ketersediaan dengan alur Anda
            ->latest()
            ->get()
            ->groupBy('order_id'); // 💡 TAMBAHKAN INI DI SINI (Setelah get)

        // B. List pesanan yang sedang diambil/ditugaskan ke driver ini
        $myDeliveries = Delivery::with(['menu', 'order.user', 'order'])
            ->where('driver_id', $driverId)
            ->whereIn('status', ['cooking', 'on_the_way'])
            ->latest()
            ->get()
            ->groupBy('order_id'); // 💡 TAMBAHKAN INI JUGA DI SINI

        return view('driver.driver', compact('availableDeliveries', 'myDeliveries'));
    }

    // 3. Driver mengambil/klaim pesanan sendiri (Tombol Ambil Pesanan)
    public function takeOrder(Request $request, int $id)
    {
        $delivery = Delivery::findOrFail($id);

        // Antisipasi jika keduluan driver lain
        if ($delivery->driver_id !== null) {
            return redirect()->back()->with('error', 'Maaf, pesanan ini sudah diambil oleh driver lain!');
        }

        $delivery->update([
            'driver_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Berhasil mengambil pesanan! Silakan bersiap melakukan pickup.');
    }

    // 4. Driver mengubah status menjadi On The Way saat berangkat
    public function updateStatusToOnTheWay(int $id)
    {
        $updatedCount = Delivery::where('order_id', $id)
            ->where('driver_id', Auth::id())
            ->update([
                'status' => 'on_the_way'
            ]);

        if ($updatedCount === 0) {
            abort(404, 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        return redirect()->back()->with('success', 'Status diperbarui: Pesanan sedang dalam perjalanan! Hati-hati di jalan.');
    }

    public function updateStatusToDelivered(int $id)
    {
        // Pastikan delivery ini memang milik driver yang sedang login
        $updatedCount = Delivery::where('order_id', $id)->where('driver_id', Auth::id())->update([
            'status' => 'delivered'
        ]);

        if ($updatedCount === 0) {
            abort(404, 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        return redirect()->back()->with('success', 'Alhamdulillah, pesanan telah sukses diantarkan ke pelanggan!');
    }

    public function updateStatusToFailed(int $id)
    {
        // Pastikan delivery ini memang milik driver yang sedang login
        $updatedCount = Delivery::where('order_id', $id)->where('driver_id', Auth::id())->update([
            'status' => 'failed'
        ]);

        if ($updatedCount === 0) {
            abort(404, 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        return redirect()->back()->with('success', 'Pesanan telah ditandai sebagai gagal diantarkan.');
    }

    // 3. Mengonfirmasi Pembayaran
    public function confirmPayment(int $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'paid']);

        return redirect()->back()->with('success', 'Pembayaran untuk invoice ' . $order->invoice_number . ' Berhasil Dikonfirmasi!');
    }

    // 4. Memilihkan Driver untuk Pengiriman
    public function assignDriver(Request $request, int $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        // 2. Update driver_id secara massal berdasarkan order_id
        // Ini otomatis akan meng-assign semua menu di dalam invoice tersebut ke 1 driver
        Delivery::where('order_id', $id)
            ->whereNull('driver_id') // Pastikan hanya menimpa yang belum punya kurir
            ->update([
                'driver_id' => $request->driver_id
            ]);

        return redirect()->back()->with('success', 'Driver berhasil ditugaskan untuk semua menu pada pesanan ini!');
    }
}
