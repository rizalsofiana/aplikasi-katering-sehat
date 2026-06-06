<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-8">

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-xl font-bold text-slate-900">Pilih Paket Langganan Katering Diet</h3>
            <p class="text-sm text-slate-500 mt-0.5">Investasikan kesehatan Anda dengan program katering sehat yang
                terhitung nutrisinya secara konsisten.</p>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if (isset($activeSubscription))
            <div
                class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-2xl text-white shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <span
                        class="bg-white/20 text-white font-bold text-[10px] px-2 py-0.5 rounded-md uppercase tracking-wider">Paket
                        Aktif Anda</span>
                    <h4 class="text-lg font-black">{{ $activeSubscription->package->package_name }}</h4>
                    <p class="text-xs text-emerald-100">
                        Periode:
                        <strong>{{ \Carbon\Carbon::parse($activeSubscription->start_date)->format('d M Y') }}</strong>
                        s/d <strong>{{ \Carbon\Carbon::parse($activeSubscription->end_date)->format('d M Y') }}</strong>
                    </p>
                </div>
                <div class="bg-white/10 px-4 py-2 rounded-xl text-center border border-white/10">
                    <p class="text-[10px] uppercase font-bold text-emerald-200">Sisa Waktu</p>
                    <p class="text-xl font-black">
                        {{ \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($activeSubscription->end_date), false) }}
                        Hari Lagi</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
            @forelse($packages as $package)
                @php
                    // Logika penanda visual paket terpopuler / rekomendasi (misal paket mingguan/bulanan)
                    $isPopular = $package->duration_type === 'monthly' || $package->total_days >= 30;
                @endphp

                <div
                    class="bg-white rounded-3xl border {{ $isPopular ? 'border-emerald-500 ring-2 ring-emerald-500/10' : 'border-slate-100' }} shadow-sm overflow-hidden flex flex-col justify-between relative transition hover:shadow-md">

                    @if ($isPopular)
                        <div
                            class="absolute top-0 right-0 bg-emerald-500 text-white text-[10px] font-black px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                            Paling Hemat ✨
                        </div>
                    @endif

                    <div class="p-6 space-y-6">
                        <div class="space-y-1">
                            <h4 class="text-lg font-bold text-slate-900">{{ $package->package_name }}</h4>
                            <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">
                                {{ $package->total_days }} Hari Full Program</p>
                        </div>

                        <div class="flex items-baseline">
                            <span class="text-2xl font-black text-slate-900">Rp
                                {{ number_format($package->price, 0, ',', '.') }}</span>
                            <span class="text-xs text-slate-400 font-medium ml-1">/paket</span>
                        </div>

                        <ul class="space-y-3 text-xs text-slate-600 border-t border-slate-50 pt-4">
                            <li class="flex items-center space-x-2.5">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>{{ $package->total_days }}x Pengiriman Makan Siang/Malam</span>
                            </li>
                            <li class="flex items-center space-x-2.5">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Bebas Konsultasi Makronutrisi Gizi</span>
                            </li>
                            <li class="flex items-center space-x-2.5">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Pengantaran Box Higienis oleh Kurir</span>
                            </li>
                            <li class="flex items-center space-x-2.5">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Bisa skip hari (pause pengiriman)</span>
                            </li>
                        </ul>
                    </div>

                    <div class="p-6 bg-slate-50/50 border-t border-slate-50">
                        <a href="{{ route('customer.subscriptions.checkout', $package->id) }}"
                            class="block w-full text-center font-bold text-xs py-3 px-4 rounded-xl transition shadow-sm 
                           {{ $isPopular ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white' }}">
                            Pilih & Mulai Langganan
                        </a>
                    </div>

                </div>
            @empty
                <div
                    class="col-span-3 bg-white p-12 text-center text-slate-400 rounded-2xl border border-dashed border-slate-200 text-sm italic">
                    Belum ada pilihan program paket langganan yang tersedia saat ini.
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
