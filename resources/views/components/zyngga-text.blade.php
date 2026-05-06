@props([
    'variant' => 'sm', // 2xl, xl, lg, base, sm, xs, 2xs (aliases: h1, h2, h3, body-l, body-m, body-s, body-xs)
    'weight'  => 'regular', // regular, medium, semibold, bold
    'color'   => 'neutral-900', // primary, neutral-900, neutral-500, neutral-400, white, danger
    'as'      => 'p', // p, span, h1, h2, h3, div
])

@php
    $variants = [
        '2xl'     => 'text-[24px] leading-tight',
        'xl'      => 'text-[20px] leading-tight',
        'lg'      => 'text-[18px] leading-snug',
        'base'    => 'text-[16px] leading-relaxed',
        'sm'      => 'text-[14px] leading-normal',
        'xs'      => 'text-[12px] leading-normal',
        '2xs'     => 'text-[10px] leading-normal',
    ];

    $weights = [
        'regular'  => 'font-normal',
        'medium'   => 'font-normal',
        'semibold' => 'font-medium',
        'bold'     => 'font-medium',
    ];

    $colors = [
        'primary'     => 'text-zyngga-blue-300',
        'blue-300'    => 'text-zyngga-blue-300',
        'blue-400'    => 'text-zyngga-blue-400',
        'blue-500'    => 'text-zyngga-blue-500',
        'yellow-300'  => 'text-zyngga-yellow-300',
        'yellow-400'  => 'text-zyngga-yellow-400',
        'yellow-500'  => 'text-zyngga-yellow-500',
        'neutral-900' => 'text-zyngga-neutral-500',
        'neutral-500' => 'text-zyngga-neutral-400',
        'neutral-400' => 'text-zyngga-neutral-300',
        'neutral-200' => 'text-zyngga-neutral-200',
        'neutral-100' => 'text-zyngga-neutral-100',
        'white'       => 'text-white',
        'danger'      => 'text-red-500',
    ];

    $classes = ($variants[$variant] ?? $variants['sm']) . ' ' .
               ($weights[$weight] ?? $weights['regular']) . ' ' .
               ($colors[$color] ?? $colors['neutral-900']);
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $as }}>
