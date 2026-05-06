<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Akun – Zyngga</title>
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
<body class="bg-zyngga-blue-50 min-h-screen">

    <div class="min-h-screen flex flex-col">
        <x-dashboard-header 
            title="Detail Akun" 
            :backUrl="route('profile')" 
            :maxWidth="'max-w-3xl'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="w-full max-w-3xl mx-auto py-6 px-5 flex-1 flex flex-col gap-6">
            
            {{-- Profile Summary Header --}}
            <div class="flex flex-col items-center py-2 mb-2">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#1660C1] to-[#0F4387] flex items-center justify-center border-4 border-white shadow-lg mb-3">
                    <span class="text-2xl font-bold text-white">
                        {{ collect(explode(' ', Auth::user()->name))->map(fn($n) => str($n)->substr(0, 1))->join('') }}
                    </span>
                </div>
                <x-zyngga-text variant="lg" weight="bold">{{ Auth::user()->name }}</x-zyngga-text>
                <x-zyngga-text variant="sm" color="neutral-500">ID Pengguna: #{{ str_pad(Auth::id(), 5, '0', STR_PAD_LEFT) }}</x-zyngga-text>
            </div>

            {{-- Personal Information Section --}}
            <x-zyngga-card>
                <livewire:profile.update-profile-information-form />
            </x-zyngga-card>

            {{-- Danger Zone Section --}}
            <div>
                <x-zyngga-text variant="sm" weight="bold" color="danger" class="uppercase tracking-widest mb-3 px-1">Zona Bahaya</x-zyngga-text>
                <x-zyngga-card class="!border-red-100 !bg-red-50/20">
                    <livewire:profile.delete-user-form />
                </x-zyngga-card>
            </div>

        </main>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
            setTimeout(() => feather.replace(), 500);
        });
        document.addEventListener('livewire:load', function () {
            feather.replace();
        });
        document.addEventListener('livewire:navigated', function () {
            feather.replace();
        });
    </script>
</body>
</html>
