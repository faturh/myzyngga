<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<body x-data="{}" class="bg-zyngga-blue-50 min-h-screen">
    {{-- Sidebar (for guests as well, showing general links) --}}
    <x-sidebar />

    {{-- ── GUEST HEADER ────────────────────────────────────────── --}}
    <header class="sticky top-0 z-40 w-full max-w-[425px] mx-auto pb-[6px]">
        <div class="bg-white rounded-b-2xl shadow-[0_4px_24px_rgba(0,0,0,0.10)] px-5 py-5 transition-shadow duration-300">
            <div class="flex items-center justify-between gap-4">
                {{-- Hamburger --}}
                <x-zyngga-button 
                    variant="neutral"
                    size="m"
                    icon="menu"
                    iconPosition="only"
                    @click="$dispatch('open-sidebar')"
                />

                {{-- Guest Auth Buttons --}}
                <div class="flex items-center gap-3">
                    <x-zyngga-button 
                        type="a"
                        href="{{ route('login') }}"
                        variant="tertiary"
                        size="m"
                        label="Masuk"
                        class="!px-2"
                    />
                    <x-zyngga-button 
                        type="a"
                        href="{{ route('register') }}"
                        variant="primary"
                        size="m"
                        label="Daftar"
                    />
                </div>
            </div>
        </div>
    </header>

    {{-- Outer wrapper constrained to mobile width, centered --}}
    <div class="w-full max-w-[425px] mx-auto min-h-screen flex flex-col">

        {{-- ── PESAN SEKARANG CARD ────────────────────────────────── --}}
        <div class="px-5 py-[6px]">
            <div class="bg-white rounded-lg p-4 space-y-4">
                {{-- Hero banner image --}}
                <div class="w-full aspect-[353/120] rounded-lg overflow-hidden relative">
                    <img src="/figma/figma_banner_hero.png" alt="Banner" class="w-full h-full object-cover">
                </div>

                {{-- Section heading --}}
                <div class="flex items-center justify-between">
                    <x-zyngga-text variant="lg" weight="semibold">Pesan Sekarang</x-zyngga-text>
                    <button onclick="window.location.href='#'">
                        <x-zyngga-text variant="xs" weight="semibold" color="primary">Lihat semua</x-zyngga-text>
                    </button>
                </div>

                {{-- Service icons --}}
                <div class="grid grid-cols-4">
                    @php
                        $services = [
                            ['label' => 'Kilat',   'icon' => 'zap'],
                            ['label' => 'Regular', 'icon' => 'refresh-cw'],
                            ['label' => 'Quick',   'icon' => 'clock'],
                            ['label' => 'Express', 'icon' => 'fast-forward'],
                        ];
                    @endphp
                    @foreach ($services as $s)
                        <a
                            href="{{ route('login') }}"
                            class="flex flex-col items-center gap-2 h-16 justify-center hover:opacity-80 transition-opacity"
                        >
                            <div class="w-9 h-9 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                <i data-feather="{{ $s['icon'] }}" class="w-[18px] h-[18px] text-zyngga-yellow-300"></i>
                            </div>
                            <x-zyngga-text variant="sm" weight="medium">{{ $s['label'] }}</x-zyngga-text>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── PROMO BANNER ────────────────────────────────────────── --}}
        <div class="px-5 py-[6px]">
            <div class="w-full aspect-[385/168] rounded-lg overflow-hidden relative">
                <img src="/figma/figma_banner_promo.png" alt="Promo Banner" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-br from-[#1660C1]/60 to-transparent flex flex-col justify-between p-5">
                    <x-zyngga-text variant="base" weight="semibold" color="white" class="max-w-[180px]">Matahari Sembunyi?<br>Tenang, Ada Kami.</x-zyngga-text>
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

        {{-- ── ALUR PEMESANAN CARD ────────────────────────────────── --}}
        <div class="px-5 py-[6px]">
            <div class="bg-white rounded-lg p-4 space-y-4">
                <div class="h-8 flex items-center">
                    <x-zyngga-text variant="lg" weight="semibold">Alur Pemesanan</x-zyngga-text>
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
                                <x-zyngga-text variant="xs" weight="semibold" color="primary">{{ $step['n'] }}</x-zyngga-text>
                            </div>
                            <div class="space-y-1">
                                <x-zyngga-text variant="sm" weight="medium" class="leading-none">{{ $step['title'] }}</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500">{{ $step['desc'] }}</x-zyngga-text>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── OUTLET KAMI CARD ─────────────────────────────────── --}}
        <div class="px-5 py-[6px]">
            <div class="bg-white rounded-lg p-4 space-y-4">
                <div class="h-8 flex items-center">
                    <x-zyngga-text variant="lg" weight="semibold">Outlet Kami</x-zyngga-text>
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
                                    <x-zyngga-text variant="base" weight="medium" class="leading-snug">{{ $outlet['name'] }}</x-zyngga-text>
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
