@props([
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
])

@php
    $id = $id ?? $name . '-' . $value;
    // Base container classes
    $containerClasses = [
        'flex items-center justify-between px-1 cursor-pointer group select-none transition-all duration-200',
        $size === 'M' ? 'h-[32px]' : 'h-[56px]',
        $disabled ? 'opacity-50 cursor-not-allowed' : '',
    ];
@endphp

<label for="{{ $id }}" class="{{ implode(' ', $containerClasses) }}">
    <div class="flex items-center gap-3">
        @if($service)
            <div class="flex-shrink-0 w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                <x-zyngga-service-icon service="{{ $service }}" class="w-3.5 h-3.5 text-zyngga-yellow-300" />
            </div>
        @elseif($icon)
            <div class="flex-shrink-0">
                {{ $icon }}
            </div>
        @endif
        
        <div class="flex flex-col">
            <x-zyngga-text variant="sm" weight="medium" class="leading-snug">
                {{ $label }}
            </x-zyngga-text>
            @if($description)
                <x-zyngga-text variant="xs" color="neutral-500" class="leading-snug mt-0.5">
                    {{ $description }}
                </x-zyngga-text>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3">
        @if($additional)
            <span class="text-[14px] text-[#0F0F0F] font-normal">{{ $additional }}</span>
        @endif
        
        <div class="relative flex items-center justify-center h-5 w-5">
            <input 
                type="radio" 
                name="{{ $name }}" 
                id="{{ $id }}" 
                value="{{ $value }}"
                {{ $checked ? 'checked' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                class="sr-only peer"
                {{ $attributes }}
            >
            {{-- Custom Radio Circle --}}
            <div class="w-5 h-5 rounded-full border-[1.5px] border-zyngga-blue-50 transition-all duration-200 
                        peer-checked:border-zyngga-blue-300 peer-checked:bg-zyngga-blue-300
                        group-hover:border-zyngga-blue-300">
            </div>
            {{-- Checkmark icon --}}
            <div class="absolute inset-0 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity duration-200 pointer-events-none">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
        </div>
    </div>
</label>
