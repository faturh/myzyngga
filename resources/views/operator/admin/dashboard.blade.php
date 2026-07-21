<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Beranda - {{ config('app.name', 'Zyngga') }}</title>

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
        
        body, input, select, textarea, button {
            font-family: 'DM Sans', sans-serif;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="antialiased h-full overflow-hidden" style="background:#E6F0FF; color:#0F0F0F;" x-data="{ sidebarOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            @include('operator.partials.header', ['title' => 'Beranda'])

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar" style="background:#E6F0FF;">
                
                <!-- INNER PAGE CONTAINER -->
                <div class="max-w-5xl mx-auto w-full flex flex-col gap-4">
                    
                    <!-- SECTION 1: PESANAN AKTIF -->
                    <section class="flex flex-col gap-3">
                        <div>
                            <h2 class="text-sm font-medium" style="color:#000000;">Pesanan Aktif</h2>
                            <p class="text-[10px] font-normal mt-0.5" style="color:#808080;">
                                Data diambil dari 7 hari terakhir
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                            <!-- Card 1 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-diproses']) }}" class="flex flex-col justify-between h-24 bg-white p-4 rounded-lg hover:shadow-sm transition-all duration-300">
                                <span class="text-xs font-normal" style="color:#808080;">Perlu Diproses</span>
                                <h3 class="text-2xl font-medium" style="color:#0F0F0F;">{{ $perluDiprosesCount }}</h3>
                            </a>

                            <!-- Card 2 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'menunggu-pembayaran']) }}" class="flex flex-col justify-between h-24 bg-white p-4 rounded-lg hover:shadow-sm transition-all duration-300">
                                <span class="text-xs font-normal" style="color:#808080;">Menunggu Pembayaran</span>
                                <h3 class="text-2xl font-medium" style="color:#0F0F0F;">{{ $menungguPembayaranCount }}</h3>
                            </a>

                            <!-- Card 3 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-dikerjakan']) }}" class="flex flex-col justify-between h-24 bg-white p-4 rounded-lg hover:shadow-sm transition-all duration-300">
                                <span class="text-xs font-normal" style="color:#808080;">Perlu Dikerjakan</span>
                                <h3 class="text-2xl font-medium" style="color:#0F0F0F;">{{ $perluDikerjakanCount }}</h3>
                            </a>

                            <!-- Card 4 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'kendala']) }}" class="flex flex-col justify-between h-24 bg-white p-4 rounded-lg hover:shadow-sm transition-all duration-300">
                                <span class="text-xs font-normal" style="color:#808080;">Kendala Pesanan</span>
                                <h3 class="text-2xl font-medium" style="color:#0F0F0F;">0</h3>
                            </a>

                            <!-- Card 5 -->
                            <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'dibatalkan']) }}" class="flex flex-col justify-between h-24 bg-white p-4 rounded-lg hover:shadow-sm transition-all duration-300">
                                <span class="text-xs font-normal" style="color:#808080;">Sedang Dibatalkan</span>
                                <h3 class="text-2xl font-medium" style="color:#0F0F0F;">0</h3>
                            </a>
                        </div>
                    </section>

                    <!-- SECTION 2: KEUANGAN TOKO -->
                    <section class="flex flex-col gap-3">
                        <div>
                            <h2 class="text-sm font-medium" style="color:#000000;">Keuangan Toko</h2>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg flex flex-col gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:#E6F0FF; color:#003E9C;">
                                    <i data-feather="credit-card" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-normal" style="color:#808080;">Saldo Toko</span>
                                    <h3 class="text-2xl font-medium mt-0.5" style="color:#0F0F0F;">Rp {{ number_format($saldoToko, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                            <a href="{{ route('admin.keuangan') }}" 
                               class="block w-full text-sm font-medium py-3.5 px-4 rounded-full text-center transition-colors"
                               style="background:#003E9C; color:#FFFFFF;"
                               onmouseover="this.style.background='#002d73'" onmouseout="this.style.background='#003E9C'">
                                Lihat Detail Keuangan
                            </a>
                        </div>
                    </section>

                    <!-- SECTION 3: PERFORMA TOKO -->
                    <section class="flex flex-col gap-3">
                        <div>
                            <h2 class="text-sm font-medium" style="color:#000000;">Performa Toko</h2>
                            <p class="text-[10px] font-normal mt-0.5" style="color:#808080;">
                                Statistik Toko - Data dari 30 hari terakhir
                            </p>
                        </div>
                        
                        <div class="bg-white rounded-lg overflow-hidden">
                            <div>
                                <div class="flex items-center justify-between p-4" style="border-bottom:1px solid #F4F4F4;">
                                    <span class="text-xs font-normal" style="color:#808080;">Jumlah Pelanggan</span>
                                    <span class="text-sm font-medium" style="color:#0F0F0F;">88</span>
                                </div>

                                <div class="flex items-center justify-between p-4" style="border-bottom:1px solid #F4F4F4;">
                                    <span class="text-xs font-normal" style="color:#808080;">Pesanan Selesai</span>
                                    <span class="text-sm font-medium" style="color:#0F0F0F;">{{ $pesananSelesaiCount }}</span>
                                </div>

                                <div class="flex items-center justify-between p-4">
                                    <span class="text-xs font-normal" style="color:#808080;">Pesanan Dibatalkan</span>
                                    <span class="text-sm font-medium" style="color:#0F0F0F;">1</span>
                                </div>
                            </div>
                        </div>
                    </section>

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