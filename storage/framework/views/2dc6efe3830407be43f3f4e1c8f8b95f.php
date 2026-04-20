<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Pilih Lokasi Pickup – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { height: 100%; margin: 0; overflow: hidden; }

        #app-wrapper {
            width: 100%;
            max-width: 425px;
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
            background: white;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -8px 32px rgba(0,0,0,0.15);
            padding: 24px 20px calc(16px + env(safe-area-inset-bottom, 16px));
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 10;
            max-height: 85vh;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            top: 0; left: 0; right: 0;
            /* height is calculated in JS to match map area only */
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
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
<body class="bg-white">

<div id="app-wrapper">

    
    <div id="map"></div>

    
    <div id="pin-overlay">
        <div id="pin-icon" style="transform: translateY(-16px); display:flex; flex-direction:column; align-items:center;">
            
            <div class="pulse" style="
                position:absolute;
                width:56px; height:56px;
                border-radius:50%;
                background:rgba(22,96,193,0.15);
                animation:pulse 1s infinite;
            "></div>
            
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
            
            <div style="width:4px;height:16px;background:#EF4444;border-radius:0 0 4px 4px;margin-top:-2px;"></div>
            
            <div style="width:12px;height:3px;background:rgba(0,0,0,0.15);border-radius:50%;filter:blur(1px);margin-top:1px;"></div>
        </div>
    </div>

    
    <div id="bottom-sheet">

        
        <div style="display:flex; align-items:center; height:40px; gap:8px;">
            <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['type' => 'a','href' => ''.e(route('dashboard')).'','variant' => 'neutral','size' => 'l','icon' => 'arrow-left','iconPosition' => 'only','ariaLabel' => 'Kembali']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'a','href' => ''.e(route('dashboard')).'','variant' => 'neutral','size' => 'l','icon' => 'arrow-left','iconPosition' => 'only','aria-label' => 'Kembali']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldfadf38ca1db54964c927e1f22e6f796)): ?>
<?php $attributes = $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796; ?>
<?php unset($__attributesOriginaldfadf38ca1db54964c927e1f22e6f796); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldfadf38ca1db54964c927e1f22e6f796)): ?>
<?php $component = $__componentOriginaldfadf38ca1db54964c927e1f22e6f796; ?>
<?php unset($__componentOriginaldfadf38ca1db54964c927e1f22e6f796); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'lg','weight' => 'semibold','as' => 'h1','class' => 'flex-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'lg','weight' => 'semibold','as' => 'h1','class' => 'flex-1']); ?>
                Pilih Lokasi Pickup
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
        </div>

        
        <div id="search-box" style="position:relative;">
            <?php if (isset($component)) { $__componentOriginalad16c34feac0a642c6c836ff61214796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad16c34feac0a642c6c836ff61214796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-input','data' => ['wrapperId' => 'search-input-wrapper','name' => 'search_input','id' => 'search-input','placeholder' => 'Cari lokasi pickup','autocomplete' => 'off']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wrapperId' => 'search-input-wrapper','name' => 'search_input','id' => 'search-input','placeholder' => 'Cari lokasi pickup','autocomplete' => 'off']); ?>
                 <?php $__env->slot('iconLeft', null, []); ?> 
                    <i data-feather="search" class="w-5 h-5 text-zyngga-neutral-400"></i>
                 <?php $__env->endSlot(); ?>
                 <?php $__env->slot('iconRight', null, []); ?> 
                    <button
                        type="button"
                        id="btn-clear-search"
                        onclick="clearSearch()"
                        style="display:none; background:none; border:none; cursor:pointer; padding:4px; color:#808080; line-height:1;"
                        aria-label="Hapus pencarian"
                    >
                        <i data-feather="x" class="w-4 h-4 text-zyngga-neutral-400"></i>
                    </button>
                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalad16c34feac0a642c6c836ff61214796)): ?>
