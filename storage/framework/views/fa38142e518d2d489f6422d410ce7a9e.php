<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Pemesanan Pickup – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; }

        /* ── scrollable main content ── */
        #page-content {
            padding-bottom: 100px; /* space for sticky footer */
        }

        /* ── section card ── */
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin: 8px 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #0F0F0F;
            margin: 0 0 20px 0;
        }

        /* ── service option ── */
        .service-option {
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            margin-bottom: 12px;
            height: 72px;
        }
        .service-option:last-child { margin-bottom: 0; }
        .service-option.selected {
            border-color: #1660C1;
            background: #e8eff9;
            height: 72px;
        }

        /* ── date button ── */
        .date-btn {
            flex: 1;
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            text-align: left;
            height: 72px;
        }
        .date-btn:hover {
            border-color: #1660C1;
            background: #e8eff9;
        }
        .date-btn.selected {
            border-color: #1660C1;
            background: #e8eff9;
        }

        /* ── time chip ── */
        .time-chip {
            flex: 1;
            height: 40px;
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #808080;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .time-chip:hover {
            border-color: #1660C1;
            color: #1660C1;
            background: #e8eff9;
        }
        .time-chip.selected {
            border-color: #1660C1;
            background: #e8eff9;
            color: #0F0F0F;
            font-weight: 600;
        }

        /* ── addon row ── */
        .addon-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 32px;
            cursor: pointer;
        }

        /* ── payment radio ── */
        .payment-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            cursor: pointer;
        }
        .radio-circle {
            width: 20px; height: 20px;
            border-radius: 50%;
            border: 2px solid #e8eff9;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: border-color 0.15s;
        }
        .radio-circle.checked {
            border-color: #1660C1;
            background: #1660C1;
        }
        .radio-circle.checked::after {
            content: '';
            width: 6px; height: 6px;
            background: white;
            border-radius: 50%;
        }

        /* ── sticky footer ── */
        #sticky-footer {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 425px;
            background: white;
            border-top: 1px solid #F4F4F4;
            border-radius: 16px 16px 0 0;
            padding: 16px 20px calc(16px + env(safe-area-inset-bottom, 0px));
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.08);
        }

        /* ── map thumbnail ── */
        #map-thumb {
            width: 100%;
            height: 144px;
            border-radius: 8px;
            overflow: hidden;
        }
        #map-thumb iframe { width:100%; height:100%; border:0; }
    </style>
