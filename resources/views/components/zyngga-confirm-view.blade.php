@props([
    'image' => null,
    'title' => '',
    'description' => '',
    'primaryLabel' => 'Konfirmasi',
    'primaryAction' => '', // attributes for wire:click or @click
    'secondaryLabel' => null,
    'secondaryAction' => '', 
    'primaryType' => 'button',
    'primaryHref' => '#',
    'secondaryType' => 'button',
    'secondaryHref' => '#',
    'primaryClass' => '',
    'secondaryClass' => '',
])

<div {{ $attributes->except(['primaryClass', 'secondaryClass'])->merge(['class' => 'flex flex-col items-center text-center']) }}>
    @if($image)
        <div class="mb-6">
            <img src="{{ $image }}" alt="Illustration" class="w-40 h-40 object-contain mx-auto">
        </div>
    @endif

    <div class="space-y-2 mb-8">
        <x-zyngga-text variant="lg" weight="medium" color="neutral-900" class="leading-snug !text-[#0F0F0F]">
            {{ $title }}
        </x-zyngga-text>
        @if($description)
            <x-zyngga-text variant="sm" weight="regular" class="leading-normal !text-[#717171]">
                {{ $description }}
            </x-zyngga-text>
        @endif
    </div>

    <div class="flex items-center gap-2 w-full">
        {{-- Tombol Kedua: Secondary (Batalkan/Kembali) --}}
        @if($secondaryLabel || $secondaryAction)
            <button 
                type="{{ $secondaryType ?? 'button' }}"
                class="inline-flex items-center justify-center rounded-full font-medium transition-all duration-200 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none whitespace-nowrap bg-white border border-[#1660C1] text-[#1660C1] hover:bg-[#F4F4F4] active:bg-[#CCCCCC] h-10 px-4 text-[14px] gap-2 flex-1 {{ $secondaryClass }}"
                @if(str_starts_with($secondaryAction, '@click') || str_starts_with($secondaryAction, 'wire:click') || str_starts_with($secondaryAction, 'onclick'))
                    {!! $secondaryAction !!}
                @else
                    @click="{{ $secondaryAction }}"
                @endif
            >
                {{ $secondaryLabel ?? 'Batalkan' }}
            </button>
        @endif

        {{-- Tombol Utama: Primary (Buat Pesanan/Konfirmasi) --}}
        <button 
            type="{{ $primaryType ?? 'button' }}"
            class="inline-flex items-center justify-center rounded-full font-medium transition-all duration-200 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none whitespace-nowrap bg-[#1660C1] text-white hover:bg-[#0F4387] active:bg-[#0D3B76] h-10 px-4 text-[14px] gap-2 flex-1 {{ $primaryClass }}"
            @if(str_starts_with($primaryAction, '@click') || str_starts_with($primaryAction, 'wire:click') || str_starts_with($primaryAction, 'onclick'))
                {!! $primaryAction !!}
            @else
                @click="{{ $primaryAction }}"
            @endif
        >
            {{ $primaryLabel }}
        </button>
    </div>
</div>
