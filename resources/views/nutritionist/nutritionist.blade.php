<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-xl font-bold text-slate-900">Ruang Kerja Ahli Gizi</h3>
            <p class="text-sm text-slate-500 mt-0.5">Validasi kecukupan gizi makro dan kelola konsultasi medis pelanggan.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Konsultasi Masuk</span>
                <h4 class="text-3xl font-black text-amber-600 mt-2">{{ $antreanKonsultasi }} Antrean</h4>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Konsultasi Berlangsung</span>
                <h4 class="text-3xl font-black text-indigo-600 mt-2">{{ $konsultasiBerlangsung }} Konsultasi</h4>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menu Terkompilasi AI</span>
                <h4 class="text-3xl font-black text-slate-800 mt-2">{{ $menuTerkompilasiAI }} Menu</h4>
            </div>
        </div>
    </div>
</x-app-layout>
