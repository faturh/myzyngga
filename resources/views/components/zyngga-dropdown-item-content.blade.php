@if($icon)
    <div class="shrink-0 flex items-center justify-center">
        <i data-feather="{{ $icon }}" class="{{ $size === 's' ? 'w-4 h-4' : 'w-5 h-5' }} text-zyngga-blue-300"></i>
    </div>
@endif

<div class="flex-1 flex items-center justify-between min-w-0">
    <div class="flex flex-col items-start min-w-0">
        <span class="{{ $labelClasses }} truncate">{{ $slot }}</span>
        @if($description && $size !== 's')
            <span class="{{ $descClasses }} truncate">{{ $description }}</span>
        @endif
    </div>

    <div class="flex items-center gap-2 ml-auto">
        @if($additional)
            <span class="{{ $additionalClasses }}">{{ $additional }}</span>
        @endif
        
        @if($showChevron)
            <div class="shrink-0">
                <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-neutral-400"></i>
            </div>
        @endif
    </div>
</div>
