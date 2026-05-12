<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/feather-icons"></script>
        <style>
            * { font-family: 'DM Sans', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased bg-[#F8FAFC] text-gray-900 min-h-screen">
        {{ $slot }}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof feather !== 'undefined') feather.replace();
            });

            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', (el, component) => {
                    if (typeof feather !== 'undefined') feather.replace();
                });
            });
            
            document.addEventListener('livewire:navigated', () => {
                if (typeof feather !== 'undefined') feather.replace();
            });
        </script>
    </body>
</html>
