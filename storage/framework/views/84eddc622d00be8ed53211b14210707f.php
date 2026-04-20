
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['open' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['open' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div
    x-data="{ open: false }"
    @open-sidebar.window="open = true"
    @close-sidebar.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[9999]"
>
    
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"
    ></div>

    
    <aside
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="absolute inset-y-0 left-0 w-[280px] bg-white shadow-2xl flex flex-col"
        style="font-family:'DM Sans',sans-serif;"
    >
        
        <div class="flex items-center justify-between px-5 h-[56px] border-b border-zyngga-neutral-200 shrink-0">
            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'base','weight' => 'semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'base','weight' => 'semibold']); ?>Menu Utama <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-button','data' => ['variant' => 'neutral','size' => 'l','icon' => 'chevron-left','iconPosition' => 'only','@click' => 'open = false','ariaLabel' => 'Tutup menu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'neutral','size' => 'l','icon' => 'chevron-left','iconPosition' => 'only','@click' => 'open = false','aria-label' => 'Tutup menu']); ?>
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

        
        <nav class="flex flex-col gap-0 pt-4 px-5">
            <?php
                $menuItems = [
                    [
                        'label'  => 'Home',
                        'icon'   => 'home',
                        'href'   => Auth::check() ? route('dashboard') : route('landing'),
                        'active' => Auth::check() ? request()->routeIs('dashboard') : request()->routeIs('landing'),
                    ],
                    [
                        'label'  => 'Cek Pesanan',
                        'icon'   => 'package',
                        'href'   => route('order.history'),
                        'active' => request()->routeIs('order.history'),
                    ],
                    [
                        'label'  => 'Layanan',
                        'icon'   => 'layers',
                        'href'   => '#',
                        'active' => false,
                    ],
                    [
                        'label'  => 'Langganan',
                        'icon'   => 'calendar',
                        'href'   => '#',
                        'active' => false,
                    ],
                    [
                        'label'  => 'Profil',
                        'icon'   => 'user',
                        'href'   => route('profile'),
                        'active' => request()->routeIs('profile'),
                    ],
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a
                    href="<?php echo e($item['href']); ?>"
                    @click="open = false"
                    class="flex items-center gap-3 h-[52px] rounded-xl px-1 transition-colors
                        <?php echo e($item['active']
                            ? 'bg-zyngga-blue-50'
                            : 'hover:bg-zyngga-neutral-200'); ?>"
                >
                    
                    <div class="w-[52px] h-[40px] flex items-center justify-center shrink-0">
                        <div class="w-10 h-10 rounded-lg bg-zyngga-blue-50 flex items-center justify-center">
                            <i data-feather="<?php echo e($item['icon']); ?>" class="w-5 h-5 text-zyngga-blue-300"></i>
                        </div>
                    </div>
                    <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'semibold','class' => 'leading-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'semibold','class' => 'leading-none']); ?>
                        <?php echo e($item['label']); ?>

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
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>

        
        <div class="mt-auto px-5 pb-8">
            <div class="border-t border-zyngga-neutral-200 pt-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="flex items-center gap-3 w-full h-[48px] rounded-xl px-1 hover:bg-red-50 text-red-500 transition-colors">
                            <div class="w-[52px] h-[40px] flex items-center justify-center shrink-0">
                                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                                    <i data-feather="log-out" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'semibold','class' => 'leading-none text-inherit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'semibold','class' => 'leading-none text-inherit']); ?>Keluar <?php echo $__env->renderComponent(); ?>
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
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="flex items-center gap-3 w-full h-[48px] rounded-xl px-1 hover:bg-zyngga-blue-50 text-zyngga-blue-300 transition-colors">
                        <div class="w-[52px] h-[40px] flex items-center justify-center shrink-0">
                            <div class="w-10 h-10 rounded-lg bg-zyngga-blue-50 flex items-center justify-center">
                                <i data-feather="log-in" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'semibold','class' => 'leading-none text-inherit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'semibold','class' => 'leading-none text-inherit']); ?>Masuk <?php echo $__env->renderComponent(); ?>
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
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </aside>
</div>
<?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views/components/sidebar.blade.php ENDPATH**/ ?>