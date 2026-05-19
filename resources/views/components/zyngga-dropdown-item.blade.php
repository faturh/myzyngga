@props([
    'icon' => null,
    'description' => null,
    'additional' => null,
    'size' => 'm', // s, m, l
    'active' => false,
    'disabled' => false,
    'showChevron' => true,
    'type' => 'button', // button or a
])

@php
    $size = strtolower($size);
    $baseClasses = "flex items-center gap-3 px-4 transition-all duration-200 rounded-lg w-full text-start group overflow-hidden border box-border";
    
    $sizeClasses = match($size) {
        's' => 'h-8 py-0',
        'm' => 'h-12 py-0',
        'l' => 'h-[72px] py-0',
        default => 'h-12 py-0',
    };

    $stateClasses = $disabled 
        ? 'opacity-50 cursor-not-allowed bg-white border-[#E5E7EB]' 
        : ($active 
            ? 'bg-white border-2 border-zyngga-blue-300' 
            : 'bg-white border-[#E5E7EB] hover:bg-[#F4F4F4] focus:border-zyngga-blue-300 focus:ring-1 focus:ring-zyngga-blue-300 focus:outline-none');

    $labelClasses = "font-medium text-[#0F0F0F] leading-tight " . match($size) {
        's' => 'text-[12px]',
        'm' => 'text-[14px]',
        'l' => 'text-[16px]',
        default => 'text-[14px]',
    };
    $descClasses = "text-[12px] text-[#808080] font-normal leading-tight mt-0.5";
    $additionalClasses = "text-[14px] text-[#808080] font-normal leading-none";
@endphp

@if($type === 'a')
    <a {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $stateClasses"]) }}>
        @include('components.zyngga-dropdown-item-content')
    </a>
@else
    <button {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $stateClasses", 'type' => 'button', 'disabled' => $disabled]) }}>
        @include('components.zyngga-dropdown-item-content')
    </button>
@endif
