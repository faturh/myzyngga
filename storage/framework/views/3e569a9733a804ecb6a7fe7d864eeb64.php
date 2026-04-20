<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Home – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <style>
        * { font-family: 'DM Sans', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{}" class="bg-zyngga-blue-50 min-h-screen">
    
    <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginald37f1b809d8dad08d9600a37cd72bf8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald37f1b809d8dad08d9600a37cd72bf8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard-header','data' => ['name' => Auth::user()->name,'points' => 4]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Auth::user()->name),'points' => 4]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald37f1b809d8dad08d9600a37cd72bf8e)): ?>
<?php $attributes = $__attributesOriginald37f1b809d8dad08d9600a37cd72bf8e; ?>
<?php unset($__attributesOriginald37f1b809d8dad08d9600a37cd72bf8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald37f1b809d8dad08d9600a37cd72bf8e)): ?>
<?php $component = $__componentOriginald37f1b809d8dad08d9600a37cd72bf8e; ?>
<?php unset($__componentOriginald37f1b809d8dad08d9600a37cd72bf8e); ?>
<?php endif; ?>

    
    <div class="w-full max-w-[425px] mx-auto min-h-screen flex flex-col">

        
        <div class="px-5 py-[6px]">
            <div class="bg-white rounded-lg p-4 space-y-4">
                
                <div class="w-full aspect-[353/120] rounded-lg overflow-hidden relative">
                    <img src="/figma/figma_banner_hero.png" alt="Banner" class="w-full h-full object-cover">
                </div>

                
                <div class="flex items-center justify-between">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold']); ?>Pesan Sekarang <?php echo $__env->renderComponent(); ?>
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

                
                <div class="grid grid-cols-5">
                    <?php
                        $services = [
                            ['label' => 'Regular', 'icon' => 'refresh-cw'],
                            ['label' => 'Quick',   'icon' => 'clock'],
                            ['label' => 'Express', 'icon' => 'fast-forward'],
                            ['label' => 'Kilat',   'icon' => 'zap'],
                            ['label' => 'Satuan',   'icon' => 'zap'],
                        ];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a
                            href="<?php echo e(route('order.pickup', ['service' => strtolower($s['label'])])); ?>"
                            class="flex flex-col items-center gap-2 h-16 justify-center hover:opacity-80 transition-opacity"
                        >
                            <div class="w-9 h-9 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                <?php if (isset($component)) { $__componentOriginaled0e275348b21562ae1efc89e9464b6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled0e275348b21562ae1efc89e9464b6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-service-icon','data' => ['service' => ''.e($s['label']).'','class' => 'w-[18px] h-[18px] text-zyngga-yellow-300']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-service-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['service' => ''.e($s['label']).'','class' => 'w-[18px] h-[18px] text-zyngga-yellow-300']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled0e275348b21562ae1efc89e9464b6d)): ?>
