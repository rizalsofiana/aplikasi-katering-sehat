<nav x-data="{ open: false }" class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center space-x-3">
                    <img src="{{ asset('logo-katering-sehat.png') }}" alt="Logo Katering Sehat"
                        class="h-9 w-auto object-contain">
                    <span class="font-black text-xl tracking-tight text-slate-900">
                        KateringSehat<span class="text-emerald-600">.AI</span>
                    </span>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    @if (Auth::user()->role === 'customer')
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href="#"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('customer.orders') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Pesanan Paket
                        </a>
                        <a href="#"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('customer.consultation') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Konsultasi Gizi
                        </a>
                    @elseif(Auth::user()->role === 'nutritionist')
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href=""
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('nutritionist.consultation') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Antrean Konsultasi
                        </a>
                        <a href="#"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('nutritionist.validation') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Validasi Menu AI
                        </a>
                    @elseif(Auth::user()->role === 'driver')
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href=""
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('driver.delivery') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Daftar Antaran Hari Ini
                        </a>
                        <a href=""
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('driver.delivery-history') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Riwayat Pengantaran
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="flex items-center space-x-4">
                    <span
                        class="text-[10px] bg-emerald-50 text-emerald-700 font-extrabold px-2.5 py-1 rounded-md tracking-wider uppercase">
                        {{ Auth::user()->role }}
                    </span>
                    <div
                        class="text-sm font-semibold text-slate-700 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                        {{ Auth::user()->name }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-400 hover:text-red-500 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-slate-50 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }"
        class="hidden sm:hidden bg-white border-t border-slate-100 animate-fade-in">
        <div class="pt-2 pb-3 space-y-1">

            @if (Auth::user()->role === 'customer')
                <a href="{{ route('dashboard') }}"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600' }} text-base font-medium">Dashboard</a>
                <a href="#"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('customer.orders') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Pesanan
                    Paket</a>
                <a href="#"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('customer.consultation') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Konsultasi
                    Gizi</a>
            @elseif(Auth::user()->role === 'nutritionist')
                <a href="{{ route('dashboard') }}"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Dashboard</a>
                <a href=""
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('nutritionist.consultation') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Antrean
                    Konsultasi</a>
                <a href="#"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('nutritionist.validation') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Validasi
                    Menu AI</a>
            @elseif(Auth::user()->role === 'driver')
                <a href="{{ route('dashboard') }}"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600' }} text-base font-medium">Dashboard</a>
                <a href=""
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('driver.antaran') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Daftar
                    Antaran Hari Ini</a>
                <a href=""
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('driver.antaran') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Riwayat
                    Pengantaran</a>
            @endif

        </div>

        <div class="pt-4 pb-1 border-t border-slate-100 bg-slate-50/50 px-4 flex justify-between items-center">
            <div>
                <div class="font-bold text-sm text-slate-800 flex items-center gap-1.5">
                    {{ Auth::user()->name }}
                    <span class="text-[9px] bg-emerald-100 text-emerald-800 font-extrabold px-1.5 py-0.5 rounded">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>
                </div>
                <div class="font-medium text-xs text-slate-500">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded-lg">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</nav>