</head>
<body>
<div class="w-full max-w-[425px] mx-auto min-h-screen flex flex-col">

    
    <div class="sticky top-0 z-40 bg-white rounded-b-2xl shadow-[0_4px_12px_rgba(0,0,0,0.04)] px-5 py-5 mb-[6px]">
        <div class="flex items-center gap-3 h-10">
            <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['type' => 'a','href' => ''.e(route('order.pickup', ['service' => $service])).'','variant' => 'neutral','size' => 'l','icon' => 'arrow-left','iconPosition' => 'only','ariaLabel' => 'Kembali']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'a','href' => ''.e(route('order.pickup', ['service' => $service])).'','variant' => 'neutral','size' => 'l','icon' => 'arrow-left','iconPosition' => 'only','aria-label' => 'Kembali']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'lg','weight' => 'semibold','as' => 'h1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'lg','weight' => 'semibold','as' => 'h1']); ?>Pemesanan Pickup <?php echo $__env->renderComponent(); ?>
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
    </div>

    
    <form method="POST" action="<?php echo e(route('order.confirm')); ?>" id="page-content">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="service"        value="<?php echo e($service); ?>">
        <input type="hidden" name="address"        value="<?php echo e($address); ?>">
        <input type="hidden" name="detail_address" value="<?php echo e($detailAddress); ?>">
        <input type="hidden" name="lat"            value="<?php echo e($lat); ?>">
        <input type="hidden" name="lng"            value="<?php echo e($lng); ?>">
        <input type="hidden" name="selected_service_id" id="selected_service_id" value="<?php echo e(strtolower($serviceLabel)); ?>">
        <input type="hidden" name="pickup_date"   id="pickup_date"   value="today">
        <input type="hidden" name="pickup_time"   id="pickup_time"   value="10:00">
        <input type="hidden" name="parfum"        id="parfum"        value="Lavender">

        
        <div class="section-card">
            <div class="flex items-center justify-between mb-4">
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold','as' => 'p']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold','as' => 'p']); ?>Lokasi Pickup <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['type' => 'a','href' => ''.e(route('order.pickup', ['service' => $service])).'','variant' => 'secondary','size' => 's','label' => 'Ubah']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'a','href' => ''.e(route('order.pickup', ['service' => $service])).'','variant' => 'secondary','size' => 's','label' => 'Ubah']); ?>
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
            </div>

            
            <div id="map-thumb" class="mb-4 relative">
                <iframe
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps/embed/v1/place?key=<?php echo e(config('services.google.maps_key')); ?>&q=<?php echo e(urlencode($address)); ?>&zoom=15&maptype=roadmap"
                    style="pointer-events:none;"
                ></iframe>
                
                <a
                    href="<?php echo e(route('order.pickup', ['service' => $service])); ?>"
                    class="absolute inset-0 z-10 block cursor-pointer"
                    aria-label="Edit lokasi pickup"
                    title="Edit lokasi pickup"
                ></a>
            </div>

            
            <div class="mb-3">
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'semibold','class' => 'mb-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'semibold','class' => 'mb-1']); ?>
                    <?php echo e($address); ?>

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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailAddress): ?>
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','color' => 'neutral-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','color' => 'neutral-500']); ?>
                        <?php echo e($detailAddress); ?>

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
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if (isset($component)) { $__componentOriginalad16c34feac0a642c6c836ff61214796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalad16c34feac0a642c6c836ff61214796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-input','data' => ['name' => 'detail_address_edit','placeholder' => 'Detail Lokasi','value' => ''.e($detailAddress).'','onkeydown' => 'if(event.key === \'Enter\') event.preventDefault();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'detail_address_edit','placeholder' => 'Detail Lokasi','value' => ''.e($detailAddress).'','onkeydown' => 'if(event.key === \'Enter\') event.preventDefault();']); ?>
                 <?php $__env->slot('iconRight', null, []); ?> 
                    <i data-feather="edit-2" class="w-4 h-4 text-[#808080]"></i>
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
        </div>

        
        <div class="section-card">
            <div class="flex items-center justify-between mb-4">
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold','as' => 'p']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold','as' => 'p']); ?>Jenis Layanan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                <button type="button" onclick="openServiceModal()">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','weight' => 'semibold','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','weight' => 'semibold','color' => 'primary']); ?>Lihat semua <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                </button>
            </div>

            <?php
                $allServices = [
                    ['id' => 'regular',  'name' => 'Regular',  'desc' => 'Layanan 3 hari (72 jam)',   'price' => 'Rp4.850/kg'],
                    ['id' => 'quick',    'name' => 'Quick',    'desc' => 'Layanan 2 hari (48 jam)',   'price' => 'Rp6.000/kg'],
                    ['id' => 'express',  'name' => 'Express',  'desc' => 'Layanan 1 hari (24 jam)',   'price' => 'Rp6.250/kg'],
                    ['id' => 'kilat',    'name' => 'Kilat',    'desc' => 'Layanan 5 jam',              'price' => 'Rp7.850/kg'],
                    ['id' => 'satuan',   'name' => 'Satuan',   'desc' => 'Selimut, Bed Cover, dll.',  'price' => 'Mulai Rp10.000'],
                ];
            ?>

            
            <div class="service-option selected" id="card-slot-0" onclick="cardSlotClick(0)">
                <div>
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'slot0-name','variant' => 'sm','weight' => 'semibold','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'slot0-name','variant' => 'sm','weight' => 'semibold','class' => 'm-0']); ?> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'slot0-desc','variant' => 'xs','color' => 'neutral-500','class' => 'm-0 mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'slot0-desc','variant' => 'xs','color' => 'neutral-500','class' => 'm-0 mt-1']); ?> <?php echo $__env->renderComponent(); ?>
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
                <div class="flex items-center gap-3">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'slot0-price','variant' => 'sm','weight' => 'semibold','class' => 'shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'slot0-price','variant' => 'sm','weight' => 'semibold','class' => 'shrink-0']); ?> <?php echo $__env->renderComponent(); ?>
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
            </div>

            
            <div class="service-option" id="card-slot-1" onclick="cardSlotClick(1)">
                <div>
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'slot1-name','variant' => 'sm','weight' => 'semibold','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'slot1-name','variant' => 'sm','weight' => 'semibold','class' => 'm-0']); ?> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'slot1-desc','variant' => 'xs','color' => 'neutral-500','class' => 'm-0 mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'slot1-desc','variant' => 'xs','color' => 'neutral-500','class' => 'm-0 mt-1']); ?> <?php echo $__env->renderComponent(); ?>
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
                <div class="flex items-center gap-3">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'slot1-price','variant' => 'sm','weight' => 'semibold','class' => 'shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'slot1-price','variant' => 'sm','weight' => 'semibold','class' => 'shrink-0']); ?> <?php echo $__env->renderComponent(); ?>
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
            </div>
        </div>

        
        <div
            id="service-modal"
            style="
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.10);
                z-index: 100;
                align-items: center;
                justify-content: center;
            "
            onclick="closeServiceModal(event)"
        >
            <div
                id="service-modal-box"
                style="
                    width: 385px;
                    max-width: calc(100vw - 40px);
                    background: white;
                    border-radius: 16px;
                    padding: 20px;
                    box-shadow: 0 8px 40px rgba(0,0,0,0.16);
                    max-height: 90vh;
                    overflow-y: auto;
                "
                onclick="event.stopPropagation()"
            >
                
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'lg','weight' => 'semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'lg','weight' => 'semibold']); ?>Jenis Layanan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                    <button
                        type="button"
                        onclick="closeServiceModal()"
                        style="
                            width:32px; height:32px;
                            border:none; background:none;
                            cursor:pointer;
                            display:flex; align-items:center; justify-content:center;
                            border-radius:50%;
                            transition:background 0.15s;
                        "
                        onmouseover="this.style.background='#F4F4F4'"
                        onmouseout="this.style.background='none'"
                        aria-label="Tutup"
                    >
                        <i data-feather="x" class="w-5 h-5 text-[#0F0F0F]"></i>
                    </button>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginala608449bff7b1b01c3e1242d7814b716 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala608449bff7b1b01c3e1242d7814b716 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-radio-row','data' => ['name' => 'modal_service','id' => 'modal-radio-'.e($svc['id']).'','value' => ''.e($svc['id']).'','label' => $svc['name'],'description' => $svc['desc'],'additional' => $svc['price'],'checked' => strtolower($serviceLabel) === $svc['id'],'onclick' => 'selectServiceFromModal(\''.e($svc['id']).'\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-radio-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'modal_service','id' => 'modal-radio-'.e($svc['id']).'','value' => ''.e($svc['id']).'','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($svc['name']),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($svc['desc']),'additional' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($svc['price']),'checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(strtolower($serviceLabel) === $svc['id']),'onclick' => 'selectServiceFromModal(\''.e($svc['id']).'\')']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala608449bff7b1b01c3e1242d7814b716)): ?>
