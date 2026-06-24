<x-app-layout>
    <div x-data="{
        cart: [],
        isLoading: false,
        openMapModal: false,
        lat: '',
        lng: '',
        search: '',
    
        addMenu(id, name, price, calories) {
            let item = this.cart.find(i => i.id === id);
            if (item) {
                item.qty++;
            } else {
                this.cart.push({ id: id, name: name, price: Number(price), calories: calories, qty: 1 });
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
    
        async processOrder() {
            if (this.cart.length === 0) return;
            this.isLoading = true;
    
            const formData = new FormData(document.getElementById('order-form'));
            formData.append('cart_data', JSON.stringify(this.cart));
            formData.append('latitude', this.lat);
            formData.append('longitude', this.lng);
    
            try {
                let response = await fetch('{{ route('customer.orders.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
    
                let result = await response.json();
    
                if (response.ok && result.snap_token) {
                    window.snap.pay(result.snap_token, {
                        onSuccess: function(statusResult) {
                            window.location.href = '{{ route('customer.orders.index') }}?status=success';
                        },
                        onPending: function(statusResult) {
                            window.location.href = '{{ route('customer.orders.index') }}?status=pending';
                        },
                        onError: function(statusResult) {
                            alert('Pembayaran gagal, silakan coba lagi dari riwayat pesanan.');
                            window.location.reload();
                        },
                        onClose: function() {
                            alert('Anda menutup halaman pembayaran. Pesanan Anda tersimpan sebagai pending.');
                            window.location.reload();
                        }
                    });
                } else {
                    alert(result.message || 'Terjadi kesalahan sistem.');
                }
            } catch (error) {
                console.error(error);
                alert('Gagal memproses pesanan.');
            } finally {
                this.isLoading = false;
            }
        }
    }" class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

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

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
                🎉 {{ session('success') }}
            </div>
        @endif/

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($menus as $menu)
                    <div x-show="search === '' || '{{ strtolower(addslashes($menu->name)) }}'.includes(search.toLowerCase())"
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
                                    <div>Protein: <span class="text-slate-800">{{ $menu->nutrition->protein_g }}g</span>
                                    </div>
                                    <div>Kalori: <span class="text-slate-800">{{ $menu->nutrition->carbs_g }}g</span>
                                    </div>
                                    <div>Lemak: <span class="text-slate-800">{{ $menu->nutrition->fat_g }}g</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-50">
                            <span class="font-black text-slate-900 text-sm">
                                Rp {{ number_format($menu->price, 0, ',', '.') }} (Stok: {{ $menu->stock }} Porsi)
                            </span>
                            <button @if ($menu->stock <= 0) disabled @endif
                                @click="addMenu({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, {{ $menu->nutrition->calories ?? 0 }})"
                                class="bg-slate-900 hover:bg-slate-800 {{ $menu->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }} text-white font-bold text-xs py-1.5 px-3 rounded-xl transition">
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
                                min="{{ \Carbon\Carbon::today('Asia/Jakarta')->format('Y-m-d') }}" name="delivery_date"
                                class="block w-full rounded-xl border-slate-200 text-xs" required>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat
                                Pengiriman</label>
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
