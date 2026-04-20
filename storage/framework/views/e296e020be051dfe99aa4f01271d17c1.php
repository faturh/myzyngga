<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'value',
    'id' => null,
    'label' => '',
    'description' => '',
    'additional' => '',
    'checked' => false,
    'disabled' => false,
    'icon' => null,
    'service' => null,
    'size' => 'L', // L or M
]));

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

foreach (array_filter(([
    'name',
    'value',
    'id' => null,
    'label' => '',
    'description' => '',
    'additional' => '',
    'checked' => false,
    'disabled' => false,
    'icon' => null,
    'service' => null,
    'size' => 'L', // L or M
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $id = $id ?? $name . '-' . $value;
    // Base container classes
    $containerClasses = [
        'flex items-center justify-between px-1 cursor-pointer group select-none transition-all duration-200',
        $size === 'M' ? 'h-[32px]' : 'h-[56px]',
        $disabled ? 'opacity-50 cursor-not-allowed' : '',
    ];
?>

<label for="<?php echo e($id); ?>" class="<?php echo e(implode(' ', $containerClasses)); ?>">
    <div class="flex items-center gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($service): ?>
            <div class="flex-shrink-0 w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                <?php if (isset($component)) { $__componentOriginaled0e275348b21562ae1efc89e9464b6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled0e275348b21562ae1efc89e9464b6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-service-icon','data' => ['service' => ''.e($service).'','class' => 'w-3.5 h-3.5 text-zyngga-yellow-300']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-service-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['service' => ''.e($service).'','class' => 'w-3.5 h-3.5 text-zyngga-yellow-300']); ?>
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
        <?php elseif($icon): ?>
            <div class="flex-shrink-0">
                <?php echo e($icon); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <div class="flex flex-col">
            <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'sm','weight' => 'semibold','class' => 'leading-snug']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'sm','weight' => 'semibold','class' => 'leading-snug']); ?>
                <?php echo e($label); ?>

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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($description): ?>
                <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','color' => 'neutral-500','class' => 'leading-snug mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','color' => 'neutral-500','class' => 'leading-snug mt-0.5']); ?>
                    <?php echo e($description); ?>

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
    </div>

    <div class="flex items-center gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($additional): ?>
            <span class="text-[14px] text-[#0F0F0F] font-medium"><?php echo e($additional); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <div class="relative flex items-center justify-center h-5 w-5">
            <input 
                type="radio" 
                name="<?php echo e($name); ?>" 
                id="<?php echo e($id); ?>" 
                value="<?php echo e($value); ?>"
                <?php echo e($checked ? 'checked' : ''); ?>

                <?php echo e($disabled ? 'disabled' : ''); ?>

                class="sr-only peer"
                <?php echo e($attributes); ?>

            >
            
            <div class="w-5 h-5 rounded-full border-[1.5px] border-zyngga-blue-50 transition-all duration-200 
                        peer-checked:border-zyngga-blue-300 peer-checked:bg-zyngga-blue-300
                        group-hover:border-zyngga-blue-300">
            </div>
            
            <div class="absolute inset-0 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity duration-200 pointer-events-none">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
        </div>
    </div>
</label>
<?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views/components/zyngga-radio-row.blade.php ENDPATH**/ ?>