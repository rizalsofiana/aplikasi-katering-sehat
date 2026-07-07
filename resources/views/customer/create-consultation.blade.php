<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">

        <div>
            <h3 class="text-xl font-bold text-slate-900">🍏 Mulai Konsultasi Gizi</h3>
            <p class="text-sm text-slate-500 mt-0.5">Diskusikan target diet, alergi, atau keluhan pola makan Anda
                langsung dengan Ahli Gizi kami.</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <form action="{{ route('customer.consultation.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="topic" class="block text-sm font-bold text-slate-700 mb-1">Topik / Judul
                        Konsultasi</label>
                    <input type="text" name="topic" id="topic" required
                        placeholder="Contoh: Program Diet Penurunan Berat Badan"
                        class="w-full text-sm border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3">
                    @error('topic')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-bold text-slate-700 mb-1">Detail Pertanyaan /
                        Keluhan</label>
                    <textarea name="message" id="message" rows="5" required
                        placeholder="Ceritakan detail kondisi Anda, misal: Tinggi badan, berat badan, riwayat penyakit, alergi makanan, atau target yang ingin dicapai..."
                        class="w-full text-sm border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3"></textarea>
                    @error('message')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm py-3 rounded-xl transition shadow-sm shadow-emerald-100">
                        Kirim Pertanyaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
