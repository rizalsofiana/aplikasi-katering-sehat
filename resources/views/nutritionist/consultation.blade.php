<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

        <div>
            <h3 class="text-xl font-bold text-slate-900">🩺 Ruang Kerja Ahli Gizi</h3>
            <p class="text-sm text-slate-500 mt-0.5">Kelola konsultasi gizi, berikan rekomendasi menu, dan pantau keluhan
                diet pelanggan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('nutritionist.consultation.index', ['status' => 'open']) }}"
                class="bg-white border {{ $status === 'open' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-100' }} p-5 rounded-2xl shadow-sm flex items-center justify-between transition">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Antrean Baru</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalOpen }}</h3>
                </div>
                <div
                    class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold">
                    📥</div>
            </a>

            <a href="{{ route('nutritionist.consultation.index', ['status' => 'on_going']) }}"
                class="bg-white border {{ $status === 'on_going' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-100' }} p-5 rounded-2xl shadow-sm flex items-center justify-between transition">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Konsultasi Aktif Saya</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalOnGoing }}</h3>
                </div>
                <div
                    class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold">
                    💬</div>
            </a>
        </div>

        <div class="flex border-b border-slate-200 gap-6 text-sm font-semibold">
            <a href="{{ route('nutritionist.consultation.index', ['status' => 'open']) }}"
                class="pb-3 border-b-2 {{ $status === 'open' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Antrean Umum ({{ $totalOpen }})
            </a>
            <a href="{{ route('nutritionist.consultation.index', ['status' => 'on_going']) }}"
                class="pb-3 border-b-2 {{ $status === 'on_going' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Sedang Ditangani
            </a>
            <a href="{{ route('nutritionist.consultation.index', ['status' => 'closed']) }}"
                class="pb-3 border-b-2 {{ $status === 'closed' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Riwayat Selesai
            </a>
        </div>

        <div class="space-y-3">
            @forelse($consultations as $item)
                <div
                    class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold text-slate-800 bg-slate-100 px-2.5 py-0.5 rounded-md">👤
                                {{ $item->customer->name }}</span>
                            <span
                                class="text-xs text-slate-400 font-mono">{{ $item->created_at->format('d M Y, H:i') }}
                                WIB</span>
                        </div>
                        <h4 class="text-base font-bold text-slate-900 mt-1">{{ $item->topic }}</h4>
                    </div>

                    <div>
                        <a href="{{ route('nutritionist.consultation.show', $item->id) }}"
                            class="inline-flex items-center justify-center bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm w-full sm:w-auto text-center">
                            @if ($item->status === 'open')
                                Jawab & Klaim Pasien
                            @else
                                Buka Obrolan
                            @endif
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-100">
                    <div class="text-4xl mb-2">🎉</div>
                    <h5 class="text-slate-800 font-bold text-base">Tidak Ada Data</h5>
                    <p class="text-xs text-slate-400 mt-0.5">Semua tugas pada kategori ini telah selesai atau belum
                        tersedia.</p>
                </div>
            @endforelse
        </div>

        @if ($consultations->hasPages())
            <div class="pt-2">
                {{ $consultations->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
