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
</head>
<body x-data="{ activeTab: 'Semua' }" class="bg-zyngga-blue-50 min-h-screen">
    
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

        <main class="flex-1 flex flex-col">
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
                <div class="notif-card">
                    <div class="notif-icon-box bg-[#E8EFF9]">
                        <i data-feather="bell" class="w-5 h-5 text-zyngga-blue-300"></i>
                    </div>
                    <div class="flex-1 space-y-1 min-w-0">
                        <x-zyngga-text variant="sm" weight="semibold" class="truncate">Belum ada notifikasi</x-zyngga-text>
                        <x-zyngga-text variant="xs" color="neutral-500" class="truncate leading-relaxed">
                            Notifikasi pesanan akan muncul setelah kamu membuat transaksi.
                        </x-zyngga-text>
                    </div>
                </div>
                @endforelse

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
