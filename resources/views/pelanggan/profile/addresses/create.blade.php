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
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; background: #e8eff9; }
        #app-wrapper { position: relative; height: 100%; display: flex; flex-direction: column; }
        
        #map { flex: 1; width: 100%; }

        #bottom-sheet {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -8px 32px rgba(0,0,0,0.15);
            z-index: 10;
            max-height: 85vh;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #bottom-sheet-content {
            max-width: 1024px;
            margin: 0 auto;
            width: 100%;
            padding: 24px 20px calc(16px + env(safe-area-inset-bottom, 16px));
            display: flex;
            flex-direction: column;
            gap: 12px;
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
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            pointer-events: none;
            z-index: 5;
        }

        @keyframes pin-bounce {
            0% { transform: translate(-50%, calc(-100% + 4px)); }
            50% { transform: translate(-50%, calc(-100% - 4px)); }
            100% { transform: translate(-50%, calc(-100% + 4px)); }
        }
        .dragging #pin-icon {
            animation: pin-bounce 0.4s infinite ease-in-out;
        }
        .pulse {
            position: absolute;
            bottom: 4px;
            width: 12px;
            height: 4px;
            background: rgba(0,0,0,0.2);
            border-radius: 50%;
            transform: scale(1);
            transition: transform 0.2s ease;
        }
        .dragging .pulse { transform: scale(1.5); opacity: 0.5; }
    </style>
