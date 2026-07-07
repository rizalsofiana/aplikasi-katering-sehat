<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900">💬 Konsultasi Gizi</h3>
                <p class="text-sm text-slate-500 mt-0.5">Riwayat diskusi dan solusi pola makan sehat bersama Ahli Gizi.
                </p>
            </div>

            <button type="button" onclick="openModal()"
                class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm shadow-emerald-100 self-start sm:self-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Mulai Sesi Baru
            </button>
        </div>

        <div class="space-y-3">
            @forelse($consultations as $consultation)
                <a href="{{ route('customer.consultation.show', $consultation->id) }}"
                    class="block bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:border-emerald-200 hover:shadow-md transition duration-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                @if ($consultation->status === 'open')
                                    <span
                                        class="bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">Baru</span>
                                @elseif($consultation->status === 'on_going')
                                    <span
                                        class="bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">Berjalan</span>
                                @else
                                    <span
                                        class="bg-slate-100 text-slate-600 border border-slate-200 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">Selesai</span>
                                @endif

                                <span
                                    class="text-xs text-slate-400 font-mono">{{ $consultation->created_at->format('d M Y') }}</span>
                            </div>

                            <h4 class="text-base font-bold text-slate-800 line-clamp-1">{{ $consultation->topic }}</h4>

                            <p class="text-xs text-slate-500 flex items-center">
                                <span class="mr-1">🩺</span> Ahli Gizi:
                                <span class="font-semibold text-slate-700 ml-1">
                                    {{ $consultation->nutritionist?->name ?? 'Menunggu konfirmasi tim medis...' }}
                                </span>
                            </p>
                        </div>

                        <div class="hidden sm:block text-slate-300 px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>

                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl p-12 border border-slate-100 shadow-sm text-center max-w-xl mx-auto">
                    <div class="text-5xl mb-4">🥦</div>
                    <h4 class="text-slate-900 font-bold text-lg">Belum Ada Riwayat Konsultasi</h4>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Punya keluhan berat badan atau bingung
                        memilih menu katering yang pas? Yuk, konsultasikan langsung dengan Ahli Gizi kami!</p>
                    <div class="mt-6">
                        <button type="button" onclick="openModal()"
                            class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm">
                            Konsultasi Sekarang
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($consultations->hasPages())
            <div class="pt-2">
                {{ $consultations->links() }}
            </div>
        @endif

    </div>

    <div id="consultationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" onclick="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">

                <div class="bg-white px-6 pt-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900" id="modal-title">🍏 Mulai Konsultasi Gizi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Sampaikan target atau keluhan pola makan Anda.</p>
                    </div>
                    <button type="button" onclick="closeModal()"
                        class="text-slate-400 hover:text-slate-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('customer.consultation.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 py-4 space-y-4">
                        <div>
                            <label for="topic" class="block text-sm font-bold text-slate-700 mb-1">Topik / Judul
                                Konsultasi</label>
                            <input type="text" name="topic" id="topic" value="{{ old('topic') }}" required
                                placeholder="Contoh: Program Diet Penurunan Berat Badan"
                                class="w-full text-sm border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3">
                            @error('topic')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-bold text-slate-700 mb-1">Detail Pertanyaan
                                / Keluhan</label>
                            <textarea name="message" id="message" rows="4" required
                                placeholder="Ceritakan detail kondisi Anda, misal: Tinggi badan, berat badan, riwayat penyakit, alergi, atau target gizi yang diinginkan..."
                                class="w-full text-sm border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div
                        class="bg-slate-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeModal()"
                            class="w-full sm:w-auto bg-white hover:bg-slate-100 text-slate-700 font-bold text-sm px-5 py-2.5 rounded-xl transition border border-slate-200">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm">
                            Kirim Pertanyaan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('consultationModal');

        function openModal() {
            modal.classList.remove('hidden');
            // Mencegah background scroll saat modal terbuka
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('hidden');
            // Mengembalikan scroll background asal
            document.body.style.overflow = 'auto';
        }

        // Otomatis buka modal jika ada error validasi dari Laravel
        @if ($errors->has('topic') || $errors->has('message'))
            openModal();
        @endif
    </script>
</x-app-layout>
