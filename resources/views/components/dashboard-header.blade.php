{{--
    Component: dashboard-header
    Props:
      - $name  : string — nama user yang ditampilkan (default: Auth user name)
      - $points: int    — jumlah poin/koin user (default: 0)
--}}
@props([
    'name'   => Auth::check() ? Auth::user()->name : 'Pengguna',
    'points' => 0,
])

<header class="sticky top-0 z-40 w-full max-w-[425px] mx-auto pb-[6px]">
    <div class="bg-white rounded-b-2xl shadow-[0_4px_24px_rgba(0,0,0,0.10)] px-5 py-5 transition-shadow duration-300">
        <div class="flex items-center gap-3">
            {{-- Hamburger — dispatches Alpine event to open sidebar --}}
            <x-zyngga-button 
                variant="neutral"
                size="l"
                icon="menu"
                iconPosition="only"
                @click="$dispatch('open-sidebar')"
                aria-label="Buka menu"
                id="dashboard-hamburger-btn"
            />

            {{-- Greeting + Name --}}
            <div class="flex-1 min-w-0">
                <x-zyngga-text variant="sm" weight="medium" color="neutral-500" class="leading-none">Selamat Datang</x-zyngga-text>
                <x-zyngga-text variant="base" weight="semibold" class="leading-none truncate">
                    Hello, {{ $name }}
                </x-zyngga-text>
            </div>

            {{-- Points badge --}}
            <div class="bg-zyngga-yellow-50 rounded-full px-3 py-2 flex items-center gap-1 shrink-0">
                <x-zyngga-text variant="lg" weight="semibold" class="leading-none">{{ $points }}</x-zyngga-text>
                <i data-feather="sun" class="w-5 h-5 text-zyngga-yellow-300 fill-zyngga-yellow-300"></i>
            </div>

        </div>
    </div>
</header>
