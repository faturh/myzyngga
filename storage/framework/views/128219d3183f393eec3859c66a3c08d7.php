<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'size' => 'M', // M or S
    'error' => null,
    'disabled' => false,
    'iconLeft' => null,
    'iconRight' => null,
    'wrapperId' => null,
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
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'size' => 'M', // M or S
    'error' => null,
    'disabled' => false,
    'iconLeft' => null,
    'iconRight' => null,
    'wrapperId' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $sizeClasses = [
        'M' => 'h-[48px] px-4',
        'S' => 'h-[40px] px-3',
    ];
    $inputSizeClasses = [
        'M' => 'text-[14px]',
        'S' => 'text-[13px]',
    ];
    
    $baseBorderColor = 'border-zyngga-blue-50';
    $focusClasses = 'focus-within:ring-0 focus-within:ring-transparent';
    $errorBorderColor = 'border-red-500';
    $disabledBg = 'bg-zyngga-neutral-100';
    
    $wrapperClasses = [
        'flex items-center gap-2 border-[1.5px] rounded-[12px] transition-all duration-200 group hover:border-zyngga-blue-300',
        $sizeClasses[$size] ?? $sizeClasses['M'],
        $error ? $errorBorderColor : ($disabled ? 'border-gray-200' : $baseBorderColor . ' ' . $focusClasses),
        $disabled ? $disabledBg : 'bg-white',
    ];
    $wrapperClassStr = implode(' ', $wrapperClasses);
?>

<div <?php echo e($attributes->only('class')); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
        <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['as' => 'label','variant' => 'sm','weight' => 'medium','color' => 'neutral-500','class' => 'block mb-2','for' => ''.e($name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['as' => 'label','variant' => 'sm','weight' => 'medium','color' => 'neutral-500','class' => 'block mb-2','for' => ''.e($name).'']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <div id="<?php echo e($wrapperId); ?>" class="<?php echo e($wrapperClassStr); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconLeft): ?>
            <div class="flex-shrink-0 text-zyngga-neutral-400">
                <?php echo e($iconLeft); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <input 
            type="<?php echo e($type); ?>" 
            name="<?php echo e($name); ?>"
            value="<?php echo e($value); ?>"
            placeholder="<?php echo e($placeholder); ?>"
            <?php if($disabled): echo 'disabled'; endif; ?>
            <?php echo e($attributes->except('class')->merge([
                'id' => $name,
                'class' => 'flex-1 bg-transparent border-none outline-none ring-0 focus:ring-0 focus:outline-none p-0 text-zyngga-neutral-500 placeholder-zyngga-neutral-400 ' . ($inputSizeClasses[$size] ?? $inputSizeClasses['M'])
            ])); ?>

        >
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($error && !$iconRight): ?>
            <div class="flex-shrink-0 text-red-500">
                <i data-feather="alert-circle" class="w-5 h-5"></i>
            </div>
        <?php elseif($iconRight): ?>
            <div class="flex-shrink-0 text-zyngga-neutral-400">
                <?php echo e($iconRight); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($error): ?>
        <?php if (isset($component)) { $__componentOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc99f47cb3b474bf8b96c2c7888cad4ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.zyngga-text','data' => ['variant' => 'xs','weight' => 'medium','color' => 'danger','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('zyngga-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'xs','weight' => 'medium','color' => 'danger','class' => 'mt-1']); ?>
            <?php echo e($error); ?>

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
<?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views/components/zyngga-input.blade.php ENDPATH**/ ?>