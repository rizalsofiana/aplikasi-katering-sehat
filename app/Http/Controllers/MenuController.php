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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calories' => 'required|integer|min:0',
            'protein_g' => 'required|numeric|min:0',
            'carbs_g' => 'required|numeric|min:0',
            'fat_g' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $menu = Menu::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_available' => $request->has('is_available') ? true : true,
            ]);

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
            'is_available' => 'required|     ',
        ]);

        $menu = Menu::findOrFail($id);

        DB::transaction(function () use ($request, $menu) {
            $menu->update([
                'name' => $request->name,
                'description' => $request->description,
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