<?php $attributes = $__attributesOriginala608449bff7b1b01c3e1242d7814b716; ?>
<?php unset($__attributesOriginala608449bff7b1b01c3e1242d7814b716); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala608449bff7b1b01c3e1242d7814b716)): ?>
<?php $component = $__componentOriginala608449bff7b1b01c3e1242d7814b716; ?>
<?php unset($__componentOriginala608449bff7b1b01c3e1242d7814b716); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < count($allServices) - 1): ?>
                        <div class="h-[1px] bg-zyngga-neutral-200 mx-1 my-1"></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="section-card space-y-3">
            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold','as' => 'p']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold','as' => 'p']); ?>Jadwal Pickup <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>

            
            <div class="flex gap-2">
                <?php
                    use Carbon\Carbon;
                    $today    = Carbon::now('Asia/Jakarta');
                    $tomorrow = $today->copy()->addDay();
                ?>
                <div class="date-btn selected" onclick="selectDate('today', this)">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'semibold','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'semibold','class' => 'm-0']); ?>Hari ini <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','color' => 'neutral-500','class' => 'm-0 mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','color' => 'neutral-500','class' => 'm-0 mt-0.5']); ?><?php echo e($today->isoFormat('D MMM YYYY')); ?> <?php echo $__env->renderComponent(); ?>
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
                <div class="date-btn" onclick="selectDate('tomorrow', this)">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'semibold','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'semibold','class' => 'm-0']); ?>Besok <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','color' => 'neutral-500','class' => 'm-0 mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','color' => 'neutral-500','class' => 'm-0 mt-0.5']); ?><?php echo e($tomorrow->isoFormat('D MMM YYYY')); ?> <?php echo $__env->renderComponent(); ?>
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
            </div>

            
            <div class="flex gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['10:00','12:00','16:00','18:00']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button
                        type="button"
                        class="time-chip <?php echo e($time === '10:00' ? 'selected' : ''); ?>"
                        onclick="selectTime('<?php echo e($time); ?>', this)"
                    >
                        <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'medium','class' => 'inherit-color']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'medium','class' => 'inherit-color']); ?><?php echo e($time); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="section-card">
            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold','as' => 'p','class' => 'mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold','as' => 'p','class' => 'mb-4']); ?>Tambahan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>

            <div class="addon-row" onclick="openParfumPicker()">
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'medium','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'medium','class' => 'm-0']); ?>Pilihan parfum <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                <div class="flex items-center gap-1">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'selected-parfum','variant' => 'sm','color' => 'neutral-500','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'selected-parfum','variant' => 'sm','color' => 'neutral-500','class' => 'm-0']); ?>Lavender <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                    <i data-feather="chevron-right" class="w-4 h-4 text-[#808080]"></i>
                </div>
            </div>

            <div class="h-[1px] bg-zyngga-neutral-200 mx-1 my-2"></div> 

            <div class="addon-row" onclick="openCatatan()">
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'medium','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'medium','class' => 'm-0']); ?>Catatan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                <div class="flex items-center gap-1">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'catatan-label','variant' => 'sm','color' => 'neutral-500','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'catatan-label','variant' => 'sm','color' => 'neutral-500','class' => 'm-0']); ?>Buat catatan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                    <i data-feather="chevron-right" class="w-4 h-4 text-[#808080]"></i>
                </div>
            </div>
        </div>

        
        <div class="section-card">
            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold','as' => 'p','class' => 'mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold','as' => 'p','class' => 'mb-4']); ?>Metode Pembayaran <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>

            <?php
                $payments = [
                    ['id' => 'cash', 'label' => 'Cash',  'desc' => 'Pembayaran dilakukan kepada kurir',
                     'feather' => 'dollar-sign', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"],
                    ['id' => 'qris', 'label' => 'QRIS',  'desc' => 'Pembayaran dilakukan melalui admin',
                     'feather' => 'grid', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"],
                    ['id' => 'transfer', 'label' => 'Transfer Bank', 'desc' => 'Pembayaran dilakukan melalui admin',
                     'feather' => 'home', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"],
                ];
            ?>

            <div class="flex flex-col">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginala608449bff7b1b01c3e1242d7814b716 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala608449bff7b1b01c3e1242d7814b716 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-radio-row','data' => ['name' => 'payment','id' => 'payment-'.e($pay['id']).'','value' => ''.e($pay['id']).'','label' => $pay['label'],'description' => $pay['desc'],'checked' => $pay['id'] === 'cash']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-radio-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'payment','id' => 'payment-'.e($pay['id']).'','value' => ''.e($pay['id']).'','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pay['label']),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pay['desc']),'checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pay['id'] === 'cash')]); ?>
                         <?php $__env->slot('icon', null, []); ?> 
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-yellow-50">
                                <i data-feather="<?php echo e($pay['feather']); ?>" class="w-5 h-5 text-zyngga-yellow-300"></i>
                            </div>
                         <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala608449bff7b1b01c3e1242d7814b716)): ?>
