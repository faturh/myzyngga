@props([
    'type' => 'primary', // primary, secondary, success, warning, error, neutral
    'size' => 'L',       // L, M, S
    'icon' => null,      // feather icon name
    'label' => '',
])

@php
    $size = strtoupper($size);
    $baseClasses = "inline-flex items-center justify-center rounded-full transition-all duration-200 whitespace-nowrap gap-1.5";
    
    // Exact colors from Figma tokens
    $typeClasses = match($type) {
        'primary'   => 'bg-zyngga-blue-300 text-white',
        'secondary' => 'bg-[#E8EFF9] text-[#1660C1]',
        'success'   => 'bg-[#E9F7EE] text-[#21B557]',
        'warning'   => 'bg-[#FEF7E6] text-[#F2AF00]',
        'error'     => 'bg-[#FEE7E6] text-[#DB0B00]',
        'neutral'   => 'bg-[#F4F4F4] text-[#808080]',
        default     => 'bg-zyngga-blue-300 text-white',
    };

    // Height and Horizontal Padding from Figma metadata
    $sizeClasses = match($size) {
        'L' => 'h-8 px-3',
        'M' => 'h-7 px-2.5',
        'S' => 'h-7 px-2',
        default => 'h-8 px-3',
    };

    $iconSize = match($size) {
        'L' => 'w-4 h-4',
        'M', 'S' => 'w-3.5 h-3.5',
        default => 'w-4 h-4',
    };

    // Typography: medium (600) for all except maybe specific cases
    $textWeight = 'font-medium';
    
    $textSize = match($size) {
        'L' => 'text-[14px]',
        'M', 'S' => 'text-[12px]',
        default => 'text-[14px]',
    };

    $finalClasses = $baseClasses . ' ' . $typeClasses . ' ' . $sizeClasses . ' ' . ($attributes->get('class') ?? '');
@endphp

<div {{ $attributes->merge(['class' => $finalClasses]) }}>
    @if ($icon)
        <i data-feather="{{ $icon }}" class="{{ $iconSize }} shrink-0"></i>
    @endif
    
    <span class="leading-none {{ $textWeight }} {{ $textSize }}">
        {{ $label ?: $slot }}
    </span>
</div>
