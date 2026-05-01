<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Home – Zyngga</title>
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
    <x-sidebar />

    {{-- Main Content Wrapper --}}
    <div 
        class="transition-all duration-300 ease-in-out min-h-screen flex flex-col"
        :class="desktopCollapsed ? 'md:pr-[80px]' : 'md:pr-[280px]'"
        @sidebar-toggled.window="desktopCollapsed = $event.detail.collapsed"
        @resize.window="desktopCollapsed = (window.innerWidth >= 768 && window.innerWidth < 1024)"
    >
        {{-- Header --}}
        <div class="w-full max-w-3xl mx-auto">
            <x-dashboard-header
                :name="Auth::user()->name"
                :points="4"
            />
        </div>

        {{-- Main Section --}}
        <main class="w-full max-w-3xl mx-auto flex-1 flex flex-col pb-10">
            {{-- ─────────────────────────────────────────────────────────
                 PESAN SEKARANG CARD
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4">
                    {{-- Hero banner image --}}
                    <div class="w-full aspect-[353/120] rounded-lg overflow-hidden relative">
                        <img src="/figma/figma_banner_hero.png" alt="Banner" class="w-full h-full object-cover">
                    </div>

                    {{-- Section heading --}}
                    <div class="flex items-center justify-between">
                        <x-zyngga-text variant="base" weight="medium">Pesan Sekarang</x-zyngga-text>
                    </div>

                    {{-- Service icons: Kilat | Regular | Quick | Express — from Figma --}}
                    <div class="grid grid-cols-5">
                        @php
                            $services = [
                                ['label' => 'Regular', 'icon' => 'refresh-cw'],
                                ['label' => 'Quick',   'icon' => 'clock'],
                                ['label' => 'Express', 'icon' => 'fast-forward'],
                                ['label' => 'Kilat',   'icon' => 'zap'],
                                ['label' => 'Satuan',   'icon' => 'zap'],
                            ];
                        @endphp
                        @foreach ($services as $s)
                            <a
                                href="{{ route('order.pickup', ['service' => strtolower($s['label'])]) }}"
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
                 PESANAN KAMU CARD (active order)
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4">
                    {{-- Section heading --}}
                    <div class="flex items-center justify-between h-8">
                        <x-zyngga-text variant="base" weight="medium">Pesanan Kamu</x-zyngga-text>
                        <a href="{{ route('order.history') }}">
                            <x-zyngga-text variant="xs" weight="medium" color="primary">Lihat semua</x-zyngga-text>
                        </a>
                    </div>

                    {{-- Order row --}}
                    <div class="space-y-[10px]">
                        <div class="flex items-start justify-between">
                            {{-- Left: service + estimated date --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                        <x-zyngga-service-icon service="Express" class="w-3.5 h-3.5 text-zyngga-yellow-300" />
                                    </div>
                                    <x-zyngga-text variant="lg" weight="medium" as="p">Express</x-zyngga-text>
                                </div>
                                <x-zyngga-text variant="sm" color="neutral-500">Estimasi Selesai: Minggu, 10 Mar</x-zyngga-text>
                            </div>
                            {{-- Right: status badge --}}
                            <x-zyngga-status type="secondary" size="M" icon="loader" label="Diproses" />
                        </div>

                        {{-- Progress bar --}}
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-1 bg-zyngga-blue-50 rounded-full overflow-hidden">
                                <div class="h-full bg-zyngga-blue-300 rounded-full" style="width:56%"></div>
                            </div>
                            <x-zyngga-text variant="base" weight="medium">56%</x-zyngga-text>
                        </div>
                    </div>

                    {{-- Detail button --}}
                    <x-zyngga-button 
                        type="a"
                        href="{{ route('order.detail') }}"
                        variant="secondary"
                        size="m"
                        label="Lihat Detail"
                        class="w-full"
                    />
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 PROMO BANNER
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="w-full aspect-[385/168] rounded-lg overflow-hidden relative">
                    <img src="/figma/figma_banner_promo.png" alt="Promo Banner" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#1660C1]/60 to-transparent flex flex-col justify-between p-5">
                        <x-zyngga-text variant="base" weight="medium" color="white" class="max-w-[180px]">
                            Matahari Sembunyi?<br>Tenang, Ada Kami.
                        </x-zyngga-text>
                        <x-zyngga-button 
                            variant="primary"
                            size="s"
                            icon="chevron-right"
                            iconPosition="right"
                            label="Pesan Sekarang"
                            class="bg-white !text-zyngga-blue-300 hover:bg-gray-100"
                        />
                    </div>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 PESANAN TERAKHIR CARD
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4">
                    <x-zyngga-text variant="base" weight="medium" class="h-8 flex items-center">Pesanan Terakhir</x-zyngga-text>

                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                    <x-zyngga-service-icon service="Quick" class="w-3.5 h-3.5 text-zyngga-yellow-300" />
                                </div>
                                <x-zyngga-text variant="lg" weight="medium" as="p">Quick</x-zyngga-text>
                            </div>
                            <x-zyngga-text variant="sm" color="neutral-500">22 Agustus 2026</x-zyngga-text>
                        </div>

                        <x-zyngga-status type="secondary" size="M" icon="check" label="Selesai" />
                    </div>

                    <x-zyngga-button 
                        variant="primary"
                        size="m"
                        icon="refresh-ccw"
                        iconPosition="left"
                        label="Ulangi Pesanan"
                        class="w-full"
                    />
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
                                ['n' => '4', 'title' => 'Antar & Bayar', 'desc' => 'Bayar dan pakaian bersih diantar kembali.'],
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
                                ['name' => 'Zyngga Laundry Sukabirus', 'address' => 'Jl. Sukabirus No. 99'],
                                ['name' => 'Zyngga Laundry Sukapura', 'address' => 'Jl. Sukapura No. 97'],
                            ];
                        @endphp
                        @foreach ($outlets as $outlet)
                            <div class="flex items-center gap-4">
                                <div class="w-[168px] h-[110px] rounded-lg overflow-hidden shrink-0">
                                    <img src="/figma/figma_outlet.png" alt="Outlet" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 flex flex-col justify-between h-[110px] py-1">
                                    <div class="space-y-1">
                                        <x-zyngga-text variant="base" weight="regular" class="leading-snug">{{ $outlet['name'] }}</x-zyngga-text>
                                        <x-zyngga-text variant="xs" color="neutral-500">{{ $outlet['address'] }}</x-zyngga-text>
                                    </div>
                                    <x-zyngga-button 
                                        variant="secondary"
                                        size="s"
                                        label="Cek Lokasi"
                                        class="w-full"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 FOOTER
            ───────────────────────────────────────────────────────── --}}
            <x-zyngga-footer />

        </main>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
            setTimeout(() => feather.replace(), 500);
        });
        document.addEventListener('livewire:load', function () {
            feather.replace();
        });
        document.addEventListener('livewire:navigated', function () {
            feather.replace();
        });
    </script>
</body>
</html>
