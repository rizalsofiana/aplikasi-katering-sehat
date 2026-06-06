<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-6">

        <div
            class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span
                    class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Admin
                    Mode</span>
                <h3 class="text-xl font-bold text-slate-900 mt-1">Kelola Paket Langganan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Atur daftar produk paket durasi katering beserta penentuan
                    harganya.</p>
            </div>
            <div>
                <a href="{{ route('admin.packages.create') }}"
                    class="inline-flex items-center bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs py-3 px-4 rounded-xl transition shadow-sm">
                    ➕ Tambah Paket Baru
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] uppercase font-bold tracking-wider">
                            <th class="py-4 px-6">Nama Paket</th>
                            <th class="py-4 px-6">Tipe Durasi</th>
                            <th class="py-4 px-6">Total Hari</th>
                            <th class="py-4 px-6">Harga Paket</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm text-slate-700">
                        @forelse($packages as $package)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6 font-bold text-slate-900">{{ $package->package_name }}</td>
                                <td class="py-4 px-6">
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs capitalize">
                                        {{ $package->duration_type }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-medium">{{ $package->total_days }} Hari</td>
                                <td class="py-4 px-6 font-semibold text-emerald-600">Rp
                                    {{ number_format($package->price, 0, ',', '.') }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.packages.edit', $package->id) }}"
                                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-2 px-3 rounded-lg transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.packages.destroy', $package->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs py-2 px-3 rounded-lg transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 italic text-sm">
                                    Belum ada data paket langganan yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $packages->links() }}
        </div>

    </div>
</x-app-layout>
