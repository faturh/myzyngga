@props(['collapsed' => false])

<div class="flex flex-col gap-2">
    @auth
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button 
                type="submit" 
                class="flex items-center w-full h-[52px] rounded-2xl transition-all duration-300 text-red-500 hover:bg-red-50 group"
                :class="desktopCollapsed ? 'justify-center px-0' : 'px-4 gap-3'"
                :title="desktopCollapsed ? 'Keluar' : ''"
            >
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <i data-feather="log-out" class="w-5 h-5 transition-transform group-hover:translate-x-1"></i>
                </div>
                <span 
                    class="text-[15px] font-medium transition-all duration-300"
                    x-show="!desktopCollapsed"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                >
                    Keluar
                </span>
            </button>
        </form>
    @else
        <a 
            href="{{ route('login') }}" 
            class="flex items-center w-full h-[52px] rounded-2xl transition-all duration-300 text-zyngga-blue-300 hover:bg-zyngga-blue-50 group"
            :class="desktopCollapsed ? 'justify-center px-0' : 'px-4 gap-3'"
            :title="desktopCollapsed ? 'Masuk' : ''"
        >
            <div class="w-10 h-10 flex items-center justify-center shrink-0">
                <i data-feather="log-in" class="w-5 h-5 transition-transform group-hover:translate-x-1"></i>
            </div>
            <span 
                class="text-[15px] font-medium transition-all duration-300"
                x-show="!desktopCollapsed"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
            >
                Masuk
            </span>
        </a>
    @endauth
</div>
