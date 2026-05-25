<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        // Mengambil menu beserta data nutrisinya sekaligus (Eager Loading)
        $menus = Menu::with('nutrition')->latest()->get();
        return view('admin.menu', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calories' => 'required|integer|min:0',
            'protein_g' => 'required|numeric|min:0',
            'carbs_g' => 'required|numeric|min:0',
            'fat_g' => 'required|numeric|min:0',
        ]);

        // Gunakan Transaction agar penyimpanan aman di dua tabel
        DB::transaction(function () use ($request) {
            // Simpan ke tabel 'menus'
            $menu = Menu::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_available' => $request->has('is_available') ? true : true, // default true
            ]);

            // Simpan ke tabel 'menu_nutritions' memanfaatkan relasi
            $menu->nutrition()->create([
                'calories' => $request->calories,
                'protein_g' => $request->protein_g,
                'carbs_g' => $request->carbs_g,
                'fat_g' => $request->fat_g,
            ]);
        });

        return redirect()->back()->with('success', 'Menu dan data gizi berhasil disimpan!');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calories' => 'required|integer|min:0',
            'protein_g' => 'required|numeric|min:0',
            'carbs_g' => 'required|numeric|min:0',
            'fat_g' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        $menu = Menu::findOrFail($id);

        // Gunakan Transaction agar perubahan di kedua tabel konsisten
        DB::transaction(function () use ($request, $menu) {
            // Update tabel 'menus'
            $menu->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_available' => $request->is_available,
            ]);

            // Update atau buat baru (jika sebelumnya kosong) di tabel 'menu_nutritions'
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
