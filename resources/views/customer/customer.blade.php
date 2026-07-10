<x-app-layout>
    <div class="max-w-7xl py-12 px-4 mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}!</h3>
                <p class="text-sm text-gray-600 mt-1">Semoga harimu menyenangkan. Tetap jaga pola makan sehatmu hari
                    ini bersama kami.</p>
            </div>
        </div>

        @if ($profile)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow-md">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-emerald-100 text-xs font-semibold uppercase tracking-wider">
                                Sisa Target Energi Hari Ini
                            </p>

                            <h4 class="text-3xl font-extrabold mt-2">
                                {{ $sisaKalori }}
                                <span class="text-sm font-normal">kkal</span>
                            </h4>

                            {{-- Opsional: Tambahkan indikator visual target asli --}}
                            <p class="text-emerald-200 text-[10px] mt-1">
                                Dari total harian: {{ $profile->daily_calorie_target ?? 0 }} kkal
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-fire" viewBox="0 0 16 16">
                                <path
                                    d="M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16m0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15" />
                            </svg>
                        </div>
                    </div>
                    <div
                        class="mt-4 pt-4 border-t border-emerald-400 border-opacity-40 flex items-center justify-between text-xs">
                        <span>Target Diet:</span>
                        <span class="font-bold bg-white bg-opacity-20 px-2 py-0.5 rounded">
                            {{ $profile->diet_goal === 'weight_loss' ? 'Weight Loss' : ($profile->diet_goal === 'weight_gain' ? 'Weight Gain' : 'Maintenance') }}
                        </span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Kebutuhan Protein</span>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Makro</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-2xl font-bold text-gray-800">{{ round($profile->weight_kg * 2) }} <span
                                class="text-sm text-gray-500 font-normal">gram</span></h4>
                        <p class="text-xs text-gray-400 mt-1">Penting untuk masa otot</p>
                    </div>
                </div>

                @if ($todayDeliveries->isEmpty())
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Antaran Hari Ini</span>
                            <span
                                class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Logistik</span>
                        </div>
                        <div class="mt-4">
                            <h4 class="text-lg font-bold text-gray-800 truncate">Menu Belum Dipilih</h4>
                            <div class="flex mt-1 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                    <path
                                        d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z" />
                                    <path
                                        d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                </svg>
                                <p class="text-xs font-medium ms-2"> Silakan order menu terlebih dahulu</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($todayDeliveries as $delivery)
                            <div
                                class="bg-white rounded-xl p-5 shadow-sm border border-emerald-100 flex flex-col justify-between">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Antaran Hari Ini</span>
                                    <span
                                        class="text-[10px] font-bold text-white bg-slate-800 px-2 py-0.5 rounded uppercase tracking-wider">
                                        {{ str_replace('_', ' ', $delivery->status) }}
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h4 class="text-lg font-bold text-gray-800 truncate">
                                        {{ $delivery->menu->name ?? 'Menu tidak tersedia' }}</h4>
                                    <div class="flex mt-1 text-emerald-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                        </svg>
                                        <p class="text-xs font-medium ms-2">
                                            @if ($delivery->status === 'cooking')
                                                Sedang Dimasak 👨‍🍳
                                            @elseif($delivery->status === 'on_the_way')
                                                Sedang Diperjalanan 🛵
                                            @elseif($delivery->status === 'delivered')
                                                Pesanan Tiba 🍽️
                                            @else
                                                Proses Terkendala
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="mt-4 text-xs text-gray-400 hover:text-gray-600 hover:underline cursor-pointer">
                                    <a href="{{ route('customer.orders.history') }}">
                                        Lihat selengkapnya →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Status Langganan</span>
                        <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded">
                            {{ $currentSubscription->package->package_name ?? 'Belum Berlangganan' }}
                        </span>
                    </div>
                    <div class="mt-4">
                        @if (!$currentSubscription)
                            <h4 class="text-2xl font-bold text-gray-400">Tidak Aktif</h4>
                            <a href="#" class="text-xs text-green-600 hover:underline font-semibold block mt-1">
                                Pilih paket sehatmu →
                            </a>
                        @else
                            @if ($currentSubscription->status === 'active')
                                <h4 class="text-2xl font-bold text-emerald-600">Aktif</h4>
                                <p class="text-xs text-gray-400 block mt-1">
                                    Hingga: <span
                                        class="font-semibold text-gray-600">{{ \Carbon\Carbon::parse($currentSubscription->end_date)->format('d M Y') }}</span>
                                </p>
                            @elseif($currentSubscription->status === 'pending')
                                <h4 class="text-2xl font-bold text-amber-500">Pending</h4>
                                <a href="#"
                                    class="text-xs text-amber-600 hover:underline font-semibold block mt-1">
                                    Selesaikan pembayaran →
                                </a>
                            @elseif($currentSubscription->status === 'cancelled')
                                <h4 class="text-2xl font-bold text-red-500">Dibatalkan</h4>
                                <a href="#"
                                    class="text-xs text-green-600 hover:underline font-semibold block mt-1">
                                    Daftar paket baru →
                                </a>
                            @elseif($currentSubscription->status === 'completed')
                                <h4 class="text-2xl font-bold text-blue-500">Selesai</h4>
                                <a href="#"
                                    class="text-xs text-green-600 hover:underline font-semibold block mt-1">
                                    Perpanjang langganan →
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

            </div>

            <!-- Pastikan ada x-data="{ editProfileModal: false }" di div parent terluar -->
            <div x-data="{ editProfileModal: false }">

                <!-- Card Metrik Fisik Anda -->
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800 mb-3">Metrik Fisik Terdaftar Anda:</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                            <div>• Umur: <span class="font-semibold text-gray-900">{{ $profile->age }} tahun</span>
                            </div>
                            <div>• Berat Badan: <span class="font-semibold text-gray-900">{{ $profile->weight_kg }}
                                    kg</span></div>
                            <div>• Tinggi Badan: <span class="font-semibold text-gray-900">{{ $profile->height_cm }}
                                    cm</span></div>
                            <div>• Aktivitas: <span
                                    class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $profile->activity_level) }}</span>
                            </div>
                        </div>
                        @if ($profile->allergies)
                            <div class="mt-3 text-sm text-rose-500">
                                • Alergi: <span class="font-semibold">{{ $profile->allergies }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Buka Modal -->
                    <button @click="editProfileModal = true"
                        class="shrink-0 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2 px-4 rounded-lg transition">
                        ✏️ Ubah Data
                    </button>
                </div>

                <!-- MODAL EDIT DATA FISIK -->
                <div x-show="editProfileModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak
                    style="display: none;">
                    <!-- Latar belakang gelap (Overlay) -->
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                        @click="editProfileModal = false"></div>

                    <div
                        class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0 pointer-events-none">
                        <div x-show="editProfileModal" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-2xl w-full pointer-events-auto border border-slate-100">

                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
                                    <h3 class="text-lg font-bold text-gray-900">Perbarui Metrik Fisik</h3>
                                    <button @click="editProfileModal = false"
                                        class="text-gray-400 hover:text-gray-500 font-bold text-xl">&times;</button>
                                </div>

                                <!-- FORM UPDATE -->
                                <form action="{{ route('customer.profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Jenis Kelamin -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Jenis
                                                Kelamin</label>
                                            <select name="gender"
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                required>
                                                <option value="male"
                                                    {{ $profile->gender == 'male' ? 'selected' : '' }}>Laki-laki
                                                </option>
                                                <option value="female"
                                                    {{ $profile->gender == 'female' ? 'selected' : '' }}>Perempuan
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Umur -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Umur
                                                (Tahun)</label>
                                            <input type="number" name="age" value="{{ $profile->age }}"
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                min="10" max="100" required>
                                        </div>

                                        <!-- Berat Badan -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Berat Badan
                                                (Kg)</label>
                                            <input type="number" step="0.1" name="weight_kg"
                                                value="{{ $profile->weight_kg }}"
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                min="30" max="300" required>
                                        </div>

                                        <!-- Tinggi Badan -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Tinggi Badan
                                                (Cm)</label>
                                            <input type="number" name="height_cm" value="{{ $profile->height_cm }}"
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                min="100" max="250" required>
                                        </div>

                                        <!-- Level Aktivitas -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Aktivitas
                                                Harian</label>
                                            <select name="activity_level"
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                required>
                                                <option value="sedentary"
                                                    {{ $profile->activity_level == 'sedentary' ? 'selected' : '' }}>
                                                    Sangat Jarang Olahraga</option>
                                                <option value="lightly_active"
                                                    {{ $profile->activity_level == 'lightly_active' ? 'selected' : '' }}>
                                                    Jarang (1-3 hari/minggu)</option>
                                                <option value="moderately_active"
                                                    {{ $profile->activity_level == 'moderately_active' ? 'selected' : '' }}>
                                                    Normal (3-5 hari/minggu)</option>
                                                <option value="very_active"
                                                    {{ $profile->activity_level == 'very_active' ? 'selected' : '' }}>
                                                    Sering (6-7 hari/minggu)</option>
                                            </select>
                                        </div>

                                        <!-- Tujuan Diet -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Tujuan
                                                Diet</label>
                                            <select name="diet_goal"
                                                class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                required>
                                                <option value="weight_loss"
                                                    {{ $profile->diet_goal == 'weight_loss' ? 'selected' : '' }}>
                                                    Menurunkan Berat Badan</option>
                                                <option value="maintenance"
                                                    {{ $profile->diet_goal == 'maintenance' ? 'selected' : '' }}>
                                                    Menjaga Berat Badan</option>
                                                <option value="weight_gain"
                                                    {{ $profile->diet_goal == 'weight_gain' ? 'selected' : '' }}>
                                                    Menaikkan Berat Badan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Alergi (Full width) -->
                                    <div class="mt-4">
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Alergi Makanan
                                            (Opsional)</label>
                                        <input type="text" name="allergies" value="{{ $profile->allergies }}"
                                            placeholder="Contoh: Kacang, Seafood, Susu sapi..."
                                            class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        <p class="text-[10px] text-gray-400 mt-1">Kosongkan jika tidak ada alergi.</p>
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="editProfileModal = false"
                                            class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded-lg text-sm transition">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-md transition">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @if (!$profile)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-70 flex items-center justify-center z-50 backdrop-blur-sm">
            <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full m-4">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Lengkapi Profil Kesehatan Anda</h3>
                <p class="text-sm text-gray-600 mb-6">Kami memerlukan data fisik Anda untuk menghitung target kalori
                    harian secara akurat lewat sistem AI kami.</p>

                <form action="{{ route('profile.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="gender"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            required>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Usia</label>
                            <input type="number" name="age"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">BB (kg)</label>
                            <input type="number" step="0.1" name="weight_kg"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">TB (cm)</label>
                            <input type="number" step="0.1" name="height_cm"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Aktivitas Harian</label>
                        <select name="activity_level"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            required>
                            <option value="sedentary">Jarang Olahraga (Kerja Kantoran Duduk)</option>
                            <option value="lightly_active">Olahraga Ringan (1-3 hari/minggu)</option>
                            <option value="moderately_active">Olahraga Sedang (3-5 hari/minggu)</option>
                            <option value="very_active">Olahraga Berat (6-7 hari/minggu)</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Target Diet</label>
                        <select name="diet_goal"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            required>
                            <option value="weight_loss">Menurunkan Berat Badan (Defisit)</option>
                            <option value="maintenance">Mempertahankan Berat Badan</option>
                            <option value="weight_gain">Menaikkan Berat Badan (Surplus)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Alergi</label>
                        <input type="text" name="allergies"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Contoh: Kacang, Gluten, Susu (opsional)">
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-200 shadow">
                        Simpan & Hitung Kalori
                    </button>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
