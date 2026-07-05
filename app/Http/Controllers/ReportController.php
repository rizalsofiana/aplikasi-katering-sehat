<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        // 2. Siapkan "Base Query" (Kerangka Utama)
        $baseQuery = Order::with('user')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // 3. Hitung angka ringkasan LANGSUNG dari database (bukan dari hasil paginate)
        $totalIncome = $baseQuery->sum('total_amount');
        $totalOrders = $baseQuery->count();

        // 4. Ambil data dengan Pagination (misal 20 baris per halaman)
        // withQueryString() memastikan parameter start_date & end_date terbawa saat pindah halaman
        $orders = $baseQuery->latest()->paginate(20)->withQueryString();

        return view('admin.report', compact('orders', 'startDate', 'endDate', 'totalIncome', 'totalOrders'));
    }
}