<?php $attributes = $__attributesOriginaled0e275348b21562ae1efc89e9464b6d; ?>
<?php unset($__attributesOriginaled0e275348b21562ae1efc89e9464b6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled0e275348b21562ae1efc89e9464b6d)): ?>
<?php $component = $__componentOriginaled0e275348b21562ae1efc89e9464b6d; ?>
<?php unset($__componentOriginaled0e275348b21562ae1efc89e9464b6d); ?>
<?php endif; ?>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'medium']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'medium']); ?><?php echo e($s['label']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="px-5 py-[6px]">
            <div class="bg-white rounded-lg p-4 space-y-4">
                
                <div class="flex items-center justify-between h-8">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold']); ?>Pesanan Kamu <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
                    <a href="<?php echo e(route('order.history')); ?>">
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
                    </a>
                </div>

                
                <div class="space-y-[10px]">
                    <div class="flex items-start justify-between">
                        
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                    <?php if (isset($component)) { $__componentOriginaled0e275348b21562ae1efc89e9464b6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled0e275348b21562ae1efc89e9464b6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-service-icon','data' => ['service' => 'Express','class' => 'w-3.5 h-3.5 text-zyngga-yellow-300']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-service-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['service' => 'Express','class' => 'w-3.5 h-3.5 text-zyngga-yellow-300']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled0e275348b21562ae1efc89e9464b6d)): ?>
<?php $attributes = $__attributesOriginaled0e275348b21562ae1efc89e9464b6d; ?>
<?php unset($__attributesOriginaled0e275348b21562ae1efc89e9464b6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled0e275348b21562ae1efc89e9464b6d)): ?>
<?php $component = $__componentOriginaled0e275348b21562ae1efc89e9464b6d; ?>
<?php unset($__componentOriginaled0e275348b21562ae1efc89e9464b6d); ?>
<?php endif; ?>
                                </div>
                                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'lg','weight' => 'semibold','as' => 'p']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'lg','weight' => 'semibold','as' => 'p']); ?>Express <?php echo $__env->renderComponent(); ?>
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
                            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','color' => 'neutral-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','color' => 'neutral-500']); ?>Estimasi Selesai: Minggu, 10 Mar <?php echo $__env->renderComponent(); ?>
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
                        
                        <?php if (isset($component)) { $__componentOriginal0ae174fdb89750d1e415bf09b13d8fb9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ae174fdb89750d1e415bf09b13d8fb9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-status','data' => ['type' => 'secondary','size' => 'M','icon' => 'loader','label' => 'Diproses']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'secondary','size' => 'M','icon' => 'loader','label' => 'Diproses']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0ae174fdb89750d1e415bf09b13d8fb9)): ?>
<?php $attributes = $__attributesOriginal0ae174fdb89750d1e415bf09b13d8fb9; ?>
<?php unset($__attributesOriginal0ae174fdb89750d1e415bf09b13d8fb9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0ae174fdb89750d1e415bf09b13d8fb9)): ?>
<?php $component = $__componentOriginal0ae174fdb89750d1e415bf09b13d8fb9; ?>
<?php unset($__componentOriginal0ae174fdb89750d1e415bf09b13d8fb9); ?>
<?php endif; ?>
                    </div>

                    
                    <div class="flex items-center gap-4">
                        <div class="flex-1 h-1 bg-zyngga-blue-50 rounded-full overflow-hidden">
                            <div class="h-full bg-zyngga-blue-300 rounded-full" style="width:56%"></div>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold']); ?>56% <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['type' => 'a','href' => ''.e(route('order.detail')).'','variant' => 'secondary','size' => 'm','label' => 'Lihat Detail','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'a','href' => ''.e(route('order.detail')).'','variant' => 'secondary','size' => 'm','label' => 'Lihat Detail','class' => 'w-full']); ?>
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

        
        <div class="px-5 py-[6px]">
            <div class="w-full aspect-[385/168] rounded-lg overflow-hidden relative">
                <img src="/figma/figma_banner_promo.png" alt="Promo Banner" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-br from-[#1660C1]/60 to-transparent flex flex-col justify-between p-5">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold','color' => 'white','class' => 'max-w-[180px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold','color' => 'white','class' => 'max-w-[180px]']); ?>
                        Matahari Sembunyi?<br>Tenang, Ada Kami.
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
                    <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['variant' => 'primary','size' => 's','icon' => 'chevron-right','iconPosition' => 'right','label' => 'Pesan Sekarang','class' => 'bg-white !text-zyngga-blue-300 hover:bg-gray-100']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','size' => 's','icon' => 'chevron-right','iconPosition' => 'right','label' => 'Pesan Sekarang','class' => 'bg-white !text-zyngga-blue-300 hover:bg-gray-100']); ?>
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
        </div>

        
        <div class="px-5 py-[6px]">
            <div class="bg-white rounded-lg p-4 space-y-4">
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold','class' => 'h-8 flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold','class' => 'h-8 flex items-center']); ?>Pesanan Terakhir <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $attributes = $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff)): ?>
<?php $component = $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff; ?>
<?php unset($__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff); ?>
<?php endif; ?>

                <div class="flex items-start justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                                <?php if (isset($component)) { $__componentOriginaled0e275348b21562ae1efc89e9464b6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled0e275348b21562ae1efc89e9464b6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-service-icon','data' => ['service' => 'Quick','class' => 'w-3.5 h-3.5 text-zyngga-yellow-300']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-service-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['service' => 'Quick','class' => 'w-3.5 h-3.5 text-zyngga-yellow-300']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled0e275348b21562ae1efc89e9464b6d)): ?>
<?php $attributes = $__attributesOriginaled0e275348b21562ae1efc89e9464b6d; ?>
<?php unset($__attributesOriginaled0e275348b21562ae1efc89e9464b6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled0e275348b21562ae1efc89e9464b6d)): ?>
<?php $component = $__componentOriginaled0e275348b21562ae1efc89e9464b6d; ?>
<?php unset($__componentOriginaled0e275348b21562ae1efc89e9464b6d); ?>
<?php endif; ?>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'lg','weight' => 'semibold','as' => 'p']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'lg','weight' => 'semibold','as' => 'p']); ?>Quick <?php echo $__env->renderComponent(); ?>
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
                        <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','color' => 'neutral-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','color' => 'neutral-500']); ?>22 Agustus 2026 <?php echo $__env->renderComponent(); ?>
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

                    <?php if (isset($component)) { $__componentOriginal0ae174fdb89750d1e415bf09b13d8fb9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ae174fdb89750d1e415bf09b13d8fb9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-status','data' => ['type' => 'secondary','size' => 'M','icon' => 'check','label' => 'Selesai']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'secondary','size' => 'M','icon' => 'check','label' => 'Selesai']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0ae174fdb89750d1e415bf09b13d8fb9)): ?>
<?php $attributes = $__attributesOriginal0ae174fdb89750d1e415bf09b13d8fb9; ?>
<?php unset($__attributesOriginal0ae174fdb89750d1e415bf09b13d8fb9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0ae174fdb89750d1e415bf09b13d8fb9)): ?>
<?php $component = $__componentOriginal0ae174fdb89750d1e415bf09b13d8fb9; ?>
<?php unset($__componentOriginal0ae174fdb89750d1e415bf09b13d8fb9); ?>
<?php endif; ?>
                </div>

                <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['variant' => 'primary','size' => 'm','icon' => 'refresh-ccw','iconPosition' => 'left','label' => 'Ulangi Pesanan','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','size' => 'm','icon' => 'refresh-ccw','iconPosition' => 'left','label' => 'Ulangi Pesanan','class' => 'w-full']); ?>
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

        
        <div class="px-5 py-[6px]">
            <div class="bg-white rounded-lg p-4 space-y-4">
                <div class="h-8 flex items-center">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold']); ?>Alur Pemesanan <?php echo $__env->renderComponent(); ?>
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

                <div class="space-y-6">
                    <?php
                        $steps = [
                            ['n' => '1', 'title' => 'Pesan Penjemputan', 'desc' => 'Pilih layanan dan atur jadwal jemput lewat aplikasi.'],
                            ['n' => '2', 'title' => 'Proses Cuci', 'desc' => 'Kurir mengambil pakaian untuk dicuci.'],
                            ['n' => '3', 'title' => 'Pantau Status', 'desc' => 'Cek progres pengerjaan secara real-time.'],
                            ['n' => '4', 'title' => 'Antar & Bayar', 'desc' => 'Bayar dan pakaian bersih diantar kembali.'],
                        ];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full border border-zyngga-blue-300 flex items-center justify-center shrink-0">
                                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','weight' => 'semibold','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','weight' => 'semibold','color' => 'primary']); ?><?php echo e($step['n']); ?> <?php echo $__env->renderComponent(); ?>
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
                            <div class="space-y-1">
                                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'medium','class' => 'leading-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'medium','class' => 'leading-none']); ?><?php echo e($step['title']); ?> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','color' => 'neutral-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','color' => 'neutral-500']); ?><?php echo e($step['desc']); ?> <?php echo $__env->renderComponent(); ?>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="px-5 py-[6px]">
            <div class="bg-white rounded-lg p-4 space-y-4">
                <div class="h-8 flex items-center">
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold']); ?>Outlet Kami <?php echo $__env->renderComponent(); ?>
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

                <div class="space-y-4">
                    <?php
                        $outlets = [
                            ['name' => 'Zyngga Laundry Sukabirus', 'address' => 'Jl. Sukabirus No. 99'],
                            ['name' => 'Zyngga Laundry Sukapura', 'address' => 'Jl. Sukapura No. 97'],
                        ];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-4">
                            <div class="w-[168px] h-[110px] rounded-lg overflow-hidden shrink-0">
                                <img src="/figma/figma_outlet.png" alt="Outlet" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 flex flex-col justify-between h-[110px] py-1">
                                <div class="space-y-1">
                                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'medium','class' => 'leading-snug']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'medium','class' => 'leading-snug']); ?><?php echo e($outlet['name']); ?> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','color' => 'neutral-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','color' => 'neutral-500']); ?><?php echo e($outlet['address']); ?> <?php echo $__env->renderComponent(); ?>
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
                                <?php if (isset($component)) { $__componentOriginaldfadf38ca1db54964c927e1f22e6f796 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfadf38ca1db54964c927e1f22e6f796 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['variant' => 'secondary','size' => 's','label' => 'Cek Lokasi','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','size' => 's','label' => 'Cek Lokasi','class' => 'w-full']); ?>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <?php if (isset($component)) { $__componentOriginal3d04130edd21e9579a6a3e028370d627 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3d04130edd21e9579a6a3e028370d627 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3d04130edd21e9579a6a3e028370d627)): ?>
<?php $attributes = $__attributesOriginal3d04130edd21e9579a6a3e028370d627; ?>
<?php unset($__attributesOriginal3d04130edd21e9579a6a3e028370d627); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3d04130edd21e9579a6a3e028370d627)): ?>
<?php $component = $__componentOriginal3d04130edd21e9579a6a3e028370d627; ?>
<?php unset($__componentOriginal3d04130edd21e9579a6a3e028370d627); ?>
<?php endif; ?>

    </div>

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
<?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views/dashboard.blade.php ENDPATH**/ ?>