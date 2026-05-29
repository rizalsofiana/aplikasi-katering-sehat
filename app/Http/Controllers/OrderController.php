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
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'delivery_date' => 'required|date|after_or_equal:today',
            'meal_time' => 'required|in:breakfast,lunch,dinner',
            'cart_data' => 'required|json'
        ]);

        $cartItems = json_decode($request->cart_data, true);
        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda kosong!');
        }

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += (35000 * $item['qty']);
        }

        DB::transaction(function () use ($request, $cartItems, $totalAmount) {

            $order = Order::create([
                'user_id' => Auth::id(),
                'subscription_id' => $request->subscription_id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(5)) . '-' . now()->format('YmdHis'),
                'total_amount' => $totalAmount,
                'status' => 'paid',
            ]);

            foreach ($cartItems as $item) {

                $order->items()->create([
                    'menu_id' => $item['id'],
                    'quantity' => $item['qty']
                ]);

                for ($i = 0; $i < $item['qty']; $i++) {
                    Delivery::create([
                        'subscription_id' => $request->subscription_id,
                        'menu_id'         => $item['id'],
                        'driver_id'       => null,
                        'delivery_date'   => $request->delivery_date,
                        'meal_time'       => $request->meal_time,
                        'status'          => 'pending',
                    ]);
                }
            }
        });

        return redirect()->route('customer.orders')->with('success', 'Pesanan berhasil diproses dan jadwal pengantaran harian Anda telah dibuat!');
    }
}
