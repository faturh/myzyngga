@props(['active' => 'home'])

<div
    x-data="{ 
        mobileOpen: false, 
        desktopCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        init() {
            this.$watch('desktopCollapsed', value => {
                localStorage.setItem('sidebarCollapsed', value);
                this.$dispatch('sidebar-toggled', { collapsed: value });
            });
            
            this.$nextTick(() => {
                this.$dispatch('sidebar-toggled', { collapsed: this.desktopCollapsed });
            });
        }
    }"
    @open-sidebar.window="mobileOpen = true"
    class="relative z-[9999]"
>
    {{-- ── MOBILE OVERLAY ────────────────────────────────────────── --}}
    <div
        x-show="mobileOpen"
        x-cloak
        class="fixed inset-0 z-[10000] md:hidden"
    >
        {{-- Backdrop --}}
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileOpen = false"
            class="absolute inset-0 bg-black/20 backdrop-blur-sm"
        ></div>

        {{-- Sidebar panel --}}
        <aside
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 w-[280px] bg-white shadow-2xl flex flex-col rounded-bl-[16px]"
        >
            <div class="flex items-center justify-between px-6 h-[72px] border-b border-gray-100">
                <span class="text-xl font-medium text-gray-800">Menu</span>
                <button @click="mobileOpen = false" class="p-2 hover:bg-gray-50 rounded-full transition-colors">
                    <i data-feather="x" class="w-6 h-6 text-gray-500"></i>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto py-6 px-4">
                <x-sidebar-items :active="$active" :collapsed="false" />
            </div>


        </aside>
    </div>

    {{-- ── DESKTOP & TABLET SIDEBAR ────────────────────────────────── --}}
    <aside
        class="hidden md:flex fixed inset-y-0 right-0 bg-white border-l border-gray-200 transition-all duration-300 ease-in-out flex-col z-[50] shadow-sm rounded-bl-[16px]"
        :class="desktopCollapsed ? 'w-[80px]' : 'w-[280px]'"
    >
        {{-- Header with Title and Toggle --}}
        <div 
            class="h-[72px] flex items-center border-b border-gray-100 shrink-0 overflow-hidden"
            :class="desktopCollapsed ? 'justify-center px-0' : 'px-4 gap-2'"
        >
            <button 
                @click="desktopCollapsed = !desktopCollapsed"
                class="w-10 h-10 flex items-center justify-center hover:bg-gray-50 rounded-full transition-colors text-gray-500 shrink-0"
                :title="desktopCollapsed ? 'Buka Menu' : 'Tutup Menu'"
            >
                <div x-show="desktopCollapsed">
                    <i data-feather="chevron-left" class="w-6 h-6"></i>
                </div>
                <div x-show="!desktopCollapsed">
                    <i data-feather="chevron-right" class="w-6 h-6"></i>
                </div>
            </button>

            <span 
                x-show="!desktopCollapsed" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="text-lg font-medium text-gray-800 whitespace-nowrap"
            >
                Menu
            </span>
        </div>

        {{-- Menu Items --}}
        <div class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar">
            <x-sidebar-items :active="$active" ::collapsed="desktopCollapsed" />
        </div>


    </aside>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #F3F4F6; border-radius: 10px; }
    
    [x-cloak] { display: none !important; }
</style>
