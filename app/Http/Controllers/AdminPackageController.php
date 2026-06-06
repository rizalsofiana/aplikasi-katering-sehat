<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class AdminPackageController extends Controller
{
    /**
     * Menampilkan semua daftar paket langganan
     */
    public function index()
    {
        // Menggunakan pagination (misal 10 data per halaman)
        $packages = Package::latest()->paginate(10);
        return view('admin.package', compact('packages'));
    }

    /**
     * Menampilkan form untuk membuat paket baru
     */
    public function create()
    {
        return view('admin.package-create');
    }

    /**
     * Menyimpan paket baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_name'  => 'required|string|max:255',
            'duration_type' => 'required|string|in:weekly,monthly,yearly,custom',
            'total_days'    => 'required|integer|min:1',
            'price'         => 'required|numeric|min:0',
        ]);

        Package::create($request->all());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket langganan berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit paket
     */
    public function edit(Package $package)
    {
        return view('admin.package-edit', compact('package'));
    }

    /**
     * Memperbarui data paket di database
     */
    public function update(Request $request, Package $package)
    {
        $request->validate([
            'package_name'  => 'required|string|max:255',
            'duration_type' => 'required|string|in:weekly,monthly,yearly,custom',
            'total_days'    => 'required|integer|min:1',
            'price'         => 'required|numeric|min:0',
        ]);

        $package->update($request->all());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket langganan berhasil diperbarui!');
    }

    /**
     * Menghapus paket dari database
     */
    public function destroy(Package $package)
    {
        // Opsional: Beri proteksi jika paket sudah memiliki database relasi subscription aktif
        if ($package->subscriptions()->count() > 0) {
            return redirect()->route('admin.packages.index')
                ->with('error', 'Paket tidak bisa dihapus karena sedang digunakan oleh customer!');
        }

        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket langganan berhasil dihapus!');
    }
}
