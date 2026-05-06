<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ 
    desktopCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || (localStorage.getItem('sidebarCollapsed') === null && window.innerWidth >= 768 && window.innerWidth < 1024),
    activeSection: 'overview' 
}" class="bg-zyngga-blue-50 min-h-screen">
    <x-sidebar active="profile" />

    {{-- Main Content Wrapper --}}
    <div 
        class="transition-all duration-300 ease-in-out min-h-screen flex flex-col"
        :class="desktopCollapsed ? 'md:pr-[80px]' : 'md:pr-[280px]'"
        @sidebar-toggled.window="desktopCollapsed = $event.detail.collapsed"
        @resize.window="desktopCollapsed = (window.innerWidth >= 768 && window.innerWidth < 1024)"
    >
        <x-dashboard-header 
            title="Profil Saya" 
            :maxWidth="'max-w-3xl'"
            :showPoints="false"
            :showMenu="true"
        />

        <main class="w-full max-w-3xl mx-auto py-4 px-5 flex-1 flex flex-col gap-3">
            
            {{-- 1. Identity & Profile Header --}}
            <div class="flex flex-col items-center py-6 mb-2">
                <div class="relative mb-4">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-[#1660C1] to-[#0F4387] flex items-center justify-center border-4 border-white shadow-xl">
                        <span class="text-3xl font-bold text-white tracking-tighter">
                            {{ collect(explode(' ', Auth::user()->name))->map(fn($n) => str($n)->substr(0, 1))->join('') }}
                        </span>
                    </div>
                    <div class="absolute bottom-1 right-1 w-7 h-7 bg-green-500 border-4 border-white rounded-full flex items-center justify-center shadow-sm" title="Akun Terverifikasi">
                        <i data-feather="check" class="w-3 h-3 text-white"></i>
                    </div>
                </div>
                
                <div class="text-center space-y-1 mb-6">
                    <x-zyngga-text variant="lg" weight="bold" class="!text-xl leading-tight">{{ Auth::user()->name }}</x-zyngga-text>
                    <x-zyngga-text variant="sm" color="neutral-500">{{ Auth::user()->email }}</x-zyngga-text>
                </div>

                {{-- Points Badge --}}
                <div class="flex items-center gap-2 bg-white rounded-full px-5 py-2 shadow-sm border border-zyngga-blue-50">
                    <div class="w-6 h-6 bg-zyngga-yellow-50 rounded-full flex items-center justify-center">
                        <i data-feather="sun" class="w-3.5 h-3.5 text-zyngga-yellow-300 fill-current"></i>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <x-zyngga-text variant="base" weight="bold" color="primary">120</x-zyngga-text>
                        <x-zyngga-text variant="xs" color="neutral-400" weight="medium" class="uppercase">Points</x-zyngga-text>
                    </div>
                </div>
            </div>

            {{-- Navigation-like rows for various features --}}
            <div x-show="activeSection === 'overview'" x-transition class="flex flex-col gap-3">
                               {{-- 2. Alamat Laundry Section --}}
                <x-zyngga-card>
                    <div class="flex items-center justify-between mb-5">
                        <x-zyngga-text variant="base" weight="bold">Alamat Penjemputan</x-zyngga-text>
                        <x-zyngga-button 
                            type="a" 
                            href="{{ route('addresses.index') }}" 
                            variant="neutral" 
                            size="s" 
                            label="{{ Auth::user()->addresses()->count() > 0 ? 'Atur Alamat' : 'Tambah Alamat' }}" 
                        />
                    </div>
                    
                    <div class="space-y-4">
                        @forelse(Auth::user()->addresses()->orderBy('is_primary', 'desc')->take(2)->get() as $address)
                            {{-- Address Item --}}
                            <a href="{{ route('addresses.index') }}" class="flex items-start gap-4 group cursor-pointer">
                                <div class="w-10 h-10 rounded-xl bg-zyngga-blue-50 flex items-center justify-center shrink-0 transition-colors group-hover:bg-zyngga-blue-100">
                                    <i data-feather="{{ strtolower($address->label) === 'rumah' ? 'home' : (strtolower($address->label) === 'kantor' ? 'briefcase' : 'map-pin') }}" class="w-5 h-5 text-zyngga-blue-300"></i>
                                </div>
                                <div class="flex-1 min-w-0 py-0.5">
                                    <div class="flex items-center gap-2 mb-1">
                                        <x-zyngga-text variant="sm" weight="bold">{{ $address->label }}</x-zyngga-text>
                                        @if($address->is_primary)
                                            <x-zyngga-status type="primary" size="S" label="Utama" />
                                        @endif
                                    </div>
                                    <x-zyngga-text variant="xs" color="neutral-500" class="line-clamp-1">{{ $address->address_detail }}</x-zyngga-text>
                                </div>
                                <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-neutral-300 self-center"></i>
                            </a>
                            
                            @if(!$loop->last)
                                <x-zyngga-divider />
                            @endif
                        @empty
                            <div class="py-4 text-center">
                                <x-zyngga-text variant="sm" color="neutral-400">Belum ada alamat tersimpan.</x-zyngga-text>
                            </div>
                        @endforelse
                    </div>
                </x-zyngga-card>

                {{-- 3. Menu List Section --}}
                <x-zyngga-card>
                    <div class="space-y-1">
                        {{-- Edit Profile --}}
                        <a href="{{ route('profile.account') }}" class="w-full flex items-center gap-4 py-3.5 px-2 -mx-2 hover:bg-gray-50 rounded-2xl transition-all duration-200 group">
                            <div class="w-10 h-10 rounded-xl bg-zyngga-blue-50 flex items-center justify-center transition-colors group-hover:bg-zyngga-blue-100">
                                <i data-feather="user" class="w-5 h-5 text-zyngga-blue-300"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <x-zyngga-text variant="sm" weight="bold">Informasi Pribadi</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-400">Nama, email, dan nomor telepon</x-zyngga-text>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-neutral-300 group-hover:translate-x-1 transition-transform"></i>
                        </a>

                        <x-zyngga-divider />

                        {{-- Change Password --}}
                        <button @click="activeSection = 'change-password'" class="w-full flex items-center gap-4 py-3.5 px-2 -mx-2 hover:bg-gray-50 rounded-2xl transition-all duration-200 group">
                            <div class="w-10 h-10 rounded-xl bg-zyngga-yellow-50 flex items-center justify-center transition-colors group-hover:bg-zyngga-yellow-100">
                                <i data-feather="lock" class="w-5 h-5 text-zyngga-yellow-400"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <x-zyngga-text variant="sm" weight="bold">Keamanan & Password</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-400">Ganti kata sandi akun Anda</x-zyngga-text>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-neutral-300 group-hover:translate-x-1 transition-transform"></i>
                        </button>

                        <x-zyngga-divider />

                        {{-- Help & Support --}}
                        <button class="w-full flex items-center gap-4 py-3.5 px-2 -mx-2 hover:bg-gray-50 rounded-2xl transition-all duration-200 group">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center transition-colors group-hover:bg-gray-100">
                                <i data-feather="help-circle" class="w-5 h-5 text-neutral-400"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <x-zyngga-text variant="sm" weight="bold">Pusat Bantuan</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-400">Hubungi kami jika ada kendala</x-zyngga-text>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-neutral-300 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </x-zyngga-card>

                {{-- 4. Logout Section --}}
                <div class="px-1">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-4 bg-white rounded-2xl shadow-sm border border-red-50 text-red-500 hover:bg-red-50 transition-colors duration-200 group">
                            <i data-feather="log-out" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            <x-zyngga-text variant="sm" weight="bold" color="danger">Keluar dari Akun</x-zyngga-text>
                        </button>
                    </form>
                </div>

                {{-- App Version --}}
                <div class="py-10 text-center">
                    <x-zyngga-text variant="xs" color="neutral-400" weight="medium">Zyngga for Android v2.4.0</x-zyngga-text>
                </div>
            </div>



            {{-- ── SECTION CHANGE PASSWORD ────────────────────────────────── --}}
            <div x-show="activeSection === 'change-password'" x-transition x-cloak class="flex flex-col gap-4">
                <div class="flex items-center gap-3 mb-2 px-1">
                    <button @click="activeSection = 'overview'" class="w-10 h-10 rounded-full bg-white border border-zyngga-blue-50 flex items-center justify-center shadow-sm hover:bg-gray-50 transition-colors">
                        <i data-feather="arrow-left" class="w-5 h-5 text-zyngga-blue-300"></i>
                    </button>
                    <x-zyngga-text variant="lg" weight="bold">Keamanan & Password</x-zyngga-text>
                </div>
                <x-zyngga-card>
                    <div class="max-w-xl">
                        <livewire:profile.update-password-form />
                    </div>
                </x-zyngga-card>
            </div>
            
            <x-zyngga-footer />
        </main>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
            // Re-init feather on Alpine changes
            document.addEventListener('livewire:load', () => feather.replace());
        });
    </script>
</body>
</html>
