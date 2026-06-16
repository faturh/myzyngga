@props([
    'bleed' => false,
])

<div {{ $attributes->merge(['class' => 'h-[1px] bg-zyngga-neutral-200' . ($bleed ? ' -mx-5' : '')]) }}></div>
