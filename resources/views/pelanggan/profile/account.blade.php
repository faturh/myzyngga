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

    <div class="min-h-screen flex flex-col" x-data>
        {{-- HEADER --}}
        <x-dashboard-header 
            title="Ubah Profil" 
            :backUrl="route('profile')" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5">
                <div class="space-y-3" id="page-content">
                    {{-- Profile Info --}}
                    <livewire:profile.update-profile-information-form />


                </div>
            </div>
        </main>
    </div>

    @livewireScripts
    <script>
        const replaceFeather = () => {
            if (typeof feather !== 'undefined') feather.replace();
        };

        document.addEventListener('DOMContentLoaded', replaceFeather);
        document.addEventListener('livewire:navigated', replaceFeather);
        
        // Listen for any Livewire updates to re-replace icons
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('profile-updated', () => setTimeout(replaceFeather, 50));
            
            // Re-replace on every Livewire request finish
            Livewire.hook('request', ({ respond }) => {
                respond(() => {
                    setTimeout(replaceFeather, 10);
                });
            });
        });
    </script>
</body>
</html>
