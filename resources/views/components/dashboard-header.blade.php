{{--
    Component: dashboard-header
    Props:
      - $name  : string — nama user yang ditampilkan (default: Auth user name)
      - $points: int    — jumlah poin/koin user (default: 0)
--}}
@props([
    'name'    => Auth::check() ? Auth::user()->name : 'Pengguna',
    'points'  => 0,
    'title'   => null,
    'backUrl' => null,
    'maxWidth' => 'max-w-full',
    'showPoints' => true,
    'menu' => true,
    'showMenu' => true, // Backward compatibility alias for 'menu'
    'back' => null,
    'hamburg' => null,
    'backAction' => null,
])

@php
    // Master menu visibility: false if either 'menu' or 'showMenu' is false
    $masterMenu = $menu && $showMenu;
    
    $displayBack = $back ?? ($masterMenu && $backUrl);
    $displayHamburger = $hamburg ?? $masterMenu;
@endphp


<header class="sticky top-0 z-40 w-full pb-[6px]">
    <div class="bg-white rounded-b-2xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] transition-shadow duration-300 {{ $maxWidth }} mx-auto min-h-[80px]">
        <div class="max-w-5xl mx-auto w-full px-5 py-5 flex flex-col justify-center min-h-[80px]">
            <div class="flex items-center gap-3">
                {{-- Back Button --}}
                @if($displayBack)
                    <x-zyngga-button 
                        type="a"
                        href="{{ $backUrl ?: 'javascript:void(0)' }}"
                        onclick="{{ $backAction ?: ($backUrl ? '' : 'window.history.back(); return false;') }}"
                        variant="neutral"
                        size="l"
                        icon="arrow-left"
                        iconPosition="only"
                        aria-label="Kembali"
                    />
                @endif

                {{-- Greeting or Title --}}
                <div class="flex-1 min-w-0">
                    @if($title)
                        <x-zyngga-text variant="lg" weight="medium" class="leading-none truncate">{{ $title }}</x-zyngga-text>
                    @else
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="leading-none">Selamat Datang</x-zyngga-text>
                        <x-zyngga-text variant="base" weight="medium" class="leading-none truncate">
                            Hello, {{ $name }}
                        </x-zyngga-text>
                    @endif
                </div>

                {{-- Points badge & Mobile Menu --}}
                <div class="flex items-center gap-2 shrink-0">
                    @if($showPoints)
                        <div class="bg-zyngga-yellow-50 rounded-full px-3 py-2 flex items-center gap-1">
                            <x-zyngga-text variant="lg" weight="medium" class="leading-none">{{ $points }}</x-zyngga-text>
                            <i data-feather="sun" class="w-5 h-5 text-zyngga-yellow-300 fill-zyngga-yellow-300"></i>
                        </div>
                    @endif

                    {{-- Hamburger for Mobile --}}
                    @if($displayHamburger)
                        <div class="md:hidden">
                            <x-zyngga-button 
                                variant="neutral"
                                size="l"
                                icon="menu"
                                iconPosition="only"
                                @click="$dispatch('open-sidebar')"
                                aria-label="Buka menu"
                            />
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Extra slot for filter chips, etc. --}}
            @isset($extra)
                <div class="mt-4">
                    {{ $extra }}
                </div>
            @endisset
        </div>
    </div>
</header>
