<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Metode Pembayaran – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; min-height: 100%; }
        
        .method-card {
            border: 2px solid #F4F4F4;
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
        }
        .method-card:hover { border-color: #D1D5DB; }
        .method-card.selected {
            border-color: #1660C1;
            background: #F0F5FA;
        }
        
        input[type="radio"] { display: none; }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{ selectedMethod: 'qris', isSubmitting: false }">
        <x-dashboard-header 
            title="Metode Pembayaran" 
            :backUrl="url()->previous() !== url()->current() ? url()->previous() : route('order.detail', $order['id'])" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="flex-1 flex flex-col relative w-full max-w-5xl mx-auto px-5 pb-[88px]">
            <x-zyngga-card padding="p-4">
                <form id="payment-form" action="{{ route('order.process-payment', $order['id']) }}">
                    @csrf
                    
                    <div class="flex flex-col space-y-2">
                        <div x-data="{ openEWallet: true }">
                            <div class="flex items-center justify-between cursor-pointer py-2 mb-2" @click="openEWallet = !openEWallet">
                                <x-zyngga-text variant="base" weight="medium" color="neutral-900">QRIS</x-zyngga-text>
                                <svg class="w-5 h-5 text-zyngga-neutral-500 transition-transform duration-200" :class="{'rotate-180': openEWallet}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            
                            <div x-show="openEWallet" x-collapse class="flex flex-col space-y-2 mt-2 pl-8">
                                <x-zyngga-radio-row 
                                    name="method"
                                    value="qris"
                                    label="QRIS"
                                    size="48"
                                    x-model="selectedMethod"
                                >
                                    <x-slot:icon>
                                        <img src="{{ asset('images/logos/qris.png') }}" alt="QRIS" class="w-10 h-10 rounded-xl object-contain">
                                    </x-slot:icon>
                                </x-zyngga-radio-row>

                            </div>
                        </div>

                        <div x-data="{ openVA: true }">
                            <div class="flex items-center justify-between cursor-pointer py-2 mb-2" @click="openVA = !openVA">
                                <x-zyngga-text variant="base" weight="medium" color="neutral-900">Virtual Account</x-zyngga-text>
                                <svg class="w-5 h-5 text-zyngga-neutral-500 transition-transform duration-200" :class="{'rotate-180': openVA}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            
                            <div x-show="openVA" x-collapse class="flex flex-col space-y-2 mt-2 pl-8">
                                <x-zyngga-radio-row 
                                    name="method"
                                    value="bca_va"
                                    label="BCA"
                                    size="48"
                                    x-model="selectedMethod"
                                >
                                    <x-slot:icon>
                                        <img src="{{ asset('images/logos/bca.png') }}" alt="BCA" class="w-10 h-10 rounded-xl object-contain">
                                    </x-slot:icon>
                                </x-zyngga-radio-row>

                                <x-zyngga-divider class="my-1" />

                                <x-zyngga-radio-row 
                                    name="method"
                                    value="bni_va"
                                    label="BNI"
                                    size="48"
                                    x-model="selectedMethod"
                                >
                                    <x-slot:icon>
                                        <img src="{{ asset('images/logos/bni.png') }}" alt="BNI" class="w-10 h-10 rounded-xl object-contain">
                                    </x-slot:icon>
                                </x-zyngga-radio-row>

                                <x-zyngga-divider class="my-1" />

                                <x-zyngga-radio-row 
                                    name="method"
                                    value="mandiri_va"
                                    label="Mandiri"
                                    size="48"
                                    x-model="selectedMethod"
                                >
                                    <x-slot:icon>
                                        <img src="{{ asset('images/logos/mandiri.png') }}" alt="Mandiri" class="w-10 h-10 rounded-xl object-contain">
                                    </x-slot:icon>
                                </x-zyngga-radio-row>

                                <x-zyngga-divider class="my-1" />

                                <x-zyngga-radio-row 
                                    name="method"
                                    value="permata_va"
                                    label="Permata"
                                    size="48"
                                    x-model="selectedMethod"
                                >
                                    <x-slot:icon>
                                        <img src="{{ asset('images/logos/permata.png') }}" alt="Permata" class="w-10 h-10 rounded-xl object-contain">
                                    </x-slot:icon>
                                </x-zyngga-radio-row>



                                <x-zyngga-radio-row 
                                    name="method"
                                    value="bsi_va"
                                    label="BSI"
                                    size="48"
                                    x-model="selectedMethod"
                                >
                                    <x-slot:icon>
                                        <img src="{{ asset('images/logos/bsi.png') }}" alt="BSI" class="w-10 h-10 rounded-xl object-contain">
                                    </x-slot:icon>
                                </x-zyngga-radio-row>

                                <x-zyngga-divider class="my-1" />

                                <x-zyngga-radio-row 
                                    name="method"
                                    value="seabank_va"
                                    label="SeaBank"
                                    size="48"
                                    x-model="selectedMethod"
                                >
                                    <x-slot:icon>
                                        <img src="{{ asset('images/logos/seabank.png') }}" alt="SeaBank" class="w-10 h-10 rounded-xl object-contain">
                                    </x-slot:icon>
                                </x-zyngga-radio-row>



                                <x-zyngga-divider class="my-1" />

                                <x-zyngga-radio-row 
                                    name="method"
                                    value="other_va"
                                    label="Bank Lainnya"
                                    size="48"
                                    x-model="selectedMethod"
                                >
                                    
                                </x-zyngga-radio-row>
                            </div>
                        </div>
                    </div>
                </form>
            </x-zyngga-card>

            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-zyngga-neutral-200 py-4 z-40">
                <div class="w-full max-w-5xl mx-auto px-5 flex items-center justify-between">
                    <div>
                        <x-zyngga-text variant="sm" color="neutral-500">Total Pembayaran</x-zyngga-text>
                        <x-zyngga-text variant="lg" weight="medium" color="neutral-900">Rp{{ number_format(max(0, (float)$order['total'] - (float)$order['cash']), 0, ',', '.') }}</x-zyngga-text>
                    </div>
                    <x-zyngga-button 
                        type="button"
                        variant="primary"
                        size="l"
                        class="w-1/2"
                        label="Lanjutkan"
                        x-bind:disabled="!selectedMethod || isSubmitting"
                        @click="submitPayment()"
                    />
                </div>
            </div>
        </main>
        <x-zyngga-toast />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => { feather.replace(); });

        window.addEventListener('toast', (e) => {
            const { message, type = 'success' } = e.detail;
            const container = document.createElement('div');
            // Reusing toast logic from other pages
            const isError = type === 'error';
            const bgColor = isError ? 'bg-[#FEF2F2]' : 'bg-[#F0FDF4]';
            const borderColor = isError ? 'border-[#FEE2E2]' : 'border-[#DCFCE7]';
            const iconColor = isError ? 'text-[#EF4444]' : 'text-[#22C55E]';
            const textColor = isError ? 'text-[#991B1B]' : 'text-[#166534]';
            const iconName = isError ? 'alert-circle' : 'check-circle';

            container.className = `fixed top-4 left-1/2 -translate-x-1/2 z-[100] flex items-start gap-3 p-4 rounded-xl border ${bgColor} ${borderColor} shadow-sm transition-all duration-300 w-[calc(100%-40px)] max-w-[400px]`;
            container.innerHTML = `
                <i data-feather="${iconName}" class="w-5 h-5 ${iconColor} shrink-0 mt-0.5"></i>
                <p class="text-sm font-medium ${textColor} flex-1 leading-snug">${message}</p>
                <button onclick="this.parentElement.remove()" class="p-1 hover:bg-black/5 rounded-full transition-colors shrink-0">
                    <i data-feather="x" class="w-4 h-4 ${iconColor} opacity-70"></i>
                </button>
            `;
            document.body.appendChild(container);
            feather.replace();
            setTimeout(() => { container.remove() }, 3000);
        });

        function submitPayment() {
            const form = document.getElementById('payment-form');
            const formData = new FormData(form);
            const button = document.querySelector('button[label="Lanjutkan"]'); // Mock state
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.deeplink) {
                        window.open(data.deeplink, '_blank');
                    }
                    window.location.href = data.redirect;
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message || 'Gagal memproses pembayaran.', type: 'error' }
                    }));
                }
            }).catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Terjadi kesalahan sistem.', type: 'error' } }));
            });
        }
    </script>
</body>
</html>
