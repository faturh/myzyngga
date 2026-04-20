<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'primary', // primary, secondary, success, warning, error, neutral
    'size' => 'L',       // L, M, S
    'icon' => null,      // feather icon name
    'label' => '',
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
    'type' => 'primary', // primary, secondary, success, warning, error, neutral
    'size' => 'L',       // L, M, S
    'icon' => null,      // feather icon name
    'label' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $baseClasses = "inline-flex items-center justify-center rounded-full transition-all duration-200 whitespace-nowrap gap-1";
    
    // Type colors for M and L sizes (with backgrounds)
    $typeClasses = [
        'primary'   => 'bg-zyngga-blue-300 text-white',
        'secondary' => 'bg-zyngga-blue-50 text-zyngga-blue-300',
        'success'   => 'bg-[#21B557]/10 text-[#21B557]',
        'warning'   => 'bg-[#F2AF00]/10 text-[#F2AF00]',
        'error'     => 'bg-[#EC0F04]/10 text-[#EC0F04]',
        'neutral'   => 'bg-[#F4F4F4] text-[#808080]',
    ][$type] ?? 'bg-zyngga-blue-300 text-white';

    // Size dimensions
    $sizeClasses = [
        'L' => 'h-8 px-3',
        'M' => 'h-7 px-2',
        'S' => 'h-7', // No background for S size in the bottom row
    ][$size] ?? 'h-8 px-3';

    // Overwrite for 'S' size: transparent background, specific text colors
    if ($size === 'S') {
        $typeClasses = match($type) {
            'primary', 'secondary' => 'bg-transparent text-zyngga-blue-300',
            'success'   => 'bg-transparent text-[#21B557]',
            'warning'   => 'bg-transparent text-[#F2AF00]',
            'error'     => 'bg-transparent text-[#EC0F04]',
            'neutral'   => 'bg-transparent text-[#808080]',
            default     => 'bg-transparent text-zyngga-blue-300',
        };
    }

    $iconSize = match($size) {
        'L' => 'w-[18px] h-[18px]',
        'M', 'S' => 'w-[14px] h-[14px]',
        default => 'w-[18px] h-[18px]',
    };

    $textVariant = match($size) {
        'L' => 'sm', // 14px
        'M', 'S' => 'xs', // 12px
        default => 'sm',
    };

    $textWeight = ($size === 'S') ? 'medium' : 'semibold';

    $finalClasses = $baseClasses . ' ' . $typeClasses . ' ' . $sizeClasses . ' ' . ($attributes->get('class') ?? '');
?>

<div <?php echo e($attributes->merge(['class' => $finalClasses])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon): ?>
        <i data-feather="<?php echo e($icon); ?>" class="<?php echo e($iconSize); ?> shrink-0"></i>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <span class="leading-none <?php echo e($size === 'S' ? 'font-medium text-[12px]' : ($size === 'L' ? 'font-semibold text-[14px]' : 'font-semibold text-[12px]')); ?>">
        <?php echo e($label ?: $slot); ?>

    </span>
</div>
<?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views/components/zyngga-status.blade.php ENDPATH**/ ?>