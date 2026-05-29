<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900">📦 Kelola Pesanan Customer</h3>
                <p class="text-sm text-slate-500 mt-0.5">Pantau transaksi masuk, konfirmasi pembayaran, dan atur logistik
                    kurir pengantaran.</p>
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="p-4">No. Invoice</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Total Bayar</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Tanggal Order</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-mono font-bold text-slate-900">{{ $order->invoice_number }}</td>
                                <td class="p-4">
                                    <p class="font-semibold text-slate-800">{{ $order->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->user->email }}</p>
                                </td>
                                <td class="p-4 font-bold text-slate-900">Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="p-4">
                                    @if ($order->status == 'paid')
                                        <span
                                            class="bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-emerald-100">Paid
                                            (Lunas)</span>
                                    @else
                                        <span
                                            class="bg-amber-50 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-amber-100">Pending</span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-400 text-xs">{{ $order->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="p-4 flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-1.5 px-3 rounded-lg transition">
                                        Detail & Driver
                                    </a>

                                    @if ($order->status == 'pending')
                                        <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST"
                                            onsubmit="return confirm('Konfirmasi pembayaran untuk order ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-1.5 px-3 rounded-lg transition">
                                                ✓ Konfirmasi
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 italic">Belum ada pesanan masuk
                                    dari pelanggan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
