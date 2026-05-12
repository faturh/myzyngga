@props([
    'name',
    'id' => null,
    'checked' => false,
    'disabled' => false,
    'label' => '',
])

@php
    $id = $id ?? $name;
@endphp

<label for="{{ $id }}" class="flex items-center justify-between h-6 px-1 cursor-pointer group select-none transition-all duration-200 {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <div class="flex flex-col">
        <x-zyngga-text variant="sm" weight="medium" class="leading-snug">
            {{ $label }}
        </x-zyngga-text>
        @if(isset($description))
            <x-zyngga-text variant="xs" color="neutral-500" class="leading-snug mt-0.5">
                {{ $description }}
            </x-zyngga-text>
        @endif
    </div>

    <div class="relative">
        <input 
            type="checkbox" 
            name="{{ $name }}" 
            id="{{ $id }}" 
            class="sr-only peer"
            {{ $checked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes }}
        >
        
        {{-- Track --}}
        <div class="w-10 h-6 bg-zyngga-neutral-200 rounded-full transition-colors duration-200 
                    peer-checked:bg-zyngga-blue-300">
        </div>
        
        {{-- Thumb --}}
        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 transform 
                    peer-checked:translate-x-4 shadow-sm">
        </div>
    </div>
</label>