<?php $attributes = $__attributesOriginala608449bff7b1b01c3e1242d7814b716; ?>
<?php unset($__attributesOriginala608449bff7b1b01c3e1242d7814b716); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala608449bff7b1b01c3e1242d7814b716)): ?>
<?php $component = $__componentOriginala608449bff7b1b01c3e1242d7814b716; ?>
<?php unset($__componentOriginala608449bff7b1b01c3e1242d7814b716); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < count($payments) - 1): ?>
                        <div class="h-[1px] bg-zyngga-neutral-200 mx-1 my-2"></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </form>

    
    <div id="sticky-footer">
        <div>
            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'footer-service-label','variant' => 'base','weight' => 'semibold','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'footer-service-label','variant' => 'base','weight' => 'semibold','class' => 'm-0']); ?>Memuat... <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
            <div class="flex items-center gap-1.5 mt-1">
                <i data-feather="info" class="w-3.5 h-3.5 text-[#1660C1]"></i>
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['id' => 'footer-eta','variant' => 'xs','color' => 'primary','class' => 'm-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'footer-eta','variant' => 'xs','color' => 'primary','class' => 'm-0']); ?>Menghitung estimasi... <?php echo $__env->renderComponent(); ?>
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
        </div>

        <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['type' => 'submit','form' => 'page-content','variant' => 'primary','size' => 'l','label' => 'Buat Pesanan','class' => 'ml-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','form' => 'page-content','variant' => 'primary','size' => 'l','label' => 'Buat Pesanan','class' => 'ml-4']); ?>
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
    </div>

