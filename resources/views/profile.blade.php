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
    activeSection: 'overview',
    showLogoutModal: false 
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
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :showMenu="true"
        />

        <main class="w-full max-w-5xl mx-auto px-5 flex-1 flex flex-col ">
            
            {{-- 1. User Profile Card --}}
            <x-zyngga-card>
                <div class="flex items-center gap-5">
                    {{-- Avatar --}}
                    <div class="w-20 h-20 rounded-full bg-zyngga-blue-300 flex items-center justify-center shrink-0">
                        <span class="text-3xl font-medium text-white uppercase">
                            {{ str(Auth::user()->name)->substr(0, 1) }}
                        </span>
                    </div>
                    
                    {{-- User Info --}}
                    <div class="flex-1 space-y-0.5">
                        <x-zyngga-text variant="lg" weight="medium" class="leading-tight">{{ Auth::user()->name }}</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" class="block">{{ Auth::user()->email }}</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" class="block">{{ Auth::user()->phone ?? '0812 3456 7890' }}</x-zyngga-text>
                    </div>
                </div>
            </x-zyngga-card>

            <div x-show="activeSection === 'overview'" x-transition class="flex flex-col">
                {{-- 2. Alamat Penjemputan Section --}}
                <x-zyngga-card>
                    <div class="flex items-center justify-between mb-5">
                        <x-zyngga-text variant="base" weight="medium">Alamat Penjemputan</x-zyngga-text>
                        <x-zyngga-button 
                            type="a" 
                            href="{{ route('addresses.create') }}" 
                            variant="secondary" 
                            icon="plus"
                            iconPosition="left"
                            size="s" 
                            label="Tambah Alamat" 
                            class="!px-4 !border-zyngga-blue-300 !text-zyngga-blue-300"
                        />
                    </div>
                    
                    <div class="space-y-4">
                        @forelse(Auth::user()->addresses()->orderBy('is_primary', 'desc')->take(3)->get() as $address)
                            <a href="{{ route('addresses.edit', $address) }}" class="flex items-center gap-4 group">
                                <div class="w-10 h-10 rounded-xl bg-zyngga-blue-50 flex items-center justify-center shrink-0">
                                    <i data-feather="map-pin" class="w-[18px] h-[18px] text-zyngga-blue-300"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <x-zyngga-text variant="sm" weight="medium" class="text-zyngga-neutral-900">{{ $address->label }}</x-zyngga-text>
                                        @if($address->is_primary)
                                            <x-zyngga-status type="secondary" size="M" label="Utama" />
                                        @endif
                                    </div>
                                    <x-zyngga-text variant="xs" color="neutral-500" class="line-clamp-1">{{ $address->address_detail }}</x-zyngga-text>
                                </div>
                            </a>
                            @if(!$loop->last)
                                <x-zyngga-divider class="my-3 opacity-50" />
                            @endif
                        @empty
                            <div class="py-4 flex justify-center items-center">
                                <x-zyngga-text variant="sm" color="neutral-400" weight="regular" class="text-center">Belum ada alamat tersimpan.</x-zyngga-text>
                            </div>
                        @endforelse
                    </div>
                </x-zyngga-card>

                {{-- 3. Menu List Card --}}
                <x-zyngga-card>
                    <div class="space-y-2">
                        {{-- Informasi Pribadi --}}
                        <a href="{{ route('profile.account') }}" class="flex items-center gap-4 h-14 group">
                            <i data-feather="user" class="w-6 h-6 text-zyngga-neutral-800"></i>
                            <div class="flex-1">
                                <x-zyngga-text variant="sm" weight="medium">Informasi Pribadi</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500">Nama, email, dan nomor WhatsApp</x-zyngga-text>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                        </a>

                        <x-zyngga-divider class="my-2" />

                        {{-- Keamanan & Password --}}
                        <button @click="activeSection = 'change-password'" class="w-full flex items-center gap-4 h-14 group">
                            <i data-feather="lock" class="w-6 h-6 text-zyngga-neutral-800"></i>
                            <div class="flex-1 text-left">
                                <x-zyngga-text variant="sm" weight="medium">Keamanan & Password</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500">Ganti kata sandi</x-zyngga-text>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                        </button>

                        <x-zyngga-divider class="my-2" />

                        {{-- Pusat Bantuan --}}
                        <button class="w-full flex items-center gap-4 h-14 group">
                            <i data-feather="message-circle" class="w-6 h-6 text-zyngga-neutral-800"></i>
                            <div class="flex-1 text-left">
                                <x-zyngga-text variant="sm" weight="medium">Pusat Bantuan</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500">Hubungi kami jika ada kendala</x-zyngga-text>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                        </button>

                        <x-zyngga-divider class="my-2" />

                        <button @click="window.dispatchEvent(new CustomEvent('open-logout-modal'))" class="w-full flex items-center gap-4 h-14 group">
                            <i data-feather="log-out" class="w-6 h-6 text-red-500"></i>
                            <x-zyngga-text variant="sm" weight="medium" color="danger">Log Out</x-zyngga-text>    
                        </button>

                    </div>
                </x-zyngga-card>
            
            {{-- ── SECTION CHANGE PASSWORD ────────────────────────────────── --}}
            <div x-show="activeSection === 'change-password'" x-transition x-cloak class="flex flex-col gap-4">
                <div class="flex items-center gap-3 mb-2 px-1">
                    <button @click="activeSection = 'overview'" class="w-10 h-10 rounded-full bg-white border border-zyngga-blue-50 flex items-center justify-center shadow-sm hover:bg-gray-50 transition-colors">
                        <i data-feather="arrow-left" class="w-5 h-5 text-zyngga-blue-300"></i>
                    </button>
                    <x-zyngga-text variant="lg" weight="medium">Keamanan & Password</x-zyngga-text>
                </div>
                <x-zyngga-card>
                    <div class="max-w-xl">
                        <livewire:profile.update-password-form />
                    </div>
                </x-zyngga-card>
            </div>
            
        </main>

        {{-- ── MODAL: KONFIRMASI LOGOUT ────────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="logout-modal-root" 
            openEvent="open-logout-modal"
            closeEvent="close-logout-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/cancel_order.png')"
                title="Yakin ingin keluar?"
                description="Anda harus masuk kembali untuk dapat melakukan pemesanan laundry."
                primaryLabel="Ya, Keluar"
                secondaryLabel="Batal"
                primaryAction="document.getElementById('logout-form').submit()"
                secondaryAction="@click=$dispatch('close-logout-modal')"
            />
        </x-zyngga-selection-modal>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>

        <x-zyngga-footer />
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
