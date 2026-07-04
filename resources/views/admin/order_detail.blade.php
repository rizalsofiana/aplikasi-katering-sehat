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

                @php
                    $driver_name = $deliveries->first();
                @endphp

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-2 text-sm">
                    <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Diantar Oleh:</h4>
                    <p class="font-semibold text-slate-800">{{ $driver_name->driver->name ?? 'Belum ditugaskan' }}
                    </p>
                    <p class="text-slate-500">{{ $driver_name->driver->email ?? 'Email tidak tersedia' }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <h4 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-2">🚚 Penugasan Kurir /
                    Driver</h4>

                @php
                    // Cek apakah ada pengiriman yang gagal (menggunakan skenario 1, jadi kita ambil salah satu alasannya)
                    $failedDeliveries = $deliveries->where('status', 'failed');
                    $hasFailed = $failedDeliveries->count() > 0;
                    $failureReason = $hasFailed ? $failedDeliveries->first()->failure_reason : '';
                @endphp

                @if ($hasFailed)
                    <!-- TAMPILAN JIKA PENGIRIMAN GAGAL -->
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-rose-100 text-rose-600 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-rose-800">Pengiriman Gagal!</h5>
                                <p class="text-xs text-rose-600 mt-1">Alasan Driver: <span
                                        class="font-bold italic">"{{ $failureReason }}"</span></p>
                            </div>
                        </div>

                        <!-- Form Re-Assign -->
                        <form action="{{ route('admin.orders.reassign_driver', $order->id) }}" method="POST"
                            class="space-y-3 pt-3 border-t border-rose-200/60">
                            @csrf
                            <div class="space-y-1">
                                <label
                                    class="block text-[10px] font-bold text-rose-500 uppercase tracking-wider">Tugaskan
                                    Kurir Pengganti:</label>
                                <select name="driver_id"
                                    class="block w-full rounded-xl border-rose-200 text-xs focus:border-rose-500 focus:ring-rose-500 bg-white"
                                    required>
                                    <option value="">-- Pilih Kurir Baru --</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }} (Active)</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs py-2.5 rounded-xl transition shadow-md shadow-rose-200">
                                🔄 Tugaskan Ulang & Lanjutkan
                            </button>
                        </form>
                    </div>
                @elseif ($deliveries->whereNull('driver_id')->count() > 0)
                    <!-- TAMPILAN NORMAL JIKA BELUM ADA KURIR -->
                    <form action="{{ route('admin.orders.assign_driver', $order->id) }}" method="POST"
                        class="space-y-4">
                        @csrf
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
                    <!-- TAMPILAN JIKA SEMUA SUDAH AMAN -->
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
