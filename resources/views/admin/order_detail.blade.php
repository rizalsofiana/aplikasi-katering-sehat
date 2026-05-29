<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.orders.index') }}" class="text-slate-400 hover:text-slate-900 font-bold text-sm">←
                Kembali</a>
            <h3 class="text-xl font-bold text-slate-900">Invoice: {{ $order->invoice_number }}</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                    <h4 class="font-bold text-slate-900 text-base border-b border-slate-50 pb-2">Daftar Menu yang Dipesan
                    </h4>

                    <div class="divide-y divide-slate-50">
                        @foreach ($order->items as $item)
                            <div class="py-3 flex justify-between items-center text-sm">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $item->menu->name }}</p>
                                    <p class="text-xs text-slate-400">🔥 {{ $item->menu->nutrition->calories ?? 0 }}
                                        Kkal per porsi</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-900">{{ $item->quantity }} Porsi</p>
                                    <p class="text-xs text-slate-400">Rp 35.000 / porsi</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div
                        class="pt-4 border-t border-slate-100 flex justify-between items-center font-bold text-base text-slate-900">
                        <span>Total Nilai Transaksi:</span>
                        <span class="text-emerald-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-2 text-sm">
                    <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Profil Pemesan</h4>
                    <p class="font-semibold text-slate-800">{{ $order->user->name }}</p>
                    <p class="text-slate-500">{{ $order->user->email }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <h4 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-2">🚚 Penugasan Kurir /
                    Driver</h4>

                @if ($deliveries->whereNull('driver_id')->count() > 0)
                    <form action="{{ route('admin.orders.assign_driver', $order->id) }}" method="POST"
                        class="space-y-4">
                        @csrf

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Pilih Antrean
                                Menu Pengiriman:</label>
                            <div
                                class="max-h-[180px] overflow-y-auto space-y-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                @foreach ($deliveries->whereNull('driver_id') as $deliv)
                                    <label
                                        class="flex items-center space-x-2 text-xs font-medium text-slate-700 cursor-pointer">
                                        <input type="checkbox" name="delivery_ids[]" value="{{ $deliv->id }}" checked
                                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        <span>{{ $deliv->menu->name }} ({{ ucfirst($deliv->meal_time) }} -
                                            {{ \Carbon\Carbon::parse($deliv->delivery_date)->format('d M') }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Pilih
                                Personel Driver:</label>
                            <select name="driver_id"
                                class="block w-full rounded-xl border-slate-200 text-xs focus:border-emerald-500"
                                required>
                                <option value="">-- Pilih Kurir --</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }} (Active)</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                            class="w-full bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs py-2.5 rounded-xl transition">
                            Tugaskan Kurir Terpilih
                        </button>
                    </form>
                @else
                    <div
                        class="bg-emerald-50 text-emerald-800 border border-emerald-100 p-4 rounded-xl text-xs font-semibold text-center italic">
                        🎉 Semua antrean pengantaran makanan untuk invoice ini sudah berhasil di-plot ke Driver
                        masing-masing!
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
