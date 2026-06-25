<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedRole = $request->query('role');

        // Gunakan Eager Loading untuk memuat profil dan detail driver sekaligus
        $query = User::with(['profile', 'driverDetail']);

        if ($selectedRole) {
            $query->where('role', $selectedRole);
        }

        // Ganti ->get() menjadi ->paginate(10) (10 adalah jumlah data per halaman)
        // withQueryString() digunakan agar parameter URL (seperti ?role=admin) tetap terbawa saat pindah halaman
        $users = $query->latest()->paginate(5)->withQueryString();

        return view('admin.users', compact('users', 'selectedRole'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,nutritionist,driver,customer',
            'phone_number' => 'nullable|string',
            // Validasi opsional khusus driver
            'vehicle_plate_number' => 'required_if:role,driver|nullable|string',
            'delivery_zone' => 'required_if:role,driver|nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone_number' => $request->phone_number,
            ]);

            // Jika yang didaftarkan adalah driver, buat baris detailnya otomatis
            if ($request->role === 'driver') {
                $user->driverDetail()->create([
                    'vehicle_plate_number' => $request->vehicle_plate_number,
                    'delivery_zone' => $request->delivery_zone,
                    'status' => 'available',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Akun pengguna berhasil didaftarkan ke sistem!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
