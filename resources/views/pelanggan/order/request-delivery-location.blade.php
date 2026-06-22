<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Lokasi Delivery – Zyngga</title>
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
            margin: 0 auto;
            height: 100dvh;
            position: relative;
            overflow: hidden;
            background: #FFFFFF;
        }

        /* Map fills the entire background */
        #map {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        /* Bottom sheet as a popup overlay from bottom */
        #bottom-sheet {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: white;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -8px 32px rgba(0,0,0,0.08);
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

        /* Drag handle for the bottom sheet popup */
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

        /* Center pin overlay */
        #pin-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
            z-index: 5;
        }

        /* Pulse ring while dragging */
        #pin-icon .pulse {
            display: none;
        }
        #pin-icon.dragging .pulse {
            display: block;
        }

        /* ── Search bar ── */
        .search-bar {
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            height: 48px;
            padding: 0 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .search-bar.focused {
            border-color: #1660C1;
            box-shadow: 0 0 0 3px rgba(22,96,193,0.10);
        }
        .search-bar input {
            flex: 1;
            font-size: 14px;
            color: #0F0F0F;
            border: none;
            outline: none;
            background: transparent;
            font-family: 'DM Sans', sans-serif;
        }
        .search-bar input::placeholder { color: #808080; }
    </style>
</head>
<body class="bg-zyngga-blue-50">
    <div class="min-h-screen flex flex-col">
        <div id="app-wrapper">


    {{-- ── Map area ───────────────────────────────────────────── --}}
    <div id="map"></div>

    {{-- Centered draggable pin overlay (visual cue) --}}
    <div id="pin-overlay">
        <div id="pin-icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, calc(-100% + 4px)); display:flex; flex-direction:column; align-items:center;">
            {{-- Pulsing ring (shown while dragging) --}}
            <div class="pulse" style="
                position:absolute;
                width:56px; height:56px;
                border-radius:50%;
                background:rgba(22,96,193,0.15);
                animation:pulse 1s infinite;
            "></div>
            {{-- Pin head --}}
            <div style="
                width:32px; height:32px;
                background:#EF4444;
                border-radius:50%;
                border:3px solid white;
                box-shadow:0 2px 8px rgba(0,0,0,0.3);
                display:flex; align-items:center; justify-content:center;
                position:relative;
            ">
                <div style="width:8px;height:8px;background:white;border-radius:50%;"></div>
            </div>
            {{-- Pin stem --}}
            <div style="width:4px;height:16px;background:#EF4444;border-radius:0 0 4px 4px;margin-top:-2px;"></div>
            {{-- Pin shadow --}}
            <div style="width:12px;height:3px;background:rgba(0,0,0,0.15);border-radius:50%;filter:blur(1px);margin-top:1px;"></div>
        </div>
    </div>

    <div id="bottom-sheet">
        {{-- Clickable drag handle area for expanding/collapsing --}}
        <div id="drag-handle" style="position: absolute; top:0; left:0; right:0; height: 32px; cursor:pointer; z-index:50;"></div>
        <div id="bottom-sheet-content">
            <div id="collapsible-top">
            {{-- Row 1: Back + Title only (no Ubah button) --}}
            <div style="display:flex; align-items:center; height:40px; gap:8px;">
                <x-zyngga-button 
                    type="a"
                    href="{{ route('order.detail', ['id' => $order['id']]) }}"
                    variant="neutral"
                    size="l"
                    icon="arrow-left"
                    iconPosition="only"
                    aria-label="Kembali"
                />

                <x-zyngga-text variant="lg" weight="medium" as="h1" class="flex-1">
                    Pilih Lokasi Delivery
                </x-zyngga-text>
            </div>

            {{-- Row 2: Search bar (always visible) --}}
            <div id="search-box" style="position:relative;">
                <x-zyngga-input 
                    wrapperId="search-input-wrapper"
                    name="search_input"
                    id="search-input"
                    placeholder="Cari lokasi delivery"
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

                {{-- Floating autocomplete popup — absolute so it overlays the layout below --}}
                <div id="search-results" style="
                    display: none;
                    position: absolute;
                    top: calc(100% + 6px);
                    left: 0;
                    right: 0;
                    z-index: 200;
                    background: white;
                    border: 1.5px solid #e8eff9;
                    border-radius: 12px;
                    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
                    max-height: 220px;
                    overflow-y: auto;
                "></div>
            </div>

            {{-- Row 2.5: Saved Addresses Quick Access --}}
            @if($savedAddresses->count() > 0)
                <div class="flex flex-col gap-2 mt-1">
                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                        @foreach($savedAddresses as $saved)
                            <x-zyngga-button 
                                type="button"
                                onclick="selectSavedAddress({{ $saved->latitude }}, {{ $saved->longitude }}, '{{ addslashes($saved->address_detail) }}', '{{ addslashes($saved->note ?? '') }}', {{ $saved->id }})"
                                variant="secondary"
                                size="m"
                                label="{{ $saved->label }}"
                                class="!border-zyngga-neutral-200 !text-zyngga-neutral-500 !bg-white shrink-0"
                            />
                        @endforeach
                    </div>
                </div>

                <style>
                    .no-scrollbar::-webkit-scrollbar { display: none; }
                    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                </style>
            @endif

            {{-- Divider between search bar and address card --}}
            <div style="height:1px; background:#e8eff9; margin:4px 4px 0;"></div>
            </div> <!-- End collapsible-top -->

            {{-- Address card — location icon vertically centered --}}
            <div
                id="address-card"
                style="
                    border:1.5px solid #e8eff9;
                    border-radius:12px;
                    padding:14px 16px;
                    display:flex;
                    align-items:center;
                    gap:12px;
                "
            >
                {{-- Icon centered vertically --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-blue-50">
                    <i data-feather="map-pin" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <x-zyngga-text id="loc-name" variant="sm" weight="medium">
                        Menentukan lokasi...
                    </x-zyngga-text>
                    <x-zyngga-text id="loc-address" variant="xs" color="neutral-500" class="overflow-hidden text-overflow-ellipsis line-clamp-2">
                        Geser pin di peta untuk memilih lokasi pengantaran
                    </x-zyngga-text>
                </div>
                {{-- Loading spinner --}}
                <div id="geocode-spinner" style="display:none; flex-shrink:0;">
                    <i data-feather="refresh-cw" class="w-4 h-4 text-zyngga-blue-300 animate-spin"></i>
                </div>
            </div>
            
            {{-- Error message for distance --}}
            <div id="distance-error" style="display:none; padding:12px 16px; background:#FEF2F2; border-radius:12px; border:1px solid #FEE2E2; align-items:center; gap:10px; margin-top:-4px;">
                <i data-feather="alert-circle" class="w-5 h-5 text-[#EF4444] shrink-0"></i>
                <x-zyngga-text variant="xs" weight="regular" color="danger">
                    Lokasi Anda berada di luar jangkauan delivery kami.
                </x-zyngga-text>
            </div>

            {{-- Row 3: Detail Lokasi input + submit --}}
            <div class="mt-auto">
                <x-zyngga-button 
                    type="button"
                    id="btn-submit"
                    onclick="redirectToDetails()"
                    variant="primary"
                    size="l"
                    icon="arrow-right"
                    iconPosition="right"
                    label="Atur Lokasi Delivery"
                    class="w-full"
                    disabled
                />
            </div>

            <script>
                let currentAddressId = "{{ request()->query('address_id') }}";
                let currentNote = '';
                let isMapMoving = false;

                function selectSavedAddress(lat, lng, address, note, id) {
                    currentLat = lat;
                    currentLng = lng;
                    currentAddressId = id;
                    currentNote = note; // Store note
                    isMapMoving = true; // Set to true to prevent syncPinOverlay from clearing it immediately
                    
                    if (map) {
                        map.panTo({lat: lat, lng: lng});
                        map.setZoom(18);
                    }

                    // Update UI elements
                    document.getElementById('hidden-lat').value = lat;
                    document.getElementById('hidden-lng').value = lng;
                    document.getElementById('hidden-address').value = address;
                    
                    syncPinOverlay();
                    reverseGeocode(lat, lng);
                    
                    // Reset moving flag after a short delay
                    setTimeout(() => { isMapMoving = false; }, 500);
                }

                function redirectToDetails() {
                    const address = document.getElementById('hidden-address').value;
                    const lat = document.getElementById('hidden-lat').value;
                    const lng = document.getElementById('hidden-lng').value;
                    
                    if (lat && lng && address) {
                        let url = `{{ route('order.request.delivery.confirm', ['id' => $order['id']]) }}?lat=${lat}&lng=${lng}&address=${encodeURIComponent(address)}&note=${encodeURIComponent(currentNote)}`;
                        window.location.href = url;
                    }
                }
            </script>

            <input type="hidden" id="hidden-address" value="">
            <input type="hidden" id="hidden-lat" value="">
            <input type="hidden" id="hidden-lng" value="">
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0%   { transform: scale(0.8); opacity:1; }
        100% { transform: scale(1.8); opacity:0; }
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    #collapsible-top {
        transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin 0.35s ease;
        max-height: 400px;
        opacity: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 12px;
    }
    #bottom-sheet.collapsed #collapsible-top {
        max-height: 0;
        opacity: 0;
        margin-bottom: 0;
    }
</style>

<script>
    // ── State ────────────────────────────────────────────────────
    let map, geocoder, searchService, autocomplete;
    let currentLat  = {{ request()->query('lat', -6.9809375) }};
    let currentLng  = {{ request()->query('lng', 107.6290625) }};
    const LAUNDRY_LAT = -6.9809375;
    const LAUNDRY_LNG = 107.6290625;
    let geocodeTimer = null;
    let isEditing   = false;

    // ── Sync pin overlay height to visible map area ────────────────
    function syncPinOverlay() {
        const sheet = document.getElementById('bottom-sheet');
        const rect = sheet.getBoundingClientRect();
        const sheetTop = rect.top; // The exact pixel where the bottom sheet starts
        
        const mapEl = document.getElementById('map');
        const pinOverlayEl = document.getElementById('pin-overlay');
        
        if (mapEl) {
            mapEl.style.height = Math.max(0, sheetTop + 80) + 'px';
            mapEl.style.bottom = 'auto';
        }
        if (pinOverlayEl) {
            pinOverlayEl.style.height = Math.max(0, sheetTop + 80) + 'px';
            pinOverlayEl.style.bottom = 'auto';
        }
        
        if (map) {
            map.setPadding({ bottom: 0, top: 0, left: 0, right: 0 });
            google.maps.event.trigger(map, 'resize');
        }
        
        const pinIcon = document.getElementById('pin-icon');
        if (pinIcon) {
            pinIcon.style.top = '50%';
        }
    }

    let isSheetExpanded = true;
    
    function animatePinSync() {
        let start = null;
        function step(timestamp) {
            if (!start) start = timestamp;
            syncPinOverlay();
            if (timestamp - start < 350) {
                requestAnimationFrame(step);
            } else {
                syncPinOverlay();
                if (map) {
                    map.panTo({lat: currentLat, lng: currentLng});
                }
            }
        }
        requestAnimationFrame(step);
    }

    function collapseSheet() {
        if (!isSheetExpanded) return;
        document.getElementById('bottom-sheet').classList.add('collapsed');
        isSheetExpanded = false;
        animatePinSync();
    }

    function expandSheet() {
        if (isSheetExpanded) return;
        document.getElementById('bottom-sheet').classList.remove('collapsed');
        isSheetExpanded = true;
        animatePinSync();
    }

    function toggleSheet() {
        if (isSheetExpanded) collapseSheet();
        else expandSheet();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const dragHandle = document.getElementById('drag-handle');
        let startY = 0;

        dragHandle.addEventListener('touchstart', e => { startY = e.touches[0].clientY; }, {passive: true});
        dragHandle.addEventListener('touchend', e => {
            let delta = e.changedTouches[0].clientY - startY;
            if (delta > 30) collapseSheet();
            else if (delta < -30) expandSheet();
            else toggleSheet(); // click
        });

        dragHandle.addEventListener('mousedown', e => { startY = e.clientY; });
        dragHandle.addEventListener('mouseup', e => {
            let delta = e.clientY - startY;
            if (delta > 30) collapseSheet();
            else if (delta < -30) expandSheet();
            else toggleSheet(); // click
        });
    });

    // ── Reverse geocode lat/lng → address string ─────────────────
    function reverseGeocode(lat, lng) {
        document.getElementById('geocode-spinner').style.display = 'block';
        document.getElementById('btn-submit').disabled = true;
        document.getElementById('btn-submit').style.opacity = '0.5';
        
        // Do not clear loc-name and loc-address to prevent layout height thrashing

        geocoder.geocode({ location: { lat, lng } }, (results, status) => {
            document.getElementById('geocode-spinner').style.display = 'none';

            if (status === 'OK' && results[0]) {
                const result = results[0];

                // Extract a short "place name" from address_components
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
                    document.getElementById('btn-submit').disabled     = true;
                    document.getElementById('btn-submit').style.opacity = '0.5';
                    
                    document.getElementById('loc-name').textContent    = name;
                    document.getElementById('loc-address').textContent = address;
                    return;
                }

                // Reset styles if valid
                document.getElementById('distance-error').style.display = 'none';
                document.getElementById('address-card').style.borderColor = "#e8eff9";
                document.getElementById('loc-name').textContent    = name;
                document.getElementById('loc-address').textContent = address;
                document.getElementById('hidden-address').value    = address;
                document.getElementById('hidden-lat').value        = lat;
                document.getElementById('hidden-lng').value        = lng;

                document.getElementById('btn-submit').disabled     = false;
                document.getElementById('btn-submit').style.opacity = '1';
            } else {
                document.getElementById('loc-name').textContent    = 'Lokasi tidak dikenali';
                document.getElementById('loc-address').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            }
        });
    }

    // ── Map drag handlers ────────────────────────────────────────
    function onMapDragStart() {
        document.getElementById('pin-icon').classList.add('dragging');
    }

    function onMapDragEnd() {
        document.getElementById('pin-icon').classList.remove('dragging');
    }

    function onMapCenterChanged() {
        if (isMapMoving) return;
        
        clearTimeout(geocodeTimer);
        geocodeTimer = setTimeout(() => {
            const center = map.getCenter();
            currentLat   = center.lat();
            currentLng   = center.lng();
            
            currentAddressId = null;
            currentNote = '';

            reverseGeocode(currentLat, currentLng);
        }, 500);
    }

    // ── Search / Autocomplete ────────────────────────────────────
    function setupSearch() {
        const input      = document.getElementById('search-input');
        const resultsBox = document.getElementById('search-results');
        const clearBtn   = document.getElementById('btn-clear-search');

        autocomplete = new google.maps.places.AutocompleteService();

        input.addEventListener('input', () => {
            const q = input.value.trim();

            // Show/hide clear button
            clearBtn.style.display = q ? 'block' : 'none';

            if (!q) { resultsBox.style.display = 'none'; return; }

            autocomplete.getPlacePredictions({ input: q, componentRestrictions: { country: 'id' } }, (preds, status) => {
                resultsBox.innerHTML = '';
                if (status !== google.maps.places.PlacesServiceStatus.OK || !preds) {
                    const empty = document.createElement('div');
                    empty.style.padding = '12px 16px';
                    empty.innerHTML = `<x-zyngga-text variant="xs" color="neutral-400">Tidak menemukan hasil</x-zyngga-text>`;
                    resultsBox.appendChild(empty);
                    resultsBox.style.display = 'block';
                    return;
                }

                preds.forEach(pred => {
                    const item = document.createElement('div');
                    item.style.padding = '12px 16px';
                    item.style.cursor  = 'pointer';
                    item.style.borderBottom = '1px solid #f8fafc';
                    item.innerHTML = `
                        <div style="display:flex; gap:10px;">
                            <i data-feather="map-pin" style="width:16px; height:16px; color:#94a3b8; margin-top:2px;"></i>
                            <div style="flex:1;">
                                <div style="font-size:13px; font-weight:500; color:#1e293b; line-height:1.2;">${pred.structured_formatting.main_text}</div>
                                <div style="font-size:11px; color:#64748b; margin-top:2px;">${pred.structured_formatting.secondary_text}</div>
                            </div>
                        </div>
                    `;
                    item.addEventListener('mouseover', () => item.style.background = '#f1f5f9');
                    item.addEventListener('mouseout',  () => item.style.background = 'white');
                    item.addEventListener('click', () => {
                        // Resolve place → lat/lng
                        const placesService = new google.maps.places.PlacesService(map);
                        placesService.getDetails({ placeId: pred.place_id, fields: ['geometry', 'formatted_address', 'name'] }, (place, s) => {
                            if (s === google.maps.places.PlacesServiceStatus.OK) {
                                const loc = place.geometry.location;
                                map.panTo(loc);
                                map.setZoom(17);
                                
                                // Clear saved address context when searching new place
                                currentAddressId = null;
                                currentNote = '';
                                reverseGeocode(loc.lat(), loc.lng());
                            }
                        });
                        // Close dropdown & clear search input
                        input.value = '';
                        clearBtn.style.display = 'none';
                        resultsBox.style.display = 'none';
                        resultsBox.innerHTML = '';
                    });
                    resultsBox.appendChild(item);
                });
                resultsBox.style.display = 'block';
                feather.replace();
            });
        });
    }

    // ── Address Selection ─────────────────────────────────────
    function selectSavedAddress(lat, lng, address, note, id) {
        currentLat = lat;
        currentLng = lng;
        currentAddressId = id;
        currentNote = note;
        isMapMoving = true; 
        
        if (map) {
            map.panTo({lat: lat, lng: lng});
            map.setZoom(18);
        }

        // Update UI elements
        document.getElementById('hidden-lat').value = lat;
        document.getElementById('hidden-lng').value = lng;
        document.getElementById('hidden-address').value = address;
        
        // Update Address Card directly
        const namePart = address.split(',')[0];
        document.getElementById('loc-name').textContent = namePart;
        document.getElementById('loc-address').textContent = address;
        document.getElementById('btn-submit').disabled = false;
        document.getElementById('btn-submit').style.opacity = '1';
        document.getElementById('distance-error').style.display = 'none';
        
        syncPinOverlay();
        
        setTimeout(() => { isMapMoving = false; }, 800);
    }



    // ── Map Init ──────────────────────────────────────────────────
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: currentLat, lng: currentLng },
            zoom: 16,
            disableDefaultUI: true,
            gestureHandling: 'greedy',
            styles: [
                { featureType: 'poi',            stylers: [{ visibility: 'off' }] },
                { featureType: 'transit',        stylers: [{ visibility: 'off' }] },
                { featureType: 'administrative', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'water',     elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'landscape', elementType: 'labels', stylers: [{ visibility: 'off' }] },
            ],
            clickableIcons: false,
        });

        geocoder      = new google.maps.Geocoder();
        searchService = new google.maps.places.PlacesService(map);

        // Sync pin overlay height after map renders
        google.maps.event.addListenerOnce(map, 'idle', () => {
            syncPinOverlay();
            reverseGeocode(currentLat, currentLng);
        });

        // Track drag events on map (pin is always at center)
        map.addListener('dragstart', onMapDragStart);
        map.addListener('dragend',   onMapDragEnd);
        map.addListener('center_changed', onMapCenterChanged);

        setupSearch();
        window.addEventListener('resize', syncPinOverlay);

        // Watch for height changes in the bottom sheet (e.g. address text wrapping)
        const sheetObserver = new ResizeObserver(() => syncPinOverlay());
        sheetObserver.observe(document.getElementById('bottom-sheet'));
    }
</script>

{{-- Load Maps JS API with Places library --}}
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key', '') }}&libraries=places,geometry&callback=initMap"
    async defer
></script>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
            setTimeout(() => feather.replace(), 500);
        });
        document.addEventListener('livewire:load', function () {
            feather.replace();
        });
        document.addEventListener('livewire:navigated', function () {
            feather.replace();
        });
    </script>
    </div>
</div>
</div>

</body>
</html>
