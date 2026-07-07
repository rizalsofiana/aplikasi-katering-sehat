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
                        <a href="{{ route('customer.orders.index') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('customer.orders.index') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Menu
                        </a>
                        <a href="{{ route('customer.orders.history') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('customer.orders.history') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Riwayat Pesanan
                        </a>
                        <a href="{{ route('customer.consultation.index') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('customer.consultation.*') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Konsultasi Gizi
                        </a>
                        <a href="{{ route('customer.subscriptions.index') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('customer.subscriptions.*') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Paket Langganan
                        </a>
                    @elseif(Auth::user()->role === 'nutritionist')
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href="{{ route('nutritionist.consultation.index') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('nutritionist.consultation.*') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Antrean Konsultasi
                        </a>
                        <a href="#"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('nutritionist.validation') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Validasi Menu AI
                        </a>
                    @elseif(Auth::user()->role === 'driver')
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('deliveries.index') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href="{{ route('deliveries.histories') }}"
                            class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('deliveries.histories') ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
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
                    <div x-data="{ showLogoutConfirm: false }" class="relative">
                        <!-- Tombol Keluar (Trigger) -->
                        <button type="button" @click="showLogoutConfirm = true"
                            class="text-sm font-medium text-slate-400 hover:text-red-500 transition">
                            Keluar
                        </button>

                        <!-- Overlay & Modal -->
                        <div x-show="showLogoutConfirm" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                            <!-- Kotak Modal -->
                            <div @click.away="showLogoutConfirm = false"
                                class="bg-white rounded-2xl p-6 shadow-xl w-full max-w-sm text-center transform transition-all">

                                <div
                                    class="w-12 h-12 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </div>

                                <h3 class="text-lg font-bold text-slate-900">Konfirmasi Keluar</h3>
                                <p class="text-sm text-slate-500 mt-2">Apakah Anda yakin ingin mengakhiri sesi dan
                                    keluar dari akun?</p>

                                <div class="mt-6 flex gap-3">
                                    <!-- Tombol Batal -->
                                    <button type="button" @click="showLogoutConfirm = false"
                                        class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-200 transition">
                                        Batal
                                    </button>

                                    <!-- Form Logout -->
                                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                        @csrf
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-rose-600 text-white font-bold text-sm rounded-xl hover:bg-rose-700 transition">
                                            Ya, Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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
                <a href={{ route('customer.orders.index') }}
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('customer.orders.index') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Menu</a>
                <a href="{{ route('customer.consultation.index') }}"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('customer.consultation.*') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Konsultasi
                    Gizi</a>
            @elseif(Auth::user()->role === 'nutritionist')
                <a href="{{ route('dashboard') }}"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Dashboard</a>
                <a href="{{ route('nutritionist.consultation.index') }}"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('nutritionist.consultation.*') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Antrean
                    Konsultasi</a>
                <a href="#"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('nutritionist.validation') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Validasi
                    Menu AI</a>
            @elseif(Auth::user()->role === 'driver')
                <a href="{{ route('dashboard') }}"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('deliveries.index') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600' }} text-base font-medium">Dashboard</a>
                <a href="{{ route('deliveries.histories') }}"
                    class="block ps-3 pr-4 py-2 border-l-4 {{ request()->routeIs('deliveries.histories') ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50 font-bold' : 'border-transparent text-slate-600 hover:text-emerald-700' }} text-base font-medium">Riwayat
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
