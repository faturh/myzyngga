<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifikasi – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        html, body { margin: 0; background: #e8eff9; color: #0F0F0F; }
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

        .notif-card {
            height: 80px;
            background: white;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid transparent;
        }
        .notif-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>
@php
    $counts = [
        'Semua' => count($notifications),
        'Transaksi' => collect($notifications)->where('category', 'Transaksi')->count(),
        'Status' => collect($notifications)->where('category', 'Status')->count(),
        'Info' => collect($notifications)->where('category', 'Info')->count(),
        'Promo' => collect($notifications)->where('category', 'Promo')->count(),
    ];
@endphp
<body x-data="{ activeTab: 'Semua', counts: {{ json_encode($counts) }} }" class="bg-zyngga-blue-50 min-h-screen">
    
    <div class="min-h-screen flex flex-col">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Notifikasi" 
            :back="true"
            :backUrl="route('home')"
            :showBell="false"
            :hamburg="false"
        >
            <x-slot:extra>
                <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide py-1">
                    @foreach (['Semua', 'Transaksi', 'Status', 'Info', 'Promo'] as $tab)
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

        <main class="flex-1 flex flex-col" style="min-height: calc(100vh - 200px);">
            <div class="w-full max-w-5xl mx-auto px-5 py-[6px] flex flex-col gap-3">
                
                @forelse($notifications as $notification)
                <div x-show="activeTab === 'Semua' || activeTab === '{{ $notification['category'] }}'" class="notif-card">
                    <div class="notif-icon-box {{ $notification['box_class'] }}">
                        <i data-feather="{{ $notification['icon'] }}" class="w-5 h-5 {{ $notification['icon_class'] }}"></i>
                    </div>
                    <div class="flex-1 space-y-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <x-zyngga-text variant="sm" weight="semibold" class="truncate">{{ $notification['title'] }}</x-zyngga-text>
                            <x-zyngga-text variant="xs" color="neutral-500" class="shrink-0">{{ $notification['time'] }}</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" class="truncate leading-relaxed">
                            {{ $notification['message'] }}
                        </x-zyngga-text>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <img src="{{ asset('assets/images/empty-order-icon.svg') }}" alt="Belum Ada Notifikasi" width="24" height="24">
                    </div>
                    <x-zyngga-text variant="lg" weight="medium" class="mb-2 text-neutral-900 tracking-tight">Belum Ada Notifikasi</x-zyngga-text>
                    <x-zyngga-text variant="sm" color="neutral-500" class="px-6 leading-[1.6]">Notifikasi pesanan akan muncul setelah kamu membuat transaksi.</x-zyngga-text>
                </div>
                @endforelse

                @if(count($notifications) > 0)
                <div x-cloak x-show="counts[activeTab] === 0" class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <img src="{{ asset('assets/images/empty-order-icon.svg') }}" alt="Kategori Kosong" width="24" height="24">
                    </div>
                    <x-zyngga-text variant="lg" weight="medium" class="mb-2 text-neutral-900 tracking-tight">Kategori Kosong</x-zyngga-text>
                    <x-zyngga-text variant="sm" color="neutral-500" class="px-6 leading-[1.6]">Belum ada notifikasi yang sesuai dengan kategori ini.</x-zyngga-text>
                </div>
                @endif

            </div>
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
