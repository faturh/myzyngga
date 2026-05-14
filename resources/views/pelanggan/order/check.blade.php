<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cek Pesanan – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; min-height: 100%; }
        #page-content { padding-bottom: 100px; }
    </style>
</head>
<body x-data="{ 
    desktopCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || (localStorage.getItem('sidebarCollapsed') === null && window.innerWidth >= 768 && window.innerWidth < 1024)
}" class="bg-zyngga-blue-50 min-h-screen">

    <x-sidebar active="cek_pesanan" />

    {{-- Main Content Wrapper --}}
    <div 
        class="transition-all duration-300 ease-in-out min-h-screen flex flex-col"
        :class="desktopCollapsed ? 'md:pr-[80px]' : 'md:pr-[280px]'"
        @sidebar-toggled.window="desktopCollapsed = $event.detail.collapsed"
        @resize.window="desktopCollapsed = (window.innerWidth >= 768 && window.innerWidth < 1024)"
    >
        {{-- HEADER --}}
        <x-dashboard-header 
            title="Cek Pesanan" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :showMenu="true"
        />        
        
        {{-- MAIN CONTENT --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5" id="page-content">

                {{-- Search Form Card --}}
                <x-zyngga-card title="Informasi Pesanan">
                    <form action="{{ route('order.check') }}" method="POST" class="space-y-4">
                        @csrf
                        {{-- Name or Delivery ID --}}
                        <div>
                            <x-zyngga-text variant="sm" weight="regular" class="mb-2 block">Nama atau ID Pesanan</x-zyngga-text>
                            <x-zyngga-input 
                                name="query"
                                id="query"
                                :value="old('query')"
                                placeholder="Contoh: Fulan atau ZYG-12345"
                                :error="$errors->first('query')"
                            >
                                <x-slot:iconRight><span></span></x-slot:iconRight>
                                <x-slot:iconLeft>
                                    <i data-feather="search" class="w-4 h-4 text-zyngga-neutral-400"></i>
                                </x-slot:iconLeft>
                            </x-zyngga-input>
                        </div>

                        {{-- Phone Verification --}}
                        <div>
                            <x-zyngga-text variant="sm" weight="regular" class="mb-2 block">4 Digit Terakhir Nomor WhatsApp</x-zyngga-text>
                            <x-zyngga-input 
                                name="phone_last_4"
                                id="phone_last_4"
                                :value="old('phone_last_4')"
                                type="tel"
                                maxlength="4"
                                placeholder="Contoh: 4321"
                                :error="$errors->first('phone_last_4')"
                            >
                                <x-slot:iconRight><span></span></x-slot:iconRight>
                                <x-slot:iconLeft>
                                    <i data-feather="phone" class="w-4 h-4 text-zyngga-neutral-400"></i>
                                </x-slot:iconLeft>
                            </x-zyngga-input>
                            <div class="flex items-center gap-2 mt-2">
                                <i data-feather="info" class="w-4 h-4 text-[#1660C1]"></i>
                                <x-zyngga-text variant="xs" color="primary">Gunakan 4 digit terakhir nomor kamu untuk verifikasi</x-zyngga-text>
                            </div>
                        </div>

                        <div class="pt-2">
                            <x-zyngga-button 
                                type="submit"
                                variant="primary"
                                size="l"
                                label="Cari Pesanan"
                                class="w-full"
                                icon="search"
                                iconPosition="left"
                            />
                        </div>
                    </form>
                </x-zyngga-card>
                
                {{-- Order Tracking Results --}}
                @if(isset($orders) && count($orders) > 0)
                    @foreach($orders as $order)
                        <x-zyngga-card 
                            onclick="window.location.href='{{ route('order.detail', ['id' => $order['id']]) }}'"
                            class="cursor-pointer"
                        >
                            {{-- Top Part: User & Order Info (Extra Elements) --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex flex-col">
                                    <x-zyngga-text variant="base" weight="medium" class="text-zyngga-neutral-900">{{ $order['customer_name'] }}</x-zyngga-text>
                                    <x-zyngga-text variant="sm" color="neutral-500">*** {{ $order['phone_last_4'] }}</x-zyngga-text>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <x-zyngga-text variant="base" weight="medium" class="text-zyngga-neutral-900">{{ $order['id'] }}</x-zyngga-text>
                                    <button 
                                        @click.stop="navigator.clipboard.writeText('{{ $order['id'] }}'); $dispatch('toast', { message: 'ID Pesanan berhasil disalin', type: 'success' })"
                                        class="text-zyngga-blue-300 hover:text-zyngga-blue-400 transition-colors"
                                    >
                                        <i data-feather="copy" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            <x-zyngga-divider class="mb-5" />

                            {{-- Card Content (Identical to History Page) --}}
                            <div class="flex items-start justify-between mb-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                                        <x-zyngga-service-icon :service="$order['service']" class="w-[18px] h-[18px] text-zyngga-yellow-300" />
                                    </div>
                                    <div class="flex flex-col">
                                        <x-zyngga-text variant="lg" weight="medium">{{ $order['service'] }}</x-zyngga-text>
                                        <x-zyngga-text variant="sm" color="neutral-500">{{ $order['date'] }}</x-zyngga-text>
                                    </div>
                                </div>
                                <x-zyngga-status type="secondary" size="M" icon="loader" :label="$order['status']" />
                            </div>
                            
                            <div class="flex items-center gap-4 mb-5">
                                <div class="progress-container flex-1 bg-zyngga-blue-50 h-1 rounded-full overflow-hidden">
                                    <div class="bg-zyngga-blue-300 h-full" style="width: {{ $order['progress'] }}%"></div>
                                </div>
                                <x-zyngga-text variant="base" weight="medium">{{ $order['progress'] }}%</x-zyngga-text>
                            </div>
                            
                        </x-zyngga-card>
                    @endforeach
                @endif

                {{-- Info Card --}}
                <x-zyngga-card>
                    <div class="flex items-center justify-between min-h-[56px]">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-zyngga-blue-50 flex items-center justify-center shrink-0">
                                <i data-feather="help-circle" class="w-5 h-5 text-zyngga-blue-300"></i>
                            </div>
                            <div class="flex flex-col">
                                <x-zyngga-text variant="sm" weight="medium" class="leading-snug">Butuh bantuan?</x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500" class="leading-snug mt-0.5">Hubungi kami via WhatsApp</x-zyngga-text>
                            </div>
                        </div>
                        <x-zyngga-button 
                            type="a"
                            href="https://wa.me/+6281297673318"
                            target="_blank"
                            variant="secondary"
                            size="m"
                            icon="message-circle"
                            label="Chat"
                            iconPosition="left"
                        />
                    </div>
                </x-zyngga-card>

            </div>
        </main>

        {{-- FOOTER NAVIGATION (Mobile) --}}
        <x-zyngga-footer active="order" />
        <x-zyngga-toast />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
    @livewireScripts
</body>
</html>
