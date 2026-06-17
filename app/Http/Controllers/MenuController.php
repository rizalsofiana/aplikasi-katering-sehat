<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('nutrition')->latest()->get();
        return view('admin.menu', compact('menus'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Gabungan (Menu & Nutrisi)
        $request->validate([
            // Validasi Menu Utama
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
            'is_available'  => 'required|boolean',

            // Validasi Nutrisi Gizi
            'calories'      => 'required|integer|min:0',
            'protein_g'     => 'required|numeric|min:0',
            'carbs_g'       => 'required|numeric|min:0',
            'fat_g'         => 'required|numeric|min:0',
        ]);

        // 2. Bungkus proses insert dengan DB Transaction demi keamanan data
        DB::transaction(function () use ($request) {

            // Handle upload gambar jika ada
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('menus', 'public');
            }

            // A. Simpan ke tabel `menus`
            $menu = Menu::create([
                'name'         => $request->name,
                'description'  => $request->description,
                'image_path'   => $imagePath,
                'is_available' => $request->is_available,
                'price'        => $request->price,
            ]);

            // B. Simpan ke tabel `menu_nutritions` memanfaatkan relasi hasOne Eloquent
            // Eloquent otomatis mengisi kolom 'menu_id' berdasarkan id $menu baru di atas
            $menu->nutrition()->create([
                'calories'  => $request->calories,
                'protein_g' => $request->protein_g,
                'carbs_g'   => $request->carbs_g,
                'fat_g'     => $request->fat_g,
            ]);
        });

        return redirect()->back()->with('success', 'Menu dan data gizi berhasil disimpan!');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'calories' => 'required|integer|min:0',
            'protein_g' => 'required|numeric|min:0',
            'carbs_g' => 'required|numeric|min:0',
            'fat_g' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        $menu = Menu::findOrFail($id);

        DB::transaction(function () use ($request, $menu) {
            $menu->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'is_available' => $request->is_available,
            ]);

            $menu->nutrition()->updateOrCreate(
                ['menu_id' => $menu->id],
                [
                    'calories' => $request->calories,
                    'protein_g' => $request->protein_g,
                    'carbs_g' => $request->carbs_g,
                    'fat_g' => $request->fat_g,
                ]
            );
        });

        return redirect()->back()->with('success', 'Data menu diet berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->back()->with('success', 'Menu berhasil dihapus dari sistem.');
    }
}
