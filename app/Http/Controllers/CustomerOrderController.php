<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $customerId = Auth::id();

        // 1. Mulai query dari model Order yang dimiliki user yang login
        // Eager load 'items.menu.nutrition' untuk menghindari N+1 query masalah gizi harian
        $query = Order::with(['items.menu.nutrition'])
            ->where('user_id', $customerId)
            ->latest();

        // 2. Fitur Pencarian Berdasarkan Nama Menu (mencari ke dalam relasi items -> menu)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('items.menu', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // 3. Fitur Filter Berdasarkan Tanggal Transaksi Order (created_at)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // 4. Ambil data dengan Pagination (misal 5 data per halaman) dan kunci query string URL
        $orders = $query->paginate(5)->withQueryString();

        return view('customer.history', compact('orders'));
    }
}