<?php $attributes = $__attributesOriginalad16c34feac0a642c6c836ff61214796; ?>
<?php unset($__attributesOriginalad16c34feac0a642c6c836ff61214796); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalad16c34feac0a642c6c836ff61214796)): ?>
<?php $component = $__componentOriginalad16c34feac0a642c6c836ff61214796; ?>
<?php unset($__componentOriginalad16c34feac0a642c6c836ff61214796); ?>
<?php endif; ?>

            
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
                box-shadow: 0 8px 24px rgba(0,0,0,0.12);
                max-height: 220px;
                overflow-y: auto;
            "></div>
        </div>

        
        <div style="height:1px; background:#e8eff9; margin:0 4px;"></div>

        
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
            
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-blue-50">
                <i data-feather="map-pin" class="w-5 h-5 text-zyngga-blue-300"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'loc-name','variant' => 'sm','weight' => 'semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'loc-name','variant' => 'sm','weight' => 'semibold']); ?>
                    Menentukan lokasi...
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'loc-address','variant' => 'xs','color' => 'neutral-500','class' => 'overflow-hidden text-overflow-ellipsis line-clamp-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'loc-address','variant' => 'xs','color' => 'neutral-500','class' => 'overflow-hidden text-overflow-ellipsis line-clamp-2']); ?>
                    Geser pin di peta untuk memilih lokasi penjemputan
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
            </div>
            
            <div id="geocode-spinner" style="display:none; flex-shrink:0;">
                <i data-feather="refresh-cw" class="w-4 h-4 text-zyngga-blue-300 animate-spin"></i>
            </div>
        </div>
        
        
        <div id="distance-error" style="display:none; padding:12px 16px; background:#FEF2F2; border-radius:12px; border:1px solid #FEE2E2; align-items:center; gap:10px; margin-top:-4px;">
            <i data-feather="alert-circle" class="w-5 h-5 text-[#EF4444] shrink-0"></i>
            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','weight' => 'medium','color' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','weight' => 'medium','color' => 'danger']); ?>
                Maaf, lokasi Anda berada di luar jangkauan pickup kami.
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
        </div>

        
        <form method="POST" action="<?php echo e(route('order.pickup.store')); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="service" value="<?php echo e($service); ?>">
            <input type="hidden" id="hidden-address" name="address" value="">
            <input type="hidden" id="hidden-lat"  name="lat" value="">
            <input type="hidden" id="hidden-lng"  name="lng" value="">



            <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['type' => 'submit','id' => 'btn-submit','variant' => 'primary','size' => 'l','icon' => 'arrow-right','iconPosition' => 'right','label' => 'Atur Lokasi Pickup','class' => 'w-full','disabled' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','id' => 'btn-submit','variant' => 'primary','size' => 'l','icon' => 'arrow-right','iconPosition' => 'right','label' => 'Atur Lokasi Pickup','class' => 'w-full','disabled' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldfadf38ca1db54964c927e1f22e6f796)): ?>
<?php $attributes = $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796; ?>
<?php unset($__attributesOriginaldfadf38ca1db54964c927e1f22e6f796); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldfadf38ca1db54964c927e1f22e6f796)): ?>
<?php $component = $__componentOriginaldfadf38ca1db54964c927e1f22e6f796; ?>
<?php unset($__componentOriginaldfadf38ca1db54964c927e1f22e6f796); ?>
<?php endif; ?>
        </form>

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
</style>

