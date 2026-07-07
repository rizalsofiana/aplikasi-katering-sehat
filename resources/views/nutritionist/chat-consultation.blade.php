<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-4">

        <div
            class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pasien Konsultasi</span>
                <h3 class="text-base font-bold text-slate-900">{{ $consultation->customer->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Topik: <span
                        class="font-semibold text-slate-700">{{ $consultation->topic }}</span></p>
            </div>

            <div class="flex items-center space-x-2 self-start sm:self-center">
                @if ($consultation->status !== 'closed')
                    <form action="{{ route('nutritionist.consultation.close', $consultation->id) }}" method="POST"
                        onsubmit="return confirm('Apakah konsultasi dengan pasien ini sudah selesai?')">
                        @csrf
                        <button type="submit"
                            class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-bold px-3 py-1.5 rounded-full transition border border-red-100">
                            Tutup Sesi Konsultasi
                        </button>
                    </form>
                @else
                    <span
                        class="bg-slate-100 text-slate-600 border border-slate-200 px-3 py-1 rounded-full text-xs font-bold capitalize">Sesi
                        Selesai</span>
                @endif
            </div>
        </div>

        <div
            class="bg-slate-50/60 rounded-2xl border border-slate-100 p-4 min-h-[350px] flex flex-col space-y-4 overflow-y-auto">

            @foreach ($consultation->messages as $msg)
                @if ($msg->sender_type === 'nutritionist')
                    <div class="flex flex-col items-end max-w-[85%] self-end">
                        <div
                            class="bg-slate-900 text-white rounded-2xl rounded-tr-none px-4 py-2.5 shadow-sm text-sm whitespace-pre-line">
                            {{ $msg->message }}
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 mr-1">{{ $msg->created_at->format('H:i') }}
                            WIB</span>
                    </div>
                @else
                    <div class="flex flex-col items-start max-w-[85%] self-start">
                        <span class="text-[10px] font-bold text-slate-400 mb-0.5 ml-1">👤 Pasien</span>
                        <div
                            class="bg-white text-slate-800 rounded-2xl rounded-tl-none px-4 py-2.5 border border-slate-100 shadow-sm text-sm whitespace-pre-line">
                            {{ $msg->message }}
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 ml-1">{{ $msg->created_at->format('H:i') }}
                            WIB</span>
                    </div>
                @endif
            @endforeach

        </div>

        @if ($consultation->status !== 'closed')
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm">
                <form action="{{ route('nutritionist.consultation.reply', $consultation->id) }}" method="POST"
                    class="flex flex-col space-y-3">
                    @csrf
                    <div>
                        <textarea name="message" rows="4" required
                            placeholder="Tulis saran gizi, rekomendasi asupan kalori, pantangan, atau anjuran menu katering di sini..."
                            class="w-full text-sm border-slate-200 rounded-xl focus:ring-slate-900 focus:border-slate-900 p-3"></textarea>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-[11px] text-slate-400 italic">💡 Tips: Gunakan 'Enter' untuk membuat poin-poin
                            agar mudah dibaca pasien.</p>
                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm">
                            Kirim Jawaban
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
</x-app-layout>
