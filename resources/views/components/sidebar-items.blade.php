@props(['active' => 'home', 'collapsed' => false])

<nav class="flex flex-col gap-2">
    @php
        $menuItems = [
            [
                'id'     => 'home',
                'label'  => 'Home',
                'icon'   => 'home',
                'href'   => Auth::check() ? route('dashboard') : route('landing'),
                'active' => request()->routeIs('dashboard') || request()->routeIs('landing'),
            ],
            [
                'id'     => 'order',
                'label'  => 'Pesanan',
                'icon'   => 'package',
                'href'   => route('order.history'),
                'active' => request()->routeIs('order.history') || request()->routeIs('order.detail'),
            ],
            [
                'id'     => 'layanan',
                'label'  => 'Layanan',
                'icon'   => 'layers',
                'href'   => '#',
                'active' => false,
            ],
            [
                'id'     => 'langganan',
                'label'  => 'Langganan',
                'icon'   => 'calendar',
                'href'   => '#',
                'active' => false,
            ],
            [
                'id'     => 'profile',
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
            class="flex items-center h-12 rounded-xl transition-all duration-200 group
                {{ $item['active']
                    ? 'bg-zyngga-blue-50 text-zyngga-blue-300'
                    : 'text-zyngga-neutral-400 hover:bg-zyngga-blue-50 hover:text-zyngga-blue-300' }}"
            :class="desktopCollapsed ? 'justify-center px-0' : 'px-3 gap-3'"
            :title="desktopCollapsed ? '{{ $item['label'] }}' : ''"
        >
            <div class="w-10 h-10 flex items-center justify-center shrink-0">
                <i data-feather="{{ $item['icon'] }}" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
            </div>
            <span 
                class="text-[15px] font-medium whitespace-nowrap transition-all duration-300"
                x-show="!desktopCollapsed"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            >
                {{ $item['label'] }}
            </span>
        </a>
    @endforeach
</nav>
