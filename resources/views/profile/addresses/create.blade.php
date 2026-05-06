<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Alamat – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { height: 100%; margin: 0; overflow: hidden; }

        #app-wrapper {
            width: 100%;
            max-width: 768px;
            margin: 0 auto;
            height: 100dvh;
            position: relative;
            overflow: hidden;
            background: #FFFFFF;
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        #bottom-sheet {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -8px 32px rgba(0,0,0,0.15);
            padding: 24px 20px calc(16px + env(safe-area-inset-bottom, 16px));
            display: flex;
            flex-direction: column;
            gap: 16px;
            z-index: 10;
            max-height: 85vh;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #bottom-sheet::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 4px;
            background: #e8eff9;
            border-radius: 2px;
        }

        #pin-overlay {
            position: absolute;
            top: 0; left: 0; right: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        @keyframes pulse {
            0%   { transform: scale(0.8); opacity:1; }
            100% { transform: scale(1.8); opacity:0; }
        }
        
        #pin-icon.dragging .pulse {
            display: block;
        }
        .pulse {
            display: none;
            position:absolute;
            width:56px; height:56px;
            border-radius:50%;
            background:rgba(22,96,193,0.15);
            animation:pulse 1s infinite;
        }
    </style>
</head>
<body class="bg-zyngga-blue-50">
    <div id="app-wrapper">

        {{-- Map area --}}
        <div id="map"></div>

        {{-- Centered draggable pin --}}
        <div id="pin-overlay">
            <div id="pin-icon" style="transform: translateY(-16px); display:flex; flex-direction:column; align-items:center;">
                <div class="pulse"></div>
                <div style="width:32px; height:32px; background:#EF4444; border-radius:50%; border:3px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; position:relative;">
                    <div style="width:8px;height:8px;background:white;border-radius:50%;"></div>
                </div>
                <div style="width:4px;height:16px;background:#EF4444;border-radius:0 0 4px 4px;margin-top:-2px;"></div>
                <div style="width:12px;height:3px;background:rgba(0,0,0,0.15);border-radius:50%;filter:blur(1px);margin-top:1px;"></div>
            </div>
        </div>

        {{-- Bottom Sheet Form --}}
        <div id="bottom-sheet">
            {{-- Header --}}
            <div class="flex items-center gap-3">
                <x-zyngga-button 
                    type="a"
                    href="{{ route('addresses.index') }}"
                    variant="neutral"
                    size="l"
                    icon="arrow-left"
                    iconPosition="only"
                />
                <x-zyngga-text variant="lg" weight="bold" class="flex-1">Tambah Alamat Baru</x-zyngga-text>
            </div>

            {{-- Search bar --}}
            <div id="search-box" style="position:relative;">
                <x-zyngga-input 
                    name="search_input"
                    id="search-input"
                    placeholder="Cari lokasi atau alamat"
                    autocomplete="off"
                >
                    <x-slot:iconLeft>
                        <i data-feather="search" class="w-5 h-5 text-zyngga-neutral-400"></i>
                    </x-slot:iconLeft>
                </x-zyngga-input>
                <div id="search-results" style="display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 200; background: white; border: 1.5px solid #e8eff9; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); max-height: 220px; overflow-y: auto;"></div>
            </div>

            <form action="{{ route('addresses.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" id="hidden-lat" name="latitude">
                <input type="hidden" id="hidden-lng" name="longitude">
                
                {{-- Address Preview Card --}}
                <div class="bg-zyngga-blue-50/50 rounded-2xl p-4 border border-zyngga-blue-50 flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shrink-0 border border-zyngga-blue-50">
                        <i data-feather="map-pin" class="w-4 h-4 text-zyngga-blue-300"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <x-zyngga-text variant="xs" color="neutral-400" weight="bold" class="uppercase tracking-widest mb-1">Alamat Terpilih</x-zyngga-text>
                        <textarea 
                            id="address-preview" 
                            name="address_detail" 
                            readonly 
                            class="w-full bg-transparent border-none p-0 text-sm font-bold text-zyngga-neutral-500 focus:ring-0 resize-none h-12 leading-snug"
                        >Menentukan lokasi...</textarea>
                    </div>
                </div>

                <div class="space-y-4">
                    <x-zyngga-input 
                        label="Label Alamat" 
                        name="label" 
                        placeholder="Contoh: Rumah, Kantor, Kost"
                        required 
                        :error="$errors->first('label')"
                    />

                    <x-zyngga-input 
                        label="Catatan (Opsional)" 
                        name="note" 
                        placeholder="Contoh: Pagar warna biru, lantai 2"
                        :error="$errors->first('note')"
                    />
                </div>

                <div class="pt-2">
                    <x-zyngga-button 
                        type="submit" 
                        id="btn-submit"
                        variant="primary" 
                        size="l" 
                        label="Simpan Alamat" 
                        class="w-full"
                        disabled
                    />
                </div>
            </form>
        </div>
    </div>

    <script>
        let map, geocoder, autocomplete;
        let currentLat = -6.9809375;
        let currentLng = 107.6290625;
        let geocodeTimer = null;

        function syncPinOverlay() {
            const sheetEl = document.getElementById('bottom-sheet');
            const overlay = document.getElementById('pin-overlay');
            const sheetHeight = sheetEl.offsetHeight;
            const visibleHeight = window.innerHeight - sheetHeight;
            overlay.style.height = visibleHeight + 'px';
            if (map) map.setPadding({ bottom: sheetHeight });
        }

        function reverseGeocode(lat, lng) {
            document.getElementById('btn-submit').disabled = true;
            document.getElementById('address-preview').value = 'Menentukan lokasi...';

            geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const address = results[0].formatted_address;
                    document.getElementById('address-preview').value = address;
                    document.getElementById('hidden-lat').value = lat;
                    document.getElementById('hidden-lng').value = lng;
                    document.getElementById('btn-submit').disabled = false;
                } else {
                    document.getElementById('address-preview').value = 'Lokasi tidak ditemukan';
                }
            });
        }

        function initMap() {
            geocoder = new google.maps.Geocoder();
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: currentLat, lng: currentLng },
                zoom: 16,
                disableDefaultUI: true,
                gestureHandling: 'greedy',
                styles: [{ elementType: 'labels', stylers: [{ visibility: 'off' }] }],
            });

            google.maps.event.addListenerOnce(map, 'idle', () => {
                syncPinOverlay();
                reverseGeocode(currentLat, currentLng);
            });

            map.addListener('dragstart', () => document.getElementById('pin-icon').classList.add('dragging'));
            map.addListener('dragend', () => {
                document.getElementById('pin-icon').classList.remove('dragging');
                const center = map.getCenter();
                clearTimeout(geocodeTimer);
                geocodeTimer = setTimeout(() => reverseGeocode(center.lat(), center.lng()), 600);
            });

            setupSearch();
            window.addEventListener('resize', syncPinOverlay);
            const sheetObserver = new ResizeObserver(() => syncPinOverlay());
            sheetObserver.observe(document.getElementById('bottom-sheet'));
        }

        function setupSearch() {
            const input = document.getElementById('search-input');
            const resultsBox = document.getElementById('search-results');
            autocomplete = new google.maps.places.AutocompleteService();

            input.addEventListener('input', () => {
                const q = input.value.trim();
                if (!q) { resultsBox.style.display = 'none'; return; }

                autocomplete.getPlacePredictions({ input: q, componentRestrictions: { country: 'id' } }, (preds, status) => {
                    resultsBox.innerHTML = '';
                    if (status !== google.maps.places.PlacesServiceStatus.OK || !preds) {
                        resultsBox.style.display = 'none';
                        return;
                    }
                    resultsBox.style.display = 'block';
                    preds.forEach(pred => {
                        const item = document.createElement('div');
                        item.style.cssText = "padding:12px 16px; font-size:13px; color:#0F0F0F; cursor:pointer; border-bottom:1px solid #F4F4F4;";
                        item.innerHTML = `<strong>${pred.structured_formatting.main_text}</strong><br><span style="color:#808080;font-size:12px;">${pred.structured_formatting.secondary_text || ''}</span>`;
                        item.addEventListener('click', () => {
                            const placesService = new google.maps.places.PlacesService(map);
                            placesService.getDetails({ placeId: pred.place_id, fields: ['geometry'] }, (place, s) => {
                                if (s === google.maps.places.PlacesServiceStatus.OK) {
                                    const loc = place.geometry.location;
                                    map.panTo(loc);
                                    reverseGeocode(loc.lat(), loc.lng());
                                }
                            });
                            input.value = '';
                            resultsBox.style.display = 'none';
                        });
                        resultsBox.appendChild(item);
                    });
                });
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key', '') }}&libraries=places,geometry&callback=initMap" async defer></script>
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
