<x-app-layout>
    <div x-data="{
        cart: [],
        addMenu(id, name, price, calories) {
            let item = this.cart.find(i => i.id === id);
            if (item) { item.qty++; } else { this.cart.push({ id: id, name: name, price: price, calories: calories, qty: 1 }); }
        },
        removeMenu(id) {
            let item = this.cart.find(i => i.id === id);
            if (item) {
                item.qty--;
                if (item.qty <= 0) { this.cart = this.cart.filter(i => i.id !== id); }
            }
        },
        get totalPrice() { return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0); },
        get totalCalories() { return this.cart.reduce((sum, item) => sum + (item.calories * item.qty), 0); }
    }" class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-xl font-bold text-slate-900">Katering Sehat & Penyedia Menu Diet 🍲</h3>
            <p class="text-sm text-slate-500 mt-0.5">Silakan pilih makanan sehat harian Anda. Sistem kasir otomatis
                mengkalkulasi zat gizi makro Anda.</p>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
                🎉 {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($menus as $menu)
                    <div
                        class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400">🔥 {{ $menu->nutrition->calories ?? 0 }}
                                    Kkal</span>
                                <span
                                    class="text-[10px] uppercase font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Tersedia</span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-base">{{ $menu->name }}</h4>
                            <p class="text-xs text-slate-400 line-clamp-2">{{ $menu->description }}</p>

                            @if ($menu->nutrition)
                                <div
                                    class="grid grid-cols-3 gap-1 bg-slate-50 p-2 rounded-xl text-[11px] text-slate-500 text-center font-semibold">
                                    <div>P: <span class="text-slate-800">{{ $menu->nutrition->protein_g }}g</span></div>
                                    <div>K: <span class="text-slate-800">{{ $menu->nutrition->carbs_g }}g</span></div>
                                    <div>L: <span class="text-slate-800">{{ $menu->nutrition->fat_g }}g</span></div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-50">
                            <span class="font-black text-slate-900 text-sm">Rp 35.000</span>
                            <button
                                @click="addMenu({{ $menu->id }}, '{{ addslashes($menu->name) }}', 35000, {{ $menu->nutrition->calories ?? 0 }})"
                                class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-1.5 px-3 rounded-xl transition">
                                + Tambah
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full bg-white p-12 text-center rounded-2xl border border-dashed border-slate-200 text-slate-400 text-sm italic">
                        Maaf, belum ada daftar makanan yang siap saji saat ini.
                    </div>
                @endforelse
            </div>

            <div id="cart-container"
                class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4 sticky top-6"
                x-data="{ openMapModal: false, lat: '', lng: '' }">

                <h4
                    class="font-bold text-slate-900 text-base border-b border-slate-100 pb-2 flex items-center justify-between">
                    <span>Struk Belanja</span>
                    <span class="text-xs text-slate-400 font-medium" x-text="cart.length + ' Menu'"></span>
                </h4>

                <div class="space-y-3 max-h-[260px] overflow-y-auto">
                    <template x-if="cart.length === 0">
                        <p class="text-xs text-slate-400 italic text-center py-6">Belum ada makanan terpilih.</p>
                    </template>
                    <template x-for="item in cart" :key="item.id">
                        <div class="flex items-center justify-between text-xs border-b border-slate-50 pb-2">
                            <div>
                                <p class="font-bold text-slate-800" x-text="item.name"></p>
                                <p class="text-slate-400" x-text="'🔥 ' + (item.calories * item.qty) + ' kkal'"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="removeMenu(item.id)"
                                    class="bg-slate-100 px-1.5 py-0.5 rounded font-bold">-</button>
                                <span class="font-bold text-slate-800 w-4 text-center" x-text="item.qty"></span>
                                <button @click="addMenu(item.id, item.name, item.price, item.calories)"
                                    class="bg-slate-100 px-1.5 py-0.5 rounded font-bold">+</button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Total Akumulasi Kalori:</span>
                        <span class="font-bold text-slate-900" x-text="totalCalories + ' kkal'"></span>
                    </div>
                    @if ($userProfile)
                        <div class="flex justify-between text-slate-500 border-b border-slate-200 pb-1.5">
                            <span>Target Batas Kalori Anda:</span>
                            <span class="font-bold text-emerald-600">{{ $userProfile->daily_calorie_target ?? 2000 }}
                                kkal</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm pt-1">
                        <span class="font-bold text-slate-800">Total Pembayaran:</span>
                        <span class="font-black text-emerald-600"
                            x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice)"></span>
                    </div>
                </div>

                <form action="{{ route('customer.orders.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cart_data" :value="JSON.stringify(cart)">

                    <input type="hidden" name="latitude" :value="lat">
                    <input type="hidden" name="longitude" :value="lng">

                    <div class="space-y-3 mb-4">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal
                                Pengiriman</label>
                            <input type="date" name="delivery_date"
                                class="block w-full rounded-xl border-slate-200 text-xs" required>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat
                                Alamat</label>
                            <textarea name="delivery_address" placeholder="Masukkan alamat disini..."
                                class="block w-full rounded-xl border-slate-200 text-xs mb-2" required></textarea>

                            <button type="button" @click="openMapModal = true; initCustomerMap()"
                                class="w-full inline-flex items-center justify-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2 px-3 rounded-xl transition text-[11px] border border-slate-200 shadow-sm">
                                <span
                                    x-text="lat ? '📍 Lokasi Terpilih (Ubah)' : '📍 Pilih Titik Koordinat Peta'"></span>
                            </button>
                            <p x-show="lat" class="text-[10px] text-emerald-600 mt-1 font-medium text-center"
                                x-text="'Koordinat Terkunci: ' + lat.substring(0,8) + ', ' + lng.substring(0,8)"></p>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Waktu
                                Makan (*Meal Time*)</label>
                            <select name="meal_time" class="block w-full rounded-xl border-slate-200 text-xs" required>
                                <option value="breakfast">Sarapan (Pagi)</option>
                                <option value="lunch">Makan Siang</option>
                                <option value="dinner">Makan Malam</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" :disabled="cart.length === 0"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-200 disabled:cursor-not-allowed text-white font-bold py-2.5 px-4 rounded-xl transition text-xs shadow-md shadow-emerald-100">
                        Amankan & Proses Pesanan
                    </button>
                </form>

                <div x-show="openMapModal"
                    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
                    x-transition>
                    <div class="bg-white rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-xl border border-slate-100"
                        @click.away="openMapModal = false">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <h3 class="font-bold text-slate-900 text-sm">Tandai Lokasi Rumah Anda</h3>
                            <button @click="openMapModal = false"
                                class="text-slate-400 hover:text-slate-600 font-bold text-sm">&times;</button>
                        </div>

                        <div id="customer_map_container" class="w-full h-72 rounded-xl border border-slate-200 z-0">
                        </div>

                        <p class="text-[11px] text-slate-400 italic text-center">Geser penanda merah (*marker*) atau
                            klik peta pada posisi rumah Anda.</p>

                        <button type="button" @click="openMapModal = false"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-xl text-xs transition">
                            Simpan Titik Lokasi
                        </button>
                    </div>
                </div>
            </div>
            <script>
                let customerMap, customerMarker;

                function initCustomerMap() {
                    let initialLat = -6.94803;
                    let initialLng = 107.6011;

                    // 🟢 GANTI BARIS INI: Gunakan getElementById agar targetnya 100% akurat
                    const cartElement = document.getElementById('cart-container');
                    if (!cartElement) return; // Keamanan jika elemen tidak ditemukan

                    const alpineData = Alpine.$data(cartElement);

                    if (alpineData.lat && alpineData.lng) {
                        initialLat = parseFloat(alpineData.lat);
                        initialLng = parseFloat(alpineData.lng);
                    }

                    // Jalankan timeout kecil agar DOM modal dirender sempurna dulu oleh Alpine
                    setTimeout(() => {
                        if (!customerMap) {
                            // Buat instance peta baru
                            customerMap = L.map('customer_map_container').setView([initialLat, initialLng], 14);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap'
                            }).addTo(customerMap);

                            // Buat marker yang bisa digerakkan (draggable)
                            customerMarker = L.marker([initialLat, initialLng], {
                                draggable: true
                            }).addTo(customerMap);

                            // Sinkronisasi koordinat saat marker digeser kurir/user
                            customerMarker.on('dragend', function(e) {
                                let pos = customerMarker.getLatLng();
                                alpineData.lat = pos.lat.toString();
                                alpineData.lng = pos.lng.toString();
                            });

                            // Sinkronisasi koordinat saat peta diklik
                            customerMap.on('click', function(e) {
                                customerMarker.setLatLng(e.latlng);
                                alpineData.lat = e.latlng.lat.toString();
                                alpineData.lng = e.latlng.lng.toString();
                            });

                            // Set nilai awal ke input Alpine jika baru pertama kali buka peta
                            if (!alpineData.lat) {
                                alpineData.lat = initialLat.toString();
                                alpineData.lng = initialLng.toString();
                            }
                        } else {
                            // Jika peta sudah ada, pindahkan posisi ke koordinat terakhir
                            customerMap.setView([initialLat, initialLng], 14);
                            customerMarker.setLatLng([initialLat, initialLng]);
                        }

                        // PENTING: Memaksa peta Leaflet merender ulang ukurannya agar tidak bug/blank kotak abu-abu
                        customerMap.invalidateSize();
                    }, 150);
                }
            </script>
        </div>
    </div>
</x-app-layout>
