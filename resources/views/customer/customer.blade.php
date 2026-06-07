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
                            <p class="text-emerald-100 text-xs font-semibold uppercase tracking-wider">Target Energi
                            </p>
                            <h4 class="text-3xl font-extrabold mt-2">{{ $profile->daily_calorie_target }} <span
                                    class="text-sm font-normal">kkal</span></h4>
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

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Antaran Hari Ini</span>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Logistik</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-lg font-bold text-gray-800 truncate">Menu Belum Dipilih</h4>
                        <div class="flex mt-1 text-amber-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                <path
                                    d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z" />
                                <path
                                    d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                            </svg>
                            <p class="text-xs font-medium ms-2"> Silakan order menu terlebih dahulu</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Status Langganan</span>
                        <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded">Paket</span>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-2xl font-bold text-gray-400">Tidak Aktif</h4>
                        <a href="#" class="text-xs text-green-600 hover:underline font-semibold block mt-1">Pilih
                            paket
                            sehatmu →</a>
                    </div>
                </div>

            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-2">Metrik Fisik Terdaftar Anda:</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                    <div>• Umur: <span class="font-semibold text-gray-900">{{ $profile->age }} tahun</span></div>
                    <div>• Berat Badan: <span class="font-semibold text-gray-900">{{ $profile->weight_kg }}
                            kg</span></div>
                    <div>• Tinggi Badan: <span class="font-semibold text-gray-900">{{ $profile->height_cm }}
                            cm</span></div>
                    <div>• Aktivitas: <span
                            class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $profile->activity_level) }}</span>
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

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-200 shadow">
                        Simpan & Hitung Kalori
                    </button>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
