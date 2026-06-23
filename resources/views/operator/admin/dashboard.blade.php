<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Operator Dashboard - {{ config('app.name', 'Zyngga') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        .transition-transform-hover:hover {
            transform: translateY(-2px);
        }
        
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="font-outfit antialiased bg-[#f8fafc] text-[#1e293b] h-full overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
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
                        <span>Dashboard</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold bg-blue-50/70 text-blue-600 border border-blue-100/20 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                Profil Toko
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
                            <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                Riwayat Pesanan
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
                            <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                Gaji Karyawan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
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
                        <div>
                            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <i data-feather="home" class="w-3.5 h-3.5"></i>
                                <span>Tokoku</span>
                            </div>
                            <ul class="space-y-1">
                                <li>
                                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold bg-blue-50/70 text-blue-600 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Beranda
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                        Profil Toko
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <i data-feather="shopping-cart" class="w-3.5 h-3.5"></i>
                                <span>Pesanan</span>
                            </div>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                        Riwayat Pesanan
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <i data-feather="users" class="w-3.5 h-3.5"></i>
                                <span>Karyawan</span>
                            </div>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                        Gaji Karyawan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                        Tambah Karyawan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            <header class="h-16 bg-white border-b border-slate-100/90 flex items-center justify-between px-6 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Mobile Hamburger Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors">
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>
                    
                    <!-- Navigation Tabs -->
                    <nav class="hidden md:flex items-center space-x-6 text-sm">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-1 font-bold text-slate-800 hover:text-blue-600 px-1 py-2 transition-colors">
                                <span>Buka</span>
                                <i data-feather="chevron-down" class="w-3.5 h-3.5"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute left-0 mt-1 w-32 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50">
                                <a href="#" class="block px-4 py-1.5 text-xs text-slate-700 hover:bg-slate-50 font-semibold">Toko Aktif</a>
                                <a href="#" class="block px-4 py-1.5 text-xs text-slate-700 hover:bg-slate-50 font-semibold">Tutup Toko</a>
                            </div>
                        </div>
                        <a href="#" class="font-semibold text-slate-500 hover:text-blue-600 px-1 py-2 transition-colors">Pesanan</a>
                        <a href="#" class="font-semibold text-slate-500 hover:text-blue-600 px-1 py-2 transition-colors">Keuangan</a>
                        <a href="#" class="font-semibold text-slate-500 hover:text-blue-600 px-1 py-2 transition-colors">Profil Toko</a>
                    </nav>
                </div>
                
                <!-- Right Header Actions -->
                <div class="flex items-center gap-4" x-data="{ open: false }">
                    <div class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 hover:bg-slate-50 px-3 py-1.5 rounded-xl transition-all">
                            <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-8 h-8 rounded-full border border-slate-100 object-cover">
                            <span class="hidden sm:inline text-sm font-bold text-[#0f172a]">MyZyngga</span>
                            <i data-feather="chevron-down" class="w-4 h-4 text-slate-400 hidden sm:inline"></i>
                        </button>
                        
                        <!-- Dropdown Settings/Logout -->
                        <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                <p class="text-xs font-bold text-[#0f172a]">MyZyngga Operator</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? 'operator@zyngga.com' }}</p>
                            </div>
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                <i data-feather="settings" class="w-3.5 h-3.5 text-slate-400"></i>
                                Pengaturan Toko
                            </a>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">
                                <i data-feather="log-out" class="w-3.5 h-3.5"></i>
                                Keluar Aplikasi
                            </a>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-6 py-8 custom-scrollbar">
                
                <!-- Inner Page Grid -->
                <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 items-start">
                    
                    <!-- LEFT/CENTER CONTENT PANEL -->
                    <div class="flex-1 w-full space-y-8">
                        
                        <!-- SECTION 1: AKTIVITAS PENTING -->
                        <section class="space-y-4">
                            <div>
                                <h1 class="text-xl font-extrabold text-[#0f172a] leading-none">Aktivitas Penting</h1>
                                <p class="text-xs font-semibold text-slate-400 mt-1.5">
                                    Hal-hal yang penting untuk kamu cek terkait tokomu. Data diambil dari 7 hari terakhir.
                                </p>
                            </div>
                            
                            <!-- 5 Cards Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                
                                <!-- Card 1 -->
                                <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-diproses']) }}" class="group block bg-white border border-slate-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-500 transition-colors">Perlu Diproses</span>
                                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                                            <i data-feather="package" class="w-4 h-4 stroke-[2.5]"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-baseline gap-1.5">
                                        <span class="text-3xl font-black text-[#0f172a]">{{ $perluDiprosesCount }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">Pesanan</span>
                                    </div>
                                </a>

                                <!-- Card 2 -->
                                <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'menunggu-pembayaran']) }}" class="group block bg-white border border-slate-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-500 transition-colors">Menunggu Pembayaran</span>
                                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                                            <i data-feather="clock" class="w-4 h-4 stroke-[2.5]"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-baseline gap-1.5">
                                        <span class="text-3xl font-black text-[#0f172a]">{{ $menungguPembayaranCount }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">Pesanan</span>
                                    </div>
                                </a>

                                <!-- Card 3 -->
                                <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-dikerjakan']) }}" class="group block bg-white border border-slate-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-500 transition-colors">Perlu Dikerjakan</span>
                                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                            <i data-feather="activity" class="w-4 h-4 stroke-[2.5]"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-baseline gap-1.5">
                                        <span class="text-3xl font-black text-[#0f172a]">{{ $perluDikerjakanCount }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">Pesanan</span>
                                    </div>
                                </a>

                                <!-- Card 4 -->
                                <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala']) }}" class="group block bg-white border border-slate-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-500 transition-colors">Kendala Pesanan</span>
                                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center">
                                            <i data-feather="alert-triangle" class="w-4 h-4 stroke-[2.5]"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-baseline gap-1.5">
                                        <span class="text-3xl font-black text-[#0f172a]">0</span>
                                        <span class="text-[10px] font-bold text-slate-400">Kasus</span>
                                    </div>
                                </a>

                                <!-- Card 5 -->
                                <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'dibatalkan']) }}" class="group block bg-white border border-slate-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-400 group-hover:text-slate-500 transition-colors">Sedang Dibatalkan</span>
                                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center">
                                            <i data-feather="x-circle" class="w-4 h-4 stroke-[2.5]"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-baseline gap-1.5">
                                        <span class="text-3xl font-black text-[#0f172a]">0</span>
                                        <span class="text-[10px] font-bold text-slate-400">Pesanan</span>
                                    </div>
                                </a>

                            </div>
                        </section>

                        <!-- SECTION 2: KEUANGAN TOKO -->
                        <section class="space-y-4">
                            <div>
                                <h1 class="text-xl font-extrabold text-[#0f172a] leading-none">Keuangan Toko</h1>
                            </div>
                            
                            <!-- 2 Columns Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <!-- Card 1: Transaksi Berjalan -->
                                <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <i data-feather="trending-up" class="w-6 h-6"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-xs font-bold text-slate-400 block">Transaksi Berjalan</span>
                                        <h3 class="text-3xl font-black text-[#0f172a] mt-1">Rp 126.280</h3>
                                    </div>
                                </div>

                                <!-- Card 2: Saldo Toko -->
                                <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                        <i data-feather="credit-card" class="w-6 h-6"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-xs font-bold text-slate-400 block">Saldo Toko</span>
                                        <h3 class="text-3xl font-black text-[#0f172a] mt-1">Rp 654.456</h3>
                                    </div>
                                    <div class="shrink-0">
                                        <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">Lihat Detail</a>
                                    </div>
                                </div>

                            </div>
                        </section>

                        <!-- SECTION 3: PERFORMA TOKO -->
                        <section class="space-y-4">
                            <div>
                                <h1 class="text-xl font-extrabold text-[#0f172a] leading-none">Performa Toko</h1>
                                <p class="text-xs font-semibold text-slate-400 mt-1.5">
                                    Statistik Toko - Data dari 30 hari terakhir
                                </p>
                            </div>
                            
                            <!-- Performa Panel Card -->
                            <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    
                                    <!-- Metric 1: Jumlah Pembeli -->
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                            <i data-feather="users" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-400 block">Jumlah Pelanggan</span>
                                            <h4 class="text-3xl font-black text-[#0f172a] mt-0.5">88</h4>
                                        </div>
                                    </div>

                                    <!-- Metric 2: Pesanan Selesai -->
                                    <div class="flex items-center gap-4 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                            <i data-feather="check-circle" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-400 block">Pesanan Selesai</span>
                                            <h4 class="text-3xl font-black text-[#0f172a] mt-0.5">{{ $pesananSelesaiCount }}</h4>
                                        </div>
                                    </div>

                                    <!-- Metric 3: Pesanan Dibatalkan -->
                                    <div class="flex items-center gap-4 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                            <i data-feather="x-square" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-400 block">Pesanan Dibatalkan</span>
                                            <h4 class="text-3xl font-black text-[#0f172a] mt-0.5">1</h4>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>

                    </div>
                    
                    <!-- RIGHT SIDEBAR PANEL -->
                    <aside class="w-full lg:w-80 shrink-0 space-y-6">
                        


                        <!-- Info Card -->
                        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-4">
                            <div class="flex items-center gap-2 border-b border-slate-50 pb-3">
                                <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i data-feather="info" class="w-3.5 h-3.5"></i>
                                </div>
                                <h2 class="font-extrabold text-sm text-[#0f172a]">Informasi Fitur Pesanan</h2>
                            </div>
                            
                            <ul class="space-y-4 text-xs font-semibold text-slate-500">
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-[10px] font-bold">1</span>
                                    <div>
                                        <p class="text-[#0f172a] font-bold text-xs">Proses Pesanan Cepat</p>
                                        <p class="text-[11px] font-normal leading-relaxed text-slate-400 mt-0.5">Kamu bisa langsung mengonfirmasi pesanan masuk dengan menekan tombol konfirmasi pada setiap detail pesanan.</p>
                                    </div>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-[10px] font-bold">2</span>
                                    <div>
                                        <p class="text-[#0f172a] font-bold text-xs">Pantau Pendapatan</p>
                                        <p class="text-[11px] font-normal leading-relaxed text-slate-400 mt-0.5">Semua dana dari transaksi berjalan akan diteruskan ke Saldo Toko secara instan setelah pelanggan menerima pesanan.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </aside>

                </div>
                
            </div>
            
        </div>
        
    </div>

    <!-- FLOATING CHAT BUTTON -->
    <a href="#" class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-5 py-3.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 flex items-center gap-2 z-40">
        <i data-feather="message-circle" class="w-4 h-4"></i>
        <span>Pesan</span>
    </a>

    <!-- Initialize Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
</body>
</html>
