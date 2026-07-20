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
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(1rem);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.riwayat-pesanan.tambah-form') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                                <i data-feather="plus" class="w-4 h-4"></i>
                                Tambah Pesanan Manual
                            </a>
                            <button class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm flex items-center gap-2 transition-all">
                                <i data-feather="download" class="w-3.5 h-3.5"></i>
                                Unduh Riwayat Pesanan
                            </button>
                        </div>
                    </div>

                    <!-- TABS NAVIGATION -->
                    <div class="flex border-b border-slate-100 overflow-x-auto scrollbar-none gap-8 text-xs font-bold">
                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'menunggu-di-jemput', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'menunggu-di-jemput' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Menunggu di Jemput
                            <span id="badge-menunggu-di-jemput" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'menunggu-di-jemput' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $menungguDiJemputCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-diproses', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'perlu-diproses' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Menunggu Diproses
                            <span id="badge-perlu-diproses" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'perlu-diproses' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $perluDiprosesCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-dikerjakan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'perlu-dikerjakan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Sedang Diproses
                            <span id="badge-perlu-dikerjakan" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'perlu-dikerjakan' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $perluDikerjakanCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'proses-pengerjaan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'proses-pengerjaan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Proses Pengerjaan
                            <span id="badge-proses-pengerjaan" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'proses-pengerjaan' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $prosesPengerjaanCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'menunggu-pembayaran', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'menunggu-pembayaran' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Menunggu Pembayaran
                            <span id="badge-menunggu-pembayaran" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'menunggu-pembayaran' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $menungguPembayaranCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-di-antar', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'perlu-di-antar' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Perlu di Antar
                            <span id="badge-perlu-di-antar" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'perlu-di-antar' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $perluDiAntarCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'selesai', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'selesai' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Pesanan Selesai
                            <span id="badge-selesai" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'selesai' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $pesananSelesaiCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'dibatalkan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-4 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'dibatalkan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            Sedang Dibatalkan
                            <span id="badge-dibatalkan" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'dibatalkan' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $sedangDibatalkanCount }}
                            </span>
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
                            @if($tab === 'kendala')
                                @php
                                    $complaint = $item;
                                    $order = $complaint->transaksi;
                                    $cust = $complaint->pelanggan ?? $order?->pelanggan;
                                @endphp
                                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-5">
                                    <!-- Header Card -->
                                    <div class="flex flex-wrap justify-between items-center border-b border-slate-50 pb-4 gap-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg">Kendala Pesanan</span>
                                            
                                            @if($order)
                                                <a href="{{ route('order.detail', $order->id) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 font-mono hover:underline inline-flex items-center gap-1.5 transition-all">
                                                    {{ $order->nota }}
                                                    <i data-feather="external-link" class="w-3 h-3 stroke-[2.5]"></i>
                                                </a>
                                            @else
                                                <span class="text-xs font-bold text-slate-400">N/A</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-semibold text-slate-400">
                                            Tanggal Laporan: <span class="text-slate-600">{{ $complaint->created_at->format('d M Y H:i:s') }}</span>
                                        </div>
                                    </div>

                                    <!-- Body Card -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs font-semibold">
                                        <!-- Issue Detail -->
                                        <div class="space-y-1.5 md:border-r border-slate-50 md:pr-6">
                                            <p class="text-[#0f172a] font-extrabold text-sm capitalize">
                                                Detail Kendala
                                            </p>
                                            <p class="text-slate-500 font-normal leading-relaxed bg-slate-50 border border-slate-100 p-3 rounded-xl mt-2">
                                                {{ $complaint->content }}
                                            </p>
                                            @if(!empty($complaint->issue_types))
                                                <p class="text-slate-400 mt-2">
                                                    Kategori: <span class="text-slate-700 font-extrabold">{{ is_array($complaint->issue_types) ? implode(', ', $complaint->issue_types) : $complaint->issue_types }}</span>
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Customer Details -->
                                        <div class="space-y-1.5 md:border-r border-slate-50 md:pr-6">
                                            <p class="text-[#0f172a] font-extrabold">Informasi Pembeli</p>
                                            <div class="space-y-1">
                                                <p class="text-slate-400">Nama: <span class="text-slate-700">{{ $cust->nama ?? 'N/A' }}</span></p>
                                                <p class="text-slate-400">Telepon: <span class="text-slate-700">{{ $cust->telepon ?? 'N/A' }}</span></p>
                                                @if($order)
                                                    <p class="text-slate-400">Alamat: <span class="text-slate-700 font-normal line-clamp-2">{{ $order->pickup_address ?? 'N/A' }}</span></p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Complaint Images (if any) -->
                                        <div class="space-y-1.5">
                                            <p class="text-[#0f172a] font-extrabold">Foto Bukti Kendala</p>
                                            @php
                                                $images = [];
                                                if (is_string($complaint->image_path)) {
                                                    $images = json_decode($complaint->image_path, true) ?? [];
                                                } elseif (is_array($complaint->image_path)) {
                                                    $images = $complaint->image_path;
                                                }
                                            @endphp
                                            @if(!empty($images))
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    @foreach($images as $img)
                                                        <a href="{{ $img }}" target="_blank" class="block w-16 h-16 rounded-xl border border-slate-100 overflow-hidden shadow-sm hover:scale-105 transition-all">
                                                            <img src="{{ $img }}" alt="Bukti Kendala" class="w-full h-full object-cover">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-slate-400 font-normal">Tidak ada foto bukti.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Footer Card / Action Buttons -->
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-t border-slate-50 pt-4 gap-4">
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Kendala</p>
                                            <p class="text-base font-extrabold text-[#0f172a]">
                                                {{ ucfirst($complaint->status) }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            @php
                                                $phone = $cust->telepon ?? '';
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                                $waText = "*Zyngga Laundry - Penanganan Kendala Pesanan " . ($order ? "#{$order->nota}" : "") . "*\n\n"
                                                        . "Halo *{$cust->nama}*,\n"
                                                        . "Kami menerima laporan kendala Anda mengenai pesanan Anda:\n\n"
                                                        . "• *Kendala*: {$complaint->content}\n"
                                                        . "• *Status*: Sedang ditangani oleh operator\n\n"
                                                        . "Kami akan segera menghubungi Anda kembali untuk menyelesaikannya. Terima kasih atas kesabaran Anda.";
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waText) }}" 
                                               target="_blank" 
                                               class="notranslate w-full sm:w-auto text-center border border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 text-emerald-600 px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat WhatsApp
                                            </a>

                                            <form action="{{ route('admin.riwayat-pesanan.selesaikan-kendala', [$complaint->id, 'tab' => $tab]) }}" method="POST" class="w-full sm:w-auto flex-1 sm:flex-none" onsubmit="return confirm('Apakah Anda yakin menyelesaikan kendala pesanan ini? Laporan kendala akan dihapus.')">
                                                @csrf
                                                <button type="submit" class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                    Selesai (Hapus Kendala)
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-5">
                                
                                <!-- Header Card -->
                                @php
                                    $meta = json_decode($item->payment_metadata, true) ?? [];
                                    $hasPendingUpgrade = isset($meta['pending_upgrade']);
                                @endphp
                                <div class="flex flex-wrap justify-between items-center border-b border-slate-50 pb-4 gap-2">
                                    <div class="flex items-center gap-3">
                                        <!-- Status Badge -->
                                        @if(in_array($item->status, ['Baru', 'created', 'Perlu Diproses']))
                                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Butuh diproses</span>
                                        @elseif($item->status === 'Menunggu Pembayaran' || (in_array($item->status, ['Pesanan Selesai', 'Selesai']) && $item->payment_status !== 'paid'))
                                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Menunggu Pembayaran</span>
                                        @elseif($item->status === 'Perlu Dikerjakan')
                                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">Perlu Dikerjakan</span>
                                        @elseif($item->status === 'Proses Pengerjaan')
                                            <span class="text-xs font-bold text-violet-600 bg-violet-50 px-2.5 py-1 rounded-lg">Proses Pengerjaan</span>
                                        @elseif(in_array($item->status, ['Pesanan Selesai', 'Selesai']) && $item->payment_status === 'paid')
                                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">Pesanan Selesai</span>
                                        @elseif($item->status === 'Sedang Dibatalkan' || $item->status === 'Batal')
                                            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg">Sedang Dibatalkan</span>
                                        @elseif(in_array($item->status, ['Menunggu di Jemput', 'Menunggu di jemput', 'Sedang Dijemput']))
                                            <span class="text-xs font-bold text-cyan-600 bg-cyan-50 px-2.5 py-1 rounded-lg">Menunggu di Jemput</span>
                                        @elseif(in_array($item->status, ['Perlu di Antar', 'Perlu di antar']))
                                            <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2.5 py-1 rounded-lg">Perlu di Antar</span>
                                        @elseif($item->status === 'Kendala Pesanan')
                                            <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-lg">Kendala Pesanan</span>
                                        @else
                                            <span class="text-xs font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">{{ $item->status }}</span>
                                        @endif

                                        @if($hasPendingUpgrade)
                                            <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-lg border border-amber-200 animate-pulse flex items-center gap-1">
                                                <i data-feather="bell" class="w-3.5 h-3.5"></i>
                                                Pending Upgrade
                                            </span>
                                        @endif

                                        <a href="{{ route('order.detail', $item->id) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 font-mono hover:underline inline-flex items-center gap-1.5 transition-all">
                                            {{ $item->nota }}
                                            <i data-feather="external-link" class="w-3 h-3 stroke-[2.5]"></i>
                                        </a>
                                        @if(strtolower($item->layananPrioritas->nama ?? '') === 'satuan' || $item->fk_tambahan !== null)
                                            <span class="text-[10px] font-black tracking-wider text-pink-700 bg-pink-100 border border-pink-200 px-2 py-0.5 rounded-md animate-pulse">SATUAN</span>
                                        @endif
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
                                        <p class="text-slate-400">
                                            Estimasi Durasi: <span class="text-indigo-600 font-extrabold">{{ $item->getEstimasiPengerjaanJam() }} Jam</span>
                                        </p>
                                        <p class="text-slate-400">
                                            Deadline: <span class="text-rose-600 font-extrabold">{{ $item->getDeadlineWaktu()->locale('id')->isoFormat('dddd, D MMM | HH.mm') }}</span>
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
                                    @if(in_array($item->status, ['Menunggu di Jemput', 'Menunggu di jemput', 'Sedang Dijemput']))
                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            <form action="{{ route('admin.riwayat-pesanan.batal', $item->id) }}" method="POST" class="flex-1 sm:flex-none"><input type="hidden" name="tab" value="{{ $tab }}">
                                                @csrf
                                                <button type="submit" class="w-full text-center border border-slate-200 hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                                                    Batalkan Pesanan
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.riwayat-pesanan.konfirmasi-jemput', $item->id) }}" method="POST" class="w-full sm:w-auto flex-1 sm:flex-none">
                                                @csrf
                                                <input type="hidden" name="tab" value="{{ $tab }}">
                                                <button type="submit" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                    Konfirmasi Sudah Dijemput
                                                </button>
                                            </form>
                                        </div>
                                    @elseif(in_array($item->status, ['Baru', 'created', 'Perlu Diproses']))
                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            <form action="{{ route('admin.riwayat-pesanan.batal', $item->id) }}" method="POST" class="flex-1 sm:flex-none"><input type="hidden" name="tab" value="{{ $tab }}">
                                                @csrf
                                                <button type="submit" class="w-full text-center border border-slate-200 hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                                                    Batalkan Pesanan
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('admin.riwayat-pesanan.proses-form', [$item->id, 'tab' => $tab]) }}" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all block text-center">
                                                 Proses Pesanan
                                             </a>
                                        </div>
                                    @elseif($item->status === 'Perlu Dikerjakan')
                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            @php
                                                $phone = $item->pelanggan->telepon ?? '';
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                                $waText = "*Zyngga Laundry - Konfirmasi Pengerjaan #{$item->nota}*\n\n"
                                                        . "Halo *{$item->pelanggan->nama}*,\n"
                                                        . "Pesanan Anda akan mulai dikerjakan. Berikut rinciannya:\n\n"
                                                        . "• *Nota*: #{$item->nota}\n"
                                                        . "• *Layanan*: " . ($item->layananPrioritas->nama ?? 'Reguler') . "\n"
                                                        . "• *Total*: Rp " . number_format($item->total_bayar_akhir, 0, ',', '.') . "\n"
                                                        . "• *Status*: Mulai Dikerjakan\n\n"
                                                        . "Lihat detail pesanan Anda di sini:\n"
                                                        . url('/order/detail/' . $item->id) . "\n\n"
                                                        . "Terima kasih!";
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waText) }}" 
                                               target="_blank" 
                                               class="notranslate w-full sm:w-auto text-center border border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 text-emerald-600 px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat Pelanggan
                                            </a>

                                            @if($item->canBeUpgraded() || $hasPendingUpgrade)
                                                <a href="{{ route('admin.riwayat-pesanan.proses-form', [$item->id, 'tab' => $tab]) }}" class="w-full sm:w-auto text-center border border-amber-200 hover:bg-amber-50 hover:text-amber-700 text-amber-600 px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                                                    <i data-feather="arrow-up-circle" class="w-4 h-4 text-amber-500"></i>
                                                    Detail & Upgrade
                                                </a>
                                            @endif

                                            <a href="{{ route('admin.riwayat-pesanan.kerjakan-form', [$item->id, 'tab' => $tab]) }}" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                                                <i data-feather="play" class="w-4 h-4"></i>
                                                Proses Pekerjaan
                                            </a>
                                        </div>
                                    @elseif($item->status === 'Proses Pengerjaan')
                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            @php
                                                $phone = $item->pelanggan->telepon ?? '';
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                                $waText = "*Zyngga Laundry - Progres Pengerjaan #{$item->nota}*\n\n"
                                                        . "Halo *{$item->pelanggan->nama}*,\n"
                                                        . "Pesanan Anda sedang diproses. Berikut rinciannya:\n\n"
                                                        . "• *Nota*: #{$item->nota}\n"
                                                        . "• *Layanan*: " . ($item->layananPrioritas->nama ?? 'Reguler') . "\n"
                                                        . "• *Total*: Rp " . number_format($item->total_bayar_akhir, 0, ',', '.') . "\n"
                                                        . "• *Status*: Sedang Diproses\n\n"
                                                        . "Lihat detail pesanan Anda di sini:\n"
                                                        . url('/order/detail/' . $item->id) . "\n\n"
                                                        . "Terima kasih!";
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waText) }}" 
                                               target="_blank" 
                                               class="notranslate w-full sm:w-auto text-center border border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 text-emerald-600 px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat Pelanggan
                                            </a>

                                            @if($item->canBeUpgraded() || $hasPendingUpgrade)
                                                <a href="{{ route('admin.riwayat-pesanan.proses-form', [$item->id, 'tab' => $tab]) }}" class="w-full sm:w-auto text-center border border-amber-200 hover:bg-amber-50 hover:text-amber-700 text-amber-600 px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                                                    <i data-feather="arrow-up-circle" class="w-4 h-4 text-amber-500"></i>
                                                    Detail & Upgrade
                                                </a>
                                            @endif

                                            <form action="{{ route('admin.riwayat-pesanan.selesaikan', [$item->id, 'tab' => $tab]) }}" method="POST" class="w-full sm:w-auto flex-1 sm:flex-none">
                                                @csrf
                                                <button type="submit" class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                    Selesaikan Pekerjaan
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($item->status === 'Menunggu Pembayaran' || (in_array($item->status, ['Pesanan Selesai', 'Selesai']) && $item->payment_status !== 'paid'))
                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            @php
                                                $phone = $item->pelanggan->telepon ?? '';
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                                $waText = "*Zyngga Laundry - Pengerjaan Selesai & Tagihan #{$item->nota}*\n\n"
                                                        . "Halo *{$item->pelanggan->nama}*,\n"
                                                        . "Cucian Anda telah selesai dikerjakan! Silakan lakukan pembayaran.\n\n"
                                                        . "• *Nota*: #{$item->nota}\n"
                                                        . "• *Layanan*: " . ($item->layananPrioritas->nama ?? 'Reguler') . "\n"
                                                        . "• *Total Tagihan*: Rp " . number_format($item->total_bayar_akhir, 0, ',', '.') . "\n"
                                                        . "• *Status*: Menunggu Pembayaran\n\n"
                                                        . "Silakan lakukan pembayaran langsung di link berikut:\n"
                                                        . url('/order/detail/' . $item->id) . "\n\n"
                                                        . "Terima kasih!";
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waText) }}" 
                                               target="_blank" 
                                               class="notranslate w-full sm:w-auto text-center border border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 text-emerald-600 px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat Pelanggan
                                            </a>

                                            @if($item->canBeUpgraded() || $hasPendingUpgrade)
                                                <a href="{{ route('admin.riwayat-pesanan.proses-form', [$item->id, 'tab' => $tab]) }}" class="w-full sm:w-auto text-center border border-amber-200 hover:bg-amber-50 hover:text-amber-700 text-amber-600 px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                                                    <i data-feather="arrow-up-circle" class="w-4 h-4 text-amber-500"></i>
                                                    Detail & Upgrade
                                                </a>
                                            @endif

                                            <form action="{{ route('admin.riwayat-pesanan.konfirmasi-bayar', [$item->id, 'tab' => $tab]) }}" method="POST" class="w-full sm:w-auto flex-1 sm:flex-none" onsubmit="return confirm('Apakah Anda yakin mengkonfirmasi pembayaran pesanan #{{ $item->nota }}?')">
                                                @csrf
                                                <button type="submit" class="w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                    Sudah Dibayar
                                                </button>
                                            </form>
                                        </div>
                                    @elseif(in_array($item->status, ['Perlu di Antar', 'Perlu di antar']))
                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            @php
                                                $phone = $item->pelanggan->telepon ?? '';
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                                $waText = "*Zyngga Laundry - Pengantaran Pesanan #{$item->nota}*\n\n"
                                                        . "Halo *{$item->pelanggan->nama}*,\n"
                                                        . "Pesanan Anda sedang diantarkan ke alamat tujuan. Berikut rinciannya:\n\n"
                                                        . "• *Nota*: #{$item->nota}\n"
                                                        . "• *Layanan*: " . ($item->layananPrioritas->nama ?? 'Reguler') . "\n"
                                                        . "• *Total*: Rp " . number_format($item->total_bayar_akhir, 0, ',', '.') . "\n"
                                                        . "• *Status*: Sedang Diantar\n\n"
                                                        . "Lihat detail pesanan Anda di sini:\n"
                                                        . url('/order/detail/' . $item->id) . "\n\n"
                                                        . "Terima kasih!";
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waText) }}" 
                                               target="_blank" 
                                               class="notranslate w-full sm:w-auto text-center border border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 text-emerald-600 px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat Pelanggan
                                            </a>

                                            <form action="{{ route('admin.riwayat-pesanan.selesaikan-antar', [$item->id, 'tab' => $tab]) }}" method="POST" class="w-full sm:w-auto flex-1 sm:flex-none">
                                                @csrf
                                                <button type="submit" class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-2">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                    Selesaikan Pengantaran
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endif

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

    <!-- Real-time Background Order Poller Component -->
    <div x-data="orderPoller({ initialCount: {{ $menungguDiJemputCount }} })" x-init="start()" class="relative" x-cloak>
        <template x-if="newOrdersCount > 0">
            <div class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-4 rounded-2xl shadow-2xl flex flex-col gap-3 max-w-sm border border-slate-700/80 animate-fade-in-up">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <div>
                        <h4 class="text-xs font-bold font-outfit text-white">Pesanan Baru Masuk!</h4>
                        <p class="text-[11px] text-slate-400 font-semibold mt-0.5" x-text="'Ada ' + newOrdersCount + ' pesanan baru menunggu di jemput.'"></p>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button @click="dismiss()" class="text-slate-400 hover:text-white text-[10px] font-bold px-3 py-1.5 rounded-lg hover:bg-slate-800 transition-all">
                        Tutup
                    </button>
                    <button @click="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-extrabold px-3 py-1.5 rounded-lg shadow-md transition-all">
                        Muat Ulang Halaman
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Poller & Audio Synth Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('orderPoller', (config) => ({
                currentCount: config.initialCount,
                newOrdersCount: 0,
                intervalId: null,

                start() {
                    this.intervalId = setInterval(() => {
                        this.fetchCounts();
                    }, 15000); // Poll every 15 seconds
                },

                destroy() {
                    if (this.intervalId) clearInterval(this.intervalId);
                },

                dismiss() {
                    // Update count to hide toast, but keep tab badges updated
                    this.currentCount += this.newOrdersCount;
                    this.newOrdersCount = 0;
                },

                fetchCounts() {
                    fetch('{{ route("admin.riwayat-pesanan.counts") }}')
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 200) {
                                // Update all badges dynamically in the DOM
                                this.updateBadge('badge-menunggu-di-jemput', data.menunggu_di_jemput);
                                this.updateBadge('badge-perlu-diproses', data.perlu_diproses);
                                this.updateBadge('badge-perlu-dikerjakan', data.perlu_dikerjakan);
                                this.updateBadge('badge-proses-pengerjaan', data.proses_pengerjaan);
                                this.updateBadge('badge-menunggu-pembayaran', data.menunggu_pembayaran);
                                this.updateBadge('badge-perlu-di-antar', data.perlu_di_antar);
                                this.updateBadge('badge-selesai', data.selesai);
                                this.updateBadge('badge-dibatalkan', data.dibatalkan);

                                // Check if there are new orders waiting for pickup
                                if (data.menunggu_di_jemput > this.currentCount) {
                                    this.newOrdersCount = data.menunggu_di_jemput - this.currentCount;
                                    this.playChime();
                                } else if (data.menunggu_di_jemput < this.currentCount) {
                                    // If counts decreased (processed manually by other operators), sync it down
                                    this.currentCount = data.menunggu_di_jemput;
                                }
                            }
                        })
                        .catch(err => console.error('Poller error:', err));
                },

                updateBadge(id, count) {
                    const badge = document.getElementById(id);
                    if (badge) {
                        badge.innerText = count;
                    }
                },

                playChime() {
                    try {
                        const context = new (window.AudioContext || window.webkitAudioContext)();
                        
                        // First tone (E5, bright and premium)
                        const osc1 = context.createOscillator();
                        const gain1 = context.createGain();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(659.25, context.currentTime); // E5
                        gain1.gain.setValueAtTime(0.1, context.currentTime);
                        gain1.gain.exponentialRampToValueAtTime(0.0001, context.currentTime + 1.0);
                        osc1.connect(gain1);
                        gain1.connect(context.destination);
                        osc1.start();
                        osc1.stop(context.currentTime + 1.0);

                        // Second tone (A5, slightly delayed to make a beautiful chime)
                        const osc2 = context.createOscillator();
                        const gain2 = context.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880.00, context.currentTime + 0.15); // A5
                        gain2.gain.setValueAtTime(0.1, context.currentTime + 0.15);
                        gain2.gain.exponentialRampToValueAtTime(0.0001, context.currentTime + 1.15);
                        osc2.connect(gain2);
                        gain2.connect(context.destination);
                        osc2.start(context.currentTime + 0.15);
                        osc2.stop(context.currentTime + 1.15);
                    } catch (e) {
                        console.error('Audio chime error:', e);
                    }
                }
            }));
        });
    </script>

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
