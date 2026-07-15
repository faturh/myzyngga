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
        .divider { border-top: 1px dashed #E5E7EB; margin: 12px 0; }
        .feather-icon { display: inline-block; width: 1em; height: 1em; vertical-align: middle; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        #sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #F4F4F4;
            border-radius: 20px 20px 0 0;
            padding: 16px 0 calc(16px + env(safe-area-inset-bottom, 0px));
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
        }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{
        content: `{{ addslashes($complaint->content) }}`
    }">
        {{-- HEADER --}}
        <x-dashboard-header 
            title="Detail Komplain" 
            :backUrl="route('profile.complaints')" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[100px]">
                
                {{-- CARD 1: ORDER INFO --}}
                <x-zyngga-card>
                    <div class="flex items-start justify-between mb-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-zyngga-yellow-50 flex items-center justify-center">
                                    <x-zyngga-service-icon service="{{ $complaint->transaksi->layananPrioritas->nama ?? '-' }}" class="w-4 h-4 text-zyngga-yellow-300" />
                                </div>
                                <x-zyngga-text variant="lg" weight="medium" color="neutral-900">{{ $complaint->transaksi->layananPrioritas->nama ?? '-' }}</x-zyngga-text>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $complaint->transaksi->nota ?? $complaint->transaksi_id }}</x-zyngga-text>
                                <button
                                    @click="navigator.clipboard.writeText('{{ $complaint->transaksi->nota ?? $complaint->transaksi_id }}');"
                                    class="text-zyngga-blue-300 hover:text-zyngga-blue-400 transition-colors"
                                >
                                    <i data-feather="copy" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <x-zyngga-status 
                            :type="($complaint->status === 'Menunggu Respon' || $complaint->status === 'pending') ? 'secondary' : ($complaint->status === 'Selesai' ? 'success' : 'secondary')" 
                            size="M" 
                            :label="$complaint->status === 'pending' ? 'Diproses' : $complaint->status" 
                        />
                    </div>

                    <div class="divider"></div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Customer</x-zyngga-text>
                            <x-zyngga-text variant="sm" color="neutral-500">{{ $complaint->pelanggan->telepon ?? '-' }}</x-zyngga-text>
                        </div>

                        <div class="divider"></div>

                        <div class="space-y-2.5">
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Kasir</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $complaint->transaksi->pegawai->name ?? 'Belum ditugaskan' }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Tanggal Komplain</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ \Carbon\Carbon::parse($complaint->created_at)->translatedFormat('l, d M | H.i') }}</x-zyngga-text>
                            </div>
                        </div>
                    </div>
                </x-zyngga-card>
                
                {{-- CARD 2: DETAIL MASALAH --}}
                <x-zyngga-card>
                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900" class="mb-3 block">Detail Masalah</x-zyngga-text>
                    
                    @foreach($complaint->issue_types ?? [] as $issue)
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-zyngga-blue-400 flex items-center justify-center">
                                <i data-feather="check" class="text-white" style="width:12px;height:12px"></i>
                            </div>
                            <x-zyngga-text variant="sm" color="neutral-900">{{ ucwords(str_replace('_', ' ', $issue)) }}</x-zyngga-text>
                        </div>
                    @endforeach

                    @if($complaint->content)
                    <div class="relative">
                        <textarea 
                            readonly
                            class="w-full bg-white border border-zyngga-neutral-200 rounded-xl px-4 py-3 text-sm text-zyngga-neutral-900 placeholder-zyngga-neutral-400 focus:outline-none resize-none min-h-[100px]"
                        >{{ $complaint->content }}</textarea>
                        <div class="absolute bottom-3 right-4">
                            <x-zyngga-text variant="xs" color="neutral-500" x-text="content.length + '/250'"></x-zyngga-text>
                        </div>
                    </div>
                    @endif
                </x-zyngga-card>

                {{-- CARD 3: GAMBAR BUKTI --}}
                <x-zyngga-card>
                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900" class="mb-3 block">Gambar Bukti</x-zyngga-text>
                    
                    @php
                        $images = is_string($complaint->image_path) ? json_decode($complaint->image_path, true) : $complaint->image_path;
                        if (!$images) $images = [];
                    @endphp

                    @if(count($images) > 0)
                        <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar snap-x">
                            @foreach($images as $img)
                                <a href="{{ $img }}" target="_blank" class="snap-start flex-shrink-0 block w-28 h-28 rounded-xl overflow-hidden relative border border-zyngga-neutral-200 bg-zyngga-neutral-50 hover:opacity-90 transition-opacity">
                                    <img src="{{ $img }}" alt="Bukti Komplain" class="w-full h-full object-cover" />
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-zyngga-neutral-50 flex items-center justify-center mb-2">
                                <i data-feather="image" class="w-5 h-5 text-zyngga-neutral-400"></i>
                            </div>
                            <x-zyngga-text variant="sm" color="neutral-500">Tidak ada Gambar</x-zyngga-text>
                        </div>
                    @endif
                </x-zyngga-card>

            </div>

            {{-- STICKY FOOTER --}}
            <div id="sticky-footer">
                <div class="max-w-5xl mx-auto px-5 flex items-center justify-center">
                    <x-zyngga-button 
                        type="a"
                        href="https://wa.me/6282125322500?text=Halo%20Admin%20Zyngga,%20saya%20ingin%20menanyakan%20status%20komplain%20saya%20untuk%20pesanan%20%23{{ $complaint->transaksi->nota ?? $complaint->transaksi_id }}"
                        target="_blank"
                        variant="secondary"
                        size="l"
                        label="Chat"
                        icon="message-circle"
                        iconPosition="left"
                        class="w-full !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                    />
                </div>
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
