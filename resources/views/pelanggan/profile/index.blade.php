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
    <x-zyngga-toast />

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

        <main class="w-full max-w-5xl mx-auto px-5 flex-1 flex flex-col" style="min-height: calc(100vh - 200px);">
            
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
                        <x-zyngga-text variant="sm" color="neutral-500" class="block">{{ Auth::user()->phone ?? '-' }}</x-zyngga-text>
                    </div>

                    <x-zyngga-button 
                        type="a" 
                        href="{{ route('profile.account') }}" 
                        variant="secondary" 
                        size="s" 
                        label="Ubah" 
                        class="!px-4 !border-zyngga-blue-300 !text-zyngga-blue-300 shrink-0"
                    />
                </div>
            </x-zyngga-card>

            <div x-show="activeSection === 'overview'" x-transition class="flex flex-col">
                {{-- 2. Alamat Penjemputan Section --}}
                <x-zyngga-card>
                    <div class="flex items-center justify-between mb-5">
                        <x-zyngga-text variant="base" weight="medium">Alamat Penjemputan</x-zyngga-text>
                        @php $addressCount = Auth::user()->addresses()->count(); @endphp
                        @if($addressCount >= 3)
                            <x-zyngga-button 
                                type="button" 
                                @click="window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Maaf, kamu sudah melebihi batas pembuatan alamat (maksimal 3).', type: 'warning' } }))"
                                variant="secondary" 
                                icon="plus"
                                iconPosition="left"
                                size="s" 
                                label="Tambah Alamat" 
                                class="!px-4 !opacity-50 !cursor-not-allowed !border-zyngga-blue-300 !text-zyngga-blue-300"
                            />
                        @else
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
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        @forelse(Auth::user()->addresses()->orderBy('is_primary', 'desc')->take(3)->get() as $address)
                            <a href="{{ route('addresses.edit', $address) }}" class="flex items-center gap-4 group">
                                <div class="w-10 h-10 rounded-xl bg-zyngga-yellow-50 flex items-center justify-center shrink-0">
                                    <i data-feather="map-pin" class="w-[18px] h-[18px] text-zyngga-yellow-300"></i>
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
                            <div class="flex flex-col items-center justify-center py-4 text-center">
                                <div class="w-12 h-12 bg-[#F4F4F4] rounded-full flex items-center justify-center mx-auto mb-3">
                                    <img src="{{ asset('assets/images/empty-order-icon.svg') }}" alt="Belum Ada Alamat" width="24" height="24">
                                </div>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-500" class="text-center">Belum ada alamat tersimpan.</x-zyngga-text>
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

                        {{-- Riwayat Komplain --}}
                        <a href="{{ route('profile.complaints') }}" class="w-full flex items-center gap-4 h-14 group">
                            <i data-feather="alert-circle" class="w-6 h-6 text-zyngga-neutral-800"></i>
                            <div class="flex-1 text-left">
                                <x-zyngga-text variant="sm" weight="medium">Riwayat Komplain</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500">Lihat status pengajuan komplain</x-zyngga-text>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                        </a>

                        <x-zyngga-divider class="my-2" />



                        <button @click="window.dispatchEvent(new CustomEvent('open-logout-modal'))" class="w-full flex items-center gap-4 h-14 group">
                            <i data-feather="log-out" class="w-6 h-6 text-red-500"></i>
                            <x-zyngga-text variant="sm" weight="medium" color="danger">Log Out</x-zyngga-text>    
                        </button>

                    </div>
                </x-zyngga-card>
            

            {{-- Deletion Modal Component --}}
            <livewire:profile.delete-user-form :hide-trigger="true" />
        </main>

        {{-- ── MODAL: KONFIRMASI LOGOUT ────────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="logout-modal-root" 
            openEvent="open-logout-modal"
            closeEvent="close-logout-modal"
        >
            <div class="flex flex-col items-center text-center">
                <div class="mb-6">
                    <img src="{{ asset('images/illustrations/log_out.png') }}" alt="Logout" class="w-40 h-40 object-contain mx-auto">
                </div>

                <div class="space-y-2 mb-8 px-2">
                    <x-zyngga-text variant="lg" weight="medium" color="neutral-900" class="leading-snug !text-[#0F0F0F]">
                        Yakin Keluar dari Akun?
                    </x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="leading-normal !text-[#717171]">
                        Kamu akan keluar dari sesi saat ini dan perlu masuk kembali untuk melanjutkan.
                    </x-zyngga-text>
                </div>

                <div class="flex gap-3 w-full">
                    <x-zyngga-button type="button" @click="isOpen = false" size="m" variant="secondary" label="Kembali" class="flex-1" />
                    <x-zyngga-button 
                        type="button"
                        size="m"
                        onclick="document.getElementById('logout-form').submit()" 
                        variant="secondary-danger" 
                        label="Log Out" 
                        icon="log-out" 
                        iconPosition="left" 
                        class="flex-1" 
                    />
                </div>
            </div>
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
