<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Pesanan – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; color: #0F0F0F; }
        .action-item:hover { border-color: #1660C1; background: #e8eff9; }
        [x-cloak] { display: none !important; }

        .order-card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .tab-btn {
            height: 32px;
            padding: 0 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .tab-btn-active {
            background: #e8eff9;
            color: #1660C1;
            border: 1px solid #1660C1;
        }
        .tab-btn-inactive {
            background: #F4F4F4;
            color: #808080;
            border: 1px solid transparent;
        }
    </style>
</head>
<body x-data="{ activeTab: 'Semua' }" class="bg-zyngga-blue-50">
    <div class="w-full max-w-[425px] mx-auto min-h-screen flex flex-col">
        <x-sidebar />
        
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <div class="sticky top-0 z-40 bg-white rounded-b-2xl shadow-[0_4px_24px_rgba(0,0,0,0.08)] px-5 py-5 mb-[6px]">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <x-zyngga-button 
                        variant="neutral"
                        size="l"
                        icon="menu"
                        iconPosition="only"
                        @click="$dispatch('open-sidebar')"
                    />
                    <x-zyngga-text variant="lg" weight="semibold" as="h1">Pesanan</x-zyngga-text>
                </div>
                <x-zyngga-button 
                    variant="neutral"
                    size="l"
                    icon="filter"
                    iconPosition="only"
                />
            </div>

            {{-- Filter Tabs --}}
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide">
                <template x-for="tab in ['Semua', 'Belum Bayar', 'Diproses', 'Selesai']" :key="tab">
                    <button 
                        @click="activeTab = tab"
                        :class="activeTab === tab ? 'tab-btn-active' : 'tab-btn-inactive'"
                        class="tab-btn"
                        x-text="tab"
                    ></button>
                </template>
            </div>
        </div>

        {{-- ── ORDER LIST ─────────────────────────────────────────── --}}
        <div class="px-5 py-[6px] flex flex-col gap-3">
            
            {{-- Order 1: Delivery --}}
            <div class="order-card">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                                <x-zyngga-service-icon service="Express" class="w-3.5 h-3.5 text-zyngga-yellow-300" />
                            </div>
                            <x-zyngga-text variant="base" weight="semibold" class="tracking-tight">Express</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" weight="medium" class="mt-1">2 Feb 2025 | 12:09</x-zyngga-text>
                    </div>
                    <x-zyngga-status type="secondary" size="M" icon="package" label="Delivery" />
                </div>
                
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="medium">Jumlah</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">3 items</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="medium">Berat timbangan</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">3.3 kg</x-zyngga-text>
                    </div>
                </div>

                <div class="flex items-end justify-between">
                    <div>
                        <x-zyngga-text variant="xs" color="neutral-500" weight="medium">Total</x-zyngga-text>
                        <x-zyngga-text variant="base" weight="semibold">Rp33.000</x-zyngga-text>
                    </div>
                    <x-zyngga-button 
                        type="a"
                        href="{{ route('order.detail') }}"
                        variant="primary"
                        size="m"
                        label="Lihat Detail"
                    />
                </div>
            </div>

            {{-- Order 2: Diproses --}}
            <div class="order-card">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                                <x-zyngga-service-icon service="Satuan" class="w-3.5 h-3.5 text-zyngga-yellow-300" />
                            </div>
                            <x-zyngga-text variant="base" weight="semibold" class="tracking-tight">Satuan</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" weight="medium" class="mt-1">2 Feb 2025 | 12:09</x-zyngga-text>
                    </div>
                    <x-zyngga-status type="secondary" size="L" icon="refresh-cw" label="Diproses" />
                </div>
                
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="medium">Jumlah</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">3 items</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="medium">Berat timbangan</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">3.3 kg</x-zyngga-text>
                    </div>
                </div>

                <div class="flex items-end justify-between">
                    <div>
                        <x-zyngga-text variant="xs" color="neutral-500" weight="medium">Total</x-zyngga-text>
                        <x-zyngga-text variant="base" weight="semibold">Rp33.000</x-zyngga-text>
                    </div>
                    <x-zyngga-button 
                        type="a"
                        href="{{ route('order.detail') }}"
                        variant="primary"
                        size="m"
                        label="Lihat Detail"
                    />
                </div>
            </div>

            {{-- Order 3: Selesai --}}
            <div class="order-card">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                                <x-zyngga-service-icon service="Satuan" class="w-3.5 h-3.5 text-zyngga-yellow-300" />
                            </div>
                            <x-zyngga-text variant="base" weight="semibold" class="tracking-tight">Satuan</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" weight="medium" class="mt-1">2 Feb 2025 | 12:09</x-zyngga-text>
                    </div>
                    <x-zyngga-status type="secondary" size="L" icon="check" label="Selesai" />
                </div>
                
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="medium">Jumlah</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">3 items</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="medium">Berat timbangan</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">3.3 kg</x-zyngga-text>
                    </div>
                </div>

                <div class="flex items-end justify-between">
                    <div>
                        <x-zyngga-text variant="xs" color="neutral-500" weight="medium">Total</x-zyngga-text>
                        <x-zyngga-text variant="base" weight="semibold">Rp33.000</x-zyngga-text>
                    </div>
                    <x-zyngga-button 
                        variant="primary"
                        size="m"
                        label="Ulangi Pesanan"
                    />
                </div>
            </div>

        </div>

        {{-- ── FOOTER ─────────────────────────────────────────────── --}}
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
