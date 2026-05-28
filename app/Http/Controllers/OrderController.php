<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $menus = Menu::with('nutrition')->where('is_available', true)->get();

        $activeSubscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        $userProfile = Auth::user()->profile;

        return view('customer.menu', compact('menus', 'activeSubscription', 'userProfile'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input dari Form POS
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'delivery_date' => 'required|date|after_or_equal:today',
            'meal_time' => 'required|in:breakfast,lunch,dinner',
            'cart_data' => 'required|json' // Mengirim data keranjang belanja berupa JSON array dari Alpine.js
        ]);

        $cartItems = json_decode($request->cart_data, true);
        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda kosong!');
        }

        // Hitung total harga belanjaan POS
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            // Asumsikan harga flat Rp 35.000 per menu, atau ambil langsung dari database jika ada field price
            $totalAmount += (35000 * $item['qty']);
        }

        // 2. Gunakan DB Transaction agar jika salah satu proses gagal, data tidak tersimpan setengah-setengah
        DB::transaction(function () use ($request, $cartItems, $totalAmount) {

            // A. Simpan data transaksi ke tabel orders
            $order = Order::create([
                'user_id' => Auth::id(),
                'subscription_id' => $request->subscription_id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(5)) . '-' . now()->format('YmdHis'),
                'total_amount' => $totalAmount,
                'status' => 'paid', // Kita langsung set 'paid' untuk simulasi, atau 'pending' jika pakai Payment Gateway
            ]);

            // B. Looping untuk simpan ke order_items & OTOMATIS masukkan ke tabel deliveries
            foreach ($cartItems as $item) {

                // Simpan ke detail order item
                $order->items()->create([
                    'menu_id' => $item['id'],
                    'quantity' => $item['qty']
                ]);

                // OTOMATIS INSERT KE TABEL DELIVERIES sebanyak jumlah quantity makanan yang dibeli
                for ($i = 0; $i < $item['qty']; $i++) {
                    Delivery::create([
                        'subscription_id' => $request->subscription_id,
                        'menu_id'         => $item['id'],
                        'driver_id'       => null, // Belum ada driver yang plot, nanti diset oleh Admin/Sistem Kurir
                        'delivery_date'   => $request->delivery_date,
                        'meal_time'       => $request->meal_time,
                        'status'          => 'pending', // Status awal pengantaran kurir
                    ]);
                }
            }
        });

        return redirect()->route('customer.orders')->with('success', 'Pesanan berhasil diproses dan jadwal pengantaran harian Anda telah dibuat!');
    }
}
