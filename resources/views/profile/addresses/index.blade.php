<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Alamat – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-zyngga-blue-50 min-h-screen">

    <div class="min-h-screen flex flex-col">
        <x-dashboard-header 
            title="Daftar Alamat" 
            :backUrl="route('profile')" 
            :maxWidth="'max-w-3xl'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="w-full max-w-3xl mx-auto py-6 px-5 flex-1 flex flex-col gap-6">
            
            {{-- Header Actions --}}
            <div class="flex items-center justify-between px-1">
                <div class="space-y-1">
                    <x-zyngga-text variant="lg" weight="bold">Alamat Tersimpan</x-zyngga-text>
                    <x-zyngga-text variant="xs" color="neutral-400">{{ $addresses->count() }}/5 Alamat digunakan</x-zyngga-text>
                </div>
                @if($addresses->count() < 5)
                    <x-zyngga-button 
                        type="a" 
                        href="{{ route('addresses.create') }}" 
                        variant="primary" 
                        size="s" 
                        icon="plus" 
                        label="Tambah Alamat" 
                    />
                @endif
            </div>

            @if($addresses->isEmpty())
                {{-- Empty State --}}
                <div class="flex-1 flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center shadow-sm mb-6 border border-zyngga-blue-50">
                        <i data-feather="map-pin" class="w-10 h-10 text-zyngga-blue-300"></i>
                    </div>
                    <x-zyngga-text variant="lg" weight="bold" class="mb-2">Belum Ada Alamat</x-zyngga-text>
                    <x-zyngga-text variant="sm" color="neutral-500" class="max-w-[240px] mb-8">Simpan alamat penjemputan kamu untuk memudahkan pemesanan laundry.</x-zyngga-text>
                    <x-zyngga-button 
                        type="a" 
                        href="{{ route('addresses.create') }}" 
                        variant="primary" 
                        size="m" 
                        label="Tambah Alamat Pertama" 
                    />
                </div>
            @else
                {{-- Address List --}}
                <div class="space-y-4">
                    @foreach($addresses as $address)
                        <x-zyngga-card class="!p-0 overflow-hidden">
                            <div class="p-5 flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zyngga-blue-50 flex items-center justify-center shrink-0">
                                    <i data-feather="{{ strtolower($address->label) === 'rumah' ? 'home' : (strtolower($address->label) === 'kantor' ? 'briefcase' : 'map-pin') }}" class="w-5 h-5 text-zyngga-blue-300"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <x-zyngga-text variant="sm" weight="bold">{{ $address->label }}</x-zyngga-text>
                                        @if($address->is_primary)
                                            <x-zyngga-status type="primary" size="S" label="Utama" />
                                        @endif
                                    </div>
                                    <x-zyngga-text variant="sm" weight="medium" class="text-neutral-800 block mb-1 leading-snug">{{ $address->address_detail }}</x-zyngga-text>
                                    @if($address->note)
                                        <x-zyngga-text variant="xs" color="neutral-400" class="italic">Catatan: {{ $address->note }}</x-zyngga-text>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Card Actions --}}
                            <div class="bg-gray-50/50 border-t border-gray-100 px-5 py-3 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('addresses.edit', $address) }}" class="flex items-center gap-1.5 text-zyngga-blue-300 hover:opacity-70 transition-opacity">
                                        <i data-feather="edit-2" class="w-3.5 h-3.5"></i>
                                        <x-zyngga-text variant="xs" weight="bold">Ubah</x-zyngga-text>
                                    </a>
                                    
                                    <form action="{{ route('addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center gap-1.5 text-red-500 hover:opacity-70 transition-opacity">
                                            <i data-feather="trash-2" class="w-3.5 h-3.5"></i>
                                            <x-zyngga-text variant="xs" weight="bold">Hapus</x-zyngga-text>
                                        </button>
                                    </form>
                                </div>

                                @if(!$address->is_primary)
                                    <form action="{{ route('addresses.primary', $address) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-white border border-zyngga-blue-100 px-3 py-1.5 rounded-full hover:bg-zyngga-blue-50 transition-colors">
                                            <x-zyngga-text variant="2xs" weight="bold" color="primary" class="uppercase">Set Utama</x-zyngga-text>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </x-zyngga-card>
                    @endforeach
                </div>
            @endif

        </main>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
