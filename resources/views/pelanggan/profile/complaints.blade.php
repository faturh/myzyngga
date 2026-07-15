<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Komplain – Zyngga</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
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
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{ activeTab: 'Semua' }">
        {{-- ── HEADER ── --}}
        <x-dashboard-header 
            title="Riwayat Komplain" 
            :backUrl="route('profile')" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        >
            <x-slot:extra>
                <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide">
                    @foreach (['Semua', 'Diproses', 'Selesai'] as $tab)
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

        {{-- ── MAIN CONTENT ── --}}
        <main class="flex-1 flex flex-col relative" style="min-height: calc(100vh - 200px);">
            <div class="w-full max-w-5xl mx-auto px-5 pb-10">
                @forelse($complaints as $complaint)
                    @php
                        // Normalize status
                        $statusRaw = strtolower($complaint->status);
                        if ($statusRaw === 'pending' || $statusRaw === 'menunggu respon') {
                            $displayStatus = 'Diproses';
                            $statusType = 'secondary';
                            $statusIcon = 'loader';
                        } elseif ($statusRaw === 'selesai') {
                            $displayStatus = 'Selesai';
                            $statusType = 'success';
                            $statusIcon = 'check';
                        } else {
                            $displayStatus = $complaint->status;
                            $statusType = 'secondary';
                            $statusIcon = '';
                        }
                    @endphp
                    
                    <a href="{{ route('profile.complaint.detail', $complaint->id) }}" 
                       class="block"
                       x-show="activeTab === 'Semua' || activeTab === '{{ $displayStatus }}'"
                    >
                        <x-zyngga-card>
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-zyngga-yellow-50 flex items-center justify-center shrink-0">
                                        <x-zyngga-service-icon service="{{ $complaint->transaksi->layananPrioritas->nama ?? '-' }}" class="w-5 h-5 text-zyngga-yellow-300" />
                                    </div>
                                    <div class="flex flex-col">
                                        <x-zyngga-text variant="lg" weight="medium" color="neutral-900">{{ $complaint->transaksi->layananPrioritas->nama ?? '-' }}</x-zyngga-text>
                                        <x-zyngga-text variant="sm" color="neutral-500">{{ $complaint->transaksi->nota ?? $complaint->transaksi_id }}</x-zyngga-text>
                                    </div>
                                </div>
                                <x-zyngga-status 
                                    :type="$statusType" 
                                    size="M" 
                                    :label="$displayStatus" 
                                    :icon="$statusIcon"
                                />
                            </div>

                            <div class="space-y-2.5 mt-5">
                                <div class="flex justify-between items-center">
                                    <x-zyngga-text variant="sm" color="neutral-900" weight="medium">Kasir</x-zyngga-text>
                                    <x-zyngga-text variant="sm" color="neutral-500">{{ $complaint->transaksi->pegawai->name ?? 'Belum ditugaskan' }}</x-zyngga-text>
                                </div>
                                <div class="flex justify-between items-center">
                                    <x-zyngga-text variant="sm" color="neutral-900" weight="medium">Tanggal Komplain</x-zyngga-text>
                                    <x-zyngga-text variant="sm" color="neutral-500">{{ \Carbon\Carbon::parse($complaint->created_at)->translatedFormat('l, d M | H.i') }}</x-zyngga-text>
                                </div>
                            </div>
                        </x-zyngga-card>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-12 h-12 bg-[#F4F4F4] rounded-full flex items-center justify-center mx-auto mb-4">
                            <img src="{{ asset('assets/images/empty-order-icon.svg') }}" alt="Belum Ada Komplain" width="24" height="24">
                        </div>
                        <x-zyngga-text variant="lg" weight="medium" class="mb-2 text-neutral-900 tracking-tight">Belum Ada Komplain</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" class="px-6 leading-[1.6]">Kamu belum pernah mengajukan komplain apapun.</x-zyngga-text>
                    </div>
                @endforelse
                
                {{-- Kategori Kosong --}}
                @if(count($complaints) > 0)
                <div x-cloak 
                     x-show="document.querySelectorAll('a[x-show]:not([style*=\'display: none\'])').length === 0" 
                     class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <div class="w-12 h-12 bg-[#F4F4F4] rounded-full flex items-center justify-center mx-auto mb-4">
                        <img src="{{ asset('assets/images/empty-order-icon.svg') }}" alt="Kosong" width="24" height="24">
                    </div>
                    <x-zyngga-text variant="lg" weight="medium" class="mb-2 text-neutral-900 tracking-tight">Kategori Kosong</x-zyngga-text>
                    <x-zyngga-text variant="sm" color="neutral-500" class="mb-6 px-6 leading-[1.6]">Belum ada komplain yang sesuai dengan kategori ini.</x-zyngga-text>
                </div>
                @endif
            </div>
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => feather.replace(), 50);
        });
    </script>
</body>
</html>
