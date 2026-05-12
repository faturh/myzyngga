<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Zyngga' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarCollapsed: window.innerWidth < 1024 }" class="bg-zyngga-blue-50 min-h-screen text-zyngga-neutral-800 antialiased">
    
    <x-sidebar />

    {{-- Main Content Area --}}
    <div 
        class="transition-all duration-300 min-h-screen flex flex-col"
        :class="{ 
            'md:ml-[80px]': sidebarCollapsed, 
            'md:ml-[280px]': !sidebarCollapsed 
        }"
        @toggle-sidebar.window="sidebarCollapsed = !sidebarCollapsed"
    >
        {{-- Header (if provided) --}}
        @isset($header)
            {{ $header }}
        @endisset

        {{-- Main Section: Max width tablet (approx 768px) --}}
        <main class="flex-1 w-full max-w-[768px] mx-auto relative px-0 md:px-4">
            {{ $slot }}
        </main>

        {{-- Footer (if provided) --}}
        @isset($footer)
            {{ $footer }}
        @endisset
    </div>

    @livewireScripts
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
            // Watch for sidebar collapse state from component
            window.addEventListener('resize', () => {
                // You might want to sync the body state with the sidebar component state
            });
        });
        document.addEventListener('livewire:load', function () { feather.replace(); });
        document.addEventListener('livewire:navigated', function () { feather.replace(); });
    </script>
</body>
</html>
