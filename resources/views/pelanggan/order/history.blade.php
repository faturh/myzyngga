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
            font-weight: 400;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .filter-chip.active {
            border: 1px solid #1660C1;
            background: #E8EFF9;
            color: #1660C1;
        }
        .filter-chip.inactive {
            background: #F4F4F4;
            color: #0F0F0F;
            border: 1px solid transparent;
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
        .history-container-height {
            min-height: calc(100vh - 255px);
        }
        @media (min-width: 768px) {
            .history-container-height {
                min-height: calc(100vh - 216px);
            }
        }
    </style>
    @php
        $tabCounts = [
            'Semua' => count($orders),
            'Belum Bayar' => collect($orders)->where('status', 'Belum Bayar')->count(),
            'Diproses' => collect($orders)->where('status', 'Diproses')->count(),
            'Selesai' => collect($orders)->where('status', 'Selesai')->count(),
        ];
    @endphp
</head>
<body x-data="{ desktopCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || (localStorage.getItem('sidebarCollapsed') === null && window.innerWidth >= 768 && window.innerWidth < 1024), activeTab: 'Semua', counts: {{ json_encode($tabCounts) }} }" class="bg-zyngga-blue-50 min-h-screen">
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
            title="Pesanan Kamu"
            :showPoints="false"
            :showMenu="true"
            :maxWidth="'max-w-full'"
        >
            <x-slot:extra>
                <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide">
                    @foreach (['Semua', 'Belum Bayar', 'Diproses', 'Selesai'] as $tab)
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
            <div class="w-full max-w-5xl mx-auto px-5">
                <div class="flex flex-col history-container-height">
                    
                    @forelse($orders as $order)
                    <x-zyngga-card x-show="activeTab === 'Semua' || activeTab === '{{ $order['status'] }}'"
                        onclick="window.location.href='{{ route('order.detail', ['id' => $order['id']]) }}'"
                        class="cursor-pointer"
                    >
                        <div class="flex items-start justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                                    <x-zyngga-service-icon :service="$order['service']" class="w-[18px] h-[18px] text-zyngga-yellow-300" />
                                </div>
                                <div class="flex flex-col">
                                    <x-zyngga-text variant="lg" weight="medium">{{ $order['service'] }}</x-zyngga-text>
                                    <x-zyngga-text variant="sm" color="neutral-500">{{ $order['date'] }}</x-zyngga-text>
                                </div>
                            </div>
                            <x-zyngga-status type="secondary" size="M" :icon="$order['delivery_icon']" :label="$order['delivery_status']" />
                        </div>

                        @if($order['status'] !== 'Selesai')
                        <div class="flex items-center gap-4 mb-5">
                            <div class="progress-container flex-1">
                                <div class="progress-bar" style="width: {{ $order['progress'] }}%"></div>
                            </div>
                            <x-zyngga-text variant="base" weight="medium">{{ $order['progress'] }}%</x-zyngga-text>
                        </div>
                        @else
                        <div class="space-y-1 mb-4">
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Jumlah</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium">{{ $order['items_count'] }} items</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Berat timbangan</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium">{{ $order['weight'] }} kg</x-zyngga-text>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <div>
                                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">Total</x-zyngga-text>
                                <x-zyngga-text variant="base" weight="medium">Rp{{ number_format($order['total'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            @if($order['status'] === 'Selesai')
                                <x-zyngga-button
                                    type="a"
                                    href="{{ route('order.repeat', $order['id']) }}"
                                    variant="primary"
                                    size="m"
                                    label="Ulangi Pesanan"
                                    @click.stop=""
                                />
                            @else
                                <x-zyngga-button
                                    type="a"
                                    href="https://wa.me/+6281297673318"
                                    target="_blank"
                                    variant="secondary"
                                    size="m"
                                    icon="message-circle"
                                    label="Chat"
                                    iconPosition="left"
                                    @click.stop=""
                                />
                            @endif
                        </div>
                    </x-zyngga-card>
                    @empty
                    <div class="flex flex-col items-center justify-center w-full max-w-sm mx-auto text-center history-container-height">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-5 shadow-sm">
                            <img src="{{ asset('assets/images/empty-order-icon.svg') }}" alt="Belum Ada Pesanan" width="36" height="36">
                        </div>
                        <x-zyngga-text variant="lg" weight="medium" class="mb-2 text-neutral-900 tracking-tight">Kamu Belum Memiliki Pesanan</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" class="mb-6 px-6 leading-[1.6]">Semua riwayat transaksi dan pengerjaan laundry kamu akan muncul di sini.</x-zyngga-text>
                        <x-zyngga-button 
                            type="a"
                            href="{{ route('order.pickup', ['service' => 'reguler']) }}"
                            variant="primary" 
                            size="m" 
                            label="Buat Pesanan" 
                        />
                    </div>
                    @endforelse

                    @if(count($orders) > 0)
                    <div x-cloak x-show="counts[activeTab] === 0" class="flex flex-col items-center justify-center w-full max-w-sm mx-auto text-center history-container-height">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-5 shadow-sm">
                            <img src="{{ asset('assets/images/empty-order-icon.svg') }}" alt="Belum Ada Pesanan" width="36" height="36">
                        </div>
                        <x-zyngga-text variant="lg" weight="medium" class="mb-2 text-neutral-900 tracking-tight">Kategori Kosong</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" class="mb-6 px-6 leading-[1.6]">Belum ada riwayat pesanan yang sesuai dengan kategori ini.</x-zyngga-text>
                    </div>
                    @endif
                </div>
            </div>

        </main>

        {{-- ── FOOTER ─────────────────────────────────────────────── --}}
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
