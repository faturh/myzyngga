<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'sm', // 2xl, xl, lg, base, sm, xs, 2xs (aliases: h1, h2, h3, body-l, body-m, body-s, body-xs)
    'weight'  => 'regular', // regular, medium, semibold, bold
    'color'   => 'neutral-900', // primary, neutral-900, neutral-500, neutral-400, white, danger
    'as'      => 'p', // p, span, h1, h2, h3, div
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
    'variant' => 'sm', // 2xl, xl, lg, base, sm, xs, 2xs (aliases: h1, h2, h3, body-l, body-m, body-s, body-xs)
    'weight'  => 'regular', // regular, medium, semibold, bold
    'color'   => 'neutral-900', // primary, neutral-900, neutral-500, neutral-400, white, danger
    'as'      => 'p', // p, span, h1, h2, h3, div
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $variants = [
        '2xl'     => 'text-[24px] leading-tight',
        'xl'      => 'text-[20px] leading-tight',
        'lg'      => 'text-[18px] leading-snug',
        'base'    => 'text-[16px] leading-relaxed',
        'sm'      => 'text-[14px] leading-normal',
        'xs'      => 'text-[12px] leading-normal',
        '2xs'     => 'text-[10px] leading-normal',
    ];

    $weights = [
        'regular'  => 'font-normal',
        'medium'   => 'font-medium',
        'semibold' => 'font-semibold',
        'bold'     => 'font-semibold', // Fallback to semibold
    ];

    $colors = [
        'primary'     => 'text-zyngga-blue-300',
        'blue-300'    => 'text-zyngga-blue-300',
        'blue-400'    => 'text-zyngga-blue-400',
        'blue-500'    => 'text-zyngga-blue-500',
        'yellow-300'  => 'text-zyngga-yellow-300',
        'yellow-400'  => 'text-zyngga-yellow-400',
        'yellow-500'  => 'text-zyngga-yellow-500',
        'neutral-900' => 'text-zyngga-neutral-500',
        'neutral-500' => 'text-zyngga-neutral-400',
        'neutral-400' => 'text-zyngga-neutral-300',
        'neutral-200' => 'text-zyngga-neutral-200',
        'neutral-100' => 'text-zyngga-neutral-100',
        'white'       => 'text-white',
        'danger'      => 'text-red-500',
    ];

    $classes = ($variants[$variant] ?? $variants['sm']) . ' ' .
               ($weights[$weight] ?? $weights['regular']) . ' ' .
               ($colors[$color] ?? $colors['neutral-900']);
?>

<<?php echo e($as); ?> <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php echo e($slot); ?>

</<?php echo e($as); ?>>
<?php /**PATH C:\Users\mrafi\OneDrive\Documents\Zyngga\resources\views/components/zyngga-text.blade.php ENDPATH**/ ?>