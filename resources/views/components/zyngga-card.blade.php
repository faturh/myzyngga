@props([
    'title' => null,
    'padding' => 'p-5',
    'gap' => 'py-[6px]'
])

<div {{ $attributes->merge(['class' => $gap]) }}>
    <div class="bg-white rounded-2xl {{ $padding }} shadow-sm border border-gray-50 overflow-hidden relative">
        @if($title || isset($headerAction))
            <div class="flex items-center justify-between mb-4">
                @if($title)
                    <x-zyngga-text variant="base" weight="medium">{{ $title }}</x-zyngga-text>
                @endif
                @if(isset($headerAction))
                    <div class="flex items-center">
                        {{ $headerAction }}
                    </div>
                @endif
            </div>
        @endif
        
        <div class="w-full">
            {{ $slot }}
        </div>
    </div>
</div>
