<!-- HEADER : bg white, h-48px, padding 0 16px, gap 16px -->
<header class="h-12 bg-white flex items-center gap-4 px-4 sticky top-0 z-30 shrink-0">
    <button @click="sidebarOpen = true" class="lg:hidden p-1 text-[#0F0F0F] hover:opacity-70 transition-opacity">
        <i data-feather="menu" class="w-5 h-5"></i>
    </button>
    <h1 class="text-sm font-medium flex-1" style="color:#0F0F0F;">{{ $title ?? 'Operator' }}</h1>
    
    <!-- Profile Avatar Dropdown -->
    <div class="relative" x-data="{ profileOpen: false }">
        <button @click="profileOpen = !profileOpen" type="button" class="flex items-center focus:outline-none cursor-pointer bg-transparent border-0 p-0">
            <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-6 h-6 rounded-full object-cover" style="border:0.5px solid #0F0F0F;">
        </button>
        
        <div x-show="profileOpen" 
             @click.outside="profileOpen = false" 
             x-transition 
             x-cloak 
             class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-slate-100 py-1 z-50">
            <button type="button" 
                    onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();" 
                    class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors text-left bg-transparent border-0 cursor-pointer">
                <i data-feather="log-out" class="w-4 h-4 text-rose-600"></i>
                <span>Keluar Aplikasi</span>
            </button>
            <form id="logout-form-header" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</header>
