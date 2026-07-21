<aside class="dark:bg-slate-850 max-w-64 ease-nav-brand z-990 fixed inset-y-0 my-4 block w-full -translate-x-full flex-wrap items-center justify-between overflow-y-auto rounded-2xl border-0 bg-white p-0 antialiased shadow-xl transition-transform duration-200 dark:shadow-none xl:left-0 xl:ml-6 xl:translate-x-0" aria-expanded="false">
    <!-- Store Profile Head -->
    <div class="p-6 border-b border-slate-100 flex flex-col items-start relative">
        <i class="ri-close-large-fill absolute right-0 top-0 cursor-pointer p-4 text-slate-400 opacity-50 dark:text-white xl:hidden" sidenav-close></i>
        <img src="/img/logo-laundry-simokerto.png" alt="MyZyngga" class="w-14 h-14 rounded-full border border-slate-100 object-cover shadow-sm mb-3">
        <h2 class="font-medium text-[#0f172a] text-lg leading-tight">{{ Auth::user()->name ?? 'Rian' }}</h2>
        <span class="text-xs text-slate-400 font-normal mt-1">Operator Laundry</span>
    </div>

    <!-- Navigation Links -->
    <div class="h-sidenav block max-h-screen w-auto grow basis-full items-center overflow-auto px-4 py-6 space-y-7">
        <!-- Group 1: Tokoku -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <i class="ri-home-5-line text-slate-400"></i>
                <span>Tokoku</span>
            </div>
            <ul class="space-y-1 pl-6 flex flex-col pl-0 list-none">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ Request::routeIs('admin.dashboard') || Request::routeIs('dashboard') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(Request::routeIs('admin.dashboard') || Request::routeIs('dashboard'))
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span>
                        @endif
                        Beranda
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.keuangan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ Request::routeIs('admin.keuangan') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(Request::routeIs('admin.keuangan'))
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span>
                        @endif
                        Keuangan Toko
                    </a>
                </li>
            </ul>
        </div>

        <!-- Group 2: Pesanan -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <i class="ri-shopping-bag-3-line text-slate-400"></i>
                <span>Pesanan</span>
            </div>
            <ul class="space-y-1 pl-6 flex flex-col pl-0 list-none">
                <li>
                    <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala' ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->routeIs('admin.riwayat-pesanan') && request()->query('tab') !== 'kendala')
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span>
                        @endif
                        Riwayat Pesanan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala']) }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->query('tab') === 'kendala' ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->query('tab') === 'kendala')
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span>
                        @endif
                        Kendala Pesanan
                    </a>
                </li>
            </ul>
        </div>

        <!-- Group 3: Karyawan -->
        <div>
            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <i class="ri-group-line text-slate-400"></i>
                <span>Karyawan</span>
            </div>
            <ul class="space-y-1 pl-6 flex flex-col pl-0 list-none">
                <li>
                    <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.gaji-karyawan') ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->routeIs('admin.gaji-karyawan'))
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span>
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
                <i class="ri-shield-line text-slate-400"></i>
                <span>Manajemen Admin</span>
            </div>
            <ul class="space-y-1 pl-6 flex flex-col pl-0 list-none">
                <li>
                    <a href="{{ route('user') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs(['user', 'user.create', 'user.edit', 'user.view', 'user.edit.password']) ? 'font-medium bg-blue-50 text-blue-600' : 'font-normal text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} transition-all">
                        @if(request()->routeIs(['user', 'user.create', 'user.edit', 'user.view', 'user.edit.password']))
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1"></span>
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
