{{--
    Component: zyngga-footer
    Description: Standard informational footer for Zyngga Laundry pages (Dashboard, History, etc.)
--}}
<div class="mt-auto pt-[6px]">
    <div class="bg-white rounded-t-2xl border-t border-zyngga-neutral-200 shadow-[0_-4px_24px_rgba(0,0,0,0.06)] px-5 py-8 space-y-4">

        {{-- Brand info --}}
        <div class="space-y-2">
            <x-zyngga-text variant="lg" weight="medium">Zyngga Laundry</x-zyngga-text>
            <x-zyngga-text variant="sm" color="neutral-500" class="leading-relaxed">
                Solusi laundry modern untuk gaya hidup praktis. Kami memastikan pakaian Anda bersih, rapi, dan harum dengan standar profesional.
            </x-zyngga-text>
        </div>

        <hr class="border-zyngga-neutral-200">

        {{-- Operating hours --}}
        <div class="space-y-2">
            <x-zyngga-text variant="base" weight="medium">Jam Operasional</x-zyngga-text>
            <x-zyngga-text variant="sm" color="neutral-500">Setiap Hari | 08:00 - 20:00 WIB</x-zyngga-text>
        </div>

        <hr class="border-zyngga-neutral-200">

        {{-- Location links --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <x-zyngga-text variant="base" weight="medium">Sukabirus</x-zyngga-text>
                <x-zyngga-text variant="sm" color="neutral-500" class="underline">Jl. Sukabirus No. 99</x-zyngga-text>
            </div>
            <div class="space-y-1">
                <x-zyngga-text variant="base" weight="medium">Sukapura</x-zyngga-text>
                <x-zyngga-text variant="sm" color="neutral-500" class="underline">Jl. Sukapura No. 99</x-zyngga-text>
            </div>
        </div>

        <hr class="border-zyngga-neutral-200">

        {{-- Links grid --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
                <x-zyngga-text variant="base" weight="medium">Layanan</x-zyngga-text>
                <ul class="space-y-0">
                    @foreach (['Regular','Quick','Express','Kilat','Satuan'] as $l)
                        <li class="h-7 flex items-center">
                            <x-zyngga-text variant="sm" color="neutral-500">{{ $l }}</x-zyngga-text>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="space-y-2">
                <x-zyngga-text variant="base" weight="medium">Menu</x-zyngga-text>
                <ul class="space-y-0">
                    <li class="h-7 flex items-center">
                        <a href="{{ route('dashboard') }}" class="underline">
                            <x-zyngga-text variant="sm" color="neutral-500">Home</x-zyngga-text>
                        </a>
                    </li>
                    <li class="h-7 flex items-center">
                        <a href="{{ route('order.history') }}" class="underline">
                            <x-zyngga-text variant="sm" color="neutral-500">Cek Pesanan</x-zyngga-text>
                        </a>
                    </li>
                    <li class="h-7 flex items-center">
                        <x-zyngga-text variant="sm" color="neutral-500" class="underline">Layanan</x-zyngga-text>
                    </li>
                    <li class="h-7 flex items-center">
                        <x-zyngga-text variant="sm" color="neutral-500" class="underline">Langganan</x-zyngga-text>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-zyngga-neutral-200">

        <x-zyngga-text variant="2xs" color="neutral-500">© 2026 Zyngga Laundry. All Rights Reserved.</x-zyngga-text>
    </div>
</div>
