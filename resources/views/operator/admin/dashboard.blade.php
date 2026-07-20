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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">

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
<body class="font-dm-sans antialiased bg-[#f8fafc] text-[#1e293b] h-full overflow-hidden" x-data="{ sidebarOpen: false }">

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
                </div>
                
                <!-- Right Header Actions -->
                <div class="flex items-center gap-4" x-data="{ open: false }">
                    <div class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 hover:bg-slate-50 px-3 py-1.5 rounded-xl transition-all">
                            <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-8 h-8 rounded-full border border-slate-100 object-cover">
                        </button>
                        
                        <!-- Dropdown Settings/Logout -->
                        <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                <p class="text-xs font-medium text-[#0f172a]">MyZyngga Operator</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? 'operator@zyngga.com' }}</p>
                            </div>
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                <i data-feather="settings" class="w-3.5 h-3.5 text-slate-400"></i>
                                Pengaturan Toko
                            </a>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50">
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
            <div class="flex-1 overflow-y-auto px-6 py-8 custom-scrollbar bg-[#f0f6ff]">
                
                <!-- Inner Page Stack -->
                <div class="max-w-3xl mx-auto w-full space-y-8">
                    
                    <!-- SECTION 1: PESANAN AKTIF -->
                    <section class="space-y-4">
                        <div>
                            <h1 class="text-xl font-medium text-[#0f172a] leading-none">Pesanan Aktif</h1>
                            <p class="text-xs font-normal text-slate-400 mt-1.5">
                                Data diambil dari 7 hari terakhir
                            </p>
                        </div>
                        
                        <!-- Cards Grid: 2 columns on mobile, 5 columns on desktop -->
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                            <!-- Card 1 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-diproses']) }}" class="flex flex-col justify-between h-28 bg-white p-5 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.01)] hover:shadow-md transition-all duration-300">
                                <span class="text-xs font-normal text-slate-400">Perlu Diproses</span>
                                <h3 class="text-3xl font-medium text-[#0f172a]">{{ $perluDiprosesCount }}</h3>
                            </a>

                            <!-- Card 2 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'menunggu-pembayaran']) }}" class="flex flex-col justify-between h-28 bg-white p-5 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.01)] hover:shadow-md transition-all duration-300">
                                <span class="text-xs font-normal text-slate-400">Menunggu Pembayaran</span>
                                <h3 class="text-3xl font-medium text-[#0f172a]">{{ $menungguPembayaranCount }}</h3>
                            </a>

                            <!-- Card 3 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-dikerjakan']) }}" class="flex flex-col justify-between h-28 bg-white p-5 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.01)] hover:shadow-md transition-all duration-300">
                                <span class="text-xs font-normal text-slate-400">Perlu Dikerjakan</span>
                                <h3 class="text-3xl font-medium text-[#0f172a]">{{ $perluDikerjakanCount }}</h3>
                            </a>

                            <!-- Card 4 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala']) }}" class="flex flex-col justify-between h-28 bg-white p-5 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.01)] hover:shadow-md transition-all duration-300">
                                <span class="text-xs font-normal text-slate-400">Kendala Pesanan</span>
                                <h3 class="text-3xl font-medium text-[#0f172a]">0</h3>
                            </a>

                            <!-- Card 5 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'dibatalkan']) }}" class="flex flex-col justify-between h-28 bg-white p-5 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.01)] hover:shadow-md transition-all duration-300">
                                <span class="text-xs font-normal text-slate-400">Sedang Dibatalkan</span>
                                <h3 class="text-3xl font-medium text-[#0f172a]">0</h3>
                            </a>
                        </div>
                    </section>

                    <!-- SECTION 2: KEUANGAN TOKO -->
                    <section class="space-y-4">
                        <div>
                            <h1 class="text-xl font-medium text-[#0f172a] leading-none">Keuangan Toko</h1>
                        </div>
                        
                        <!-- Keuangan Card -->
                        <div class="bg-white p-6 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.01)] w-full">
                            <div class="flex items-center gap-4 mb-5">
                                <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0">
                                    <i data-feather="credit-card" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-slate-400 block">Saldo Toko</span>
                                    <h3 class="text-2xl sm:text-3xl font-medium text-[#0f172a] mt-1">Rp {{ number_format($saldoToko, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                            <a href="{{ route('admin.keuangan') }}" class="block w-full bg-[#0b4ab1] hover:bg-blue-800 text-white text-sm font-medium py-3 px-4 rounded-xl text-center transition-colors">
                                Lihat Detail Keuangan
                            </a>
                        </div>
                    </section>

                    <!-- SECTION 3: PERFORMA TOKO -->
                    <section class="space-y-4">
                        <div>
                            <h1 class="text-xl font-medium text-[#0f172a] leading-none">Performa Toko</h1>
                            <p class="text-xs font-normal text-slate-400 mt-1.5">
                                Statistik Toko - Data dari 30 hari terakhir
                            </p>
                        </div>
                        
                        <!-- Performa Panel Card -->
                        <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.01)] overflow-hidden">
                            <div class="divide-y divide-slate-100">
                                <!-- Metric 1: Jumlah Pelanggan -->
                                <div class="flex items-center justify-between p-5">
                                    <span class="text-sm font-medium text-slate-700">Jumlah Pelanggan</span>
                                    <span class="text-base font-medium text-[#0f172a]">88</span>
                                </div>

                                <!-- Metric 2: Pesanan Selesai -->
                                <div class="flex items-center justify-between p-5">
                                    <span class="text-sm font-medium text-slate-700">Pesanan Selesai</span>
                                    <span class="text-base font-medium text-[#0f172a]">{{ $pesananSelesaiCount }}</span>
                                </div>

                                <!-- Metric 3: Pesanan Dibatalkan -->
                                <div class="flex items-center justify-between p-5">
                                    <span class="text-sm font-medium text-slate-700">Pesanan Dibatalkan</span>
                                    <span class="text-base font-medium text-[#0f172a]">1</span>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
                
            </div>
            
        </div>
        
    </div>

    <!-- FLOATING CHAT BUTTON -->
    <a href="#" class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-5 py-3.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 flex items-center gap-2 z-40">
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
