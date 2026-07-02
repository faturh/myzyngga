<!-- SIDEBAR LEFT (DESKTOP) -->
<aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-100/90 h-full shrink-0">
    <!-- Store Profile Head -->
    <div class="p-6 border-b border-slate-100 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-10 h-10 rounded-full border border-slate-100 object-cover shadow-sm">
            <div>
                <h2 class="font-bold text-[#0f172a] text-sm leading-tight">MyZyngga</h2>
                <span class="text-[11px] text-slate-400 font-medium">Laundry Operator</span>
            </div>
        </div>
        
        <!-- Buka Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-600 px-2.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 transition-all">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Buka
                <i data-feather="chevron-down" class="w-3 h-3 stroke-[2.5]"></i>
            </button>
            <!-- Dropdown Options -->
            <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-1.5 w-36 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50">
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Buka (Aktif)
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Istirahat
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Tutup Toko
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 overflow-y-auto custom-scrollbar px-4 py-6 space-y-7">
        <!-- Group 1: Tokoku -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <i data-feather="home" class="w-3.5 h-3.5"></i>
                <span>Tokoku</span>
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.dashboard') ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                        Beranda
                    </a>
                </li>
            </ul>
        </div>

        <!-- Group 2: Transaksi -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <i data-feather="shopping-cart" class="w-3.5 h-3.5"></i>
                <span>Pesanan</span>
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala' ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala' ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                        Riwayat Pesanan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.riwayat-pesanan.tambah-form') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.riwayat-pesanan.tambah-form') ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.riwayat-pesanan.tambah-form') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                        Tambah Pesanan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->query('tab') === 'kendala' ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->query('tab') === 'kendala' ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                        Kendala Pesanan
                    </a>
                </li>
            </ul>
        </div>

        <!-- Group 3: Karyawan -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <i data-feather="users" class="w-3.5 h-3.5"></i>
                <span>Karyawan</span>
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.gaji-karyawan') ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.gaji-karyawan') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                        Gaji Karyawan
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('user.create') ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('user.create') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                        Tambah Karyawan
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Sidebar Footer Info -->
    <div class="p-6 border-t border-slate-100 bg-slate-50/50">
        <div class="text-[11px] text-slate-400 text-center font-medium">
            &copy; 2026 Zyngga Laundry.
        </div>
    </div>
</aside>

<!-- SIDEBAR LEFT (MOBILE SLIDE-OVER) -->
<div x-show="sidebarOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div class="fixed inset-0 flex">
        <!-- Sidebar container -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             @click.away="sidebarOpen = false" 
             class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white">
            
            <!-- Close Button -->
            <div class="absolute right-[-48px] top-3">
                <button @click="sidebarOpen = false" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 focus:outline-none">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Store Profile Head -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-10 h-10 rounded-full border border-slate-100 object-cover shadow-sm">
                    <div>
                        <h2 class="font-bold text-[#0f172a] text-sm leading-tight">MyZyngga</h2>
                        <span class="text-[11px] text-slate-400 font-medium">Laundry Operator</span>
                    </div>
                </div>
                
                <!-- Buka Badge -->
                <span class="bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Buka
                </span>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
                <!-- Group 1: Tokoku -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i data-feather="home" class="w-3.5 h-3.5"></i>
                        <span>Tokoku</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.dashboard') ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                                Beranda
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 2: Transaksi -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i data-feather="shopping-cart" class="w-3.5 h-3.5"></i>
                        <span>Pesanan</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala' ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala' ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                                Riwayat Pesanan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.riwayat-pesanan.tambah-form') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.riwayat-pesanan.tambah-form') ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.riwayat-pesanan.tambah-form') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                                Tambah Pesanan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->query('tab') === 'kendala' ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->query('tab') === 'kendala' ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                                Kendala Pesanan
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 3: Karyawan -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i data-feather="users" class="w-3.5 h-3.5"></i>
                        <span>Karyawan</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.gaji-karyawan') ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.gaji-karyawan') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                                Gaji Karyawan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('user.create') ? 'font-bold bg-blue-50/70 text-blue-600' : 'font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('user.create') ? 'bg-blue-500' : 'bg-transparent' }}"></span>
                                Tambah Karyawan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Sidebar Footer Info -->
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                <div class="text-[11px] text-slate-400 text-center font-medium">
                    &copy; 2026 Zyngga Laundry.
                </div>
            </div>
        </div>
    </div>
</div>
