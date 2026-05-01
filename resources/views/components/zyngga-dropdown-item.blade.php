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
    $baseClasses = "flex items-center gap-2 px-4 transition-all duration-200 rounded-lg w-full text-start group";
    
    $sizeClasses = match($size) {
        's' => 'h-8 py-1',
        'l' => 'h-[72px] py-3',
        default => 'h-12 py-2', // m
    };

    $stateClasses = $disabled 
        ? 'opacity-50 cursor-not-allowed bg-white border border-zyngga-neutral-300' 
        : ($active 
            ? 'bg-white border-2 border-zyngga-blue-300' 
            : 'bg-white border border-zyngga-neutral-300 hover:bg-zyngga-neutral-200 focus:border-zyngga-blue-300 focus:ring-1 focus:ring-zyngga-blue-300 focus:outline-none');

    $labelClasses = "font-medium text-zyngga-neutral-500 leading-none " . ($size === 's' ? 'text-[14px]' : 'text-[16px]');
    $descClasses = "text-[14px] text-zyngga-neutral-400 font-normal leading-none mt-1";
    $additionalClasses = "text-[16px] text-zyngga-neutral-500 font-normal leading-none";
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
