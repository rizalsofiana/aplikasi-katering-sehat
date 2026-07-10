<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Illuminate\Support\Str;
use Midtrans\Snap;

class SubscriptionController extends Controller
{
    /**
     * Menampilkan daftar pilihan paket langganan (Pricing Cards)
     */
    public function index()
    {
        $packages = Package::all();
        $userId = Auth::id();

        // Gunakan zona waktu yang sesuai agar pergantian hari presisi
        $today = Carbon::today('Asia/Jakarta');

        // 1. SWEEPING OTOMATIS (Lazy Update)
        // Cari langganan user ini yang masih 'active' tapi end_date-nya sudah lewat dari hari ini
        // Lalu langsung update statusnya di tabel database menjadi 'completed'
        Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'completed']);

        // Mengambil langganan aktif milik user saat ini (jika ada)
        $activeSubscription = Subscription::with('package')
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today('Asia/Jakarta'))
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
        $startDate = Carbon::today('Asia/Jakarta');
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

        // 1. Cek jadwal langganan
        $existingActive = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->first();

        $startDate = $existingActive ? Carbon::parse($existingActive->end_date)->addDay() : Carbon::today('Asia/Jakarta');
        $endDate = $startDate->copy()->addDays($package->total_days);

        // 2. Simpan Data Langganan (Status PENDING)
        $subscription = Subscription::create([
            'user_id' => $userId,
            'package_id' => $package->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => 'active', // Ubah menjadi pending karena belum dibayar
        ]);

        // 3. Simpan Data Transaksi Pembayaran
        $invoiceNumber = 'SUB-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
        $payment = SubscriptionPayment::create([
            'subscription_id' => $subscription->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $package->price,
            'payment_method' => 'cashless',
            'status' => 'pending',
        ]);

        // 4. Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $invoiceNumber,
                'gross_amount' => (int) $package->price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $payment->update(['status' => 'completed', 'paid_at' => Carbon::now('Asia/Jakarta')]);

            // Kembalikan response JSON untuk ditangkap oleh Alpine.js
            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke gerbang pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
