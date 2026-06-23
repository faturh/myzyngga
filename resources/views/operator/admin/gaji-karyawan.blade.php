<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Gaji Karyawan - {{ config('app.name', 'Zyngga') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        .transition-transform-hover:hover {
            transform: translateY(-2px);
        }
        
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="font-outfit antialiased bg-[#f8fafc] text-[#1e293b] h-full overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR LEFT (DESKTOP) -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-100/90 h-full shrink-0">
            <!-- Store Profile Head -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-10 h-10 rounded-full border border-slate-100 object-cover shadow-sm">
                    <div>
                        <h2 class="font-bold text-[#0f172a] text-sm leading-tight">MyZyngga</h2>
                        <span class="text-[11px] text-slate-400 font-medium">Laundry Operator</span>
                    </div>
                </div>
                
                <!-- Buka Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-600 px-2.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 transition-all">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Buka
                        <i data-feather="chevron-down" class="w-3 h-3 stroke-[2.5]"></i>
                    </button>
                    <!-- Dropdown Options -->
                    <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-1.5 w-36 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50">
                        <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Buka (Aktif)
                        </a>
                        <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Istirahat
                        </a>
                        <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Tutup Toko
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto custom-scrollbar px-4 py-6 space-y-7">
                <!-- Group 1: Tokoku -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i data-feather="home" class="w-3.5 h-3.5"></i>
                        <span>Dashboard</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                Profil Toko
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 2: Transaksi -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i data-feather="shopping-cart" class="w-3.5 h-3.5"></i>
                        <span>Pesanan</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                Riwayat Pesanan
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 3: Karyawan -->
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i data-feather="users" class="w-3.5 h-3.5"></i>
                        <span>Karyawan</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold bg-blue-50/70 text-blue-600 border border-blue-100/20 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Gaji Karyawan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                Tambah Karyawan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Sidebar Footer Info -->
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                <div class="text-[11px] text-slate-400 text-center font-medium">
                    &copy; 2026 Zyngga Laundry.
                </div>
            </div>
        </aside>

        <!-- SIDEBAR LEFT (MOBILE SLIDE-OVER) -->
        <div x-show="sidebarOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 flex">
                <!-- Sidebar container -->
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     @click.away="sidebarOpen = false" 
                     class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white">
                    
                    <!-- Close Button -->
                    <div class="absolute right-[-48px] top-3">
                        <button @click="sidebarOpen = false" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 focus:outline-none">
                            <i data-feather="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <!-- Store Profile Head -->
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-10 h-10 rounded-full border border-slate-100 object-cover shadow-sm">
                            <div>
                                <h2 class="font-bold text-[#0f172a] text-sm leading-tight">MyZyngga</h2>
                                <span class="text-[11px] text-slate-400 font-medium">Laundry Operator</span>
                            </div>
                        </div>
                        
                        <!-- Buka Badge -->
                        <span class="bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Buka
                        </span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
                        <div>
                            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <i data-feather="home" class="w-3.5 h-3.5"></i>
                                <span>Tokoku</span>
                            </div>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                        Beranda
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                        Profil Toko
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <i data-feather="shopping-cart" class="w-3.5 h-3.5"></i>
                                <span>Pesanan</span>
                            </div>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                        Riwayat Pesanan
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <i data-feather="users" class="w-3.5 h-3.5"></i>
                                <span>Karyawan</span>
                            </div>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.gaji-karyawan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold bg-blue-50/70 text-blue-600 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Gaji Karyawan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                        Tambah Karyawan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            <header class="h-16 bg-white border-b border-slate-100/90 flex items-center justify-between px-6 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Mobile Hamburger Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors">
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>
                    
                    <!-- Navigation Tabs -->
                    <nav class="hidden md:flex items-center space-x-6 text-sm">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-1 font-bold text-slate-800 hover:text-blue-600 px-1 py-2 transition-colors">
                                <span>Buka</span>
                                <i data-feather="chevron-down" class="w-3.5 h-3.5"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute left-0 mt-1 w-32 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50">
                                <a href="#" class="block px-4 py-1.5 text-xs text-slate-700 hover:bg-slate-50 font-semibold">Toko Aktif</a>
                                <a href="#" class="block px-4 py-1.5 text-xs text-slate-700 hover:bg-slate-50 font-semibold">Tutup Toko</a>
                            </div>
                        </div>
                        <a href="{{ route('admin.riwayat-pesanan') }}" class="font-semibold text-slate-500 hover:text-blue-600 px-1 py-2 transition-colors">Pesanan</a>
                        <a href="#" class="font-semibold text-slate-500 hover:text-blue-600 px-1 py-2 transition-colors">Keuangan</a>
                        <a href="#" class="font-semibold text-slate-500 hover:text-blue-600 px-1 py-2 transition-colors">Profil Toko</a>
                    </nav>
                </div>
                
                <!-- Right Header Actions -->
                <div class="flex items-center gap-4" x-data="{ open: false }">
                    <div class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 hover:bg-slate-50 px-3 py-1.5 rounded-xl transition-all">
                            <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-8 h-8 rounded-full border border-slate-100 object-cover">
                            <span class="hidden sm:inline text-sm font-bold text-[#0f172a]">MyZyngga</span>
                            <i data-feather="chevron-down" class="w-4 h-4 text-slate-400 hidden sm:inline"></i>
                        </button>
                        
                        <!-- Dropdown Settings/Logout -->
                        <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                <p class="text-xs font-bold text-[#0f172a]">MyZyngga Operator</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? 'operator@zyngga.com' }}</p>
                            </div>
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                <i data-feather="settings" class="w-3.5 h-3.5 text-slate-400"></i>
                                Pengaturan Toko
                            </a>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">
                                <i data-feather="log-out" class="w-3.5 h-3.5"></i>
                                Keluar Aplikasi
                            </a>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-6 py-8 custom-scrollbar">
                
                <!-- Inner Page Grid -->
                <div class="max-w-4xl mx-auto">
                    
                    <!-- LEFT/CENTER CONTENT PANEL -->
                    <div class="w-full space-y-6 bg-white border border-slate-100 p-6 rounded-2xl shadow-sm"
                         x-data="{
                             sortOrder: 'default',
                             employees: {{ $karyawan->map(function($user) {
                                 return [
                                     'id' => $user->id,
                                     'name' => $user->name ?? $user->username,
                                     'role' => str_replace('_', ' ', ucwords($user->roles->first()?->name ?? 'pegawai_laundry', '_')),
                                     'gaji' => (int) ($user->gaji ?? 0),
                                     'initial' => strtoupper(substr($user->name ?? $user->username, 0, 2)),
                                 ];
                             })->values()->toJson() }},
                             get sortedEmployees() {
                                 if (this.sortOrder === 'asc') {
                                     return [...this.employees].sort((a, b) => a.gaji - b.gaji);
                                 } else if (this.sortOrder === 'desc') {
                                     return [...this.employees].sort((a, b) => b.gaji - a.gaji);
                                 }
                                 return this.employees;
                             }
                         }">
                        
                        <!-- Header Row inside Card -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-5">
                            <div>
                                <h1 class="text-xl font-extrabold text-[#0f172a] leading-none">Daftar Gaji Karyawan</h1>
                                <p class="text-xs font-semibold text-slate-400 mt-1.5">
                                    Urutkan dan pantau pengeluaran gaji karyawan toko laundry Anda.
                                </p>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="text-slate-400 font-semibold whitespace-nowrap">Urutkan Gaji:</span>
                                    <select x-model="sortOrder" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 font-bold focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all">
                                        <option value="default">Default</option>
                                        <option value="asc">Terendah -> Tertinggi</option>
                                        <option value="desc">Tertinggi -> Terendah</option>
                                    </select>
                                </div>
                                <a href="{{ route('user.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                                    <i data-feather="plus" class="w-4 h-4"></i>
                                    Tambah Karyawan
                                </a>
                            </div>
                        </div>

                        <!-- Employees List Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="pb-3 pl-2">Karyawan</th>
                                        <th class="pb-3">Jabatan / Role</th>
                                        <th class="pb-3 text-right pr-2">Gaji Bulanan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-xs">
                                    <template x-for="emp in sortedEmployees" :key="emp.id">
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-4 pl-2">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shadow-sm" x-text="emp.initial"></div>
                                                    <div>
                                                        <p class="font-extrabold text-[#0f172a] text-sm" x-text="emp.name"></p>
                                                        <span class="text-[10px] text-slate-400 font-medium">ID Karyawan: #<span x-text="emp.id"></span></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 font-semibold text-slate-500 capitalize" x-text="emp.role"></td>
                                            <td class="py-4 text-right pr-2 font-black text-[#0f172a] text-sm" x-text="'Rp ' + emp.gaji.toLocaleString('id-ID')"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                
            </div>
            
        </div>
        
    </div>

    <!-- Initialize Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
</body>
</html>
