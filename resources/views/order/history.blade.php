<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Pesanan – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        html, body { margin: 0; background: #e8eff9; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }


        .filter-chip {
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .filter-chip.active {
            background: #1660C1;
            color: white;
        }
        .filter-chip.inactive {
            background: #F4F4F4;
            color: #808080;
        }
    </style>
</head>
<body x-data="{ desktopCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || (localStorage.getItem('sidebarCollapsed') === null && window.innerWidth >= 768 && window.innerWidth < 1024), activeTab: 'Semua' }" class="bg-zyngga-blue-50 min-h-screen">
    <x-sidebar active="order" />

    {{-- Main Content Wrapper --}}
    <div 
        class="transition-all duration-300 ease-in-out min-h-screen flex flex-col"
        :class="desktopCollapsed ? 'md:pr-[80px]' : 'md:pr-[280px]'"
        @sidebar-toggled.window="desktopCollapsed = $event.detail.collapsed"
        @resize.window="desktopCollapsed = (window.innerWidth >= 768 && window.innerWidth < 1024)"
    >
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Riwayat Pesanan"
            :showPoints="false"
            :showMenu="true"
            :maxWidth="'max-w-3xl'"
        >
            <x-slot:extra>
                <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide py-1">
                    @foreach (['Semua', 'Berlangsung', 'Selesai', 'Dibatalkan'] as $tab)
                        <button 
                            @click="activeTab = '{{ $tab }}'"
                            :class="activeTab === '{{ $tab }}' ? 'filter-chip active' : 'filter-chip inactive'"
                        >
                            {{ $tab }}
                        </button>
                    @endforeach
                </div>
            </x-slot:extra>
        </x-dashboard-header>

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col">
            <div class="w-full max-w-3xl mx-auto px-5">
                <div class="flex flex-col py-[6px]">
                    
                    {{-- Order 1: Delivery --}}
                    <x-zyngga-card x-show="activeTab === 'Semua' || activeTab === 'Berlangsung'">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                    <x-zyngga-service-icon service="Express" class="w-4 h-4 text-zyngga-yellow-300" />
                                </div>
                                <div>
                                    <x-zyngga-text variant="base" weight="medium">Express</x-zyngga-text>
                                    <x-zyngga-text variant="xs" color="neutral-500">22 Agustus 2026</x-zyngga-text>
                                </div>
                            </div>
                            <x-zyngga-status type="secondary" size="M" icon="truck" label="Delivery" />
                        </div>
                        
                        <div class="bg-[#F4F4F4] rounded-lg p-3 space-y-2 mb-4">
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Estimasi Selesai</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium">24 Agu, 12:00</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Total</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium">Rp45.000</x-zyngga-text>
                            </div>
                        </div>

                        <x-zyngga-button 
                            type="a"
                            href="{{ route('order.detail') }}"
                            variant="secondary"
                            size="m"
                            label="Lihat Detail"
                            class="w-full"
                        />
                    </x-zyngga-card>

                    {{-- Order 2: Selesai --}}
                    <x-zyngga-card x-show="activeTab === 'Semua' || activeTab === 'Selesai'">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                    <x-zyngga-service-icon service="Quick" class="w-4 h-4 text-zyngga-yellow-300" />
                                </div>
                                <div>
                                    <x-zyngga-text variant="base" weight="medium">Quick</x-zyngga-text>
                                    <x-zyngga-text variant="xs" color="neutral-500">15 Agustus 2026</x-zyngga-text>
                                </div>
                            </div>
                            <x-zyngga-status type="secondary" size="M" icon="check" label="Selesai" />
                        </div>

                        <div class="bg-[#F4F4F4] rounded-lg p-3 space-y-2 mb-4">
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Jumlah item</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium">3 items</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Berat timbangan</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium">3.3 kg</x-zyngga-text>
                            </div>
                        </div>

                        <div class="flex items-end justify-between">
                            <div>
                                <x-zyngga-text variant="xs" color="neutral-500" weight="regular">Total</x-zyngga-text>
                                <x-zyngga-text variant="base" weight="medium">Rp33.000</x-zyngga-text>
                            </div>
                            <x-zyngga-button 
                                variant="primary"
                                size="m"
                                label="Ulangi Pesanan"
                            />
                        </div>
                    </x-zyngga-card>

                </div>
            </div>

            {{-- ── FOOTER ─────────────────────────────────────────────── --}}
            <x-zyngga-footer :maxWidth="'max-w-3xl'" />
        </main>
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