</div>


<div
    id="parfum-modal"
    style="
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.10);
        z-index: 100;
        align-items: center;
        justify-content: center;
    "
    onclick="closeParfumPicker(event)"
>
    <div
        id="parfum-modal-box"
        style="
            width: 385px;
            max-width: calc(100vw - 40px);
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.16);
            max-height: 90vh;
            overflow-y: auto;
        "
        onclick="event.stopPropagation()"
    >
        
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <p style="font-size:18px; font-weight:700; color:theme('colors.zyngga.neutral.500'); margin:0;">Pilih Parfum</p>
            <button
                type="button"
                onclick="closeParfumPicker()"
                style="
                    width:32px; height:32px;
                    border:none; background:none;
                    cursor:pointer;
                    display:flex; align-items:center; justify-content:center;
                    border-radius:50%;
                    transition:background 0.15s;
                "
                onmouseover="this.style.background='#F4F4F4'"
                onmouseout="this.style.background='none'"
                aria-label="Tutup"
            >
                <i data-feather="x" class="w-5 h-5 text-[#0F0F0F]"></i>
            </button>
        </div>

        
        <?php $parfums = ['Lavender','Rose','Jasmine','Fresh','Unscented']; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $parfums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginala608449bff7b1b01c3e1242d7814b716 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala608449bff7b1b01c3e1242d7814b716 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-radio-row','data' => ['name' => 'modal_parfum','id' => 'parfum-radio-row-'.e($p).'','value' => ''.e($p).'','label' => $p,'size' => 'M','checked' => $p === 'Lavender','onclick' => 'chooseParfum(\''.e($p).'\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-radio-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'modal_parfum','id' => 'parfum-radio-row-'.e($p).'','value' => ''.e($p).'','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p),'size' => 'M','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p === 'Lavender'),'onclick' => 'chooseParfum(\''.e($p).'\')']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala608449bff7b1b01c3e1242d7814b716)): ?>
