<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-4">

        <div
            class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Topik Konsultasi</span>
                <h3 class="text-base font-bold text-slate-900">{{ $consultation->topic }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Ahli Gizi: <span
                        class="font-semibold text-slate-700">{{ $consultation->nutritionist?->name ?? 'Menunggu Ahli Gizi...' }}</span>
                </p>
            </div>

            <div class="flex items-center space-x-2 self-start sm:self-center">
                @if ($consultation->status === 'open')
                    <span
                        class="bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-xs font-bold capitalize">Baru</span>
                @elseif($consultation->status === 'on_going')
                    <span
                        class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-xs font-bold capitalize">Berjalan</span>
                @else
                    <span
                        class="bg-slate-100 text-slate-600 border border-slate-200 px-3 py-1 rounded-full text-xs font-bold capitalize">Selesai</span>
                @endif

                @if ($consultation->status !== 'closed')
                    <div x-data="{ openConfirm: false }">
                        <button type="button" @click="openConfirm = true"
                            class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-bold px-3 py-1.5 rounded-full transition border border-red-100">
                            Akhiri Sesi
                        </button>

                        <div x-show="openConfirm" x-cloak
                            class="fixed inset-0 z-[150] overflow-y-auto flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95">

                            <div class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl border border-slate-100"
                                @click.away="openConfirm = false">

                                <div class="text-center space-y-2">
                                    <div
                                        class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl">
                                        ⚠️
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-base">Akhiri Sesi Konsultasi?</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        Apakah Anda yakin ingin menyelesaikan sesi ini? Setelah ditutup, Anda tidak
                                        dapat mengirim pesan lagi di obrolan ini.
                                    </p>
                                </div>

                                <div class="flex space-x-2 pt-2">
                                    <button type="button" @click="openConfirm = false"
                                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2 rounded-xl text-xs transition border border-slate-200">
                                        Kembali
                                    </button>

                                    <form action="{{ route('customer.consultation.close', $consultation->id) }}"
                                        method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit"
                                            class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 rounded-xl text-xs transition shadow-md shadow-rose-100">
                                            Ya, Akhiri
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div
            class="bg-slate-50/60 rounded-2xl border border-slate-100 p-4 min-h-[350px] flex flex-col space-y-4 overflow-y-auto">

            @foreach ($consultation->messages as $msg)
                @if ($msg->sender_type === 'customer')
                    <div class="flex flex-col items-end max-w-[85%] self-end">
                        <div
                            class="bg-emerald-600 text-white rounded-2xl rounded-tr-none px-4 py-2.5 shadow-sm text-sm whitespace-pre-line">
                            {{ $msg->message }}
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 mr-1">{{ $msg->created_at->format('H:i') }}
                            WIB</span>
                    </div>
                @else
                    <div class="flex flex-col items-start max-w-[85%] self-start">
                        <span class="text-[10px] font-bold text-slate-400 mb-0.5 ml-1">🩺 Ahli Gizi</span>
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
                <form action="{{ route('customer.consultation.reply', $consultation->id) }}" method="POST"
                    class="flex flex-col space-y-3">
                    @csrf
                    <div>
                        <textarea name="message" rows="3" required placeholder="Ketik balasan atau tanggapan Anda di sini..."
                            class="w-full text-sm border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm">
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div
                class="bg-slate-100 rounded-2xl p-4 text-center text-sm font-medium text-slate-500 border border-slate-200">
                🔒 Sesi konsultasi ini telah ditutup. Silakan buka sesi baru jika memiliki pertanyaan lain.
            </div>
        @endif

    </div>
</x-app-layout>
