@props([
    'name',
    'value' => '1',
    'id' => null,
    'label' => '',
    'description' => '',
    'checked' => false,
    'disabled' => false,
])

@php
    $id = $id ?? $name;
@endphp

<label for="{{ $id }}" class="flex items-center justify-between h-[56px] px-1 cursor-pointer group select-none transition-all duration-200 {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
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

    <div class="relative flex items-center justify-center h-5 w-5">
        <input 
            type="checkbox" 
            name="{{ $name }}" 
            id="{{ $id }}" 
            value="{{ $value }}"
            {{ $checked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="sr-only peer"
            {{ $attributes }}
        >
        {{-- Custom Checkbox Square --}}
        <div class="w-5 h-5 rounded-md border-[1.5px] border-zyngga-blue-50 transition-all duration-200 
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
</label>
