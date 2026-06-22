<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Komplain – Zyngga</title>
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
            title="Detail Komplain" 
            :backUrl="route('profile.complaints')" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-10">
                <x-zyngga-card class="mb-4">
                    <div class="flex justify-between items-start mb-5">
                        <div>
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900" class="block mb-1">
                                Pesanan #{{ $complaint->transaksi->nota_layanan ?? $complaint->transaksi_id }}
                            </x-zyngga-text>
                            <x-zyngga-text variant="xs" color="neutral-500">
                                Diajukan pada {{ \Carbon\Carbon::parse($complaint->created_at)->translatedFormat('d M Y, H:i') }}
                            </x-zyngga-text>
                        </div>
                        <x-zyngga-status 
                            :type="$complaint->status === 'Menunggu Respon' ? 'warning' : ($complaint->status === 'Selesai' ? 'success' : 'secondary')" 
                            size="M" 
                            :label="$complaint->status" 
                        />
                    </div>
                    
                    <div class="mb-5">
                        <x-zyngga-text variant="sm" weight="medium" class="mb-2 block">Jenis Kendala</x-zyngga-text>
                        <ul class="list-disc pl-5 mb-0 space-y-1">
                            @foreach($complaint->issue_types ?? [] as $issue)
                                <li><x-zyngga-text variant="sm" color="neutral-700">{{ ucwords(str_replace('_', ' ', $issue)) }}</x-zyngga-text></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mb-5">
                        <x-zyngga-text variant="sm" weight="medium" class="mb-2 block">Deskripsi Masalah</x-zyngga-text>
                        <div class="bg-zyngga-neutral-50 p-4 rounded-xl border border-zyngga-neutral-200">
                            <x-zyngga-text variant="sm" color="neutral-700" class="leading-relaxed">
                                {{ $complaint->description }}
                            </x-zyngga-text>
                        </div>
                    </div>

                    @if($complaint->image_path)
                    <div>
                        <x-zyngga-text variant="sm" weight="medium" class="mb-2 block">Gambar Bukti</x-zyngga-text>
                        <div class="rounded-xl overflow-hidden border border-zyngga-neutral-200">
                            <img src="{{ Str::startsWith($complaint->image_path, 'http') ? $complaint->image_path : asset('storage/' . $complaint->image_path) }}" alt="Gambar Bukti Komplain" class="w-full h-auto object-cover max-h-96">
                        </div>
                    </div>
                    @endif
                </x-zyngga-card>
                
                <x-zyngga-button 
                    type="a"
                    href="https://wa.me/+6281297673318?text=Halo%20Admin%20Zyngga,%20saya%20ingin%20menanyakan%20status%20komplain%20saya%20untuk%20pesanan%20%23{{ $complaint->transaksi->nota_layanan ?? $complaint->transaksi_id }}"
                    target="_blank"
                    variant="secondary"
                    size="l"
                    label="Hubungi Admin (WhatsApp)"
                    icon="message-circle"
                    iconPosition="left"
                    class="w-full mt-2"
                />
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