<?php $attributes = $__attributesOriginala608449bff7b1b01c3e1242d7814b716; ?>
<?php unset($__attributesOriginala608449bff7b1b01c3e1242d7814b716); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala608449bff7b1b01c3e1242d7814b716)): ?>
<?php $component = $__componentOriginala608449bff7b1b01c3e1242d7814b716; ?>
<?php unset($__componentOriginala608449bff7b1b01c3e1242d7814b716); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < count($parfums) - 1): ?>
                <div class="h-[1px] bg-zyngga-neutral-200 mx-1 my-2"></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<script>
    // ── Service catalogue (mirrors PHP $allServices) ───────────
    const ALL_SERVICES = [
        { id: 'regular', name: 'Regular', desc: 'Layanan 3 hari (72 jam)',   price: 'Rp4.850/kg',      eta: 3 },
        { id: 'quick',   name: 'Quick',   desc: 'Layanan 2 hari (48 jam)',   price: 'Rp6.000/kg',      eta: 2 },
        { id: 'express', name: 'Express', desc: 'Layanan 1 hari (24 jam)',   price: 'Rp6.250/kg',      eta: 1 },
        { id: 'kilat',   name: 'Kilat',   desc: 'Layanan 5 jam',              price: 'Rp7.850/kg',      eta: 0 },
        { id: 'satuan',  name: 'Satuan',  desc: 'Selimut, Bed Cover, dll.',  price: 'Mulai Rp10.000',  eta: 3 },
    ];

    // The service currently selected (read from the server-rendered initial value)
    let selectedId = document.getElementById('selected_service_id').value || 'regular';

    // ── Render both card slots ─────────────────────────────────
    function renderCardSlots() {
        const selIdx   = ALL_SERVICES.findIndex(s => s.id === selectedId);
        const selected = ALL_SERVICES[selIdx] || ALL_SERVICES[0];

        // Slot 1: the next service after selected (wraps around, skipping selected)
        const altIdx   = (selIdx + 1) % ALL_SERVICES.length;
        const alt      = ALL_SERVICES[altIdx];

        // Populate slot 0 (selected)
        fillSlot(0, selected, true);

        // Populate slot 1 (alternative — not selected)
        fillSlot(1, alt, false);
    }

    function fillSlot(slot, svc, isSelected) {
        document.getElementById(`slot${slot}-name`).textContent  = svc.name;
        document.getElementById(`slot${slot}-desc`).textContent  = svc.desc;
        document.getElementById(`slot${slot}-price`).textContent = svc.price;

        const el    = document.getElementById(`card-slot-${slot}`);

        // Store which service this slot represents for click handler
        el.dataset.serviceId = svc.id;

        if (isSelected) {
            el.classList.add('selected');
        } else {
            el.classList.remove('selected');
        }
    }

    // ── Clicking a card slot selects that slot's service ───────
    function cardSlotClick(slot) {
        const el = document.getElementById(`card-slot-${slot}`);
        if (!el) return;
        applySelection(el.dataset.serviceId);
    }

    // ── Core: apply selection state everywhere ─────────────────
    function applySelection(id) {
        selectedId = id;

        // 1. Hidden input
        document.getElementById('selected_service_id').value = id;

        // 2. Footer label & ETA
        const svc = ALL_SERVICES.find(s => s.id === id) || ALL_SERVICES[0];
        
        // Label: Name (X hari) or Name (Hari yang sama)
        const daysLabel = svc.eta === 0 ? 'Hari yang sama' : `${svc.eta} hari`;
        document.getElementById('footer-service-label').textContent = `${svc.name} (${daysLabel})`;
        
        // ETA Date calculation based on Pickup Date
        const pickupDateVal = document.getElementById('pickup_date').value;
        const offset = (pickupDateVal === 'tomorrow') ? 1 : 0;

        const d = new Date();
        d.setDate(d.getDate() + svc.eta + offset);
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const formattedDate = `${dayNames[d.getDay()]}, ${d.getDate()} ${monthNames[d.getMonth()]}`;
        
        document.getElementById('footer-eta').textContent = `Estimasi Selesai: ${formattedDate}`;

        // 3. Re-render card slots: slot-0 = selected, slot-1 = alternative
        renderCardSlots();

        // 4. Sync modal radios
        document.querySelectorAll('input[name="modal_service"]').forEach(r => {
            r.checked = (r.value === id);
        });
    }

    // ── Called from in-card click (legacy wrapper, kept for safety)
    function selectService(id) { applySelection(id); }

    // ── Modal open / close ─────────────────────────────────────
    function openServiceModal() {
        document.getElementById('service-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeServiceModal() {
        document.getElementById('service-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // ── Called from modal row click ────────────────────────────
    function selectServiceFromModal(id) {
        applySelection(id);
        closeServiceModal();
    }

    // ── Init on page load ──────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        applySelection(selectedId);
    });

    // ── Date selection ─────────────────────────────────────────
    function selectDate(val, el) {
        document.querySelectorAll('.date-btn').forEach(e => e.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('pickup_date').value = val;
        
        // Refresh ETA based on new date
        applySelection(selectedId);
    }

    // ── Time selection ─────────────────────────────────────────
    function selectTime(val, el) {
        document.querySelectorAll('.time-chip').forEach(e => e.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('pickup_time').value = val;
    }



    // ── Parfum picker ──────────────────────────────────────────
    function openParfumPicker() {
        document.getElementById('parfum-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeParfumPicker() {
        document.getElementById('parfum-modal').style.display = 'none';
        document.body.style.overflow = '';
    }
    function chooseParfum(val) {
        // Update hidden input and label
        document.getElementById('selected-parfum').textContent = val;
        document.getElementById('parfum').value = val;

        // Sync radios
        document.querySelectorAll('input[name="modal_parfum"]').forEach(r => {
            r.checked = (r.value === val);
        });

        closeParfumPicker();
    }

    // ── Catatan ────────────────────────────────────────────────
    function openCatatan() {
        const note = prompt('Tulis catatan untuk kurir:');
        if (note) document.getElementById('catatan-label').textContent = note;
    }

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
<?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views/order/booking.blade.php ENDPATH**/ ?>