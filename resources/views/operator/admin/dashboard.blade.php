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
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

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
                            
                            <!-- 1 Column Grid -->
                            <div class="grid grid-cols-1 gap-4">
                                
                                <!-- Card: Saldo Toko -->
                                <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                            <i data-feather="credit-card" class="w-6 h-6"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-xs font-bold text-slate-400 block">Saldo Toko</span>
                                            <h3 class="text-3xl font-black text-[#0f172a] mt-1">Rp {{ number_format($saldoToko, 0, ',', '.') }}</h3>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <a href="{{ route('admin.keuangan') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1 bg-blue-50 px-3 py-2 rounded-xl transition-all">
                                            <span>Lihat Detail</span>
                                            <i data-feather="arrow-right" class="w-3.5 h-3.5"></i>
                                        </a>
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
