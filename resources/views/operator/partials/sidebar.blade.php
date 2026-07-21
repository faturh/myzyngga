<!-- SIDEBAR LEFT (DESKTOP) -->
<aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-100/90 h-full shrink-0">
    <!-- Store Profile Head -->
    <div class="p-6 border-b border-slate-100 flex flex-col items-start">
        <img src="/img/logo-laundry-simokerto.png" alt="MyZyngga" class="w-14 h-14 rounded-full border border-slate-100 object-cover shadow-sm mb-3">
        <h2 class="font-medium text-[#0f172a] text-lg leading-tight">{{ Auth::user()->name ?? 'Rian' }}</h2>
        <span class="text-xs text-slate-400 font-normal mt-1">Operator Laundry</span>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 overflow-y-auto custom-scrollbar px-4 py-6 space-y-7">
        <!-- Group 1: Tokoku -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <i data-feather="home" class="w-3.5 h-3.5"></i>
                <span>Tokoku</span>
            </div>
            <ul class="space-y-1 pl-6">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.dashboard') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->routeIs('admin.dashboard'))
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        @endif
                        Beranda
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.keuangan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.keuangan') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->routeIs('admin.keuangan'))
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        @endif
                        Keuangan Toko
                    </a>
                </li>
            </ul>
        </div>

        <!-- Group 2: Pesanan -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <i data-feather="shopping-bag" class="w-3.5 h-3.5"></i>
                <span>Pesanan</span>
            </div>
            <ul class="space-y-1 pl-6">
                <li>
                    <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala' ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala')
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        @endif
                        Riwayat Pesanan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala']) }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->query('tab') === 'kendala' ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->query('tab') === 'kendala')
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        @endif
                        Kendala Pesanan
                    </a>
                </li>
            </ul>
        </div>

        <!-- Group 3: Karyawan -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <i data-feather="users" class="w-3.5 h-3.5"></i>
                <span>Karyawan</span>
            </div>
            <ul class="space-y-1 pl-6">
                <li>
                    <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.gaji-karyawan') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->routeIs('admin.gaji-karyawan'))
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        @endif
                        Gaji Karyawan
                    </a>
                </li>
            </ul>
        </div>

        @if(auth()->user()->isAdmin())
        <!-- Group 4: Manajemen Admin -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <i data-feather="shield" class="w-3.5 h-3.5"></i>
                <span>Manajemen Admin</span>
            </div>
            <ul class="space-y-1 pl-6">
                <li>
                    <a href="{{ route('user') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs(['user', 'user.create', 'user.edit', 'user.view', 'user.edit.password']) ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->routeIs(['user', 'user.create', 'user.edit', 'user.view', 'user.edit.password']))
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        @endif
                        Kelola Akun
                    </a>
                </li>
            </ul>
        </div>
        @endif
    </div>
    
    <!-- Sidebar Footer Info -->
    <div class="p-6 border-t border-slate-100 bg-slate-50/50">
        <div class="text-[11px] text-slate-400 text-center font-normal">
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
            <div class="p-6 border-b border-slate-100 flex flex-col items-start">
                <img src="/img/logo-laundry-simokerto.png" alt="MyZyngga" class="w-14 h-14 rounded-full border border-slate-100 object-cover shadow-sm mb-3">
                <h2 class="font-medium text-[#0f172a] text-lg leading-tight">{{ Auth::user()->name ?? 'Rian' }}</h2>
                <span class="text-xs text-slate-400 font-normal mt-1">Operator Laundry</span>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-7">
                <!-- Group 1: Tokoku -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                        <i data-feather="home" class="w-3.5 h-3.5"></i>
                        <span>Tokoku</span>
                    </div>
                    <ul class="space-y-1 pl-6">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.dashboard') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                @if(request()->routeIs('admin.dashboard'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                @endif
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.keuangan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.keuangan') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                @if(request()->routeIs('admin.keuangan'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                @endif
                                Keuangan Toko
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 2: Pesanan -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                        <i data-feather="shopping-bag" class="w-3.5 h-3.5"></i>
                        <span>Pesanan</span>
                    </div>
                    <ul class="space-y-1 pl-6">
                        <li>
                            <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala' ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                @if(request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala')
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                @endif
                                Riwayat Pesanan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala']) }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->query('tab') === 'kendala' ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                @if(request()->query('tab') === 'kendala')
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                @endif
                                Kendala Pesanan
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 3: Karyawan -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                        <i data-feather="users" class="w-3.5 h-3.5"></i>
                        <span>Karyawan</span>
                    </div>
                    <ul class="space-y-1 pl-6">
                        <li>
                            <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.gaji-karyawan') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                @if(request()->routeIs('admin.gaji-karyawan'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                @endif
                                Gaji Karyawan
                            </a>
                        </li>
                    </ul>
                </div>

                @if(auth()->user()->isAdmin())
                <!-- Group 4: Manajemen Admin -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                        <i data-feather="shield" class="w-3.5 h-3.5"></i>
                        <span>Manajemen Admin</span>
                    </div>
                    <ul class="space-y-1 pl-6">
                        <li>
                            <a href="{{ route('user') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs(['user', 'user.create', 'user.edit', 'user.view', 'user.edit.password']) ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                                @if(request()->routeIs(['user', 'user.create', 'user.edit', 'user.view', 'user.edit.password']))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                @endif
                                Kelola Akun
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            </div>
            
            <!-- Sidebar Footer Info -->
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                <div class="text-[11px] text-slate-400 text-center font-normal">
                    &copy; 2026 Zyngga Laundry.
                </div>
            </div>
        </div>
    </div>
</div>
