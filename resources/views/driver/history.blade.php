<x-app-layout>
    <div class="py-6 px-4 sm:px-6 max-w-3xl mx-auto space-y-6 mb-20">

        <!-- Header -->
        <div>
            <h3 class="text-xl font-bold text-slate-900">Riwayat Pengantaran 📜</h3>
            <p class="text-sm text-slate-500 mt-0.5">Daftar semua pesanan yang telah berhasil Anda antarkan.</p>
        </div>

        <!-- List Riwayat (Cards) -->
        <div class="space-y-4">
            @forelse ($groupedDeliveries->groupBy('order_id') as $orderId => $group)
                @php
                    // Ambil baris pertama dari grup ini untuk mewakili data alamat & customer
                    $firstDeliv = $group->first();
                    $order = $firstDeliv->order;
                @endphp

                <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex flex-col space-y-4">

                    <!-- Bagian Atas: Invoice, Waktu & Badge -->
                    <div class="flex justify-between items-start border-b border-slate-50 pb-3">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Invoice</p>
                            <p class="text-sm font-bold text-slate-900">{{ $order->invoice_number }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Selesai: {{ $firstDeliv->updated_at->format('d M Y, H:i') }} WIB
                            </p>
                        </div>
                        <span
                            class="bg-emerald-50 text-emerald-600 border border-emerald-200 text-[10px] font-bold px-2 py-1 rounded-lg flex items-center">
                            ✓ Selesai
                        </span>
                    </div>

                    <!-- Bagian Tengah: Info Customer & Alamat -->
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $order->user->name ?? 'Customer' }}</p>
                        <div class="flex items-start space-x-1.5 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 mt-0.5 shrink-0"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-xs text-slate-500 leading-relaxed">{{ $firstDeliv->delivery_address }}</p>
                        </div>
                    </div>

                    <!-- Bagian Bawah: List Menu yang diantar dalam 1 Invoice -->
                    <div class="bg-slate-50 rounded-xl p-3 space-y-2 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Detail Pesanan:
                        </p>

                        @foreach ($group as $delivery)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="text-base">🍱</span>
                                    <span
                                        class="font-semibold text-slate-800">{{ $delivery->menu->name ?? 'Menu Katering' }}</span>
                                </div>
                                <!-- Jika Anda punya data porsi/qty di tabel delivery, bisa ditampilkan di sini -->
                                <span class="text-xs text-slate-500 font-medium">✓ Dikirim</span>
                            </div>
                        @endforeach
                    </div>

                </div>
            @empty
                <!-- Tampilan jika riwayat kosong -->
                <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm text-center">
                    <div class="text-4xl mb-3">🛵</div>
                    <h4 class="text-slate-900 font-bold">Belum Ada Riwayat</h4>
                    <p class="text-sm text-slate-500 mt-1">Anda belum menyelesaikan pengantaran apa pun. Tetap semangat!
                    </p>
                </div>
            @endforelse
        </div>
        @if ($groupedDeliveries->hasPages())
            <div class="pt-6">
                {{ $groupedDeliveries->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
