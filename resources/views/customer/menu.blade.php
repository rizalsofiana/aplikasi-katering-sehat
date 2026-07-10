<x-app-layout>
    <div x-data="{
        cart: [],
        isLoading: false,
        openMapModal: false,
        lat: '',
        lng: '',
        search: '',
        recommendationData: null,
        recommendedMeals: {},
    
        async fetchRecommendation() {
            this.isLoading = true;
            this.recommendedMeals = {};
            try {
                const response = await fetch('{{ route('customer.orders.ai') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                if (response.ok) {
                    this.recommendationData = data.recommendation;
                    const meals = data.recommendation.meals;
                    this.recommendedMeals = {
                        [meals.breakfast_menu_id]: 'Sarapan 🌅',
                        [meals.lunch_menu_id]: 'Makan Siang ☀️',
                        [meals.dinner_menu_id]: 'Makan Malam 🌙'
                    };
                } else {
                    alert(data.error || 'Gagal memproses');
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },
    
        toast: { show: false, message: '', type: 'error' },
    
        showToast(message, type = 'error') {
            this.toast.message = message;
            this.toast.type = type;
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3000);
        },
    
        addMenu(id, name, price, calories, stock) {
            let item = this.cart.find(i => i.id === id);
            if (item) {
                if (item.qty < stock) {
                    item.qty++;
                } else {
                    this.showToast(`Maaf, sisa stok untuk ${name} hanya ${stock} porsi.`, 'warning');
                }
            } else {
                if (stock > 0) {
                    this.cart.push({ id, name, price: Number(price), calories, stock: Number(stock), qty: 1 });
                } else {
                    this.showToast(`Maaf, stok ${name} sudah habis.`, 'error');
                }
            }
        },
    
        removeMenu(id) {
            let item = this.cart.find(i => i.id === id);
            if (item) {
                item.qty--;
                if (item.qty <= 0) { this.cart = this.cart.filter(i => i.id !== id); }
            }
        },
    
        get totalPrice() { return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0); },
        get totalCalories() { return this.cart.reduce((sum, item) => sum + (item.calories * item.qty), 0); },
    
        // Fungsi pembantu untuk filter
        isMenuVisible(menuName) {
            return this.search === '' || menuName.toLowerCase().includes(this.search.toLowerCase());
        },
    
        async processOrder() {
            if (this.cart.length === 0) {
                this.showToast('Keranjang belanja Anda masih kosong.', 'warning');
                return;
            }
    
            this.isLoading = true;
            const formData = new FormData(document.getElementById('order-form'));
            formData.append('cart_data', JSON.stringify(this.cart));
            formData.append('latitude', this.lat);
            formData.append('longitude', this.lng);
    
            try {
                let response = await fetch('{{ route('customer.orders.store') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                });
    
                let result = await response.json();
    
                if (response.ok && result.snap_token) {
                    window.snap.pay(result.snap_token, {
                        onSuccess: (statusResult) => {
                            this.showToast('Pembayaran berhasil diproses!.', 'success');
                            setTimeout(() => { window.location.href = '{{ route('customer.orders.index') }}?status=success'; }, 2500);
                        },
                        onPending: (statusResult) => {
                            this.showToast('Menunggu pembayaran diselesaikan.', 'warning');
                            setTimeout(() => { window.location.href = '{{ route('customer.orders.index') }}?status=pending'; }, 2500);
                        },
                        onError: (statusResult) => {
                            this.showToast('Pembayaran gagal.', 'error');
                            setTimeout(() => { window.location.reload(); }, 2500);
                        },
                        onClose: () => {
                            this.showToast('Anda menutup halaman pembayaran.', 'warning');
                            setTimeout(() => { window.location.reload(); }, 2500);
                        }
                    });
                } else {
                    this.showToast(result.message || 'Terjadi kesalahan sistem.', 'error');
                }
            } catch (error) {
                this.showToast('Gagal memproses pesanan.', 'error');
            } finally {
                this.isLoading = false;
            }
        }
    }" class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <div x-show="toast.show" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10 sm:translate-y-0 sm:scale-95 sm:translate-x-10"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100 sm:translate-x-0"
            x-transition:leave-end="opacity-0 translate-y-10 sm:translate-y-0 sm:scale-95 sm:translate-x-10"
            class="fixed bottom-5 right-5 sm:top-5 sm:bottom-auto sm:right-5 z-[100] flex items-center p-4 w-full max-w-sm rounded-2xl shadow-xl border"
            :class="{
                'bg-rose-50 border-rose-200 text-rose-700': toast.type === 'error',
                'bg-emerald-50 border-emerald-200 text-emerald-700': toast.type === 'success',
                'bg-amber-50 border-amber-200 text-amber-700': toast.type === 'warning'
            }"
            role="alert">

            <svg x-show="toast.type === 'error'" class="flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <svg x-show="toast.type === 'success'" class="flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg x-show="toast.type === 'warning'" class="flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <div class="font-semibold text-sm" x-text="toast.message"></div>

            <button @click="toast.show = false" type="button"
                class="ml-auto -mx-1.5 -my-1.5 p-1.5 rounded-xl transition focus:outline-none focus:ring-2"
                :class="{
                    'hover:bg-rose-100 focus:ring-rose-400 text-rose-500': toast.type === 'error',
                    'hover:bg-emerald-100 focus:ring-emerald-400 text-emerald-500': toast.type === 'success',
                    'hover:bg-amber-100 focus:ring-amber-400 text-amber-500': toast.type === 'warning'
                }">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div
            class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Katering Sehat & Penyedia Menu Diet 🍲</h3>
                <p class="text-sm text-slate-500 mt-0.5">Silakan pilih makanan sehat harian Anda. Sistem kasir otomatis
                    mengkalkulasi zat gizi makro Anda.</p>
            </div>

            <div class="w-full md:w-72">
                <div class="relative">
                    <span
                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">🔍</span>
                    <input type="text" x-model="search" placeholder="Cari menu diet Anda..."
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm placeholder:text-slate-400">
                </div>
            </div>
        </div>
        <button @click="fetchRecommendation" :disabled="isLoading"
            class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
            <span x-text="isLoading ? 'Menganalisis profil Anda...' : '✨ Dapatkan Rekomendasi Menu dari AI'">
                ✨ Dapatkan Rekomendasi Menu dari AI
            </span>
        </button>

        <div x-show="recommendationData" class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-200" x-cloak>
            <h4 class="font-bold text-emerald-800">Rekomendasi dari AI:</h4>
            <p class="text-sm text-slate-600 mt-1" x-text="recommendationData?.reasoning"></p>
            <p class="text-xs font-semibold text-emerald-700 mt-2">
                Total Energi Rekomendasi: <span x-text="recommendationData?.total_calories_recommended"></span>
                Kkal
            </p>
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
                    {{-- PERBAIKAN: Menggunakan fungsi isMenuVisible agar tidak ada error quote --}}
                    <div x-show="isMenuVisible('{{ addslashes($menu->name) }}')"
                        :class="recommendedMeals[{{ $menu->id }}] ?
                            'order-first ring-2 ring-violet-500 shadow-lg scale-[1.02]' : 'border-slate-100'"
                        class="bg-white rounded-2xl border p-4 shadow-sm flex flex-col justify-between hover:shadow-md transition-all duration-300 relative">

                        <div class="w-full h-40 mb-4 rounded-xl overflow-hidden bg-slate-50 relative group">

                            <div x-show="recommendedMeals[{{ $menu->id }}]"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90 translate-y-[-10px]"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                class="absolute top-2 right-2 z-10 bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-extrabold text-[10px] tracking-wide uppercase px-2.5 py-1 rounded-lg shadow-md flex items-center gap-1"
                                x-cloak>
                                <svg class="w-3 h-3 animate-pulse text-amber-300" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 11-2 0v-1a1 1 0 112 0v1zM14.243 15.657a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM10 16a1 1 0 100 2v-1a1 1 0 100-2v1zM5.757 14.243a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zM4 10a1 1 0 112 0v1a1 1 0 11-2 0v-1zM6.464 4.343a1 1 0 10-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707z">
                                    </path>
                                </svg>
                                <span x-text="recommendedMeals[{{ $menu->id }}]"></span>
                            </div>

                            @if ($menu->image_path)
                                <img src="{{ asset('storage/' . $menu->image_path) }}" alt="{{ $menu->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <span class="text-xs font-medium">Tidak ada gambar</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2 flex-1">
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
                                    class="grid grid-cols-3 gap-1 bg-slate-50 p-2 rounded-xl text-[11px] text-slate-500 text-center font-semibold mt-2">
                                    <div>Protein: <span
                                            class="text-slate-800">{{ $menu->nutrition->protein_g }}g</span></div>
                                    <div>Karbo: <span class="text-slate-800">{{ $menu->nutrition->carbs_g }}g</span>
                                    </div>
                                    <div>Lemak: <span class="text-slate-800">{{ $menu->nutrition->fat_g }}g</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-50">
                            <span class="font-black text-slate-900 text-sm flex flex-col">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-400 font-normal">Stok: {{ $menu->stock }}
                                    Porsi</span>
                            </span>
                            <button @if ($menu->stock <= 0) disabled @endif
                                @click="addMenu({{ $menu->id ?? 0 }}, '{{ addslashes($menu->name) }}', {{ $menu->price ?? 0 }}, {{ $menu->nutrition->calories ?? 0 }}, {{ $menu->stock ?? 0 }} )"
                                class="bg-slate-900 hover:bg-slate-800 {{ $menu->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }} text-white font-bold text-xs py-2 px-4 rounded-xl transition shadow-sm">
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
                class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4 sticky top-6">

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

                                <button @click="addMenu(item.id, item.name, item.price, item.calories, item.stock)"
                                    class="bg-slate-100 px-1.5 py-0.5 rounded font-bold hover:bg-slate-200 transition">+</button>
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
                            <span class="font-bold text-emerald-600">{{ $userProfile->daily_calorie_target ?? 0 }}
                                kkal</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm pt-1">
                        <span class="font-bold text-slate-800">Total Pembayaran:</span>
                        <span class="font-black text-emerald-600"
                            x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice)"></span>
                    </div>
                </div>

                <form id="order-form" @submit.prevent="processOrder">
                    @csrf
                    <input type="hidden" name="cart_data" :value="JSON.stringify(cart)">

                    <input type="hidden" name="latitude" :value="lat">
                    <input type="hidden" name="longitude" :value="lng">

                    <div class="space-y-3 mb-4">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal
                                Pengiriman</label>
                            <input type="date" id="delivery_date"
                                min="{{ \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d') }}"
                                name="delivery_date" class="block w-full rounded-xl border-slate-200 text-xs"
                                required>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat
                                Pengiriman</label>
                            <textarea name="delivery_address" placeholder="Masukkan detail alamat disini..."
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
                            <select name="meal_time" id="meal_time"
                                class="block w-full rounded-xl border-slate-200 text-xs" required>
                                <option value="">-- Pilih Waktu Makan --</option>
                                <option value="breakfast">Sarapan (07:00 - 09:00)</option>
                                <option value="lunch">Makan Siang (12:00 - 14:00)</option>
                                <option value="dinner">Makan Malam (18:00 - 20:00)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" :disabled="cart.length === 0 || isLoading"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-200 disabled:cursor-not-allowed text-white font-bold py-2.5 px-4 rounded-xl transition text-xs shadow-md shadow-emerald-100">
                        <span x-show="!isLoading">Amankan & Proses Pesanan</span>
                        <span x-show="isLoading" class="flex items-center justify-center space-x-2">
                            ⏳ Memproses Transaksi...
                        </span>
                    </button>
                </form>

                <div x-show="openMapModal" x-cloak
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

                document.addEventListener('DOMContentLoaded', function() {
                    const dateInput = document.getElementById('delivery_date');
                    const mealSelect = document.getElementById('meal_time');

                    const now = new Date();
                    const currentHour = now.getHours();

                    // =========================================================
                    // 1. LOGIKA PEMBATASAN TANGGAL (Kunci hari ini jika > 20:00)
                    // =========================================================
                    let minDate = new Date(); // Ambil tanggal hari ini

                    // Jika sudah jam 20:00 (8 malam) atau lebih, geser minDate ke besok harinya
                    if (currentHour >= 20) {
                        minDate.setDate(minDate.getDate() + 1);
                    }

                    // Format tanggal menjadi YYYY-MM-DD agar dikenali oleh HTML
                    const year = minDate.getFullYear();
                    const month = String(minDate.getMonth() + 1).padStart(2, '0');
                    const day = String(minDate.getDate()).padStart(2, '0');
                    const minDateString = `${year}-${month}-${day}`;

                    // Terapkan batas minimal ke input tanggal
                    dateInput.setAttribute('min', minDateString);

                    // Mencegah bug: Jika sebelumnya input sudah terisi tanggal hari ini, 
                    // lalu waktu berubah melewati jam 8 malam, langsung kosongkan inputnya
                    if (dateInput.value && dateInput.value < minDateString) {
                        dateInput.value = '';
                    }

                    // Fungsi untuk memperbarui opsi waktu makan
                    function updateMealOptions() {
                        if (!dateInput.value) return;

                        // Ambil tanggal yang dipilih customer dan waktu saat ini
                        const selectedDate = new Date(dateInput.value);
                        const now = new Date();

                        // 1. Kembalikan semua opsi ke keadaan normal (bisa diklik & terlihat)
                        Array.from(mealSelect.options).forEach(opt => {
                            opt.disabled = false;
                            opt.hidden = false;
                        });

                        // 2. Cek apakah tanggal yang dipilih adalah HARI INI
                        if (selectedDate.toDateString() === now.toDateString()) {
                            const currentHour = now.getHours();

                            // Aturan Batas Waktu (Bisa Anda sesuaikan jamnya)
                            // Jika lewat jam 09:00 pagi, sembunyikan Sarapan
                            if (currentHour >= 9) {
                                disableOption('breakfast');
                            }
                            // Jika lewat jam 14:00 siang, sembunyikan Makan Siang
                            if (currentHour >= 14) {
                                disableOption('lunch');
                            }
                            // Jika lewat jam 20:00 malam, sembunyikan Makan Malam
                            if (currentHour >= 20) {
                                disableOption('dinner');
                            }

                            // Jika opsi yang sedang terpilih ternyata di-disable, reset select ke kosong
                            if (mealSelect.options[mealSelect.selectedIndex].disabled) {
                                mealSelect.value = '';
                            }
                        }
                    }

                    // Fungsi bantuan untuk men-disable dan menyembunyikan opsi
                    function disableOption(value) {
                        const option = mealSelect.querySelector(`option[value="${value}"]`);
                        if (option) {
                            option.disabled = true;
                            option.hidden = true; // Menghilangkan dari daftar dropdown
                        }
                    }

                    // Jalankan fungsi setiap kali customer mengganti tanggal
                    if (dateInput) {
                        dateInput.addEventListener('change', updateMealOptions);
                    }

                    // Jalankan sekali saat halaman pertama kali dimuat (jika tanggal sudah terisi)
                    updateMealOptions();
                });
            </script>
        </div>
    </div>
</x-app-layout>
