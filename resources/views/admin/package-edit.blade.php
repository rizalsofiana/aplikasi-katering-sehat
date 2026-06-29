<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-xl mx-auto space-y-6">

        <a href="{{ route('admin.packages.index') }}"
            class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800 space-x-1">
            <span>← Kembali ke Daftar Paket</span>
        </a>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900">Ubah Paket Langganan 📝</h3>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui data spesifikasi atau harga paket yang Anda pilih.</p>
            </div>

            <form action="{{ route('admin.packages.update', $package->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Paket</label>
                    <input type="text" name="package_name" value="{{ old('package_name', $package->package_name) }}"
                        required
                        class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    @error('package_name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Deskripsi Paket</label>
                    <textarea name="description" value="{{ old('description', $package->description) }}" rows="3"
                        placeholder="Contoh: Paket ini mencakup menu makan siang dan malam kaya serat..."
                        class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tipe Durasi</label>
                        <select name="duration_type" required
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                            <option value="weekly"
                                {{ old('duration_type', $package->duration_type) == 'weekly' ? 'selected' : '' }}>
                                Mingguan (Weekly)</option>
                            <option value="monthly"
                                {{ old('duration_type', $package->duration_type) == 'monthly' ? 'selected' : '' }}>
                                Bulanan (Monthly)</option>
                            <option value="yearly"
                                {{ old('duration_type', $package->duration_type) == 'yearly' ? 'selected' : '' }}>
                                Tahunan (Yearly)</option>
                        </select>
                        @error('duration_type')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Total Hari Aktif</label>
                        <input type="number" name="total_days" value="{{ old('total_days', $package->total_days) }}"
                            min="1" required
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                        @error('total_days')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Harga Paket (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $package->price) }}" min="0"
                        required
                        class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    @error('price')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition text-xs uppercase tracking-wider pt-2">
                    Perbarui Paket Langganan
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
