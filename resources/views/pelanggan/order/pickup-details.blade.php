<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Penjemputan – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; min-height: 100%; }
        #page-content { padding-bottom: 100px; }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data>
        <x-zyngga-toast />
        {{-- HEADER --}}
        <x-dashboard-header 
            title="Lengkapi Detail Alamat" 
            :backUrl="route('order.pickup', $service)" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- MAIN CONTENT --}}
        <main class="flex-1 flex flex-col relative mt-2">
            <div class="w-full max-w-5xl mx-auto px-5 space-y-3" id="page-content">
                
                {{-- Card 1: Lokasi Terpilih --}}
                <x-zyngga-card title="Lokasi Pickup" gap="py-0">
                    <x-slot:headerAction>
                        <x-zyngga-button 
                            type="a"
                            href="{{ route('order.pickup', ['service' => $service, 'lat' => $lat, 'lng' => $lng]) }}"
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
                            src="https://www.google.com/maps/embed/v1/search?key={{ config('services.google.maps_key') }}&q={{ $lat }},{{ $lng }}&zoom=18&maptype=roadmap"
                            class="w-full h-full border-0 pointer-events-none"
                        ></iframe>
                        <a href="{{ route('order.pickup', ['service' => $service, 'lat' => $lat, 'lng' => $lng]) }}" class="absolute inset-0 z-10 block cursor-pointer" aria-label="Edit lokasi"></a>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-blue-50">
                            <i data-feather="map-pin" class="w-5 h-5 text-zyngga-blue-300"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <x-zyngga-text variant="sm" weight="medium">
                                {{ explode(',', $address)[0] }}
                            </x-zyngga-text>
                            <x-zyngga-text variant="xs" color="neutral-500" class="overflow-hidden text-overflow-ellipsis line-clamp-2">
                                {{ $address }}
                            </x-zyngga-text>
                        </div>
                    </div>
                </x-zyngga-card>

                {{-- Form Details --}}
                <form action="{{ route('order.pickup.details.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="service" value="{{ $service }}">
                    <input type="hidden" name="latitude" value="{{ $lat }}">
                    <input type="hidden" name="longitude" value="{{ $lng }}">
                    <input type="hidden" name="address_detail" value="{{ $address }}">
                    @if(isset($existingAddress))
                        <input type="hidden" name="address_id" value="{{ $existingAddress->id }}">
                    @endif
                    
                    <x-zyngga-card gap="py-0">
                        <div class="flex flex-col gap-4">
                            <x-zyngga-input 
                                label="Label Alamat" 
                                name="label" 
                                placeholder="Contoh: Rumah, Kantor, Kost"
                                required 
                                value="{{ old('label', $existingAddress->label ?? '') }}"
                                :error="$errors->first('label')"
                                autofocus
                            >
                            </x-zyngga-input>

                            <x-zyngga-input 
                                label="Catatan (Opsional)" 
                                name="note" 
                                placeholder="Contoh: Rumah nomor 123"
                                value="{{ old('note', $existingAddress->note ?? '') }}"
                                :error="$errors->first('note')"
                            >
                            </x-zyngga-input>
                        </div>
                    </x-zyngga-card>

                    {{-- Card 3: Save to Profile (only for new address) --}}
                    @if(!isset($existingAddress))
                        <x-zyngga-card gap="py-0">
                            @php 
                                $addressCount = auth()->user() ? auth()->user()->addresses()->count() : 0;
                                $isLimitReached = $addressCount >= 3;
                            @endphp
                            <x-zyngga-switch 
                                label="Simpan ke Daftar Alamat"
                                description="Alamat ini akan tersimpan di profil Anda untuk pemesanan berikutnya"
                                name="save_address"
                                value="1"
                                :checked="!$isLimitReached"
                                :disabled="$isLimitReached"
                                @click="if({{ $isLimitReached ? 'true' : 'false' }}) window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Maaf, kamu sudah melebihi batas pembuatan alamat (maksimal 3).', type: 'warning' } }))"
                            />
                        </x-zyngga-card>
                    @endif

                    {{-- Sticky Footer --}}
                    <div class="fixed bottom-0 left-0 right-0 p-5 bg-white border-t border-zyngga-neutral-50 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] z-50 rounded-t-[16px]">
                        <div class="max-w-5xl mx-auto">
                            <x-zyngga-button 
                                type="submit" 
                                variant="primary" 
                                size="l" 
                                label="Atur Lokasi Pickup" 
                                class="w-full"
                            />
                        </div>
                    </div>
                </form>
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