<script>
    // ── State ────────────────────────────────────────────────────
    let map, geocoder, searchService, autocomplete;
    let currentLat  = -6.9809375;
    let currentLng  = 107.6290625;
    const LAUNDRY_LAT = -6.9809375;
    const LAUNDRY_LNG = 107.6290625;
    let geocodeTimer = null;
    let isEditing   = false;

    // ── Sync pin overlay height to visible map area ────────────────
    function syncPinOverlay() {
        const sheetEl  = document.getElementById('bottom-sheet');
        const overlay  = document.getElementById('pin-overlay');
        const sheetHeight = sheetEl.offsetHeight;
        
        // Calculate the visible height above the bottom sheet
        const visibleHeight = window.innerHeight - sheetHeight;
        
        overlay.style.height = visibleHeight + 'px';
        overlay.style.top    = '0px';

        // Tell Google Maps to offset its logical center so the pin stays accurate
        if (map) {
            map.setPadding({ bottom: sheetHeight });
        }
    }

    // ── Reverse geocode lat/lng → address string ─────────────────
    function reverseGeocode(lat, lng) {
        document.getElementById('geocode-spinner').style.display = 'block';
        document.getElementById('btn-submit').disabled = true;
        document.getElementById('btn-submit').style.opacity = '0.5';
        document.getElementById('loc-name').textContent    = 'Menentukan lokasi...';
        document.getElementById('loc-address').textContent = '';

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
        const center = map.getCenter();
        currentLat   = center.lat();
        currentLng   = center.lng();

        // Debounce: wait 600ms after drag ends before geocoding
        clearTimeout(geocodeTimer);
        geocodeTimer = setTimeout(() => reverseGeocode(currentLat, currentLng), 600);
    }

    // ── Search / Autocomplete ────────────────────────────────────
    function setupSearch() {
        const input      = document.getElementById('search-input');
        const wrapper    = document.getElementById('search-input-wrapper');
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
                    resultsBox.style.display = 'none';
                    return;
                }
                resultsBox.style.display = 'block';
                preds.forEach(pred => {
                    const item = document.createElement('div');
                    item.style.cssText = "padding:12px 16px; font-size:13px; color:#0F0F0F; cursor:pointer; border-bottom:1px solid #F4F4F4;";
                    item.innerHTML = `<strong>${pred.structured_formatting.main_text}</strong><br><span style="color:#808080;font-size:12px;">${pred.structured_formatting.secondary_text || ''}</span>`;
                    item.addEventListener('mouseover', () => item.style.background = "#e8eff9");
                    item.addEventListener('mouseout',  () => item.style.background = 'white');
                    item.addEventListener('click', () => {
                        // Resolve place → lat/lng
                        const placesService = new google.maps.places.PlacesService(map);
                        placesService.getDetails({ placeId: pred.place_id, fields: ['geometry', 'formatted_address', 'name'] }, (place, s) => {
                            if (s === google.maps.places.PlacesServiceStatus.OK) {
                                const loc = place.geometry.location;
                                map.panTo(loc);
                                map.setZoom(17);
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
            });
        });
    }

    // ── Clear search input ────────────────────────────────────────
    function clearSearch() {
        const input = document.getElementById('search-input');
        const resultsBox = document.getElementById('search-results');
        const clearBtn = document.getElementById('btn-clear-search');
        input.value = '';
        clearBtn.style.display = 'none';
        resultsBox.style.display = 'none';
        resultsBox.innerHTML = '';
        input.focus();
    }


    // ── Initialize Google Maps ────────────────────────────────────
    function initMap() {
        geocoder = new google.maps.Geocoder();

        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: currentLat, lng: currentLng },
            zoom: 16,
            disableDefaultUI: true,
            gestureHandling: 'greedy',
            styles: [
                // Hide ALL labels and text across every feature type
                { elementType: 'labels',                        stylers: [{ visibility: 'off' }] },
                { featureType: 'poi',       elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'transit',   elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'road',      elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'administrative', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'water',     elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'landscape', elementType: 'labels', stylers: [{ visibility: 'off' }] },
            ],
            clickableIcons: false,
        });

        // Sync pin overlay height after map renders
        google.maps.event.addListenerOnce(map, 'idle', () => {
            syncPinOverlay();
            // Initial reverse geocode for default location
            reverseGeocode(currentLat, currentLng);
        });

        // Track drag events on map (pin is always at center)
        map.addListener('dragstart', onMapDragStart);
        map.addListener('dragend',   onMapDragEnd);

        // Also update on zoom change
        map.addListener('zoom_changed', () => {
            clearTimeout(geocodeTimer);
            geocodeTimer = setTimeout(() => {
                const c = map.getCenter();
                reverseGeocode(c.lat(), c.lng());
            }, 800);
        });

        setupSearch();
        window.addEventListener('resize', syncPinOverlay);

        // Watch for height changes in the bottom sheet (e.g. address text wrapping)
        const sheetObserver = new ResizeObserver(() => syncPinOverlay());
        sheetObserver.observe(document.getElementById('bottom-sheet'));
    }
</script>


<script
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('services.google.maps_key', '')); ?>&libraries=places,geometry&callback=initMap"
    async defer
></script>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

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
</body>
</html>
<?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views/order/pickup-location.blade.php ENDPATH**/ ?>