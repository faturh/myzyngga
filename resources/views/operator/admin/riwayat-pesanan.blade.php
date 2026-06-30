<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Riwayat Pesanan - {{ config('app.name', 'Zyngga') }}</title>

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
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
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
                        <span>Tokoku</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
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
                            <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold bg-blue-50/70 text-blue-600 border border-blue-100/20 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
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
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
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
                                    <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold bg-blue-50/70 text-blue-600 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
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
                        <a href="{{ route('admin.riwayat-pesanan') }}" class="font-bold text-blue-600 px-1 py-2 transition-colors">Pesanan</a>
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
                
                <div class="max-w-7xl mx-auto space-y-6">
                    
                    <!-- Alerts for Success/Error -->
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold px-4 py-3 rounded-xl flex items-center gap-2">
                            <i data-feather="check-circle" class="w-4 h-4 stroke-[2.5]"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- TOP HEADER TITLE ROW -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <h1 class="text-2xl font-extrabold text-[#0f172a] tracking-tight">Riwayat Pesanan</h1>
                            <p class="text-xs font-semibold text-slate-400 mt-1">Kelola dan pantau status transaksi laundry tokomu.</p>
                        </div>
                        <button class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm flex items-center gap-2 transition-all">
                            <i data-feather="download" class="w-3.5 h-3.5"></i>
                            Unduh Riwayat Pesanan
                        </button>
                    </div>

                    <!-- TABS NAVIGATION -->
                    <div class="flex border-b border-slate-100 overflow-x-auto scrollbar-none gap-8 text-xs font-bold">
                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-diproses', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'perlu-diproses' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Perlu Diproses
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'perlu-diproses' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $perluDiprosesCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'menunggu-pembayaran', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'menunggu-pembayaran' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Menunggu Pembayaran
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'menunggu-pembayaran' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $menungguPembayaranCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-dikerjakan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'perlu-dikerjakan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Perlu Dikerjakan
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'perlu-dikerjakan' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $perluDikerjakanCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'proses-pengerjaan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'proses-pengerjaan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Proses Pengerjaan
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'proses-pengerjaan' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $prosesPengerjaanCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'selesai', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'selesai' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Pesanan Selesai
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-500">
                                {{ $pesananSelesaiCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'kendala' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Kendala Pesanan
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'kendala' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $kendalaPesananCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'dibatalkan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'dibatalkan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Sedang Dibatalkan
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'dibatalkan' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $sedangDibatalkanCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'sedang-dijemput', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'sedang-dijemput' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Sedang Dijemput
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'sedang-dijemput' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $sedangDijemputCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'semua', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'semua' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Semua Pesanan
                        </a>
                    </div>

                    <!-- SEARCH & FILTER ROW -->
                    <form method="GET" action="{{ route('admin.riwayat-pesanan') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4 bg-white border border-slate-100 p-4 rounded-2xl shadow-sm">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        
                        <div class="relative col-span-1">
                            <select class="w-full bg-slate-50 border border-slate-200/80 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 outline-none appearance-none">
                                <option>Nomor Pesanan</option>
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                                <i data-feather="chevron-down" class="w-3.5 h-3.5"></i>
                            </div>
                        </div>

                        <!-- Dropdown Sorting -->
                        <div class="relative col-span-1">
                            <select name="sort" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200/80 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 outline-none appearance-none cursor-pointer">
                                <option value="deadline" {{ $sort === 'deadline' ? 'selected' : '' }}>Deadline Terdekat</option>
                                <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                <option value="terlama" {{ $sort === 'terlama' ? 'selected' : '' }}>Terlama</option>
                                <option value="prioritas_desc" {{ $sort === 'prioritas_desc' ? 'selected' : '' }}>Prioritas Teratas</option>
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                                <i data-feather="chevron-down" class="w-3.5 h-3.5"></i>
                            </div>
                        </div>

                        <div class="relative col-span-1 sm:col-span-2">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Cari Nomor Pesanan atau Nama Pembeli..." 
                                   class="w-full bg-slate-50 border border-slate-200/80 rounded-xl pl-9 pr-3 py-2.5 text-xs font-semibold text-slate-700 outline-none placeholder:text-slate-400 focus:border-blue-500 focus:bg-white transition-all">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                <i data-feather="search" class="w-3.5 h-3.5"></i>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                Terapkan
                            </button>
                            @if(!empty($search) || $sort !== 'deadline')
                                <a href="{{ route('admin.riwayat-pesanan', ['tab' => $tab]) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-3 py-2.5 rounded-xl flex items-center justify-center transition-all">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>

                    <!-- TRANSAKSI CARDS / LIST -->
                    <div class="space-y-4">
                        @forelse($transaksi as $item)
                            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-5">
                                
                                <!-- Header Card -->
                                <div class="flex flex-wrap justify-between items-center border-b border-slate-50 pb-4 gap-2">
                                    <div class="flex items-center gap-3">
                                        <!-- Status Badge -->
                                        @if(in_array($item->status, ['Baru', 'created']))
                                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Butuh diproses</span>
                                        @elseif($item->status === 'Proses')
                                            @if($item->payment_status === 'pending')
                                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Menunggu Pembayaran</span>
                                            @else
                                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">Perlu Dikerjakan</span>
                                            @endif
                                        @elseif($item->status === 'Selesai')
                                            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">Selesai</span>
                                        @elseif($item->status === 'Batal')
                                            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg">Dibatalkan</span>
                                        @else
                                            <span class="text-xs font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">{{ $item->status }}</span>
                                        @endif

                                        <span class="text-xs font-bold text-blue-600 font-mono">{{ $item->nota }}</span>
                                    </div>
                                    <div class="text-[11px] font-semibold text-slate-400">
                                        Tanggal Transaksi: <span class="text-slate-600">{{ $item->waktu->format('d M Y H:i:s') }}</span>
                                    </div>
                                </div>

                                <!-- Body Card -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-semibold">
                                    <!-- Laundry Service Details -->
                                    <div class="space-y-1.5 md:border-r border-slate-50 md:pr-6">
                                        <p class="text-[#0f172a] font-extrabold text-sm capitalize">
                                            {{ $item->layananPrioritas->nama ?? 'Layanan Laundry' }}
                                        </p>
                                        <p class="text-slate-400">
                                            Parfum: <span class="text-slate-700 font-medium">{{ $item->parfum ?? 'Standard' }}</span>
                                        </p>
                                        <p class="text-slate-400">
                                            Layanan: <span class="text-slate-700 font-medium">{{ $item->is_roundtrip ? 'Antar-Jemput' : 'Satu Arah' }}</span>
                                        </p>
                                        <p class="text-slate-400">
                                            Metode Pembayaran: <span class="text-slate-700 font-medium uppercase">{{ $item->jenis_pembayaran }}</span>
                                        </p>
                                    </div>

                                    <!-- Customer Details -->
                                    <div class="space-y-1.5 md:border-r border-slate-50 md:pr-6">
                                        <p class="text-[#0f172a] font-extrabold">Informasi Pembeli</p>
                                        <div class="space-y-1">
                                            <p class="text-slate-400">Nama: <span class="text-slate-700">{{ $item->pelanggan->nama ?? 'N/A' }}</span></p>
                                            <p class="text-slate-400">Telepon: <span class="text-slate-700">{{ $item->pelanggan->telepon ?? 'N/A' }}</span></p>
                                            <p class="text-slate-400">Alamat: <span class="text-slate-700 font-normal line-clamp-2">{{ $item->pickup_address ?? 'N/A' }}</span></p>
                                        </div>
                                    </div>

                                    <!-- Laundry Notes / Specific Instructions -->
                                    <div class="space-y-1.5">
                                        <p class="text-[#0f172a] font-extrabold">Catatan Khusus</p>
                                        <p class="text-slate-500 font-normal leading-relaxed">
                                            {{ $item->catatan ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Footer Card / Action Buttons -->
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-t border-slate-50 pt-4 gap-4">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</p>
                                        <p class="text-base font-extrabold text-[#0f172a]">
                                            Rp {{ number_format($item->total_bayar_akhir, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <!-- Actions for "Perlu Diproses" (status 'Baru' / 'created') -->
                                    @if(in_array($item->status, ['Baru', 'created']))
                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            <form action="{{ route('admin.riwayat-pesanan.batal', $item->id) }}" method="POST" class="flex-1 sm:flex-none">
                                                @csrf
                                                <button type="submit" class="w-full text-center border border-slate-200 hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                                                    Batalkan Pesanan
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('admin.riwayat-pesanan.proses-form', $item->id) }}" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all block text-center">
                                                 Proses Pesanan
                                             </a>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @empty
                            <!-- Empty State -->
                            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mb-4">
                                    <i data-feather="inbox" class="w-8 h-8"></i>
                                </div>
                                <h3 class="text-sm font-bold text-[#0f172a]">Belum ada pesanan</h3>
                                <p class="text-xs font-semibold text-slate-400 mt-1 max-w-xs mx-auto">
                                    Tidak ada transaksi laundry yang ditemukan untuk kategori atau pencarian saat ini.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <!-- PAGINATION LINKS -->
                    @if($transaksi->hasPages())
                        <div class="mt-6 flex justify-center">
                            {{ $transaksi->links() }}
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

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
