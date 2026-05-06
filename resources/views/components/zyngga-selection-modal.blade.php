@props([
    'id',
    'title' => null,     // Modal header title (optional)
    'openEvent' => null, // Event name to listen for to open the modal
    'closeEvent' => null, // Event name to dispatch when closed
])

<div
    id="{{ $id }}"
    x-data="{ isOpen: false }"
    x-init="
        $watch('isOpen', value => document.body.style.overflow = value ? 'hidden' : 'auto');
        @if($openEvent) window.addEventListener('{{ $openEvent }}', () => isOpen = true); @endif
        @if($closeEvent) window.addEventListener('{{ $closeEvent }}', () => isOpen = false); @endif
    "
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-5"
    style="background: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(12px);"
    x-on:click="isOpen = false"
>
    <div
        class="w-full max-w-[385px] bg-white rounded-2xl p-6 max-height-[90vh] overflow-y-auto"
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300 delay-75"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        @click.stop
    >
        {{-- Modal Header --}}
        @if($title)
            <div class="flex items-center justify-between mb-6">
                <x-zyngga-text variant="lg" weight="medium">{{ $title }}</x-zyngga-text>
                <x-zyngga-button 
                    variant="neutral"
                    size="m"
                    icon="x"
                    iconPosition="only"
                    x-on:click="isOpen = false"
                    aria-label="Tutup"
                />
            </div>
        @endif

        {{-- Modal Content (Slot) --}}
        <div class="space-y-1">
            {{ $slot }}
        </div>
    </div>
</div>
