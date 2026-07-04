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
                    <span class="bg-slate-100 text-slate-700 text-xs px-2.5 py-1 rounded-full font-bold">
                        {{ $availableDeliveries->count() }} Order Tersedia
                    </span>
                </h4>

                <div class="space-y-3">
                    @forelse($availableDeliveries as $orderId => $deliveries)
                        @php
                            // Mengambil data representatif untuk satu invoice
                            $firstDeliv = $deliveries->first();
                        @endphp
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-4">

                            <div class="flex items-start justify-between">
                                <div class="space-y-1">
                                    <span
                                        class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-100 uppercase tracking-wider">
                                        Invoice Number: #{{ $deliveries->first()->order->invoice_number }}
                                    </span><br>
                                    <span
                                        class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-100 uppercase tracking-wider">
                                        Invoice ID: #{{ $orderId }}
                                    </span>

                                    <div class="mt-2 space-y-1">
                                        <p class="text-xs text-slate-400 font-medium">Menu yang dipesan:</p>
                                        @foreach ($deliveries as $deliv)
                                            <h5 class="font-bold text-slate-800 text-sm flex items-center gap-1">
                                                • {{ $deliv->menu->name }}
                                                <span
                                                    class="text-xs text-emerald-600 font-normal">({{ $deliv->status }})</span>
                                            </h5>
                                        @endforeach
                                    </div>

                                    <p class="text-xs text-slate-400 mt-2">📅
                                        {{ \Carbon\Carbon::parse($firstDeliv->delivery_date)->format('d M Y') }} | 🕒
                                        {{ ucfirst($firstDeliv->meal_time) }}
                                    </p>
                                </div>

                                <form action="{{ route('deliveries.take', $firstDeliv->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2 px-4 rounded-xl transition shadow-sm whitespace-nowrap">
                                        Ambil Tugas ➔
                                    </button>
                                </form>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs space-y-1.5">
                                <p class="text-slate-600"><span class="font-bold text-slate-800">👤 Penerima:</span>
                                    {{ $firstDeliv->order->user->name ?? 'Customer' }}</p>
                                <p class="text-slate-600"><span class="font-bold text-slate-800">📞 Nomor
                                        Telepon:</span>
                                    {{ $firstDeliv->order->user->phone ?? '-' }}</p>
                                <p class="text-slate-600"><span class="font-bold text-slate-800">📍 Alamat:</span>
                                    {{ $firstDeliv->delivery_address ?? 'Alamat belum diatur' }}</p>
                                @if ($firstDeliv->latitude && $firstDeliv->longitude)
                                    <button type="button"
                                        onclick="viewDriverMap('{{ $firstDeliv->latitude }}', '{{ $firstDeliv->longitude }}', '{{ $firstDeliv->order->user->name ?? 'Customer' }}')"
                                        class="mt-2 inline-flex items-center space-x-1.5 bg-sky-500 hover:bg-sky-600 text-white font-bold px-2.5 py-1.5 rounded-lg transition text-[11px] shadow-sm">
                                        <span>🗺️ Lihat Peta Lokasi</span>
                                    </button>
                                @else
                                    <p class="text-[10px] text-amber-500 italic mt-1">⚠️ Customer tidak menandai peta
                                    </p>
                                @endif
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
                    <span class="bg-emerald-50 text-emerald-700 text-xs px-2.5 py-1 rounded-full font-bold">
                        {{ $myDeliveries->count() }} Invoice Proses
                    </span>
                </h4>

                <div class="space-y-3">
                    @forelse($myDeliveries as $orderId => $deliveries)
                        @php
                            $firstDeliv = $deliveries->first();
                        @endphp
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">

                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[10px] font-mono text-slate-400 block mb-1">Invoice
                                        #{{ $orderId }}</span>

                                    <div class="space-y-1">
                                        @foreach ($deliveries as $deliv)
                                            <h5 class="font-bold text-slate-800 text-sm">• {{ $deliv->menu->name }}
                                            </h5>
                                        @endforeach
                                    </div>

                                    <p class="text-xs text-slate-400 mt-2">Kirim:
                                        {{ \Carbon\Carbon::parse($firstDeliv->delivery_date)->format('d M Y') }}
                                        ({{ ucfirst($firstDeliv->meal_time) }})
                                    </p>
                                </div>

                                <div>
                                    @if ($firstDeliv->status == 'on_the_way')
                                        <span
                                            class="bg-sky-50 text-sky-700 border border-sky-100 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider animate-pulse">🚚
                                            On The Way</span>
                                    @elseif($firstDeliv->status == 'cooking')
                                        <span
                                            class="bg-amber-50 text-amber-700 border border-amber-100 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">🍳
                                            Cooking</span>
                                    @elseif($firstDeliv->status == 'delivered')
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

                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs space-y-1.5">
                                <p class="text-slate-600"><span class="font-bold text-slate-800">👤 Penerima:</span>
                                    {{ $firstDeliv->order->user->name ?? 'Customer' }}</p>
                                <p class="text-slate-600"><span class="font-bold text-slate-800">📍 Alamat
                                        Lengkap:</span>
                                    {{ $firstDeliv->delivery_address ?? 'Alamat belum diatur' }}</p>

                                @if ($firstDeliv->latitude && $firstDeliv->longitude)
                                    <button type="button"
                                        onclick="viewDriverMap('{{ $firstDeliv->latitude }}', '{{ $firstDeliv->longitude }}', '{{ $firstDeliv->order->user->name ?? 'Customer' }}')"
                                        class="mt-2 inline-flex items-center space-x-1.5 bg-sky-500 hover:bg-sky-600 text-white font-bold px-2.5 py-1.5 rounded-lg transition text-[11px] shadow-sm">
                                        <span>🗺️ Lihat Peta Lokasi</span>
                                    </button>
                                @else
                                    <p class="text-[10px] text-amber-500 italic mt-1">⚠️ Customer tidak menandai peta
                                    </p>
                                @endif

                                @if ($firstDeliv->notes)
                                    <p class="text-slate-600"><span class="font-bold text-slate-800">📝 Catatan:</span>
                                        <span class="italic text-amber-600">{{ $firstDeliv->notes }}</span>
                                    </p>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-slate-50 flex justify-end">
                                @if ($firstDeliv->status == 'cooking')
                                    <form action="{{ route('deliveries.otw', $orderId) }}" method="POST"
                                        onsubmit="return confirm('Mulai mengantarkan makanan di invoice ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs py-1.5 px-3 rounded-xl transition">
                                            🚀 Kirim Sekarang (Set OTW)
                                        </button>
                                    </form>
                                @elseif($firstDeliv->status == 'on_the_way')
                                    <div class="flex space-x-2 w-full justify-end">
                                        <!-- 💡 Tombol Gagal dibungkus Modal Alpine.js -->
                                        <div x-data="{ showFailModal: false }">
                                            <!-- Tombol Trigger Modal -->
                                            <button type="button" @click="showFailModal = true"
                                                class="bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-600 font-bold text-xs py-1.5 px-3 rounded-xl transition">
                                                <span>✕ Gagal</span>
                                            </button>

                                            <!-- Modal Overlay -->
                                            <div x-show="showFailModal" x-cloak
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 text-left">

                                                <div @click.away="showFailModal = false"
                                                    class="bg-white rounded-2xl p-6 shadow-xl w-full max-w-sm transform transition-all">

                                                    <h3 class="text-lg font-bold text-slate-900 mb-1">Laporkan Kendala
                                                    </h3>
                                                    <p class="text-xs text-slate-500 mb-4">Pilih alasan mengapa pesanan
                                                        ini gagal diantarkan.</p>

                                                    <form action="{{ route('deliveries.failed', $orderId) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <!-- Dropdown Alasan -->
                                                        <div class="mb-5">
                                                            <select name="failure_reason" required
                                                                class="w-full text-sm border-slate-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 text-slate-700">
                                                                <option value="">-- Pilih Alasan --</option>
                                                                <option value="Kendaraan bermasalah / Kecelakaan">
                                                                    Kendaraan bermasalah / Kecelakaan</option>
                                                                <option value="Alamat tidak ditemukan">Alamat tidak
                                                                    ditemukan</option>
                                                                <option value="Pelanggan tidak bisa dihubungi">Pelanggan
                                                                    tidak bisa dihubungi</option>
                                                                <option value="Makanan rusak / tumpah">Makanan rusak /
                                                                    tumpah</option>
                                                            </select>
                                                        </div>

                                                        <div class="flex gap-3">
                                                            <button type="button" @click="showFailModal = false"
                                                                class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                                                                Batal
                                                            </button>
                                                            <button type="submit"
                                                                class="flex-1 px-4 py-2 bg-rose-600 text-white font-bold text-xs rounded-xl hover:bg-rose-700 transition">
                                                                Kirim Laporan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tombol Selesai Diantar (Tetap Sama) -->
                                        <form action="{{ route('deliveries.delivered', $orderId) }}" method="POST"
                                            onsubmit="return confirm('Konfirmasi bahwa makanan telah diterima oleh customer?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-1.5 px-3 rounded-xl transition shadow-md shadow-emerald-100">
                                                <span>✓ Selesai Diantar</span>
                                            </button>
                                        </form>
                                    </div>
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
    <div id="driverMapModal"
        class="fixed inset-0 z-50 overflow-y-auto hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-xl border border-slate-100">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-1">
                    <span>📍 Lokasi Pengantaran:</span>
                    <span id="driver_map_title" class="text-sky-600 font-extrabold"></span>
                </h3>
                <button onclick="closeDriverMap()"
                    class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <div id="driver_map_container" class="w-full h-72 rounded-xl border border-slate-200 z-0"></div>

            <div class="flex space-x-2">
                <button type="button" onclick="closeDriverMap()"
                    class="w-1/3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 rounded-xl text-xs transition">
                    Kembali
                </button>
                <a id="google_maps_redirect" href="#" target="_blank"
                    class="w-2/3 text-center bg-sky-500 hover:bg-sky-600 text-white font-bold py-2 rounded-xl text-xs transition block shadow-sm shadow-sky-100">
                    🚀 Buka di Google Maps HP
                </a>
            </div>
        </div>
    </div>

    <script>
        let driverMap, driverMarker;

        function viewDriverMap(lat, lng, customerName) {
            // Tampilkan judul nama penerima di modal
            document.getElementById('driver_map_title').innerText = customerName;

            // Buat link pintas ke google maps aplikasi
            document.getElementById('google_maps_redirect').href =
                `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

            // Munculkan Modal Element
            const modal = document.getElementById('driverMapModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Render Peta Leaflet
            setTimeout(() => {
                if (!driverMap) {
                    driverMap = L.map('driver_map_container').setView([lat, lng], 16);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap'
                    }).addTo(driverMap);

                    driverMarker = L.marker([lat, lng]).addTo(driverMap);
                } else {
                    // Jika peta sudah dimuat sebelumnya, cukup ganti titik fokusnya saja
                    driverMap.setView([lat, lng], 16);
                    driverMarker.setLatLng([lat, lng]);
                }

                // Fix bug render peta kotak abu-abu di dalam modal hiddens
                driverMap.invalidateSize();
            }, 150);
        }

        function closeDriverMap() {
            document.getElementById('driverMapModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
