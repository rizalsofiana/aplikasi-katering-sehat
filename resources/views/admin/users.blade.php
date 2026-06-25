<x-app-layout>
    <div x-data="{ userRole: 'customer' }" class="py-8 px-4 sm:px-6 lg:px-8 space-y-6">

        <div
            class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Kelola Pengguna Sistem</h3>
                <p class="text-sm text-slate-500 mt-0.5">Pantau data pengguna, daftarkan akun staf operasional, dan
                    kelola kurir.</p>
            </div>

            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-slate-400 uppercase">Filter:</span>
                <select onchange="location = this.value;"
                    class="rounded-xl border-slate-200 text-xs font-semibold text-slate-600 focus:border-emerald-500 focus:ring-emerald-500 bg-white shadow-sm">
                    <option value="{{ route('admin.users') }}">Semua Pengguna</option>
                    <option value="{{ route('admin.users', ['role' => 'customer']) }}"
                        {{ $selectedRole == 'customer' ? 'selected' : '' }}>Customer (Pelanggan)</option>
                    <option value="{{ route('admin.users', ['role' => 'nutritionist']) }}"
                        {{ $selectedRole == 'nutritionist' ? 'selected' : '' }}>Nutritionist (Ahli Gizi)</option>
                    <option value="{{ route('admin.users', ['role' => 'driver']) }}"
                        {{ $selectedRole == 'driver' ? 'selected' : '' }}>Driver (Kurir Antar)</option>
                    <option value="{{ route('admin.users', ['role' => 'admin']) }}"
                        {{ $selectedRole == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <h4 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-2">Registrasi Akun Baru</h4>

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama
                            Lengkap</label>
                        <input type="text" name="name" placeholder="Nama lengkap..."
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500" required>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Email</label>
                        <input type="email" name="email" placeholder="name@katering.com"
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Password</label>
                            <input type="password" name="password" placeholder="Min 8 karakter"
                                class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Hak
                                Akses / Role</label>
                            <select name="role" x-model="userRole"
                                class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500"
                                required>
                                <option value="customer">Customer</option>
                                <option value="nutritionist">Nutritionist</option>
                                <option value="driver">Driver (Kurir)</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nomor
                            Telepon/WA</label>
                        <input type="text" name="phone_number" placeholder="0812xxxxxxxx"
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500">
                    </div>

                    <div x-show="userRole === 'driver'" x-transition
                        class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-3">
                        <p class="text-xs font-bold text-slate-700">🚚 Atribut Khusus Kurir</p>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor
                                Plat Kendaraan</label>
                            <input type="text" name="vehicle_plate_number" placeholder="Contoh: D 1234 ABC"
                                class="block w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Wilayah
                                Distribusi (Zonasi)</label>
                            <input type="text" name="delivery_zone" placeholder="Contoh: Bandung Utara"
                                class="block w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl transition shadow-md shadow-emerald-100 text-sm">
                        Daftarkan Pengguna
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr
                                class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
                                <th class="py-4 px-6">Identitas Pengguna</th>
                                <th class="py-4 px-6">Peran (Role)</th>
                                <th class="py-4 px-6">Detail Kontak & Status</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                        <p class="text-[11px] text-slate-400 italic mt-0.5">Joined:
                                            {{ $user->created_at->format('d M Y') }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span
                                            class="text-[10px] font-extrabold px-2.5 py-1 rounded-md uppercase tracking-wider
                                        {{ $user->role === 'admin'
                                            ? 'bg-rose-50 text-rose-700 border border-rose-100'
                                            : ($user->role === 'nutritionist'
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
                                                : ($user->role === 'driver'
                                                    ? 'bg-amber-50 text-amber-700 border border-amber-100'
                                                    : 'bg-blue-50 text-blue-700 border border-blue-100')) }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-600">
                                        <p class="font-semibold text-slate-800">📞 {{ $user->phone_number ?? '-' }}</p>

                                        @if ($user->role === 'driver' && $user->driverDetail)
                                            <div
                                                class="mt-1 bg-slate-50 p-1.5 rounded-lg border border-slate-100 space-y-0.5 text-[11px]">
                                                <p class="text-slate-700"><span class="font-bold">Plat:</span>
                                                    {{ $user->driverDetail->vehicle_plate_number }}</p>
                                                <p class="text-slate-700"><span class="font-bold">Wilayah:</span>
                                                    {{ $user->driverDetail->delivery_zone ?? 'Belum Diatur' }}</p>
                                                <p
                                                    class="font-bold {{ $user->driverDetail->status === 'available' ? 'text-emerald-600' : 'text-amber-600' }}">
                                                    📍 Status: {{ strtoupper($user->driverDetail->status) }}
                                                </p>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus pengguna ini? Semua data relasi profil/driver akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs font-bold text-red-500 hover:text-red-700 hover:underline">
                                                Hapus Akun
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-slate-400 italic">Tidak
                                        ditemukan data pengguna untuk kategori ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($users->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
