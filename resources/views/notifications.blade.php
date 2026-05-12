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
                    @foreach (['Semua', 'Transaksi', 'Status', 'Promo'] as $tab)
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
                
                {{-- STATUS: Pesanan Selesai --}}
                <div x-show="activeTab === 'Semua' || activeTab === 'Status'" class="notif-card">
                    <div class="notif-icon-box bg-[#E8EFF9]">
                        <i data-feather="check-circle" class="w-5 h-5 text-zyngga-blue-300"></i>
                    </div>
                    <div class="flex-1 space-y-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <x-zyngga-text variant="sm" weight="semibold" class="truncate">Pesanan Selesai!</x-zyngga-text>
                            <x-zyngga-text variant="2xs" color="neutral-500" class="shrink-0">Baru saja</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" class="truncate leading-relaxed">
                            Pesanan Quick Anda (IJK902H8MAHD) telah selesai dicuci dan siap diambil.
                        </x-zyngga-text>
                    </div>
                </div>

                {{-- TRANSAKSI: Pembayaran Berhasil --}}
                <div x-show="activeTab === 'Semua' || activeTab === 'Transaksi'" class="notif-card">
                    <div class="notif-icon-box bg-[#E9F7EE]">
                        <i data-feather="credit-card" class="w-5 h-5 text-zyngga-status-success"></i>
                    </div>
                    <div class="flex-1 space-y-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <x-zyngga-text variant="sm" weight="semibold" class="truncate">Pembayaran Berhasil</x-zyngga-text>
                            <x-zyngga-text variant="2xs" color="neutral-500" class="shrink-0">15 menit lalu</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" class="truncate leading-relaxed">
                            Pembayaran untuk pesanan #IJK902H8MAHD sebesar Rp33.000 telah kami terima.
                        </x-zyngga-text>
                    </div>
                </div>

                {{-- STATUS: Kurir Menuju Lokasi --}}
                <div x-show="activeTab === 'Semua' || activeTab === 'Status'" class="notif-card">
                    <div class="notif-icon-box bg-[#E8EFF9]">
                        <i data-feather="truck" class="w-5 h-5 text-zyngga-blue-300"></i>
                    </div>
                    <div class="flex-1 space-y-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <x-zyngga-text variant="sm" weight="semibold" class="truncate">Kurir Menuju Lokasi</x-zyngga-text>
                            <x-zyngga-text variant="2xs" color="neutral-500" class="shrink-0">2 jam yang lalu</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" class="truncate leading-relaxed">
                            Kurir sedang menuju lokasi Anda untuk menjemput pakaian. Mohon tunggu ya!
                        </x-zyngga-text>
                    </div>
                </div>

                {{-- PROMO: Diskon --}}
                <div x-show="activeTab === 'Semua' || activeTab === 'Promo'" class="notif-card">
                    <div class="notif-icon-box bg-[#FDF4E9]">
                        <i data-feather="gift" class="w-5 h-5 text-zyngga-yellow-300"></i>
                    </div>
                    <div class="flex-1 space-y-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <x-zyngga-text variant="sm" weight="semibold" class="truncate">Promo Khusus Member</x-zyngga-text>
                            <x-zyngga-text variant="2xs" color="neutral-500" class="shrink-0">1 hari yang lalu</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" class="truncate leading-relaxed">
                            Dapatkan diskon 20% untuk semua layanan Express hari ini. Gunakan kode: ZYNGGA20
                        </x-zyngga-text>
                    </div>
                </div>

                {{-- TRANSAKSI: Tagihan --}}
                <div x-show="activeTab === 'Semua' || activeTab === 'Transaksi'" class="notif-card">
                    <div class="notif-icon-box bg-[#E9F7EE]">
                        <i data-feather="file-text" class="w-5 h-5 text-zyngga-status-success"></i>
                    </div>
                    <div class="flex-1 space-y-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <x-zyngga-text variant="sm" weight="semibold" class="truncate">Tagihan Baru Tersedia</x-zyngga-text>
                            <x-zyngga-text variant="2xs" color="neutral-500" class="shrink-0">2 hari yang lalu</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="xs" color="neutral-500" class="truncate leading-relaxed">
                            Pesanan Anda telah selesai ditimbang. Segera lakukan pembayaran untuk proses selanjutnya.
                        </x-zyngga-text>
                    </div>
                </div>

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
