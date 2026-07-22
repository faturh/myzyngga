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
        .divider {
            height: 1px;
            background-color: #F4F4F4;
            width: 100%;
            margin: 12px 0;
        }
        .progress-container {
            width: 100%;
            height: 4px;
            background: #E8EFF9;
            border-radius: 100px;
        }
        .progress-bar {
            height: 100%;
            background: #1660C1;
            border-radius: 100px;
        }
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
        <x-dashboard-header
            :name="Auth::user()->name"
            :points="4"
            :showBell="true"
        />

        {{-- Main Section --}}
        <main class="w-full max-w-5xl mx-auto flex-1 flex flex-col" style="min-height: calc(100vh - 200px);">
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
                    <div class="h-8 flex items-center">
                        <x-zyngga-text variant="base" weight="medium">Pesan Sekarang</x-zyngga-text>
                    </div>

                    {{-- Service icons: Kilat | Regular | Quick | Express | Satuan --}}
                    <div class="grid grid-cols-5">
                        @php
                            $services = [
                                ['label' => 'Reguler', 'icon' => 'star'],
                                ['label' => 'Quick',   'icon' => 'clock'],
                                ['label' => 'Express', 'icon' => 'fast-forward'],
                                ['label' => 'Kilat',   'icon' => 'zap'],
                                ['label' => 'Satuan',  'icon' => 'package'],
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
                 MAU CEK PESANAN CARD
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5">
                <x-zyngga-card padding="p-4">
                    <div class="flex items-center justify-between min-h-[56px]">
                        <div class="flex flex-col">
                            <x-zyngga-text variant="sm" weight="medium" class="leading-snug">Mau Cek Pesanan?</x-zyngga-text>
                            <x-zyngga-text variant="xs" color="neutral-500" class="leading-snug mt-0.5">Cek status pesananmu disini</x-zyngga-text>
                        </div>
                        <x-zyngga-button 
                            type="a"
                            href="{{ route('order.check') }}"
                            variant="secondary"
                            size="m"
                            icon="search"
                            label="Cek Status"
                            iconPosition="left"
                        />
                    </div>
                </x-zyngga-card>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 PESANAN KAMU CARD (active order)
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4 {{ $activeOrder ? 'cursor-pointer' : '' }}" @if($activeOrder) onclick="window.location.href='{{ route('order.detail', ['id' => $activeOrder['nota_layanan']]) }}'" @endif>
                    {{-- Section heading --}}
                    <div class="flex items-center justify-between h-8">
                        <x-zyngga-text variant="base" weight="medium">Pesanan Kamu</x-zyngga-text>
                        <x-zyngga-button 
                            type="a" 
                            href="{{ route('order.history') }}" 
                            variant="tertiary" 
                            size="s" 
                            label="Lihat semua"                     
                            onclick="event.stopPropagation()"
                        />
                    </div>

                    {{-- Order info row --}}
                    @if($activeOrder)
                    <div class="space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                                    <x-zyngga-service-icon :service="$activeOrder['service']" class="w-[18px] h-[18px] text-zyngga-yellow-300" />
                                </div>
                                <div class="flex flex-col">
                                    <x-zyngga-text variant="lg" weight="medium">{{ $activeOrder['service'] }}</x-zyngga-text>
                                    <x-zyngga-text variant="sm" color="neutral-500">{{ $activeOrder['date'] }}</x-zyngga-text>
                                </div>
                            </div>
                            <x-zyngga-status type="secondary" size="M" :icon="$activeOrder['delivery_icon']" :label="$activeOrder['delivery_status']" />
                        </div>
                        
                        <div class="flex items-center gap-4 mb-4">
                            <div class="progress-container flex-1">
                                <div class="progress-bar" style="width: {{ $activeOrder['progress'] }}%"></div>
                            </div>
                            <x-zyngga-text variant="sm" weight="medium">{{ $activeOrder['progress'] }}%</x-zyngga-text>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Total</x-zyngga-text>
                                <x-zyngga-text variant="base" weight="medium">Rp{{ number_format($activeOrder['total'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            <x-zyngga-button 
                                type="a"
                                href="https://wa.me/6282125322500"
                                target="_blank"
                                variant="secondary"
                                size="m"
                                icon="message-circle"
                                label="Chat"
                                iconPosition="left"
                                @click.stop=""
                            />
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-4 text-center">
                        <div class="w-12 h-12 bg-[#F4F4F4] rounded-full flex items-center justify-center mx-auto mb-3">
                            <img src="{{ asset('assets/images/empty-order-icon.svg') }}" alt="Belum ada pesanan aktif" width="24" height="24">
                        </div>
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-500" class="text-center">Belum ada pesanan aktif.</x-zyngga-text>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 PESANAN TERAKHIR CARD
            ───────────────────────────────────────────────────────── --}}
            @if($latestOrder)
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4 cursor-pointer" onclick="window.location.href='{{ route('order.detail', ['id' => $latestOrder['nota_layanan']]) }}'">
                    <x-zyngga-text variant="base" weight="medium" class="h-8 flex items-center">Pesanan Terakhir</x-zyngga-text>

                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                                <x-zyngga-service-icon :service="$latestOrder['service']" class="w-[18px] h-[18px] text-zyngga-yellow-300" />
                            </div>
                            <div class="flex flex-col">
                                <x-zyngga-text variant="lg" weight="medium">{{ $latestOrder['service'] }}</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $latestOrder['date'] }}</x-zyngga-text>
                            </div>
                        </div>
                        <x-zyngga-status type="secondary" size="M" :icon="$latestOrder['delivery_icon']" :label="$latestOrder['delivery_status']" />
                    </div>

                    <div class="flex items-end justify-between">
                        <div>
                            <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Total</x-zyngga-text>
                            <x-zyngga-text variant="base" weight="medium">Rp{{ number_format($latestOrder['total'], 0, ',', '.') }}</x-zyngga-text>
                        </div>
                        <x-zyngga-button
                            type="a"
                            href="{{ route('order.repeat', $latestOrder['id']) }}"
                            variant="primary"
                            size="m"
                            label="Ulangi Pesanan"
                            @click.stop=""
                        />
                    </div>
                </div>
            </div>
            @endif

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
                                    <x-zyngga-text variant="xs" weight="medium" color="primary" class="leading-none pt-[2px]">{{ $step['n'] }}</x-zyngga-text>
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
                 JENIS LAYANAN CARD
            ───────────────────────────────────────────────────────── --}}
            <div class="px-5 py-[6px]">
                <div class="bg-white rounded-lg p-4 space-y-4">
                    <div class="h-8 flex items-center">
                        <x-zyngga-text variant="base" weight="medium">Jenis Layanan</x-zyngga-text>
                    </div>

                    <div class="flex flex-col">
                        @php
                            $services = [
                                ['name' => 'Regular', 'desc' => 'Layanan 3 hari (72 jam)', 'price' => 'Rp4.850/kg'],
                                ['name' => 'Quick', 'desc' => 'Layanan 2 hari (48 jam)', 'price' => 'Rp6.000/kg'],
                                ['name' => 'Express', 'desc' => 'Layanan 1 hari (24 jam)', 'price' => 'Rp6.250/kg'],
                                ['name' => 'Kilat', 'desc' => 'Layanan 5 jam', 'price' => 'Rp7.850/kg'],
                                ['name' => 'Satuan', 'desc' => 'Selimut, Bed Cover, dll.', 'price' => 'Mulai Rp10.000'],
                            ];
                        @endphp
                        @foreach ($services as $service)
                            <div class="flex items-center justify-between h-[56px]">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col">
                                        <x-zyngga-text variant="sm" weight="medium" class="leading-snug text-neutral-900">
                                            {{ $service['name'] }}
                                        </x-zyngga-text>
                                        <x-zyngga-text variant="xs" color="neutral-500" class="leading-snug mt-0.5">
                                            {{ $service['desc'] }}
                                        </x-zyngga-text>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[14px] text-[#0F0F0F] font-normal">{{ $service['price'] }}</span>
                                </div>
                            </div>
                            @if(!$loop->last)
                            <x-zyngga-divider class=" !my-[6px]" />
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

        </main>

        {{-- ─────────────────────────────────────────────────────────
             FOOTER
        ───────────────────────────────────────────────────────── --}}
        <x-zyngga-footer />
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