</head>
<body class="bg-[#e8eff9]">
    <div id="app-wrapper">
        {{-- Hidden fields for internal use --}}
        <input type="hidden" id="hidden-lat">
        <input type="hidden" id="hidden-lng">
        <input type="hidden" id="hidden-address">

        {{-- Map area --}}
        <div id="map"></div>

        {{-- Centered draggable pin --}}
        <div id="pin-overlay">
            <div id="pin-icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, calc(-100% + 4px)); display:flex; flex-direction:column; align-items:center;">
                <div class="pulse"></div>
                <div style="width:32px; height:32px; background:#EF4444; border-radius:50%; border:3px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; position:relative;">
                    <div style="width:8px;height:8px;background:white;border-radius:50%;"></div>
                </div>
                <div style="width:4px;height:16px;background:#EF4444;border-radius:0 0 4px 4px;margin-top:-2px;"></div>
                <div style="width:12px;height:3px;background:rgba(0,0,0,0.15);border-radius:50%;filter:blur(1px);margin-top:1px;"></div>
            </div>
        </div>

        {{-- Bottom Sheet --}}
        <div id="bottom-sheet">
            <div id="bottom-sheet-content">
                {{-- Header --}}
                <div style="display:flex; align-items:center; height:40px; gap:8px;">
                    <x-zyngga-button 
                        type="a"
                        href="{{ request()->has('address_id') ? route('addresses.edit', request()->query('address_id')) : route('profile') }}"
                        variant="neutral"
                        size="l"
                        icon="arrow-left"
                        iconPosition="only"
                        aria-label="Kembali"
                    />
                    <x-zyngga-text variant="lg" weight="medium" as="h1" class="flex-1">Pilih Lokasi Alamat</x-zyngga-text>
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
                        <x-slot:iconRight>
                            <button
                                type="button"
                                id="btn-clear-search"
                                onclick="clearSearch()"
                                style="display:none; background:none; border:none; cursor:pointer; padding:4px; color:#808080; line-height:1;"
                                aria-label="Hapus pencarian"
                            >
                                <i data-feather="x" class="w-4 h-4 text-zyngga-neutral-400"></i>
                            </button>
                        </x-slot:iconRight>
                    </x-zyngga-input>
                    <div id="search-results" style="display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 200; background: white; border: 1.5px solid #e8eff9; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); max-height: 220px; overflow-y: auto;"></div>
                </div>

                <div style="height:1px; background:#e8eff9; margin:0 4px;"></div>

                {{-- Address Card --}}
                <div id="address-card" style="border:1.5px solid #e8eff9; border-radius:12px; padding:14px 16px; display:flex; align-items:center; gap:12px;">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-blue-50">
                        <i data-feather="map-pin" class="w-5 h-5 text-zyngga-blue-300"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <x-zyngga-text id="loc-name" variant="sm" weight="medium">Menentukan lokasi...</x-zyngga-text>
                        <x-zyngga-text id="loc-address" variant="xs" color="neutral-500" class="overflow-hidden text-overflow-ellipsis line-clamp-2">
                            Geser pin di peta untuk memilih alamat
                        </x-zyngga-text>
                    </div>
                    <div id="geocode-spinner" style="display:none; flex-shrink:0;">
                        <i data-feather="refresh-cw" class="w-4 h-4 text-zyngga-blue-300 animate-spin"></i>
                    </div>
                </div>

                {{-- Error message for distance --}}
                <div id="distance-error" style="display:none; padding:12px 16px; background:#FEF2F2; border-radius:12px; border:1px solid #FEE2E2; align-items:center; gap:10px; margin-top:4px;">
                    <i data-feather="alert-circle" class="w-5 h-5 text-[#EF4444] shrink-0"></i>
                    <x-zyngga-text variant="xs" weight="regular" color="danger">
                        Maaf, lokasi ini berada di luar jangkauan layanan kami.
                    </x-zyngga-text>
                </div>

                <x-zyngga-button 
                    onclick="goToDetails()"
                    type="button" 
                    id="btn-next"
                    variant="primary" 
                    size="l" 
                    label="Pilih Lokasi ini" 
                    class="w-full"
                    disabled
                />
            </div>
        </div>
    </div>

    <script>
        function goToDetails() {
            const lat = document.getElementById('hidden-lat').value;
            const lng = document.getElementById('hidden-lng').value;
            const address = document.getElementById('hidden-address').value;
            const service = "{{ request()->query('service') }}";
            const addressId = "{{ request()->query('address_id') }}";
            
            if (lat && lng && address) {
                let url;
                if (addressId) {
                    url = `{{ url('addresses') }}/${addressId}/edit?lat=${lat}&lng=${lng}&address=${encodeURIComponent(address)}`;
                } else {
                    url = `{{ route('addresses.create.details') }}?lat=${lat}&lng=${lng}&address=${encodeURIComponent(address)}`;
                }
                
                if (service) url += (url.includes('?') ? '&' : '?') + `service=${service}`;
                window.location.href = url;
            }
        }

        let map, geocoder, autocomplete;
        let currentLat = {{ request()->query('lat', -6.9809375) }};
        let currentLng = {{ request()->query('lng', 107.6290625) }};
        const LAUNDRY_LAT = -6.9809375;
        const LAUNDRY_LNG = 107.6290625;
        let geocodeTimer = null;

        function syncPinOverlay() {
            // We now center the pin to the entire map container instead of the visible area.
            // No padding or height adjustments are needed.
        }

        function reverseGeocode(lat, lng) {
            const btnNext = document.getElementById('btn-next');
            if (!btnNext) return;

            document.getElementById('geocode-spinner').style.display = 'block';
            btnNext.disabled = true;
            document.getElementById('loc-name').textContent = 'Menentukan lokasi...';
            document.getElementById('loc-address').textContent = '';

            geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                document.getElementById('geocode-spinner').style.display = 'none';
                if (status === 'OK' && results[0]) {
                    const result = results[0];
                    let name = '';
                    const types = ['establishment', 'point_of_interest', 'premise', 'route', 'sublocality_level_1', 'locality'];
                    for (const type of types) {
                        const comp = result.address_components.find(c => c.types.includes(type));
                        if (comp) { name = comp.long_name; break; }
                    }
                    if (!name) name = result.formatted_address.split(',')[0];

                    const address = result.formatted_address;

                    // ── Distance Check ─────────────────────────────────────
                    const laundryLoc = new google.maps.LatLng(LAUNDRY_LAT, LAUNDRY_LNG);
                    const selectedLoc = new google.maps.LatLng(lat, lng);
                    const distance = google.maps.geometry.spherical.computeDistanceBetween(laundryLoc, selectedLoc);

                    if (distance > 1400) {
                        document.getElementById('distance-error').style.display = 'flex';
                        document.getElementById('loc-name').textContent = name;
                        document.getElementById('loc-address').textContent = address;
                        btnNext.disabled = true;
                        return;
                    }

                    document.getElementById('distance-error').style.display = 'none';
                    document.getElementById('loc-name').textContent = name;
                    document.getElementById('loc-address').textContent = address;
                    document.getElementById('hidden-address').value = address;
                    document.getElementById('hidden-lat').value = lat;
                    document.getElementById('hidden-lng').value = lng;
                    btnNext.disabled = false;
                } else {
                    document.getElementById('loc-name').textContent = 'Lokasi tidak ditemukan';
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

            map.addListener('dragstart', () => {
                document.getElementById('pin-icon').classList.add('dragging');
            });
            map.addListener('dragend', () => {
                document.getElementById('pin-icon').classList.remove('dragging');
                const center = map.getCenter();
                clearTimeout(geocodeTimer);
                geocodeTimer = setTimeout(() => reverseGeocode(center.lat(), center.lng()), 600);
            });

            setupSearch();
            window.addEventListener('resize', syncPinOverlay);
            const sheet = document.getElementById('bottom-sheet');
            if (sheet) {
                const sheetObserver = new ResizeObserver(() => syncPinOverlay());
                sheetObserver.observe(sheet);
            }
        }

        function setupSearch() {
            const input = document.getElementById('search-input');
            const resultsBox = document.getElementById('search-results');
            const clearBtn = document.getElementById('btn-clear-search');
            if (!input || !resultsBox) return;

            autocomplete = new google.maps.places.AutocompleteService();

            input.addEventListener('input', () => {
                const q = input.value.trim();
                if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';
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
                            if (clearBtn) clearBtn.style.display = 'none';
                            resultsBox.style.display = 'none';
                        });
                        resultsBox.appendChild(item);
                    });
                });
            });
        }

        function clearSearch() {
            const input = document.getElementById('search-input');
            const resultsBox = document.getElementById('search-results');
            const clearBtn = document.getElementById('btn-clear-search');
            if (!input) return;
            input.value = '';
            if (clearBtn) clearBtn.style.display = 'none';
            if (resultsBox) resultsBox.style.display = 'none';
            input.focus();
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key', '') }}&libraries=places,geometry&callback=initMap" async defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
