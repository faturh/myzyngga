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
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{}">
        {{-- ── HEADER ── --}}
        <x-dashboard-header 
            title="Riwayat Komplain" 
            :backUrl="route('profile')" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-10">
                @forelse($complaints as $complaint)
                    <a href="{{ route('profile.complaint.detail', $complaint->id) }}" class="block mb-4 transition-transform hover:scale-[1.01] active:scale-[0.99]">
                        <x-zyngga-card>
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900" class="block mb-1">
                                        Pesanan #{{ $complaint->transaksi->nota_layanan ?? $complaint->transaksi_id }}
                                    </x-zyngga-text>
                                    <x-zyngga-text variant="xs" color="neutral-500">
                                        {{ \Carbon\Carbon::parse($complaint->created_at)->translatedFormat('d M Y, H:i') }}
                                    </x-zyngga-text>
                                </div>
                                <x-zyngga-status 
                                    :type="$complaint->status === 'Menunggu Respon' ? 'warning' : ($complaint->status === 'Selesai' ? 'success' : 'secondary')" 
                                    size="M" 
                                    :label="$complaint->status" 
                                />
                            </div>
                            
                            <div class="bg-zyngga-neutral-50 p-3 rounded-xl mb-3">
                                <x-zyngga-text variant="xs" weight="medium" class="mb-1 block">Jenis Kendala:</x-zyngga-text>
                                <ul class="list-disc pl-4 mb-0">
                                    @foreach($complaint->issue_types ?? [] as $issue)
                                        <li><x-zyngga-text variant="xs" color="neutral-700">{{ ucwords(str_replace('_', ' ', $issue)) }}</x-zyngga-text></li>
                                    @endforeach
                                </ul>
                            </div>

                            <div>
                                <x-zyngga-text variant="xs" weight="medium" class="mb-1 block">Deskripsi:</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-700" class="leading-relaxed line-clamp-2">
                                    {{ $complaint->description }}
                                </x-zyngga-text>
                            </div>
                        </x-zyngga-card>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-16 h-16 bg-[#F4F4F4] rounded-full flex items-center justify-center mb-5">
                            <img src="{{ asset('assets/images/image.svg') }}" alt="Empty" width="32" height="32">
                        </div>
                        <x-zyngga-text variant="lg" weight="medium" class="mb-2 text-neutral-900 tracking-tight">Belum Ada Komplain</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" class="px-6 leading-[1.6]">Kamu belum pernah mengajukan komplain apapun.</x-zyngga-text>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
