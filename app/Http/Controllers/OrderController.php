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

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += ($item['price'] * $item['qty']);
        }

        // 🟢 Ubah closure agar me-return data $order keluar dari DB Transaction
        $order = DB::transaction(function () use ($request, $cartItems, $totalAmount) {

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

                for ($i = 0; $i < $item['qty']; $i++) {
                    Delivery::create([
                        'order_id'        => $order->id,
                        'menu_id'         => $item['id'],
                        'driver_id'       => null,
                        'delivery_date'   => $request->delivery_date,
                        'delivery_address' => $request->delivery_address,
                        'latitude'        => $request->latitude ?? null,
                        'longitude'       => $request->longitude ?? null,
                        'meal_time'       => $request->meal_time,
                        'status'          => 'cooking', // 💡 Disarankan default 'pending_payment' sampai lunas
                        'notes'           => null,
                    ]);
                }
            }

            return $order; // Kembalikan data order
        });

        // ================= 🟢 INTEGRASI MIDTRANS SNAP =================
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $order->invoice_number, // Gunakan nomor invoice unik Anda
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ];

        try {
            // Minta Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // Simpan token ke database orders
            $order->update(['snap_token' => $snapToken]);

            // Alihkan customer ke halaman pembayaran khusus beserta ID ordernya
            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke sistem pembayaran: ' . $e->getMessage());
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
            ->whereNull('driver_id')
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

        // A. List pesanan yang BELUM memiliki driver menggunakan nested relationship 'order.user'
        $availableDeliveries = Delivery::with(['menu', 'order'])
            ->whereNull('driver_id')
            ->whereIn('status', ['cooking'])
            ->latest()
            ->get();

        // B. List pesanan yang sedang diambil/ditugaskan ke driver ini
        $myDeliveries = Delivery::with(['menu', 'order'])
            ->where('driver_id', $driverId)
            ->whereIn('status', ['cooking', 'on_the_way'])
            ->latest()
            ->get();

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
        $delivery = Delivery::where('id', $id)->where('driver_id', Auth::id())->firstOrFail();

        $delivery->update([
            'status' => 'on_the_way'
        ]);

        return redirect()->back()->with('success', 'Status diperbarui: Pesanan sedang dalam perjalanan! Hati-hati di jalan.');
    }

    public function updateStatusToDelivered(int $id)
    {
        // Pastikan delivery ini memang milik driver yang sedang login
        $delivery = Delivery::where('id', $id)->where('driver_id', Auth::id())->firstOrFail();

        $delivery->update([
            'status' => 'delivered'
        ]);

        return redirect()->back()->with('success', 'Alhamdulillah, pesanan telah sukses diantarkan ke pelanggan!');
    }

    public function updateStatusToFailed(int $id)
    {
        // Pastikan delivery ini memang milik driver yang sedang login
        $delivery = Delivery::where('id', $id)->where('driver_id', Auth::id())->firstOrFail();

        $delivery->update([
            'status' => 'failed'
        ]);

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
            'driver_id' => 'required|exists:users,id',
            'delivery_ids' => 'required|array',
            'delivery_ids.*' => 'exists:deliveries,id'
        ]);

        // Update driver_id pada tabel deliveries untuk item-item yang dipilih
        Delivery::whereIn('id', $request->delivery_ids)->update([
            'driver_id' => $request->driver_id
        ]);

        return redirect()->back()->with('success', 'Driver berhasil ditugaskan untuk pengantaran menu!');
    }
}
