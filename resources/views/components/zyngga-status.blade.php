@props([
    'type' => 'primary', // primary, secondary, success, warning, error, neutral
    'size' => 'L',       // L, M, S
    'icon' => null,      // feather icon name
    'label' => '',
])

@php
    $baseClasses = "inline-flex items-center justify-center rounded-full transition-all duration-200 whitespace-nowrap gap-1";
    
    // Type colors for M and L sizes (with backgrounds)
    $typeClasses = [
        'primary'   => 'bg-zyngga-blue-300 text-white',
        'secondary' => 'bg-zyngga-blue-50 text-zyngga-blue-300',
        'success'   => 'bg-[#E9F7EE] text-[#21B557]',
        'warning'   => 'bg-[#FEF7E6] text-[#F2AF00]',
        'error'     => 'bg-[#FEE7E6] text-[#EC0F04]',
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
        'M', 'S' => 'w-[12px] h-[12px]',
        default => 'w-[18px] h-[18px]',
    };

    $textVariant = match($size) {
        'L' => 'sm', // 14px
        'M', 'S' => 'xs', // 12px
        default => 'sm',
    };

    $textWeight = ($size === 'S') ? 'medium' : 'semibold';

    $finalClasses = $baseClasses . ' ' . $typeClasses . ' ' . $sizeClasses . ' ' . ($attributes->get('class') ?? '');
@endphp

<div {{ $attributes->merge(['class' => $finalClasses]) }}>
    @if ($icon)
        <i data-feather="{{ $icon }}" class="{{ $iconSize }} shrink-0"></i>
    @endif
    
    <span class="leading-none {{ $size === 'S' ? 'font-normal text-[12px]' : ($size === 'L' ? 'font-medium text-[14px]' : 'font-medium text-[12px]') }}">
        {{ $label ?: $slot }}
    </span>
</div>
