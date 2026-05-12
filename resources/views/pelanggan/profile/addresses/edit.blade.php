<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Alamat – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; min-height: 100%; }
        #page-content { padding-bottom: 100px; }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data>
        {{-- HEADER --}}
        <x-dashboard-header 
            title="Edit Detail Alamat" 
            :backUrl="route('profile')" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />        
        
        {{-- MAIN CONTENT --}}
        <main class="flex-1 flex flex-col relative mt-2">
            <div class="w-full max-w-5xl mx-auto px-5 space-y-3" id="page-content">
                
                {{-- Card 1: Lokasi Terpilih --}}
                <x-zyngga-card title="Detail Lokasi Alamat" gap="py-0">
                    <x-slot:headerAction>
                        <x-zyngga-button 
                            type="a"
                            href="{!! route(isset($service) ? 'order.pickup' : 'addresses.create', ['service' => $service ?? null, 'lat' => $address->latitude, 'lng' => $address->longitude, 'address_id' => $address->id]) !!}"
                            variant="secondary"
                            size="s"
                            label="Ubah"
                        />
                    </x-slot:headerAction>

                    {{-- Map thumbnail --}}
                    <div id="map-thumb" class="mb-4 relative h-36 w-full rounded-xl overflow-hidden border border-zyngga-neutral-100">
                        <iframe
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed/v1/search?key={{ config('services.google.maps_key') }}&q={{ $address->latitude }},{{ $address->longitude }}&zoom=18&maptype=roadmap"
                            class="w-full h-full border-0 pointer-events-none"
                        ></iframe>
                        <a href="{!! route(isset($service) ? 'order.pickup' : 'addresses.create', ['service' => $service ?? null, 'lat' => $address->latitude, 'lng' => $address->longitude, 'address_id' => $address->id]) !!}" class="absolute inset-0 z-10 block cursor-pointer" aria-label="Edit lokasi"></a>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-blue-50">
                            <i data-feather="map-pin" class="w-5 h-5 text-zyngga-blue-300"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <x-zyngga-text variant="sm" weight="medium">
                                {{ explode(',', $address->address_detail)[0] }}
                            </x-zyngga-text>
                            <x-zyngga-text variant="xs" color="neutral-500" class="overflow-hidden text-overflow-ellipsis line-clamp-2">
                                {{ $address->address_detail }}
                            </x-zyngga-text>
                        </div>
                    </div>
                </x-zyngga-card>

                {{-- Card 2 & 3: Form Details --}}
                <form action="{{ route('addresses.update', $address) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    
                    @if(isset($service))
                        <input type="hidden" name="service" value="{{ $service }}">
                    @endif
                    <input type="hidden" name="latitude" value="{{ $address->latitude }}">
                    <input type="hidden" name="longitude" value="{{ $address->longitude }}">
                    <input type="hidden" name="address_detail" value="{{ $address->address_detail }}">
                    
                    <x-zyngga-card gap="py-0">
                        <div class="flex flex-col gap-4">
                            <x-zyngga-input 
                                label="Label Alamat" 
                                name="label" 
                                placeholder="Contoh: Rumah, Kantor, Kost"
                                :value="old('label', $address->label)"
                                required 
                                :error="$errors->first('label')"
                                autofocus
                            >
                            </x-zyngga-input>

                            <x-zyngga-input 
                                label="Detail Alamat (Opsional)" 
                                name="note" 
                                placeholder="Contoh: Rumah nomor 123"
                                :value="old('note', $address->note)"
                                :error="$errors->first('note')"
                            >
                            </x-zyngga-input>
                        </div>
                    </x-zyngga-card>

                    {{-- Card 3: Set Primary --}}
                    <x-zyngga-card gap="py-0">
                        <x-zyngga-switch 
                            label="Jadikan sebagai Alamat Utama"
                            description="Alamat ini akan otomatis terpilih saat melakukan pemesanan"
                            name="is_primary"
                            value="1"
                            :checked="$address->is_primary"
                            :disabled="$addressCount === 1"
                        />
                    </x-zyngga-card>

                    {{-- Sticky Footer --}}
                    <div class="fixed bottom-0 left-0 right-0 p-5 bg-white border-t border-zyngga-neutral-50 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] z-50 rounded-t-[16px]">
                        <div class="max-w-5xl mx-auto flex gap-4">
                            {{-- Delete button only if not from service flow --}}
                            @if(!isset($service))
                                <x-zyngga-button 
                                    type="button"
                                    @click="window.dispatchEvent(new CustomEvent('open-delete-modal'))"
                                    variant="secondary"
                                    size="l"
                                    label="Hapus Alamat"
                                    class="flex-1 !border-red-500 !text-red-500 hover:!bg-red-50 active:!bg-red-100"
                                />
                            @endif

                            <x-zyngga-button 
                                type="submit" 
                                variant="primary" 
                                size="l" 
                                label="{{ isset($service) ? 'Atur Lokasi Pickup' : 'Simpan' }}" 
                                class="flex-1"
                            />
                        </div>
                    </div>
                </form>

                @if(!isset($service))
                    <form id="delete-form" action="{{ route('addresses.destroy', $address) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>
        </main>

        {{-- ── MODAL: KONFIRMASI HAPUS ALAMAT ────────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="delete-modal-root" 
            openEvent="open-delete-modal"
            closeEvent="close-delete-modal"
        >
            <div class="flex flex-col items-center text-center">
                <div class="mb-6">
                    <img src="{{ asset('images/illustrations/cancel_order.png') }}" alt="Hapus Alamat" class="w-40 h-40 object-contain mx-auto">
                </div>

                <div class="space-y-2 mb-8 px-2">
                    <x-zyngga-text variant="lg" weight="medium" color="neutral-900" class="leading-snug !text-[#0F0F0F]">
                        Hapus Alamat Ini?
                    </x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="leading-normal !text-[#717171]">
                        Alamat ini akan dihapus dari daftar simpananmu. Tindakan ini tidak dapat dikembalikan.
                    </x-zyngga-text>
                </div>

                <div class="flex gap-3 w-full">
                    <x-zyngga-button type="button" @click="isOpen = false" size="m" variant="secondary" label="Batalkan" class="flex-1" />
                    <x-zyngga-button 
                        type="button" 
                        onclick="document.getElementById('delete-form').submit()" 
                        variant="secondary-danger" 
                        size="m"
                        label="Hapus" 
                        class="flex-1" 
                    />
                </div>
            </div>
        </x-zyngga-selection-modal>

    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
        document.addEventListener('livewire:load', function() {
            feather.replace();
        });
    </script>
</body>
</html>
