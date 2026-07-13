<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ubah Metode Pembayaran – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; min-height: 100%; }
        [x-cloak] { display: none !important; }

        /* ── sticky footer ── */
        #sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: white;
            border-top: 1px solid #F4F4F4;
            border-radius: 16px 16px 0 0;
            padding: 16px 0px calc(16px + env(safe-area-inset-bottom, 0px));
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        /* Reset sticky footer position (no sidebar) */
        @media (min-width: 768px) {
            #sticky-footer {
                left: 0;
                right: 0;
                transform: none;
            }
        }

        .divider {
            height: 1px;
            background-color: #F4F4F4;
            width: 100%;
            margin: 12px 0;
        }

        i[data-feather] {
            display: inline-block;
            width: 1em;
            height: 1em;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{ paymentMethod: '{{ strtolower($order['payment_method']) === 'qris' ? 'qris' : (strtolower($order['payment_method']) === 'transfer' ? 'transfer' : 'qris') }}' }">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Ubah Metode Pembayaran" 
            :backUrl="route('order.detail', ['id' => $order['nota_layanan']])" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5">
                
                <form method="POST" action="{{ route('order.payment.update', ['id' => $order['nota_layanan']]) }}" id="page-content" class="flex-1 flex flex-col">
                    @csrf
                    
                    {{-- CARD: METODE PEMBAYARAN --}}
                    <x-zyngga-card title="Metode Pembayaran">
                        @php
                            $payments = [
                                ['id' => 'qris', 'label' => 'Non tunai',  'desc' => 'Bayar via aplikasi (QRIS & transfer)',
                                 'feather' => 'grid', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"]
                            ];
                        @endphp

                        <div class="flex flex-col">
                            @foreach($payments as $i => $pay)
                                <x-zyngga-radio-row 
                                    x-model="paymentMethod"
                                    name="payment_method"
                                    id="payment-{{ $pay['id'] }}"
                                    value="{{ $pay['id'] }}"
                                    :label="$pay['label']"
                                    :description="$pay['desc']"
                                    :checked="$pay['id'] === strtolower($order['payment_method'])"
                                >
                                    <x-slot:icon>
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-yellow-50">
                                            <i data-feather="{{ $pay['feather'] }}" class="w-[18px] h-[18px] text-zyngga-yellow-300"></i>
                                        </div>
                                    </x-slot:icon>
                                </x-zyngga-radio-row>
                                @if ($i < count($payments) - 1)
                                    <x-zyngga-divider class=" my-2" />
                                @endif
                            @endforeach
                        </div>
                    </x-zyngga-card>
                </form>

            </div>

            {{-- STICKY FOOTER --}}
            <div id="sticky-footer">
                <div class="max-w-5xl mx-auto w-full px-5">
                    <x-zyngga-button 
                        type="button"
                        variant="primary"
                        size="l"
                        class="w-full"
                        @click="document.getElementById('page-content').submit()"
                        label="Konfirmasi"
                    />
                </div>
            </div>
        </main>
        
        <x-zyngga-toast />
    </div>

    @livewireScripts
    <script>
        function initFeather() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }

        document.addEventListener('DOMContentLoaded', () => setTimeout(initFeather, 100));
        document.addEventListener('livewire:navigated', () => setTimeout(initFeather, 100));
        document.addEventListener('livewire:initialized', () => {
            initFeather();
            Livewire.hook('morph.updated', (el, component) => {
                initFeather();
            });
        });

        let count = 0;
        let interval = setInterval(() => {
            initFeather();
            if (++count > 5) clearInterval(interval);
        }, 500);
    </script>
</body>
</html>
