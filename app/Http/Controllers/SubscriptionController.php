<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Menampilkan daftar pilihan paket langganan (Pricing Cards)
     */
    public function index()
    {
        $packages = Package::all();

        // Mengambil langganan aktif milik user saat ini (jika ada)
        $activeSubscription = Subscription::with('package')
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->first();

        return view('customer.subscription', compact('packages', 'activeSubscription'));
    }

    /**
     * Halaman konfirmasi checkout pembelian paket
     */
    public function checkout(int $packageId)
    {
        $package = Package::findOrFail($packageId);

        // Hitung simulasi tanggal jika langganan dimulai hari ini
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays($package->total_days);

        return view('customer.subscriptionOrder', compact('package', 'startDate', 'endDate'));
    }

    /**
     * Memproses penyimpanan transaksi pembelian paket
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);

        $package = Package::findOrFail($request->package_id);
        $userId = Auth::id();

        // Cek apakah user memiliki langganan yang masih aktif
        $existingActive = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->first();

        // Aturan Tanggal Mulai: Jika masih punya paket aktif, paket baru dimulai setelah paket lama habis. 
        // Jika tidak ada, dimulai dari hari ini.
        $startDate = $existingActive ? Carbon::parse($existingActive->end_date)->addDay() : Carbon::today();
        $endDate = $startDate->copy()->addDays($package->total_days);

        // Simpan data transaksi langganan baru
        Subscription::create([
            'user_id' => $userId,
            'package_id' => $package->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => 'active', // Status awal bisa disesuaikan ('pending' jika ingin diintegrasikan dengan payment gateway nantinya)
        ]);

        return redirect()->route('customer.subscriptions.index')
            ->with('success', '🎉 Selamat! Pembelian paket berlangganan ' . $package->package_name . ' berhasil diaktifkan.');
    }
}
