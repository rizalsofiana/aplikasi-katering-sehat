<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <div>
            <h3 class="text-xl font-bold text-slate-900">🛵 Ruang Kerja Driver (Kurir)</h3>
            <p class="text-sm text-slate-500 mt-0.5">Ambil pesanan katering yang tersedia secara mandiri atau antar tugas
                yang diberikan Admin.</p>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
                🎉 {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm font-semibold">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            <div class="space-y-4">
                <h4 class="font-bold text-slate-900 text-base flex items-center justify-between">
                    <span>📦 Lowongan Pengiriman Terbuka</span>
                    <span
                        class="bg-slate-100 text-slate-700 text-xs px-2.5 py-1 rounded-full font-bold">{{ $availableDeliveries->count() }}
                        Tersedia</span>
                </h4>

                <div class="space-y-3">
                    @forelse($availableDeliveries as $deliv)
                        <div
                            class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="space-y-1">
                                <span
                                    class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-100 uppercase tracking-wider">
                                    {{ $deliv->status }}
                                </span>
                                <h5 class="font-bold text-slate-800 text-sm mt-1">{{ $deliv->menu->name }}</h5>
                                <p class="text-xs text-slate-400">📅
                                    {{ \Carbon\Carbon::parse($deliv->delivery_date)->format('d M Y') }} | 🕒 Waktu:
                                    <span class="font-semibold text-slate-700">{{ ucfirst($deliv->meal_time) }}</span>
                                </p>
                            </div>

                            <div>
                                <form action="{{ route('deliveries.take', $deliv->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2 px-4 rounded-xl transition shadow-sm">
                                        Ambil Tugas ➔
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div
                            class="bg-white p-8 text-center text-slate-400 rounded-2xl border border-dashed border-slate-200 text-xs italic">
                            Belum ada pesanan katering kosong yang bebas diambil saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-4">
                <h4 class="font-bold text-slate-900 text-base flex items-center justify-between">
                    <span>📋 Daftar Pengantaran Aktif Anda</span>
                    <span
                        class="bg-emerald-50 text-emerald-700 text-xs px-2.5 py-1 rounded-full font-bold">{{ $myDeliveries->count() }}
                        Proses</span>
                </h4>

                <div class="space-y-3">
                    @forelse($myDeliveries as $myDeliv)
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="font-bold text-slate-800 text-sm">{{ $myDeliv->menu->name }}</h5>
                                    <p class="text-xs text-slate-400">Kirim:
                                        {{ \Carbon\Carbon::parse($myDeliv->delivery_date)->format('d M Y') }}
                                        ({{ ucfirst($myDeliv->meal_time) }})
                                    </p>
                                </div>

                                <div>
                                    @if ($myDeliv->status == 'on_the_way')
                                        <span
                                            class="bg-sky-50 text-sky-700 border border-sky-100 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider animate-pulse">🚚
                                            On The Way</span>
                                    @elseif($myDeliv->status == 'cooking')
                                        <span
                                            class="bg-amber-50 text-amber-700 border border-amber-100 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">🍳
                                            Dapur: Cooking</span>
                                    @elseif($myDeliv->status == 'delivered')
                                        <span
                                            class="bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">✓
                                            Delivered</span>
                                    @else
                                        <span
                                            class="bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">📦
                                            Ready Pickup</span>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-50 flex justify-end">
                                @if ($myDeliv->status == 'cooking' || $myDeliv->status == 'ready')
                                    <form action="{{ route('deliveries.otw', $myDeliv->id) }}" method="POST"
                                        onsubmit="return confirm('Mulai mengantarkan makanan ini sekarang?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs py-1.5 px-3 rounded-xl transition">
                                            🚀 Kirim Sekarang (Set OTW)
                                        </button>
                                    </form>
                                @elseif($myDeliv->status == 'on_the_way')
                                    <form action="{{ route('deliveries.delivered', $myDeliv->id) }}" method="POST"
                                        onsubmit="return confirm('Konfirmasi bahwa makanan telah diterima oleh customer?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-700 me-1 text-white font-bold text-xs py-1.5 px-3 rounded-xl transition shadow-md shadow-emerald-100 flex items-center space-x-1">
                                            <span>✓ Selesai Diantar (Set Delivered)</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('deliveries.failed', $myDeliv->id) }}" method="POST"
                                        onsubmit="return confirm('Konfirmasi bahwa makanan batal diantarkan?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-1.5 px-3 rounded-xl transition shadow-md shadow-emerald-100 flex items-center space-x-1">
                                            <span>x Batalkan (Set Failed)</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-semibold text-slate-400 italic">Pengiriman Selesai
                                        ✨</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div
                            class="bg-white p-8 text-center text-slate-400 rounded-2xl border border-dashed border-slate-200 text-xs italic">
                            Anda belum memiliki jadwal pengantaran makanan hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
