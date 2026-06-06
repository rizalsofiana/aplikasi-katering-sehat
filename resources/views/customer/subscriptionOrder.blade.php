<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">

        <a href="{{ route('customer.subscriptions.index') }}"
            class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800 space-x-1">
            <span>← Kembali ke Pilihan Paket</span>
        </a>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900">Konfirmasi Pembayaran Langganan 💳</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tinjau kembali detail masa aktif komitmen paket kesehatan Anda.
                </p>
            </div>

            <div class="p-6 space-y-5">

                <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100 flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-emerald-900 text-sm">{{ $package->package_name }}</h4>
                        <p class="text-xs text-emerald-700">Durasi Kontrak Program: <strong>{{ $package->total_days }}
                                Hari</strong></p>
                    </div>
                    <span class="text-sm font-black text-emerald-900">Rp
                        {{ number_format($package->price, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Simulasi Jadwal Pengiriman</p>

                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                        <div>
                            <p class="text-slate-400 font-medium">Tanggal Mulai Program</p>
                            <p class="font-bold text-slate-800 mt-0.5">{{ $startDate->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 font-medium">Tanggal Selesai Berlangganan</p>
                            <p class="font-bold text-slate-800 mt-0.5">{{ $endDate->format('d M Y') }}</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 italic">Note: Jika Anda saat ini sedang memiliki paket
                        langganan aktif, paket baru ini otomatis akan dijadwalkan berjalan tepat setelah masa aktif
                        paket sebelumnya berakhir.</p>
                </div>

                <div class="border-t border-slate-100 pt-4 flex items-center justify-between text-sm">
                    <span class="text-slate-500 font-bold">Total Pembayaran</span>
                    <span class="text-lg font-black text-slate-950">Rp
                        {{ number_format($package->price, 0, ',', '.') }}</span>
                </div>

                <form action="{{ route('customer.subscriptions.store') }}" method="POST" class="pt-2">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">

                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition shadow-md shadow-emerald-100 text-xs uppercase tracking-wider">
                        Konfirmasi & Aktifkan Langganan Sekarang
                    </button>
                </form>

            </div>
        </div>

    </div>
</x-app-layout>
