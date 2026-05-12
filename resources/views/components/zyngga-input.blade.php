@props([
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
])

@php
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
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <x-zyngga-text as="label" variant="sm" weight="regular" class="block mb-2" for="{{ $name }}">
            {{ $label }}
        </x-zyngga-text>
    @endif
    
    <div id="{{ $wrapperId }}" class="{{ $wrapperClassStr }}">
        @if($iconLeft)
            <div class="flex-shrink-0 text-zyngga-neutral-400">
                {{ $iconLeft }}
            </div>
        @endif
        
        <input 
            type="{{ $type }}" 
            placeholder="{{ $placeholder }}"
            @disabled($disabled)
            {{ $attributes->except('class')->merge([
                'id' => $name ?: ($attributes->get('id') ?: 'input-'.uniqid()),
                'name' => $name ?: ($attributes->get('name')),
                'class' => 'flex-1 bg-transparent border-none outline-none ring-0 focus:ring-0 focus:outline-none p-0 text-zyngga-neutral-500 placeholder-zyngga-neutral-400 ' . ($inputSizeClasses[$size] ?? $inputSizeClasses['M'])
            ]) }}
        >
        
        @if($error && !$iconRight)
            <div class="flex-shrink-0 text-red-500">
                <i data-feather="alert-circle" class="w-5 h-5"></i>
            </div>
        @elseif($iconRight)
            <div class="flex-shrink-0 text-zyngga-neutral-400">
                {{ $iconRight }}
            </div>
        @endif
    </div>
    
    @if($error)
        <span class="text-xs text-red-500 mt-1 block">
            {{ $error }}
        </span>
    @endif
</div>
