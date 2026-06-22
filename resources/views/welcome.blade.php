<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zyngga Laundry – Solusi Laundry Modern</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ desktopCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || (localStorage.getItem('sidebarCollapsed') === null && window.innerWidth >= 768 && window.innerWidth < 1024) }" class="bg-zyngga-blue-50 min-h-screen">
    {{-- Sidebar for guest context --}}
    <x-sidebar />

    {{-- Main Content Wrapper (Centered for Landing) --}}
    <div 
        class="transition-all duration-300 ease-in-out min-h-screen flex flex-col"
        :class="desktopCollapsed ? 'md:pr-[80px]' : 'md:pr-[280px]'"
        @sidebar-toggled.window="desktopCollapsed = $event.detail.collapsed"
        @resize.window="desktopCollapsed = (window.innerWidth >= 768 && window.innerWidth < 1024)"
    >
        
        {{-- ── GUEST HEADER ────────────────────────────────────────── --}}
        <header class="sticky top-0 z-40 w-full pb-[6px]">
            <div class="bg-white rounded-b-2xl shadow-[0_4px_24px_rgba(0,0,0,0.08)] transition-shadow duration-300 w-full min-h-[80px]">
                <div class="max-w-5xl mx-auto w-full px-5 py-5 flex items-center justify-end min-h-[80px]">
                    {{-- Guest Auth Buttons --}}
                    <div class="flex items-center gap-2">
                        <x-zyngga-button 
                            type="a"
                            href="{{ route('register') }}"
                            variant="secondary"
                            size="m"
                            label="Daftar"
                        />
                        <x-zyngga-button 
                            type="a"
                            href="{{ route('login') }}"
                            variant="primary"
                            size="m"
                            label="Masuk"
                        />
                        
                        {{-- Mobile Hamburger --}}
                        <div class="md:hidden">
                            <x-zyngga-button 
                                variant="neutral"
                                size="m"
                                icon="menu"
                                iconPosition="only"
                                @click="$dispatch('open-sidebar')"
                                aria-label="Buka menu"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Section --}}
        <main class="w-full max-w-5xl mx-auto flex-1 flex flex-col">
            {{-- ─────────────────────────────────────────────────────────
                 HERO BANNER
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="w-full aspect-[353/120] rounded-lg overflow-hidden relative">
                    <img src="https://res.cloudinary.com/dba18pvit/image/upload/v1782060573/myzyngga_assets/d992csqcoldopqufuqgz.png" alt="Promo Banner" class="w-full h-full object-cover">
                    
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 PESAN SEKARANG CARD
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4">
                    {{-- Section heading --}}
                    <div class="flex items-center justify-between">
                        <x-zyngga-text variant="base" weight="medium">Pesan Sekarang</x-zyngga-text>
                    </div>

                    {{-- Service icons: Kilat | Regular | Quick | Express | Satuan --}}
                    <div class="grid grid-cols-5">
                        @php
                            $services = [
                                ['label' => 'Regular', 'icon' => 'star',    'key' => 'regular'],
                                ['label' => 'Quick',   'icon' => 'clock',   'key' => 'quick'],
                                ['label' => 'Express', 'icon' => 'fast-forward', 'key' => 'express'],
                                ['label' => 'Kilat',   'icon' => 'zap',     'key' => 'kilat'],
                                ['label' => 'Satuan',  'icon' => 'package', 'key' => 'satuan'],
                            ];
                        @endphp
                        @foreach ($services as $s)
                            <a
                                href="{{ route('order.pickup', ['service' => $s['key']]) }}"
                                class="flex flex-col items-center gap-2 h-16 justify-center hover:opacity-80 transition-opacity"
                            >
                                <div class="w-9 h-9 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                    <x-zyngga-service-icon service="{{ $s['label'] }}" class="w-[18px] h-[18px] text-zyngga-yellow-300" />
                                </div>
                                <x-zyngga-text variant="sm" weight="regular">{{ $s['label'] }}</x-zyngga-text>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 ALUR PEMESANAN CARD
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4">
                    <div class="h-8 flex items-center">
                        <x-zyngga-text variant="base" weight="medium">Alur Pemesanan</x-zyngga-text>
                    </div>

                    <div class="space-y-6">
                        @php
                            $steps = [
                                ['n' => '1', 'title' => 'Pesan Penjemputan', 'desc' => 'Pilih layanan dan atur jadwal jemput lewat aplikasi.'],
                                ['n' => '2', 'title' => 'Proses Cuci', 'desc' => 'Kurir mengambil pakaian untuk dicuci.'],
                                ['n' => '3', 'title' => 'Pantau Status', 'desc' => 'Cek progres pengerjaan secara real-time.'],
                                ['n' => '4', 'title' => 'Antar & Bayar', 'desc' => 'Bayar and pakaian bersih diantar kembali.'],
                            ];
                        @endphp
                        @foreach ($steps as $step)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full border border-zyngga-blue-300 flex items-center justify-center shrink-0">
                                    <x-zyngga-text variant="xs" weight="medium" color="primary">{{ $step['n'] }}</x-zyngga-text>
                                </div>
                                <div class="space-y-1">
                                    <x-zyngga-text variant="sm" weight="regular" class="leading-none">{{ $step['title'] }}</x-zyngga-text>
                                    <x-zyngga-text variant="xs" color="neutral-500">{{ $step['desc'] }}</x-zyngga-text>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 OUTLET KAMI CARD
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4">
                    <div class="h-8 flex items-center">
                        <x-zyngga-text variant="base" weight="medium">Outlet Kami</x-zyngga-text>
                    </div>

                    <div class="space-y-4">
                        @php
                            $outlets = [
                                ['name' => 'Zyngga Laundry Sukabirus', 'address' => 'Jl. Sukabirus No. 99', 'map' => 'https://maps.app.goo.gl/uMGkcaDueS74pU3T7'],
                                ['name' => 'Zyngga Laundry Sukapura', 'address' => 'Jl. Sukapura No. 97', 'map' => 'https://maps.app.goo.gl/1DKMzTAJ7FbG9YDa7'],
                            ];
                        @endphp
                        @foreach ($outlets as $outlet)
                            <div class="flex items-center gap-4">
                                <div class="w-[168px] h-[110px] rounded-lg overflow-hidden shrink-0">
                                    <img src="https://res.cloudinary.com/dba18pvit/image/upload/v1782060609/myzyngga_assets/dpk9dumi8qkyeiphpqxm.png" alt="Outlet" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 flex flex-col justify-between h-[110px] py-1">
                                    <div class="space-y-1">
                                        <x-zyngga-text variant="base" weight="regular" class="leading-snug">{{ $outlet['name'] }}</x-zyngga-text>
                                        <x-zyngga-text variant="xs" color="neutral-500">{{ $outlet['address'] }}</x-zyngga-text>
                                    </div>
                                    <x-zyngga-button 
                                        type="a"
                                        target="_blank"
                                        variant="secondary"
                                        size="s"
                                        label="Cek Lokasi"
                                        href="{{ $outlet['map'] }}"
                                        class="w-fit !px-4"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </main>

        {{-- FOOTER --}}
        <x-zyngga-footer />
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
            setTimeout(() => feather.replace(), 500);
        });
        document.addEventListener('livewire:load', function () { feather.replace(); });
        document.addEventListener('livewire:navigated', function () { feather.replace(); });
    </script>
</body>
</html>
