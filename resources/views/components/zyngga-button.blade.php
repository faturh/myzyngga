@props([
    'variant'      => 'primary', // primary, secondary, tertiary
    'size'         => 'l',       // s, m, l
    'icon'         => null,      // feather icon name
    'iconPosition' => 'none',    // none, left, right, only
    'label'        => '',
    'type'         => 'button',  // button, submit, a
    'href'         => '#',
    'disabled'     => false,
])

@php
    $baseClasses = "inline-flex items-center justify-center rounded-full font-medium transition-all duration-200 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none whitespace-nowrap";
    
    $variantClasses = [
        'primary'   => 'bg-zyngga-blue-300 text-white hover:bg-zyngga-blue-400 active:bg-zyngga-blue-500',
        'secondary' => 'bg-white border border-zyngga-blue-300 text-zyngga-blue-300 hover:bg-zyngga-neutral-200 active:bg-zyngga-neutral-300',
        'tertiary'  => 'bg-transparent text-zyngga-blue-300 hover:bg-zyngga-blue-50 active:bg-zyngga-neutral-300',
        'neutral'   => 'bg-transparent text-zyngga-neutral-500 hover:bg-zyngga-neutral-200 active:bg-zyngga-neutral-300',
        'danger'    => 'bg-red-500 text-white hover:bg-red-600 active:bg-red-700',
    ][$variant] ?? $variantClasses['primary'];

    $sizeClasses = [
        's' => 'h-8 px-3 text-[12px] gap-2',
        'm' => 'h-10 px-4 text-[14px] gap-2',
        'l' => 'h-12 px-5 text-[16px] gap-2',
    ][$size] ?? $sizeClasses['l'];

    $iconSizes = [
        's' => 'w-3 h-3',
        'm' => 'w-[18px] h-[18px]',
        'l' => 'w-6 h-6',
    ][$size] ?? $iconSizes['l'];

    // Adjust padding for 'only' icon
    if ($iconPosition === 'only') {
        $sizeClasses = [
            's' => 'w-7 h-7 flex shrink-0',
            'm' => 'w-8 h-8 flex shrink-0',
            'l' => 'w-10 h-10 flex shrink-0',
        ][$size] ?? $sizeClasses['l'];
    }

    $finalClasses = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses . ' ' . ($attributes->get('class') ?? '');
@endphp

@if ($type === 'a')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $finalClasses]) }}>
        @if ($icon && ($iconPosition === 'left' || $iconPosition === 'only'))
            <i data-feather="{{ $icon }}" class="{{ $iconSizes }} transition-transform duration-200"></i>
        @endif
        
        @if ($iconPosition !== 'only')
            <span>{{ $label ?: $slot }}</span>
        @endif

        @if ($icon && $iconPosition === 'right')
            <i data-feather="{{ $icon }}" class="{{ $iconSizes }}"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $finalClasses]) }}>
        @if ($icon && ($iconPosition === 'left' || $iconPosition === 'only'))
            <i data-feather="{{ $icon }}" class="{{ $iconSizes }}"></i>
        @endif
        
        @if ($iconPosition !== 'only')
            <span>{{ $label ?: $slot }}</span>
        @endif

        @if ($icon && $iconPosition === 'right')
            <i data-feather="{{ $icon }}" class="{{ $iconSizes }} transition-transform duration-200"></i>
        @endif
    </button>
@endif
