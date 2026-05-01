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
            
            {{-- 1. Identity & Points Card --}}
            <x-zyngga-card>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-zyngga-blue-100 flex items-center justify-center border-2 border-white shadow-sm">
                        <span class="text-xl font-medium text-zyngga-blue-300">
                            {{ collect(explode(' ', Auth::user()->name))->map(fn($n) => str($n)->substr(0, 1))->join('') }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <x-zyngga-text variant="lg" weight="medium" class="leading-tight truncate">{{ Auth::user()->name }}</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" class="leading-none mt-1">{{ Auth::user()->email }}</x-zyngga-text>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <div class="bg-zyngga-yellow-50 rounded-full px-3 py-1 flex items-center gap-1 border border-zyngga-yellow-100">
                            <x-zyngga-text variant="sm" weight="medium" class="text-zyngga-yellow-400">120</x-zyngga-text>
                            <i data-feather="sun" class="w-3 h-3 text-zyngga-yellow-400 fill-current"></i>
                        </div>
                        <x-zyngga-text variant="2xs" color="neutral-400" class="uppercase tracking-wider">Points</x-zyngga-text>
                    </div>
                </div>
            </x-zyngga-card>

            {{-- Navigation-like rows for various features --}}
            <div x-show="activeSection === 'overview'" x-transition class="flex flex-col gap-3">
                
                {{-- 2. Alamat Laundry (Quick Action) --}}
                <x-zyngga-card>
                    <div class="flex items-center justify-between mb-4">
                        <x-zyngga-text variant="base" weight="medium">Alamat Laundry</x-zyngga-text>
                        <x-zyngga-button variant="neutral" size="s" label="Atur Alamat" />
                    </div>
                    
                    <div class="space-y-4">
                        {{-- Address Item 1 --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-zyngga-blue-50 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-feather="home" class="w-4 h-4 text-zyngga-blue-300"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <x-zyngga-text variant="sm" weight="medium">Rumah Utama</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500" class="mt-0.5 truncate">Jl. Telekomunikasi No. 1, Bojongsoang, Bandung</x-zyngga-text>
                            </div>
                            <x-zyngga-status type="primary" size="S" label="Utama" />
                        </div>
                        
                        <x-zyngga-divider />

                        {{-- Address Item 2 --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-feather="briefcase" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <x-zyngga-text variant="sm" weight="medium">Kantor</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500" class="mt-0.5 truncate">Gedung Landmark, Lt. 12, Braga, Bandung</x-zyngga-text>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-neutral-300"></i>
                        </div>
                    </div>
                </x-zyngga-card>

                {{-- 3. Menu List Section --}}
                <x-zyngga-card>
                    <div class="space-y-1">
                        {{-- Edit Profile --}}
                        <button @click="activeSection = 'edit-profile'" class="w-full flex items-center gap-3 py-3 hover:bg-gray-50 rounded-xl px-2 -mx-2 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-zyngga-blue-50 flex items-center justify-center">
                                <i data-feather="user" class="w-4 h-4 text-zyngga-blue-300"></i>
                            </div>
                            <x-zyngga-text variant="sm" weight="medium" class="flex-1 text-left">Informasi Pribadi</x-zyngga-text>
                            <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-neutral-300"></i>
                        </button>

                        <x-zyngga-divider />

                        {{-- Change Password --}}
                        <button @click="activeSection = 'change-password'" class="w-full flex items-center gap-3 py-3 hover:bg-gray-50 rounded-xl px-2 -mx-2 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-zyngga-yellow-50 flex items-center justify-center">
                                <i data-feather="lock" class="w-4 h-4 text-zyngga-yellow-400"></i>
                            </div>
                            <x-zyngga-text variant="sm" weight="medium" class="flex-1 text-left">Keamanan & Password</x-zyngga-text>
                            <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-neutral-300"></i>
                        </button>

                        <x-zyngga-divider />

                        {{-- Help & Support --}}
                        <button class="w-full flex items-center gap-3 py-3 hover:bg-gray-50 rounded-xl px-2 -mx-2 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center">
                                <i data-feather="help-circle" class="w-4 h-4 text-gray-500"></i>
                            </div>
                            <x-zyngga-text variant="sm" weight="medium" class="flex-1 text-left">Pusat Bantuan</x-zyngga-text>
                            <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-neutral-300"></i>
                        </button>
                    </div>
                </x-zyngga-card>

                {{-- 4. Logout Section --}}
                <x-zyngga-card class="!p-3">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 py-2 px-2 hover:bg-red-50 rounded-xl transition-colors group">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                                <i data-feather="log-out" class="w-4 h-4 text-red-500"></i>
                            </div>
                            <x-zyngga-text variant="sm" weight="medium" color="danger" class="flex-1 text-left">Keluar Akun</x-zyngga-text>
                        </button>
                    </form>
                </x-zyngga-card>

                {{-- Danger Zone --}}
                <div class="mt-4 px-1">
                    <x-zyngga-text variant="2xs" color="neutral-400" weight="medium" class="uppercase tracking-wider mb-2">Zona Bahaya</x-zyngga-text>
                    <x-zyngga-card class="!border-red-100 bg-red-50/10">
                        <div class="max-w-xl">
                            <livewire:profile.delete-user-form />
                        </div>
                    </x-zyngga-card>
                </div>

                {{-- App Version --}}
                <div class="py-8 text-center">
                    <x-zyngga-text variant="xs" color="neutral-400">Zyngga for Android v2.4.0</x-zyngga-text>
                </div>
            </div>

            {{-- ── SECTION EDIT PROFILE ───────────────────────────────────── --}}
            <div x-show="activeSection === 'edit-profile'" x-transition x-cloak class="flex flex-col gap-3">
                <div class="flex items-center gap-2 mb-2">
                    <x-zyngga-button variant="neutral" size="s" icon="arrow-left" @click="activeSection = 'overview'" />
                    <x-zyngga-text variant="base" weight="medium">Informasi Pribadi</x-zyngga-text>
                </div>
                <x-zyngga-card>
                    <div class="max-w-xl">
                        <livewire:profile.update-profile-information-form />
                    </div>
                </x-zyngga-card>
            </div>

            {{-- ── SECTION CHANGE PASSWORD ────────────────────────────────── --}}
            <div x-show="activeSection === 'change-password'" x-transition x-cloak class="flex flex-col gap-3">
                <div class="flex items-center gap-2 mb-2">
                    <x-zyngga-button variant="neutral" size="s" icon="arrow-left" @click="activeSection = 'overview'" />
                    <x-zyngga-text variant="base" weight="medium">Keamanan & Password</x-zyngga-text>
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
