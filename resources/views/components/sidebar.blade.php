{{--
    Sidebar Menu — Zyngga
    Figma node: 102:10  (Frame 39, 240 × 917)

    Usage:
        Include <x-sidebar /> in any layout.
        Toggle with Alpine: x-data="{ sidebarOpen: false }" + @click="sidebarOpen = !sidebarOpen"
--}}
@props(['open' => false])

<div
    x-data="{ open: false }"
    @open-sidebar.window="open = true"
    @close-sidebar.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[9999]"
>
    {{-- ── Backdrop ────────────────────────────────────────────── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"
    ></div>

    {{-- ── Sidebar panel ──────────────────────────────────────── --}}
    <aside
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="absolute inset-y-0 left-0 w-[280px] bg-white shadow-2xl flex flex-col"
        style="font-family:'DM Sans',sans-serif;"
    >
        {{-- Header row: "Menu Utama" + close chevron --}}
        <div class="flex items-center justify-between px-5 h-[56px] border-b border-zyngga-neutral-200 shrink-0">
            <x-zyngga-text variant="base" weight="semibold">Menu Utama</x-zyngga-text>
            <x-zyngga-button 
                variant="neutral"
                size="l"
                icon="chevron-left"
                iconPosition="only"
                @click="open = false"
                aria-label="Tutup menu"
            />
        </div>

        {{-- Menu items ────────────────────────────────────────── --}}
        <nav class="flex flex-col gap-0 pt-4 px-5">
            @php
                $menuItems = [
                    [
                        'label'  => 'Home',
                        'icon'   => 'home',
                        'href'   => Auth::check() ? route('dashboard') : route('landing'),
                        'active' => Auth::check() ? request()->routeIs('dashboard') : request()->routeIs('landing'),
                    ],
                    [
                        'label'  => 'Cek Pesanan',
                        'icon'   => 'package',
                        'href'   => route('order.history'),
                        'active' => request()->routeIs('order.history'),
                    ],
                    [
                        'label'  => 'Layanan',
                        'icon'   => 'layers',
                        'href'   => '#',
                        'active' => false,
                    ],
                    [
                        'label'  => 'Langganan',
                        'icon'   => 'calendar',
                        'href'   => '#',
                        'active' => false,
                    ],
                    [
                        'label'  => 'Profil',
                        'icon'   => 'user',
                        'href'   => route('profile'),
                        'active' => request()->routeIs('profile'),
                    ],
                ];
            @endphp

            @foreach ($menuItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    @click="open = false"
                    class="flex items-center gap-3 h-[52px] rounded-xl px-1 transition-colors
                        {{ $item['active']
                            ? 'bg-zyngga-blue-50'
                            : 'hover:bg-zyngga-neutral-200' }}"
                >
                    {{-- 40×40 icon with rounded-lg bg --}}
                    <div class="w-[52px] h-[40px] flex items-center justify-center shrink-0">
                        <div class="w-10 h-10 rounded-lg bg-zyngga-blue-50 flex items-center justify-center">
                            <i data-feather="{{ $item['icon'] }}" class="w-5 h-5 text-zyngga-blue-300"></i>
                        </div>
                    </div>
                    <x-zyngga-text variant="sm" weight="semibold" class="leading-none">
                        {{ $item['label'] }}
                    </x-zyngga-text>
                </a>
            @endforeach
        </nav>

        {{-- Spacer + bottom logout/login link --}}
        <div class="mt-auto px-5 pb-8">
            <div class="border-t border-zyngga-neutral-200 pt-4">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full h-[48px] rounded-xl px-1 hover:bg-red-50 text-red-500 transition-colors">
                            <div class="w-[52px] h-[40px] flex items-center justify-center shrink-0">
                                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                                    <i data-feather="log-out" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <x-zyngga-text variant="sm" weight="semibold" class="leading-none text-inherit">Keluar</x-zyngga-text>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 w-full h-[48px] rounded-xl px-1 hover:bg-zyngga-blue-50 text-zyngga-blue-300 transition-colors">
                        <div class="w-[52px] h-[40px] flex items-center justify-center shrink-0">
                            <div class="w-10 h-10 rounded-lg bg-zyngga-blue-50 flex items-center justify-center">
                                <i data-feather="log-in" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <x-zyngga-text variant="sm" weight="semibold" class="leading-none text-inherit">Masuk</x-zyngga-text>
                    </a>
                @endauth
            </div>
        </div>
    </aside>
</div>
