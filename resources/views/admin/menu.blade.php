<x-app-layout>
    <div x-data="{
        editModalOpen: false,
        editUrl: '',
        menuData: { name: '', description: '', calories: '', protein_g: '', carbs_g: '', fat_g: '', is_available: '1' }
    }" class="py-8 px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-xl font-bold text-slate-900">Kelola Menu Katering Diet</h3>
            <p class="text-sm text-slate-500 mt-0.5">Manajemen basis data kuliner sehat terintegrasi tabel nutrisi makro.
            </p>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
                🎉 {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs">
                <p class="font-bold mb-1">⚠️ Terjadi kesalahan input:</p>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <h4 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-2">Tambah Menu Baru</h4>

                <form action="{{ route('admin.menu.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama
                            Makanan</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="Contoh: Berry Nutty Oatmeal"
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Deskripsi /
                            Komposisi</label>
                        <textarea name="description" rows="2" placeholder="Komposisi bahan makanan..."
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Harga</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                            placeholder="0.00"
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Energi /
                                Kalori</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input type="number" name="calories" value="{{ old('calories') }}" placeholder="380"
                                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm pr-12"
                                    required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-xs text-slate-400 font-semibold">kkal</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Ketersediaan</label>
                            <select name="is_available"
                                class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                                <option value="1" {{ old('is_available') == '1' ? 'selected' : '' }}>Ready
                                    (Tersedia)</option>
                                <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Kosong (Habis)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Harga</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                            placeholder="0.00"
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            required>
                    </div>

                    <div class="grid grid-cols-3 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Protein
                                (g)</label>
                            <input type="number" step="0.01" name="protein_g" value="{{ old('protein_g') }}"
                                placeholder="24.5"
                                class="block w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                                required>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Karbo
                                (g)</label>
                            <input type="number" step="0.01" name="carbs_g" value="{{ old('carbs_g') }}"
                                placeholder="40.2"
                                class="block w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                                required>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Lemak
                                (g)</label>
                            <input type="number" step="0.01" name="fat_g" value="{{ old('fat_g') }}"
                                placeholder="8.7"
                                class="block w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                                required>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl transition shadow-md shadow-emerald-100 text-sm">
                        Simpan Data Menu
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h4 class="font-bold text-slate-900 text-base">Daftar Menu Katering Aktif</h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr
                                class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
                                <th class="py-4 px-6">Nama Menu</th>
                                <th class="py-4 px-6">Nilai Gizi Makro</th>
                                <th class="py-4 px-6">Harga</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($menus as $menu)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-slate-900">{{ $menu->name }}
                                            <span
                                                class="text-xs {{ $menu->is_available ? 'text-green-600' : 'text-red-600' }} ml-1">({{ $menu->is_available ? 'Ready' : 'Kosong' }})</span>
                                        </p>
                                        <p class="text-xs text-slate-400 truncate max-w-[220px]">
                                            {{ $menu->description ?? 'Tidak ada deskripsi.' }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-600">
                                        @if ($menu->nutrition)
                                            <p class="font-bold text-slate-900">{{ $menu->nutrition->calories }} kkal
                                            </p>
                                            <p class="text-slate-400 mt-0.5">P: {{ $menu->nutrition->protein_g }}g |
                                                K:
                                                {{ $menu->nutrition->carbs_g }}g | L: {{ $menu->nutrition->fat_g }}g
                                            </p>
                                        @else
                                            <span class="text-xs text-rose-400 italic font-medium">Data gizi
                                                kosong</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-slate-900">Rp
                                            {{ number_format($menu->price, 0, ',', '.') }}
                                        </p>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button
                                                @click="
                                                    editUrl = '{{ route('admin.menu.update', $menu->id) }}';
                                                    menuData.name = '{{ addslashes($menu->name) }}';
                                                    menuData.description = '{{ addslashes($menu->description) }}';
                                                    menuData.price = '{{ $menu->price }}';
                                                    menuData.calories = '{{ $menu->nutrition->calories ?? 0 }}';
                                                    menuData.protein_g = '{{ $menu->nutrition->protein_g ?? 0 }}';
                                                    menuData.carbs_g = '{{ $menu->nutrition->carbs_g ?? 0 }}';
                                                    menuData.fat_g = '{{ $menu->nutrition->fat_g ?? 0 }}';
                                                    menuData.is_available = '{{ $menu->is_available ? '1' : '0' }}';
                                                    editModalOpen = true;
                                                "
                                                class="text-xs font-bold text-emerald-600 hover:text-emerald-800 hover:underline">
                                                Edit
                                            </button>

                                            <form action="{{ route('admin.menu.destroy', $menu->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus menu ini beserta data gizinya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs font-bold text-red-500 hover:text-red-700 hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-slate-400 italic">Belum
                                        ada
                                        menu makanan diet yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                @click="editModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 space-y-4">

                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-lg font-bold text-slate-900">Ubah Data Menu Diet 📝</h3>
                        <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="editUrl" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama
                                Makanan</label>
                            <input type="text" name="name" x-model="menuData.name"
                                class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                                required>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Deskripsi
                                / Komposisi</label>
                            <textarea name="description" rows="2" x-model="menuData.description"
                                class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"></textarea>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Harga</label>
                            <input type="number" step="0.01" name="price" x-model="menuData.price"
                                class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                                required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Energi
                                    / Kalori (kkal)</label>
                                <input type="number" name="calories" x-model="menuData.calories"
                                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                                    required>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status
                                    Ketersediaan</label>
                                <select name="is_available" x-model="menuData.is_available"
                                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                                    required>
                                    <option value="1">Ready (Tersedia)</option>
                                    <option value="0">Kosong (Habis)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Protein
                                    (g)</label>
                                <input type="number" step="0.01" name="protein_g" x-model="menuData.protein_g"
                                    class="block w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                                    required>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Karbo
                                    (g)</label>
                                <input type="number" step="0.01" name="carbs_g" x-model="menuData.carbs_g"
                                    class="block w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                                    required>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Lemak
                                    (g)</label>
                                <input type="number" step="0.01" name="fat_g" x-model="menuData.fat_g"
                                    class="block w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                                    required>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
                            <button type="button" @click="editModalOpen = false"
                                class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-slate-50 transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-md shadow-emerald-100">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
