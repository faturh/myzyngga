<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detail & Upgrade Pesanan - {{ config('app.name', 'Zyngga') }}</title>

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

    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

    <style>
        [x-cloak] { display: none !important; }
        
        body, input, select, textarea, button {
            font-family: 'DM Sans', sans-serif;
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

        select {
            background-image: none !important;
            -webkit-appearance: none;
            appearance: none;
        }
        select::-ms-expand { display: none; }
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
            <header class="h-12 bg-white flex items-center justify-between px-4 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-1 text-[#0F0F0F] hover:opacity-70 transition-opacity">
                        <i data-feather="menu" class="w-5 h-5"></i>
                    </button>
                    <a href="{{ route('admin.riwayat-pesanan', ['tab' => $tab]) }}" class="flex items-center gap-1.5 text-xs font-medium text-[#0F0F0F] hover:text-[#003E9C] transition-colors">
                        <i class="ri-arrow-left-line text-base"></i>
                        <span>Kembali</span>
                    </a>
                </div>
                
                <div class="relative" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" type="button" class="flex items-center focus:outline-none cursor-pointer bg-transparent border-0 p-0">
                        <img src="/img/logo-zyngga.png" alt="MyZyngga" class="w-6 h-6 rounded-full object-cover" style="border:0.5px solid #0F0F0F;">
                    </button>
                    
                    <div x-show="profileOpen" 
                         @click.outside="profileOpen = false" 
                         x-transition 
                         x-cloak 
                         class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-slate-100 py-1 z-50">
                        <button type="button" 
                                onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();" 
                                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors text-left bg-transparent border-0 cursor-pointer">
                            <i data-feather="log-out" class="w-4 h-4 text-rose-600"></i>
                            <span>Keluar Aplikasi</span>
                        </button>
                        <form id="logout-form-header" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar" style="background:#E6F0FF;">
                
                <div class="max-w-xl mx-auto w-full flex flex-col gap-4">

                    <!-- ALERTS FOR ERRORS -->
                    @if(session('error'))
                        <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-medium px-4 py-3 rounded-xl flex items-center gap-2">
                            <i data-feather="alert-circle" class="w-4 h-4 shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium px-4 py-3 rounded-xl flex items-center gap-2">
                            <i data-feather="check-circle" class="w-4 h-4 shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- PAGE TITLE SECTION -->
                    <div>
                        <h1 class="text-base font-medium" style="color:#0F0F0F;">Detail & Upgrade Pesanan</h1>
                        <p class="text-xs font-normal" style="color:#808080;">{{ $transaksi->nota }}</p>
                    </div>

                    @php
                        $hasHistory = isset($transaksi->upgradeLayanans) && $transaksi->upgradeLayanans->isNotEmpty();
                        $originalService = $hasHistory ? ($transaksi->upgradeLayanans->first()->layananAsal->nama ?? 'Reguler') : null;
                        $currentService = $transaksi->layananPrioritas->nama ?? 'Reguler';
                    @endphp

                    <!-- KARTU 1: DETAIL PESANAN (Border Radius 8px) -->
                    <div class="bg-white p-5 shadow-sm space-y-3 border border-slate-100/80" style="border-radius: 8px;">
                        <h3 class="text-sm font-medium" style="color:#0F0F0F;">Detail Pesanan</h3>

                        <div class="space-y-2.5 text-xs font-normal">
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Nama Pelanggan</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->pelanggan->nama ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Nomor Telepon</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->pelanggan->telepon ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Jenis Layanan</span>
                                @if($hasHistory && $originalService && strtolower($originalService) !== strtolower($currentService))
                                    <div class="flex items-center gap-1.5 font-normal text-xs">
                                        <span class="text-slate-400 capitalize">{{ $originalService }}</span>
                                        <i class="ri-arrow-right-line text-slate-400 text-xs"></i>
                                        <span class="text-[#F2994A] font-medium capitalize">{{ $currentService }}</span>
                                    </div>
                                @else
                                    <span class="font-medium capitalize" style="color:#0F0F0F;">{{ $currentService }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Jenis Parfum</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->parfum ?? 'Standard' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Catatan</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->catatan ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Estimasi Awal</span>
                                <span class="font-medium" style="color:#003E9C;">Rp {{ number_format($transaksi->total_bayar_akhir, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU 2: UPGRADE LAYANAN PRIORITAS (Border Radius 8px) -->
                    @if(isset($pendingUpgrade) && $pendingUpgrade)
                        <div class="bg-amber-50 border border-amber-200 p-5 shadow-sm space-y-3" style="border-radius: 8px;">
                            <h3 class="font-medium text-amber-800 text-sm flex items-center gap-2">
                                <i data-feather="bell" class="w-4 h-4 text-amber-600"></i>
                                Permintaan Upgrade Layanan
                            </h3>
                            <div class="text-xs space-y-2 font-normal text-amber-700">
                                <div>
                                    <p class="text-[10px] text-amber-600 uppercase mb-1">Layanan Sebelum & Sesudah</p>
                                    <div class="flex items-center gap-1.5 text-xs">
                                        <span class="text-slate-500 font-normal capitalize">{{ $currentService }}</span>
                                        <i class="ri-arrow-right-line text-slate-400"></i>
                                        <span class="text-[#F2994A] font-medium capitalize">{{ $pendingUpgrade['target_service_name'] }}</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] text-amber-600 uppercase">Biaya Selisih (Tunai)</p>
                                    <p class="text-amber-900 font-medium text-sm">Rp {{ number_format($pendingUpgrade['price_diff'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <form action="{{ route('admin.riwayat-pesanan.konfirmasi-upgrade', $transaksi->id) }}" method="POST" class="pt-1">
                                @csrf
                                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-2">
                                    Konfirmasi Upgrade & Terima Cash
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-white p-5 shadow-sm space-y-4 border border-slate-100/80" style="border-radius: 8px;" x-data="{ selectedUpgradeName: '' }">
                            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                                <i class="ri-arrow-up-circle-fill text-xl text-[#003E9C]"></i>
                                <h3 class="text-sm font-medium text-[#0F0F0F]">Upgrade Layanan Prioritas</h3>
                            </div>
                            
                            <form action="{{ route('admin.riwayat-pesanan.inisiasi-upgrade', $transaksi->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-medium text-[#0F0F0F]">Pilih Layanan</label>
                                    <div class="relative">
                                        <select name="new_service_id" 
                                                @change="selectedUpgradeName = $event.target.options[$event.target.selectedIndex].getAttribute('data-nama') || ''"
                                                class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none appearance-none cursor-pointer" 
                                                style="border-color:#CCCCCC; height:44px;"
                                                required>
                                            <option value="">-- Pilih Layanan Baru --</option>
                                            @if(isset($availableUpgrades) && $availableUpgrades->isNotEmpty())
                                                @foreach($availableUpgrades as $upg)
                                                    <option value="{{ $upg->id }}" data-nama="{{ $upg->nama }}">
                                                        {{ $currentService }} → {{ $upg->nama }} (Rp {{ number_format($upg->harga, 0, ',', '.') }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#808080]">
                                            <i data-feather="chevron-down" class="w-4 h-4"></i>
                                        </div>
                                    </div>

                                    <!-- PREVIEW DYNAMIC SEBELUM & SESUDAH -->
                                    <template x-if="selectedUpgradeName">
                                        <div class="flex items-center gap-1.5 text-xs pt-1 px-1">
                                            <span class="text-slate-400 font-normal capitalize">{{ $currentService }}</span>
                                            <i class="ri-arrow-right-line text-slate-400 text-xs"></i>
                                            <span class="text-[#F2994A] font-medium capitalize" x-text="selectedUpgradeName"></span>
                                        </div>
                                    </template>
                                </div>

                                <button type="submit" 
                                        class="w-full text-white font-medium text-xs rounded-full transition-all border-0 cursor-pointer flex items-center justify-center"
                                        style="background:#003E9C; height:48px;">
                                    Proses Upgrade Layanan
                                </button>
                            </form>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

    <!-- CDNs and Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
</body>
</html>
