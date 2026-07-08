<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    ### -------------------------------------------------------------
    ### BAGIAN 1: FUNGSI UNTUK PELANGGAN (CUSTOMER)
    ### -------------------------------------------------------------

    public function index()
    {
        $hasActiveSubscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active') // 💡 Sesuaikan string 'active' dengan isi database-mu (misal: 'aktif' atau 'active')
            ->where('end_date', '>=', now())
            ->exists();

        // 2. Jika tidak memiliki langganan aktif, alihkan ke halaman pembelian paket
        if (!$hasActiveSubscription) {
            return redirect()->back() // 💡 Sesuaikan nama route halaman beli paketmu
                ->with('error', 'Akses ditolak! Anda harus memiliki paket langganan aktif untuk berkonsultasi dengan Ahli Gizi.');
        }

        // 3. Jika lolos validasi, ambil riwayat konsultasi seperti biasa
        $consultations = Consultation::with('nutritionist')
            ->where('customer_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.consultation', compact('consultations'));
    }

    // Menyimpan tiket konsultasi baru dari pelanggan
    public function store(Request $request)
    {
        $request->validate([
            'topic'   => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // 1. Buat wadah/topik konsultasinya
        $consultation = Consultation::create([
            'customer_id' => Auth::id(),
            'topic'       => $request->topic,
        ]);

        // 2. Masukkan pesan pertamanya ke tabel messages
        ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_type'     => 'customer',
            'message'         => $request->message,
        ]);

        // Redirect ke halaman detail obrolan (Route disesuaikan nanti)
        return redirect()->route('customer.consultation.show', $consultation->id)
            ->with('success', 'Konsultasi berhasil diajukan!');
    }

    ### -------------------------------------------------------------
    ### BAGIAN 2: FUNGSI BERSAMA (MELIHAT & MEMBALAS OBROLAN)
    ### -------------------------------------------------------------

    // Menampilkan halaman detail obrolan (Chat room)
    public function show(int $id)
    {
        // Ambil data konsultasi beserta relasi pelanggan, ahli gizi, dan riwayat pesannya
        $consultation = Consultation::with(['customer', 'nutritionist', 'messages'])->findOrFail($id);
        $user = Auth::user();

        // Cek Role (Silakan sesuaikan pengecekan role ini dengan sistem Anda)
        // Asumsi: Anda memiliki kolom 'role' di tabel users
        if ($user->role === 'nutritionist') {
            return view('nutritionist.chat-consultation', compact('consultation'));
        }

        return view('customer.chat-consultation', compact('consultation'));
    }

    // Mengirim pesan balasan (Bisa dari Pelanggan atau Ahli Gizi)
    public function reply(Request $request, int $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $consultation = Consultation::findOrFail($id);
        $user = Auth::user();

        // Tentukan siapa yang membalas berdasarkan role
        $isNutritionist = $user->role === 'nutritionist';
        $senderType = $isNutritionist ? 'nutritionist' : 'customer';

        // Jika yang membalas adalah ahli gizi, dan tiket ini belum ada ahli gizinya, maka "klaim" tiket ini
        if ($isNutritionist && is_null($consultation->nutritionist_id)) {
            $consultation->update([
                'nutritionist_id' => $user->id,
            ]);

            if ($consultation->status === 'open') {
                $consultation->update(['status' => 'on_going']);
            }
        }

        // Simpan pesan balasan
        ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_type'     => $senderType,
            'message'         => $request->message,
        ]);

        return back()->with('success', 'Pesan terkirim!');
    }

    // Fungsi untuk menutup sesi konsultasi (Akhiri obrolan)
    public function close(int $id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->update(['status' => 'closed']);

        return back()->with('success', 'Sesi konsultasi telah ditutup.');
    }

    // NUTRITIONIST SIDE

    public function nutritionistIndex(Request $request)
    {
        $nutritionistId = Auth::id();
        // Tangkap tab status filter (default: open)
        $status = $request->status ?? 'open';

        $query = Consultation::with('customer');

        if ($status === 'open') {
            // Antrean baru masuk yang belum diklaim oleh ahli gizi mana pun
            $query->where('status', 'open')->whereNull('nutritionist_id');
        } elseif ($status === 'on_going') {
            // Konsultasi aktif yang sedang ditangani oleh ahli gizi yang sedang login
            $query->where('status', 'on_going')->where('nutritionist_id', $nutritionistId);
        } else {
            // Riwayat konsultasi selesai yang ditangani oleh ahli gizi yang sedang login
            $query->where('status', 'closed')->where('nutritionist_id', $nutritionistId);
        }

        // Ambil data dengan pagination 10 baris per halaman
        $consultations = $query->latest()->paginate(10)->withQueryString();

        // Hitung angka ringkasan untuk widget di atas dashboard
        $totalOpen = Consultation::where('status', 'open')->whereNull('nutritionist_id')->count();
        $totalOnGoing = Consultation::where('status', 'on_going')->where('nutritionist_id', $nutritionistId)->count();

        return view('nutritionist.consultation', compact('consultations', 'status', 'totalOpen', 'totalOnGoing'));
    }
}
