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
<body
    class="antialiased h-full overflow-hidden"
    style="background:#E6F0FF; color:#0F0F0F;"
    x-data="{
        sidebarOpen: false,
        filterOpen: false,
        confirmModalOpen: false,
        confirmMessage: '',
        confirmTitle: '',
        confirmTargetForm: '',
        openConfirm(formId, message, title = 'Konfirmasi Tindakan') {
            this.confirmTargetForm = formId;
            this.confirmMessage = message;
            this.confirmTitle = title;
            this.confirmModalOpen = true;
        },
        closeConfirm() {
            this.confirmModalOpen = false;
            this.confirmTargetForm = '';
            this.confirmMessage = '';
            this.confirmTitle = '';
        },
        confirmYes() {
            const form = document.getElementById(this.confirmTargetForm);
            if (form) form.submit();
            this.confirmModalOpen = false;
        }
    }"
>

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            @include('operator.partials.header', ['title' => 'Riwayat Pesanan'])

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar" style="background:#E6F0FF;">
                
                <div class="max-w-5xl mx-auto w-full flex flex-col gap-4">
                    
                    <!-- Pop-Up Notifikasi Sukses (Modal style matching Image 2) -->
                    @if(session('success'))
                        <div x-data="{ successOpen: true }" x-show="successOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-transition>
                            <div @click.outside="successOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm sm:max-w-md w-full shadow-2xl text-center flex flex-col items-center gap-4 border border-slate-100 animate-in fade-in zoom-in-95">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i data-feather="check-circle" class="w-6 h-6 stroke-[2.5]"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold text-slate-900">Berhasil!</h3>
                                <p class="text-xs sm:text-sm font-normal text-slate-600 max-w-xs leading-relaxed">{{ session('success') }}</p>
                                <button type="button" @click="successOpen = false" class="w-full text-center text-white font-semibold text-xs rounded-full bg-[#003E9C] py-3.5 px-6 hover:bg-blue-800 transition-colors shadow-sm cursor-pointer border-0">
                                    Selesai
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- 1. BUTTON TAMBAH PESANAN MANUAL : bg #003E9C, radius 100, h-48, text 14/500 white -->
                    <a href="{{ route('admin.riwayat-pesanan.tambah-form') }}" 
                       class="w-full text-sm font-medium py-3.5 px-4 rounded-full shadow-sm transition-colors text-center block"
                       style="background:#003E9C; color:#FFFFFF;"
                       onmouseover="this.style.background='#002d73'" onmouseout="this.style.background='#003E9C'">
                        + Tambah Pesanan Manual
                    </a>

                    <!-- TABS NAVIGATION -->
                    <div class="flex overflow-x-auto scrollbar-none gap-8 text-xs font-medium border-b border-[#F4F4F4] pb-0.5">
                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'menunggu-di-jemput', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-3 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'menunggu-di-jemput' ? 'border-[#003E9C] text-[#003E9C] font-medium' : 'border-transparent text-[#808080] hover:text-slate-600 font-normal' }}">
                            Menunggu di Jemput
                            <span id="badge-menunggu-di-jemput" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'menunggu-di-jemput' ? 'bg-[#003E9C] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $menungguDiJemputCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-diproses', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-3 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'perlu-diproses' ? 'border-[#003E9C] text-[#003E9C] font-medium' : 'border-transparent text-[#808080] hover:text-slate-600 font-normal' }}">
                            Menunggu Diproses
                            <span id="badge-perlu-diproses" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'perlu-diproses' ? 'bg-[#003E9C] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $perluDiprosesCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-dikerjakan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-3 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'perlu-dikerjakan' ? 'border-[#003E9C] text-[#003E9C] font-medium' : 'border-transparent text-[#808080] hover:text-slate-600 font-normal' }}">
                            Sedang Diproses
                            <span id="badge-perlu-dikerjakan" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'perlu-dikerjakan' ? 'bg-[#003E9C] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $perluDikerjakanCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'proses-pengerjaan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-3 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'proses-pengerjaan' ? 'border-[#003E9C] text-[#003E9C] font-medium' : 'border-transparent text-[#808080] hover:text-slate-600 font-normal' }}">
                            Proses Pengerjaan
                            <span id="badge-proses-pengerjaan" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'proses-pengerjaan' ? 'bg-[#003E9C] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $prosesPengerjaanCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'menunggu-pembayaran', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-3 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'menunggu-pembayaran' ? 'border-[#003E9C] text-[#003E9C] font-medium' : 'border-transparent text-[#808080] hover:text-slate-600 font-normal' }}">
                            Menunggu Pembayaran
                            <span id="badge-menunggu-pembayaran" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'menunggu-pembayaran' ? 'bg-[#003E9C] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $menungguPembayaranCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'perlu-di-antar', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-3 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'perlu-di-antar' ? 'border-[#003E9C] text-[#003E9C] font-medium' : 'border-transparent text-[#808080] hover:text-slate-600 font-normal' }}">
                            Perlu di Antar
                            <span id="badge-perlu-di-antar" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'perlu-di-antar' ? 'bg-[#003E9C] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $perluDiAntarCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'selesai', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-3 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'selesai' ? 'border-[#003E9C] text-[#003E9C] font-medium' : 'border-transparent text-[#808080] hover:text-slate-600 font-normal' }}">
                            Pesanan Selesai
                            <span id="badge-selesai" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'selesai' ? 'bg-[#003E9C] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $pesananSelesaiCount }}
                            </span>
                        </a>

                        <a href="{{ route('admin.riwayat-pesanan', ['tab' => 'dibatalkan', 'search' => $search, 'sort' => $sort]) }}" 
                           class="pb-3 border-b-2 transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'dibatalkan' ? 'border-[#003E9C] text-[#003E9C] font-medium' : 'border-transparent text-[#808080] hover:text-slate-600 font-normal' }}">
                            Sedang Dibatalkan
                            <span id="badge-dibatalkan" class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'dibatalkan' ? 'bg-[#003E9C] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $sedangDibatalkanCount }}
                            </span>
                        </a>
                    </div>

                    <!-- SEARCH & FILTER ROW -->
                    <form method="GET" action="{{ route('admin.riwayat-pesanan') }}" class="flex flex-col gap-3">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        
                        <div class="flex items-center gap-3">
                            <div class="relative flex-1">
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search }}" 
                                       placeholder="Cari nama atau nomor nota..." 
                                       class="w-full text-xs font-normal rounded-full pl-10 pr-4 focus:outline-none placeholder:text-[#808080] bg-white"
                                       style="border:1px solid #CCCCCC; color:#0F0F0F; height:48px;">
                                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none" style="color:#808080;">
                                    <i data-feather="search" class="w-4 h-4"></i>
                                </div>
                            </div>

                            <button type="button" @click="filterOpen = !filterOpen" 
                                    class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 shadow-sm transition-colors cursor-pointer border-0"
                                    style="background:#003E9C; color:#FFFFFF;">
                                <i data-feather="sliders" class="w-5 h-5" x-show="!filterOpen"></i>
                                <i data-feather="x" class="w-5 h-5" x-show="filterOpen" x-cloak></i>
                            </button>
                        </div>

                        <!-- Collapsible Sorting Panel (Alpine.js) -->
                        <div x-show="filterOpen" x-transition x-cloak class="bg-white p-4 rounded-lg flex flex-col sm:flex-row items-stretch sm:items-end gap-3 shadow-sm border border-slate-50">
                            <div class="relative flex-1">
                                <label class="text-xs font-medium text-slate-500 block mb-1.5">Urutkan Berdasarkan</label>
                                <div class="relative">
                                    <select name="sort" onchange="this.form.submit()" 
                                            class="w-full text-xs font-normal rounded-full px-4 focus:outline-none appearance-none"
                                            style="border:1px solid #CCCCCC; color:#808080; height:48px; background-color:#FFFFFF;">
                                        <option value="deadline" {{ $sort === 'deadline' ? 'selected' : '' }}>Deadline Terdekat</option>
                                        <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                        <option value="terlama" {{ $sort === 'terlama' ? 'selected' : '' }}>Terlama</option>
                                        <option value="prioritas_desc" {{ $sort === 'prioritas_desc' ? 'selected' : '' }}>Prioritas Teratas</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none" style="color:#808080;">
                                        <i data-feather="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button type="submit" class="text-xs font-medium px-5 py-2.5 rounded-full text-white transition-all shadow-sm border-0 cursor-pointer h-12" style="background:#003E9C; min-width: 100px;">
                                    Terapkan
                                </button>
                                @if(!empty($search) || $sort !== 'deadline')
                                    <a href="{{ route('admin.riwayat-pesanan', ['tab' => $tab]) }}" 
                                       class="text-xs font-medium px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all h-12 min-width: 80px;">
                                        Reset
                                    </a>
                                @endif
                            </div>
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
                                    $kendalaFormId = 'form-selesai-kendala-' . $complaint->id;
                                @endphp
                                <div class="bg-white rounded-lg p-4 flex flex-col gap-3 shadow-sm">
                                    <!-- Baris 1: Status & Nota -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#EF4444;">Kendala Pesanan</span>
                                            <span class="text-[11px] font-medium text-slate-400">
                                                Laporan: {{ $complaint->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                        
                                        @if($order)
                                            <a href="{{ route('order.detail', $order->id) }}" target="_blank" class="text-xs font-medium px-3 py-1 rounded-full border border-[#003E9C] text-[#003E9C] hover:bg-blue-50 transition-all inline-flex items-center gap-1.5 no-underline">
                                                <span>{{ $order->nota }}</span>
                                                <i class="ri-arrow-right-line text-xs"></i>
                                            </a>
                                        @else
                                            <span class="text-xs font-medium px-3 py-1 rounded-full border border-slate-200 text-slate-400 inline-flex items-center gap-1.5">
                                                <span>N/A</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Baris 2: Nama Pelanggan -->
                                    <div class="text-sm font-medium" style="color:#0F0F0F;">
                                        {{ $cust->nama ?? 'N/A' }}
                                    </div>

                                    <!-- Baris 3: Nomor Telepon -->
                                    <div class="text-xs font-normal" style="color:#808080;">
                                        {{ $cust->telepon ?? 'N/A' }}
                                    </div>

                                    <!-- Baris 4: Alamat -->
                                    <div class="text-xs font-normal leading-relaxed" style="color:#808080;">
                                        {{ $order->pickup_address ?? 'N/A' }}
                                    </div>

                                    <!-- Baris 5: Detail Kendala -->
                                    <div class="text-xs font-normal p-3 rounded-lg bg-slate-50 border border-slate-100" style="color:#808080;">
                                        <p class="font-medium text-[#0F0F0F] mb-1">Detail Kendala:</p>
                                        {{ $complaint->content }}
                                        @if(!empty($complaint->issue_types))
                                            <div class="mt-2 text-[10px] font-medium text-slate-500">
                                                Kategori: {{ is_array($complaint->issue_types) ? implode(', ', $complaint->issue_types) : $complaint->issue_types }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Foto Bukti Kendala (if any) -->
                                    @php
                                        $images = [];
                                        if (is_string($complaint->image_path)) {
                                            $images = json_decode($complaint->image_path, true) ?? [];
                                        } elseif (is_array($complaint->image_path)) {
                                            $images = $complaint->image_path;
                                        }
                                    @endphp
                                    @if(!empty($images))
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($images as $img)
                                                <a href="{{ $img }}" target="_blank" class="block w-12 h-12 rounded-lg border border-slate-100 overflow-hidden shadow-sm hover:scale-105 transition-transform">
                                                    <img src="{{ $img }}" alt="Bukti Kendala" class="w-full h-full object-cover">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Baris 6 & 7: Status & Aksi -->
                                    <div class="flex items-center justify-between pt-2 border-t border-[#F4F4F4] mt-1 flex-wrap gap-2">
                                        <div>
                                            <p class="text-[9px] font-normal text-slate-400 uppercase">Status Kendala</p>
                                            <p class="text-xs font-medium text-[#0F0F0F]">
                                                {{ ucfirst($complaint->status) }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-2">
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
                                               class="notranslate text-center bg-white border border-emerald-200 hover:bg-emerald-50 text-emerald-600 px-5 py-2 rounded-full text-xs font-medium transition-all flex items-center justify-center gap-1.5"
                                               style="height:38px; border-width:1px;"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat WhatsApp
                                            </a>

                                            <!-- Form ini di-submit lewat JS setelah user konfirmasi di modal custom (bukan confirm() native) -->
                                            <form id="{{ $kendalaFormId }}" action="{{ route('admin.riwayat-pesanan.selesaikan-kendala', [$complaint->id, 'tab' => $tab]) }}" method="POST" class="inline-block">
                                                @csrf
                                            </form>
                                            <button
                                                type="button"
                                                @click="openConfirm('{{ $kendalaFormId }}', 'Apakah Anda yakin menyelesaikan kendala pesanan ini? Laporan kendala akan dihapus.', 'Yakin ingin menyelesaikan kendala ini?')"
                                                class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:38px;">
                                                <i data-feather="check" class="w-4 h-4"></i>
                                                Selesai (Hapus Kendala)
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                            <div class="bg-white rounded-lg p-4 flex flex-col gap-3 shadow-sm">
                                
                                @php
                                    $meta = json_decode($item->payment_metadata, true) ?? [];
                                    $hasPendingUpgrade = isset($meta['pending_upgrade']);
                                    $bayarFormId = 'form-konfirmasi-bayar-' . $item->id;
                                @endphp
                                
                                <!-- Baris 1: Status & Nota -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        @if(in_array($item->status, ['Baru', 'created', 'Perlu Diproses']))
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#F2994A;">Butuh diproses</span>
                                        @elseif($item->status === 'Menunggu Pembayaran' || (in_array($item->status, ['Pesanan Selesai', 'Selesai']) && $item->payment_status !== 'paid'))
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#003E9C;">Menunggu Pembayaran</span>
                                        @elseif($item->status === 'Perlu Dikerjakan')
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#F2994A;">Perlu Dikerjakan</span>
                                        @elseif($item->status === 'Proses Pengerjaan')
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#003E9C;">Proses Pengerjaan</span>
                                        @elseif(in_array($item->status, ['Pesanan Selesai', 'Selesai']) && $item->payment_status === 'paid')
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#10B981;">Pesanan Selesai</span>
                                        @elseif($item->status === 'Sedang Dibatalkan' || $item->status === 'Batal')
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#EF4444;">Sedang Dibatalkan</span>
                                        @elseif(in_array($item->status, ['Menunggu di Jemput', 'Menunggu di jemput', 'Sedang Dijemput']))
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#F2994A;">Menunggu di Jemput</span>
                                        @elseif(in_array($item->status, ['Perlu di Antar', 'Perlu di antar']))
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#003E9C;">Perlu di Antar</span>
                                        @elseif($item->status === 'Kendala Pesanan')
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full" style="background:#EF4444;">Kendala Pesanan</span>
                                        @else
                                            <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full bg-slate-500">{{ $item->status }}</span>
                                        @endif

                                        @if($hasPendingUpgrade)
                                            <span class="text-[10px] font-medium text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full animate-pulse border border-amber-200">
                                                Pending Upgrade
                                            </span>
                                        @endif
                                        
                                        @if(strtolower($item->layananPrioritas->nama ?? '') === 'satuan' || $item->fk_tambahan !== null)
                                            <span class="text-[9px] font-medium text-pink-700 bg-pink-100 border border-pink-200 px-1.5 py-0.5 rounded-md animate-pulse">SATUAN</span>
                                        @endif
                                    </div>

                                    <a href="{{ route('order.detail', $item->id) }}" target="_blank" class="text-xs font-medium px-3 py-1 rounded-full border border-[#003E9C] text-[#003E9C] hover:bg-blue-50 transition-all inline-flex items-center gap-1.5 no-underline">
                                        <span>{{ $item->nota }}</span>
                                        <i class="ri-arrow-right-line text-xs"></i>
                                    </a>
                                </div>

                                <!-- Baris 2: Nama Pelanggan -->
                                <div class="text-sm font-medium" style="color:#0F0F0F;">
                                    {{ $item->pelanggan->nama ?? 'N/A' }}
                                </div>

                                <!-- Baris 3: Nomor Telepon -->
                                <div class="text-xs font-normal" style="color:#808080;">
                                    {{ $item->pelanggan->telepon ?? 'N/A' }}
                                </div>

                                <!-- Baris 4: Alamat -->
                                <div class="text-xs font-normal leading-relaxed" style="color:#808080;">
                                    {{ $item->pickup_address ?? 'N/A' }}
                                </div>

                                <!-- Baris 5: Catatan Khusus -->
                                <div class="text-xs font-normal" style="color:#808080;">
                                    {{ $item->catatan ?? '-' }}
                                </div>

                                <!-- Baris 6 & 7: Total & Aksi -->
                                <div class="flex items-center justify-between pt-2 border-t border-[#F4F4F4] mt-1 flex-wrap gap-2">
                                    <div class="text-sm font-medium" style="color:#0F0F0F;">
                                        Rp {{ number_format($item->total_bayar_akhir, 0, ',', '.') }}
                                    </div>

                                    <!-- Actions for "Perlu Diproses" (status 'Baru' / 'created') -->
                                    @if(in_array($item->status, ['Menunggu di Jemput', 'Menunggu di jemput', 'Sedang Dijemput']))
                                        <div class="flex items-center gap-2">
                                            @php $batalJemputFormId = 'form-batal-jemput-' . $item->id; @endphp
                                            <form id="{{ $batalJemputFormId }}" action="{{ route('admin.riwayat-pesanan.batal', $item->id) }}" method="POST" class="inline-block">
                                                <input type="hidden" name="tab" value="{{ $tab }}">
                                                @csrf
                                            </form>
                                            <button type="button" 
                                                    @click="openConfirm('{{ $batalJemputFormId }}', 'Apakah Anda yakin ingin membatalkan pesanan ini? Tindakan ini akan mengonfirmasi pembatalan pesanan.', 'Yakin ingin membatalkan pesanan ini?')" 
                                                    class="text-center bg-white border px-5 py-2 rounded-full text-xs font-medium transition-all cursor-pointer" 
                                                    style="border-color:#003E9C; color:#003E9C; height:38px; border-width:1px;">
                                                Batalkan Pesanan
                                            </button>
                                            
                                            <form action="{{ route('admin.riwayat-pesanan.konfirmasi-jemput', $item->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="tab" value="{{ $tab }}">
                                                <button type="submit" class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:38px;">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                    Konfirmasi Sudah Dijemput
                                                </button>
                                            </form>
                                        </div>
                                    @elseif(in_array($item->status, ['Baru', 'created', 'Perlu Diproses']))
                                        <div class="flex items-center gap-2">
                                            @php $batalProsesFormId = 'form-batal-proses-' . $item->id; @endphp
                                            <form id="{{ $batalProsesFormId }}" action="{{ route('admin.riwayat-pesanan.batal', $item->id) }}" method="POST" class="inline-block">
                                                <input type="hidden" name="tab" value="{{ $tab }}">
                                                @csrf
                                            </form>
                                            <button type="button" 
                                                    @click="openConfirm('{{ $batalProsesFormId }}', 'Apakah Anda yakin ingin membatalkan pesanan ini? Tindakan ini akan mengonfirmasi pembatalan pesanan.', 'Yakin ingin membatalkan pesanan ini?')" 
                                                    class="text-center bg-white border px-5 py-2 rounded-full text-xs font-medium transition-all cursor-pointer" 
                                                    style="border-color:#003E9C; color:#003E9C; height:38px; border-width:1px;">
                                                Batalkan Pesanan
                                            </button>
                                            
                                            <a href="{{ route('admin.riwayat-pesanan.proses-form', [$item->id, 'tab' => $tab]) }}" class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:38px;">
                                                 Proses Pesanan
                                             </a>
                                        </div>
                                    @elseif($item->status === 'Perlu Dikerjakan')
                                        <div class="flex items-center gap-2">
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
                                               class="notranslate text-center bg-white border border-emerald-200 hover:bg-emerald-50 text-emerald-600 px-5 py-2 rounded-full text-xs font-medium transition-all flex items-center justify-center gap-1.5"
                                               style="height:38px; border-width:1px;"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat Pelanggan
                                            </a>

                                            {{--
                                            @if($item->canBeUpgraded() || $hasPendingUpgrade)
                                                <a href="{{ route('admin.riwayat-pesanan.proses-form', [$item->id, 'tab' => $tab]) }}" class="text-center bg-white border border-amber-200 hover:bg-amber-50 text-amber-600 px-5 py-2 rounded-full text-xs font-medium transition-all flex items-center justify-center gap-1.5" style="height:38px; border-width:1px;">
                                                    <i data-feather="arrow-up-circle" class="w-4 h-4 text-amber-500"></i>
                                                    Detail & Upgrade
                                                </a>
                                            @endif
                                            --}}

                                            <a href="{{ route('admin.riwayat-pesanan.kerjakan-form', [$item->id, 'tab' => $tab]) }}" class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:38px;">
                                                <i data-feather="play" class="w-4 h-4"></i>
                                                Proses Pekerjaan
                                            </a>
                                        </div>
                                    @elseif($item->status === 'Proses Pengerjaan')
                                        <div class="flex items-center gap-2">
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
                                               class="notranslate text-center bg-white border border-emerald-200 hover:bg-emerald-50 text-emerald-600 px-5 py-2 rounded-full text-xs font-medium transition-all flex items-center justify-center gap-1.5"
                                               style="height:38px; border-width:1px;"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat Pelanggan
                                            </a>

                                            {{--
                                            @if($item->canBeUpgraded() || $hasPendingUpgrade)
                                                <a href="{{ route('admin.riwayat-pesanan.proses-form', [$item->id, 'tab' => $tab]) }}" class="text-center bg-white border border-amber-200 hover:bg-amber-50 text-amber-600 px-5 py-2 rounded-full text-xs font-medium transition-all flex items-center justify-center gap-1.5" style="height:38px; border-width:1px;">
                                                    <i data-feather="arrow-up-circle" class="w-4 h-4 text-amber-500"></i>
                                                    Detail & Upgrade
                                                </a>
                                            @endif
                                            --}}

                                            <form action="{{ route('admin.riwayat-pesanan.selesaikan', [$item->id, 'tab' => $tab]) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:38px;">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                    Selesaikan Pekerjaan
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($item->status === 'Menunggu Pembayaran' || (in_array($item->status, ['Pesanan Selesai', 'Selesai']) && $item->payment_status !== 'paid'))
                                        <div class="flex items-center gap-2">
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
                                               class="notranslate text-center bg-white border border-emerald-200 hover:bg-emerald-50 text-emerald-600 px-5 py-2 rounded-full text-xs font-medium transition-all flex items-center justify-center gap-1.5"
                                               style="height:38px; border-width:1px;"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat Pelanggan
                                            </a>

                                            {{--
                                            @if($item->canBeUpgraded() || $hasPendingUpgrade)
                                                <a href="{{ route('admin.riwayat-pesanan.proses-form', [$item->id, 'tab' => $tab]) }}" class="text-center bg-white border border-amber-200 hover:bg-amber-50 text-amber-600 px-5 py-2 rounded-full text-xs font-medium transition-all flex items-center justify-center gap-1.5" style="height:38px; border-width:1px;">
                                                    <i data-feather="arrow-up-circle" class="w-4 h-4 text-amber-500"></i>
                                                    Detail & Upgrade
                                                </a>
                                            @endif
                                            --}}

                                            <!-- Form ini di-submit lewat JS setelah user konfirmasi di modal custom (bukan confirm() native) -->
                                            <form id="{{ $bayarFormId }}" action="{{ route('admin.riwayat-pesanan.konfirmasi-bayar', [$item->id, 'tab' => $tab]) }}" method="POST" class="inline-block">
                                                @csrf
                                            </form>
                                            <button
                                                type="button"
                                                @click="openConfirm('{{ $bayarFormId }}', 'Apakah Anda yakin konfirmasi pembayaran pesanan ini?', 'Konfirmasi Pembayaran Pesanan')"
                                                class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:38px;">
                                                <i data-feather="check" class="w-4 h-4"></i>
                                                Sudah Dibayar
                                            </button>
                                        </div>
                                    @elseif(in_array($item->status, ['Perlu di Antar', 'Perlu di antar']))
                                        <div class="flex items-center gap-2">
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
                                               class="notranslate text-center bg-white border border-emerald-200 hover:bg-emerald-50 text-emerald-600 px-5 py-2 rounded-full text-xs font-medium transition-all flex items-center justify-center gap-1.5"
                                               style="height:38px; border-width:1px;"
                                               translate="no">
                                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                                Chat Pelanggan
                                            </a>

                                            <form action="{{ route('admin.riwayat-pesanan.selesaikan-antar', [$item->id, 'tab' => $tab]) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:38px;">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                    Selesaikan Pengantaran
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @empty
                            <!-- Empty State -->
                            <div class="bg-white rounded-lg p-12 text-center flex flex-col items-center justify-center shadow-sm">
                                <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mb-4">
                                    <i data-feather="inbox" class="w-8 h-8"></i>
                                </div>
                                <h3 class="text-sm font-medium text-[#0f172a]">Belum ada pesanan</h3>
                                <p class="text-xs font-medium text-slate-400 mt-1 max-w-xs mx-auto">
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
                        <h4 class="text-xs font-medium font-dm-sans text-white">Pesanan Baru Masuk!</h4>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5" x-text="'Ada ' + newOrdersCount + ' pesanan baru menunggu di jemput.'"></p>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button @click="dismiss()" class="text-slate-400 hover:text-white text-[10px] font-medium px-3 py-1.5 rounded-lg hover:bg-slate-800 transition-all">
                        Tutup
                    </button>
                    <button @click="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-medium px-3 py-1.5 rounded-lg shadow-md transition-all">
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

    <!-- POP-UP MODAL KONFIRMASI (Gambar 2 Modal Style, tanpa pixel art) -->
    <div x-show="confirmModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-transition>
        <div @click.outside="closeConfirm()" class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm sm:max-w-md w-full shadow-2xl text-center flex flex-col items-center gap-4 border border-slate-100 animate-in fade-in zoom-in-95">
            <h3 class="text-lg sm:text-xl font-bold text-slate-900" x-text="confirmTitle || 'Konfirmasi Tindakan'"></h3>
            <p class="text-xs sm:text-sm font-normal text-slate-500 max-w-xs leading-relaxed" x-text="confirmMessage"></p>
            <div class="flex items-center justify-center gap-3 w-full pt-2">
                <button type="button" @click="closeConfirm()" class="flex-1 text-center font-semibold text-xs rounded-full border border-[#003E9C] text-[#003E9C] py-3.5 px-5 hover:bg-blue-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="confirmYes()" class="flex-1 text-center text-white font-semibold text-xs rounded-full bg-[#003E9C] py-3.5 px-5 hover:bg-blue-800 transition-colors shadow-sm cursor-pointer border-0">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <!-- Initialize Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            document.addEventListener('alpine:updated', function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
        });
    </script>
</body>
</html>