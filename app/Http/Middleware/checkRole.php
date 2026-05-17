<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login atau belum
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Ambil data user yang sedang login
        $user = Auth::user();

        // 3. Cocokkan role user dengan daftar role yang diizinkan di rute tersebut
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Jika tidak punya akses, lemparkan ke halaman default atau tampilkan error 403
        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
