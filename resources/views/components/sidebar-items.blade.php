@props(['active' => 'home', 'collapsed' => false])

<nav class="flex flex-col gap-2">
    @php
        $isCustomer = Auth::check();
        
        $menuItems = [
            [
                'id'     => 'home',
                'label'  => 'Home',
                'icon'   => 'home',
                'href'   => $isCustomer ? route('home') : route('landing'),
                'active' => request()->routeIs('home') || request()->routeIs('landing'),
                'show'   => true,
            ],
            [
                'id'     => 'order',
                'label'  => 'Pesanan Kamu',
                'icon'   => 'package',
                'href'   => route('order.history'),
                'active' => request()->routeIs('order.history') || request()->routeIs('order.detail'),
                'show'   => $isCustomer,
            ],
            [
                'id'     => 'cek_pesanan',
                'label'  => 'Cek Pesanan',
                'icon'   => 'search',
                'href'   => route('order.check'),
                'active' => request()->routeIs('order.check'),
                'show'   => true,
            ],
            [
                'id'     => 'layanan',
                'label'  => 'Layanan',
                'icon'   => 'shopping-bag',
                'href'   => '#',
                'active' => false,
                'show'   => true,
            ],
            [
                'id'     => 'langganan',
                'label'  => 'Langganan',
                'icon'   => 'tag',
                'href'   => '#',
                'active' => false,
                'show'   => $isCustomer,
            ],
            [
                'id'     => 'profile',
                'label'  => 'Profil',
                'icon'   => 'user',
                'href'   => $isCustomer ? route('profile') : route('login'),
                'active' => request()->routeIs('profile'),
                'show'   => true,
            ],
        ];
    @endphp

    @foreach ($menuItems as $item)
        @if ($item['show'])
            <a
                href="{{ $item['href'] }}"
                class="flex items-center h-12 rounded-xl transition-all duration-200 group
                    {{ $item['active']
                        ? 'bg-zyngga-blue-50 text-zyngga-blue-300'
                        : 'text-zyngga-neutral-400 hover:bg-zyngga-blue-50 hover:text-zyngga-blue-300' }}"
                :class="desktopCollapsed ? 'justify-center px-0' : 'px-4 gap-4'"
                :title="desktopCollapsed ? '{{ $item['label'] }}' : ''"
            >
                <div class="flex items-center justify-center shrink-0">
                    <i data-feather="{{ $item['icon'] }}" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                </div>
                <span 
                    class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                    x-show="!desktopCollapsed"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                >
                    {{ $item['label'] }}
                </span>
            </a>
        @endif
    @endforeach
</nav>
