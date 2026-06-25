<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-xl font-bold text-slate-900">Riwayat Pesanan Katering Anda</h3>
            <p class="text-sm text-slate-500 mt-0.5">Pantau status transaksi, invoice, dan rekap menu diet sehat Anda.
            </p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <form action="{{ route('customer.orders.history') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Nama
                        Menu</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Contoh: Oatmeal / Salmon..."
                        class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Dari
                        Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai
                        Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                </div>

                <div class="flex space-x-2">
                    <button type="submit"
                        class="flex-1 bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs py-3 rounded-xl transition shadow-sm text-center">
                        🔍 Filter & Cari
                    </button>
                    @if (request()->anyFilled(['search', 'start_date', 'end_date']))
                        <a href="{{ route('customer.orders.history') }}"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs py-3 px-4 rounded-xl transition text-center flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <div class="space-y-4">
            @forelse($orders as $order)
                <div
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden hover:border-slate-200 transition">

                    <div
                        class="bg-slate-50/70 px-5 py-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div class="flex items-center space-x-3">
                            <span class="font-mono font-bold text-slate-700">{{ $order->invoice_number }}</span>
                            <span class="text-slate-400">•</span>
                            <span
                                class="text-slate-500">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</span>
                        </div>

                        <div>
                            @if ($order->status == 'pending')
                                <span
                                    class="bg-amber-50 text-amber-700 border border-amber-100 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Menunggu
                                    Pembayaran</span>
                            @elseif($order->status == 'paid' || $order->status == 'paid')
                                <span
                                    class="bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Selesai</span>
                            @elseif($order->status == 'cancelled' || $order->status == 'failed')
                                <span
                                    class="bg-rose-50 text-rose-700 border border-rose-100 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Batal
                                    / Gagal</span>
                            @else
                                <span
                                    class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">{{ $order->status }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-5 divide-y divide-slate-50 space-y-4">
                        @foreach ($order->items as $item)
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 {{ !$loop->first ? 'pt-4' : '' }}">

                                <div class="space-y-1 flex-1">
                                    <div class="flex items-baseline space-x-2">
                                        <h4 class="font-bold text-slate-900 text-base">{{ $item->menu->name }}</h4>
                                        <span class="text-xs text-slate-400 font-medium">x{{ $item->quantity }}
                                            porsi</span>
                                    </div>

                                    @if ($item->menu->nutrition)
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                                            <span
                                                class="bg-emerald-50 text-emerald-800 font-bold px-2 py-0.5 rounded text-[11px]">{{ $item->menu->nutrition->calories }}
                                                kkal</span>
                                            <span>Protein: <strong
                                                    class="text-slate-600">{{ $item->menu->nutrition->protein_g }}g</strong></span>
                                            <span>Karbo: <strong
                                                    class="text-slate-600">{{ $item->menu->nutrition->carbs_g }}g</strong></span>
                                            <span>Lemak: <strong
                                                    class="text-slate-600">{{ $item->menu->nutrition->fat_g }}g</strong></span>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <div
                        class="bg-slate-50/30 px-5 py-3 border-t border-slate-50 flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Total Belanja</span>
                        <span class="font-bold text-slate-900 text-base">Rp
                            {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>

                </div>
            @empty
                <div
                    class="bg-white p-12 text-center text-slate-400 rounded-2xl border border-dashed border-slate-200 text-sm italic">
                    @if (request()->anyFilled(['search', 'start_date', 'end_date']))
                        Tidak ada riwayat orderan yang cocok dengan kriteria pencarian/filter Anda.
                    @else
                        Anda belum pernah melakukan pemesanan katering sehat.
                    @endif
                </div>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $orders->links() }}
        </div>

    </div>
</x-app-layout>
