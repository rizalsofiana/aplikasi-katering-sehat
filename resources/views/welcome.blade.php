<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @fonts

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
    @endif
</head>

<body class="antialiased bg-slate-50 text-slate-800">

    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-2">
                    <img class="w-20 h-16" src="{{ asset('logo-katering-sehat.png') }}" alt="Logo Katering Sehat">
                    <span class="font-extrabold text-xl tracking-tight text-slate-900">
                        KateringSehat<span class="text-emerald-600">.AI</span>
                    </span>
                </div>

                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition">Masuk</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition shadow-sm shadow-emerald-200">
                                    Daftar Sekarang
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <header class="relative overflow-hidden bg-white py-20 lg:py-32 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <div class="space-y-6 text-center lg:text-left">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                    #1 AI Powered Healthy Catering di Bandung
                </span>
                <h1 class="text-4xl sm:text-5xl font-black text-slate-950 leading-tight">
                    Makan Sehat Ala Resto, <br>
                    <span class="text-emerald-600">Sesuai Kebutuhan Kalorimu.</span>
                </h1>
                <p class="text-base sm:text-lg text-slate-600 max-w-xl mx-auto lg:mx-0">
                    Dapatkan paket katering mingguan/bulanan yang dirancang khusus oleh AI dan ahli gizi untuk pekerja
                    kantoran sibuk. Sehat, enak, dan diantar tepat waktu.
                </p>
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('register') }}"
                        class="bg-emerald-600 text-white text-center px-6 py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-md shadow-emerald-200">
                        Mulai Hitung Kalori Gratis
                    </a>
                    <a href="#fitur"
                        class="border border-slate-200 text-slate-600 text-center px-6 py-3 rounded-xl font-bold hover:bg-slate-50 transition">
                        Pelajari Fitur
                    </a>
                </div>
            </div>

            <div class="relative flex justify-center">
                <div
                    class="w-72 h-72 sm:w-96 sm:h-96 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-600 absolute transform rotate-6 opacity-10">
                </div>
                <div
                    class="w-72 h-72 sm:w-96 sm:h-96 bg-white border border-slate-100 shadow-xl rounded-3xl p-8 flex flex-col justify-between relative z-10">
                    <div class="flex justify-between items-center">
                        <span class="bg-emerald-100 text-emerald-800 font-bold text-xs px-2.5 py-1 rounded-full">Menu
                            Hari Ini</span>
                        <span class="text-sm font-bold text-slate-400 flex">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-fire me-2 text-red-500" viewBox="0 0 16 16">
                                <path
                                    d="M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16m0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15" />
                            </svg>
                            450 kkal</span>
                    </div>
                    <div class="my-6">
                        <span class="text-4xl">🥗</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-2">Grilled Chicken Salad</h3>
                        <p class="text-xs text-slate-500 mt-1">Dada ayam panggang, selada organik, alpukat, dengan lemon
                            dressing.</p>
                    </div>
                    <div class="border-t border-slate-100 pt-4 flex justify-between text-center text-xs text-slate-600">
                        <div>
                            <p class="font-bold text-slate-900">35g</p>
                            <p>Protein</p>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">20g</p>
                            <p>Karbo</p>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">12g</p>
                            <p>Lemak</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <section id="fitur" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl font-extrabold text-slate-950">Mengapa Memilih KateringSehat.AI?</h2>
                <p class="text-sm sm:text-base text-slate-600 mt-2">Solusi praktis makan sehat untuk mengatasi kejenuhan
                    makanan cepat saji di area perkantoran.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                            class="bi bi-reddit" viewBox="0 0 16 16">
                            <path
                                d="M6.167 8a.83.83 0 0 0-.83.83c0 .459.372.84.83.831a.831.831 0 0 0 0-1.661m1.843 3.647c.315 0 1.403-.038 1.976-.611a.23.23 0 0 0 0-.306.213.213 0 0 0-.306 0c-.353.363-1.126.487-1.67.487-.545 0-1.308-.124-1.671-.487a.213.213 0 0 0-.306 0 .213.213 0 0 0 0 .306c.564.563 1.652.61 1.977.61zm.992-2.807c0 .458.373.83.831.83s.83-.381.83-.83a.831.831 0 0 0-1.66 0z" />
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.828-1.165c-.315 0-.602.124-.812.325-.801-.573-1.9-.945-3.121-.993l.534-2.501 1.738.372a.83.83 0 1 0 .83-.869.83.83 0 0 0-.744.468l-1.938-.41a.2.2 0 0 0-.153.028.2.2 0 0 0-.086.134l-.592 2.788c-1.24.038-2.358.41-3.17.992-.21-.2-.496-.324-.81-.324a1.163 1.163 0 0 0-.478 2.224q-.03.17-.029.353c0 1.795 2.091 3.256 4.669 3.256s4.668-1.451 4.668-3.256c0-.114-.01-.238-.029-.353.401-.181.688-.592.688-1.069 0-.65-.525-1.165-1.165-1.165" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Personalisasi Menu AI</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Menu makanan Anda dihitung otomatis oleh AI berdasarkan data berat badan, usia, tingkat
                        aktivitas, dan target diet harian Anda.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                            class="bi bi-calendar-check" viewBox="0 0 16 16">
                            <path
                                d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                            <path
                                d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Pengantaran Terjadwal</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Makanan sehat Anda akan diantarkan langsung ke meja kantor atau rumah tepat sebelum jam makan
                        siang dimulai. Tetap segar dan hangat.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                            class="bi bi-person-hearts" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M11.5 1.246c.832-.855 2.913.642 0 2.566-2.913-1.924-.832-3.421 0-2.566M9 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h10s1 0 1-1-1-4-6-4-6 3-6 4m13.5-8.09c1.387-1.425 4.855 1.07 0 4.277-4.854-3.207-1.387-5.702 0-4.276ZM15 2.165c.555-.57 1.942.428 0 1.711-1.942-1.283-.555-2.281 0-1.71Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Konsultasi Gizi Gratis</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Dapatkan akses obrolan langsung gratis bersama tim Nutrisionis bersertifikat kami langsung
                        melalui dashboard aplikasi Anda.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <footer class="bg-white border-t border-slate-100 py-8 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} KateringSehat.AI. All rights reserved.</p>
        <p class="mt-1 text-slate-400">Dibuat dengan penuh nutrisi untuk gaya hidup perkantoran yang lebih baik.</p>
    </footer>

</body>

</html>
