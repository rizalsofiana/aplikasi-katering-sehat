<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <!-- Header & Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900">📊 Laporan Penjualan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Pantau ringkasan pendapatan katering berdasarkan periode.</p>
            </div>

            <!-- Form Filter -->
            <form action="{{ route('admin.reports.sales') }}" method="GET"
                class="flex items-center space-x-2 bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
                <div>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="text-sm border-slate-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <span class="text-slate-400 text-sm">s/d</span>
                <div>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="text-sm border-slate-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button type="submit"
                    class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-4 py-2 rounded-lg transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- Ringkasan Angka (Summary Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Card Pendapatan -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
                <div class="bg-emerald-100 p-4 rounded-full text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</p>
                    <h4 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                </div>
            </div>

            <!-- Card Total Pesanan -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
                <div class="bg-blue-100 p-4 rounded-full text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pesanan Selesai (Lunas)</p>
                    <h4 class="text-2xl font-bold text-slate-900">{{ $totalOrders }} <span
                            class="text-sm font-normal text-slate-500 capitalize">Transaksi</span></h4>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="p-4">No. Invoice</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Tanggal Order</th>
                            <th class="p-4 text-right">Nilai Transaksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-mono font-bold text-slate-900">{{ $order->invoice_number }}</td>
                                <td class="p-4">
                                    <p class="font-semibold text-slate-800">{{ $order->user->name }}</p>
                                </td>
                                <td class="p-4 text-slate-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="p-4 font-bold text-emerald-600 text-right">Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400 italic">Tidak ada transaksi
                                    lunas pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="p-4 border-t border-slate-100 bg-white">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
