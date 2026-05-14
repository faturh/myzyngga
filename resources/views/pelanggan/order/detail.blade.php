<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Pesanan – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    
    {{-- Memuat Feather lebih awal --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; min-height: 100%; }
        [x-cloak] { display: none !important; }

        #sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #F4F4F4;
            border-radius: 20px 20px 0 0;
            padding: 16px 0 calc(16px + env(safe-area-inset-bottom, 0px));
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
        }

        .divider {
            height: 1px;
            background-color: #F4F4F4;
            width: 100%;
            margin: 12px 0;
        }

        .rotate-icon svg, .rotate-icon i {
            transition: transform 0.3s ease;
            transform: rotate(180deg);
        }

        {{-- Cadangan jika ikon tidak muncul, minimal ada jarak --}}
        i[data-feather] {
            display: inline-block;
            width: 1em;
            height: 1em;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{ 
        status: '{{ $order['status'] }}',
        isPaid: {{ $order['payment_status'] === 'Lunas' ? 'true' : 'false' }},
        showStatusDetail: false,
        showPaymentDetail: false
    }">
        {{-- HEADER --}}
        <x-dashboard-header 
            title="Detail Pesanan" 
            :backUrl="Auth::check() ? route('order.history') : route('order.check')" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- Gunakan struktur flex-col pada main agar identik dengan booking --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[88px]">
                
                {{-- CARD 1: ORDER INFO --}}
                <x-zyngga-card padding="p-4" gap="py-[6px]">
                    <div class="flex items-start justify-between mb-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-zyngga-yellow-50 flex items-center justify-center">
                                    <x-zyngga-service-icon service="{{ $order['service_type'] }}" class="w-4 h-4 text-zyngga-yellow-300" />
                                </div>
                                <x-zyngga-text variant="lg" weight="medium" color="neutral-900">{{ $order['service_type'] }}</x-zyngga-text>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['id'] }}</x-zyngga-text>
                                <button 
                                    @click="navigator.clipboard.writeText('{{ $order['id'] }}'); $dispatch('toast', { message: 'ID Pesanan berhasil disalin', type: 'success' })"
                                    class="text-zyngga-blue-300 hover:text-zyngga-blue-400 transition-colors"
                                >
                                    <i data-feather="copy" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <x-zyngga-status 
                            :type="$order['status'] === 'finished' ? 'secondary' : 'secondary'" 
                            size="L" 
                            :icon="$order['status'] === 'finished' ? 'shopping-bag' : 'truck'" 
                            :label="$order['status'] === 'finished' ? 'Ambil di Outlet' : $order['status_label']" 
                        />
                    </div>

                    <div class="divider"></div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $order['customer_name'] }}</x-zyngga-text>
                            <x-zyngga-text variant="sm" color="neutral-500">{{ $order['customer_phone'] }}</x-zyngga-text>
                        </div>

                        <div class="divider"></div>

                        <div class="space-y-1">
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $order['address'] }}</x-zyngga-text>
                            <x-zyngga-text variant="sm" color="neutral-500" class="leading-relaxed">{{ $order['address_detail'] }}</x-zyngga-text>
                        </div>

                        <div class="divider"></div>

                        <div class="space-y-2.5">
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Tanggal Pemesanan</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['order_date'] }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Estimasi Selesai</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['estimated_finished'] }}</x-zyngga-text>
                            </div>
                        </div>
                    </div>
                </x-zyngga-card>

                {{-- CARD 2: STATUS PENGERJAAN --}}
                <x-zyngga-card title="Status Pengerjaan">
                    <x-slot:headerAction>
                        <x-zyngga-status type="primary" size="L">
                            <span x-text="status === 'finished' ? '100%' : '56%'"></span>
                        </x-zyngga-status>
                    </x-slot:headerAction>

                    <div class="space-y-4">
                        @php $lastDate = null; @endphp
                        
                        {{-- Log pertama (selalu tampil) --}}
                        @php 
                            $firstLog = $order['logs'][0]; 
                            $lastDate = $firstLog['date'];
                        @endphp
                        <div class="space-y-3">
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $firstLog['date'] }}</x-zyngga-text>
                            <div class="flex gap-4">
                                <x-zyngga-text variant="sm" color="neutral-500" class="w-10 pt-3">{{ $firstLog['time'] }}</x-zyngga-text>
                                <div class="flex-1 bg-zyngga-neutral-200 rounded-xl p-4">
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $firstLog['note'] }}</x-zyngga-text>
                                </div>
                            </div>
                        </div>

                        <div x-show="showStatusDetail" x-collapse x-cloak class="space-y-3">
                            @foreach(array_slice($order['logs'], 1) as $log)
                            <div class="space-y-3">
                                @if($log['date'] !== $lastDate)
                                    <div class="divider !my-6"></div>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $log['date'] }}</x-zyngga-text>
                                    @php $lastDate = $log['date']; @endphp
                                @endif
                                <div class="flex gap-4">
                                    <x-zyngga-text variant="sm" color="neutral-500" class="w-10 pt-3">{{ $log['time'] }}</x-zyngga-text>
                                    <div class="flex-1 bg-zyngga-neutral-200 rounded-xl p-4">
                                        <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $log['note'] }}</x-zyngga-text>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="flex justify-center pt-2">
                            <x-zyngga-button 
                                variant="tertiary" 
                                size="m" 
                                icon="chevron-down"
                                iconPosition="right"
                                ::class="showStatusDetail ? 'rotate-icon' : ''"
                                @click="showStatusDetail = !showStatusDetail"
                            >
                                <span x-text="showStatusDetail ? 'Sembunyikan' : 'Lihat Detail'"></span>
                            </x-zyngga-button>
                        </div>
                    </div>
                </x-zyngga-card>

                {{-- CARD 3: RINCIAN PEMBAYARAN --}}
                <x-zyngga-card title="Rincian Pembayaran">
                    <x-slot:headerAction>
                        <x-zyngga-status :type="($order['payment_status'] === 'Lunas' || $order['status'] === 'finished') ? 'success' : 'error'" size="L">
                            <span x-text="(isPaid || status === 'finished') ? 'Lunas' : 'Belum Bayar'"></span>
                        </x-zyngga-status>
                    </x-slot:headerAction>

                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <div class="space-y-1">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $order['items'][0]['name'] }}</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['items'][0]['qty'] }} x Rp{{ number_format($order['items'][0]['price'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['total'], 0, ',', '.') }}</x-zyngga-text>
                        </div>

                        <div x-show="showPaymentDetail" x-collapse x-cloak class="space-y-3 pt-2">
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-500">Subtotal</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['subtotal'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-500">Diskon</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['discount'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between pb-2">
                                <x-zyngga-text variant="sm" color="neutral-500">Pajak</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['tax'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <div class="flex justify-between pt-2">
                                <x-zyngga-text variant="sm" color="neutral-500">Metode Pembayaran</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900" x-text="isPaid ? 'QRIS' : 'Cash'"></x-zyngga-text>
                            </div>
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Total</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['total'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            <template x-if="isPaid">
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Tunai</x-zyngga-text>
                                        <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['cash'], 0, ',', '.') }}</x-zyngga-text>
                                    </div>
                                    <div class="flex justify-between">
                                        <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Kembalian</x-zyngga-text>
                                        <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['change'], 0, ',', '.') }}</x-zyngga-text>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-center pt-2">
                            <x-zyngga-button 
                                variant="tertiary" 
                                size="m" 
                                icon="chevron-down"
                                iconPosition="right"
                                ::class="showPaymentDetail ? 'rotate-icon' : ''"
                                @click="showPaymentDetail = !showPaymentDetail"
                            >
                                <span x-text="showPaymentDetail ? 'Sembunyikan' : 'Lihat Detail'"></span>
                            </x-zyngga-button>
                        </div>
                    </div>
                </x-zyngga-card>

                {{-- CARD 4: BANTUAN/LAYANAN --}}
                <x-zyngga-card title="Bantuan/Layanan">
                    <div class="flex flex-col gap-3">
                        <template x-if="status !== 'finished'">
                            <div class="flex flex-col gap-3">
                                <x-zyngga-dropdown-item icon="arrow-up-circle" size="M">Upgrade Layanan</x-zyngga-dropdown-item>
                                <x-zyngga-dropdown-item icon="help-circle" size="M">Ubah Metode Pembayaran</x-zyngga-dropdown-item>
                            </div>
                        </template>
                        <template x-if="status === 'finished'">
                            <div class="flex flex-col gap-3">
                                <x-zyngga-dropdown-item 
                                    type="a" 
                                    href="https://wa.me/+6281297673318" 
                                    target="_blank" 
                                    icon="message-square" 
                                    size="M"
                                >Hubungi Kami</x-zyngga-dropdown-item>
                                <x-zyngga-dropdown-item icon="info" size="M">Ajukan Komplain</x-zyngga-dropdown-item>
                            </div>
                        </template>
                    </div>
                </x-zyngga-card>

                {{-- CARD 5: SYARAT DAN KETENTUAN --}}
                <x-zyngga-card title="Syarat dan Ketentuan">
                    <div>
                        @foreach(['Pengambilan barang harap disertai nota', 'Barang yang tidak diambil selama 1 bulan, hilang/rusak tidak diganti', 'Barang hilang/rusak karena proses pengerjaan diganti maksimal 5x biaya', 'Klaim luntur tidak dipisah di luar tanggungan', 'Hak klaim berlaku 1x24 jam setelah barang diambil', 'Setiap konsumen dianggap setuju dengan poin tersebut di atas'] as $index => $text)
                        <div class="flex gap-2">
                            <x-zyngga-text variant="sm" color="neutral-500">{{ $index + 1 }}.</x-zyngga-text>
                            <x-zyngga-text variant="sm" color="neutral-500" class="leading-relaxed">{{ $text }}</x-zyngga-text>
                        </div>
                        @endforeach
                    </div>
                </x-zyngga-card>

            </div>

            {{-- STICKY FOOTER --}}
            <div id="sticky-footer">
                <div class="max-w-5xl mx-auto px-5 flex items-center gap-4">
                    <template x-if="status !== 'finished'">
                        <div class="w-full flex items-center gap-4">
                            <x-zyngga-button 
                                type="a"
                                href="https://wa.me/+6281297673318"
                                target="_blank"
                                variant="secondary" 
                                class="w-[120px] !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                                size="l"
                                icon="message-circle"
                                iconPosition="left"
                                label="Chat"
                            />
                            
                            <div class="flex-1 h-12">
                                <template x-if="!isPaid">
                                    <x-zyngga-button 
                                        type="button"
                                        size="l"
                                        variant="primary" 
                                        class="w-full h-full font-medium"
                                    >
                                        <x-zyngga-text variant="base" weight="medium" color="white">Bayar Sekarang</x-zyngga-text>
                                    </x-zyngga-button>
                                </template>
                                <template x-if="isPaid">
                                    <x-zyngga-status type="primary" size="L" class="w-full h-full">
                                        Sudah Dibayar
                                    </x-zyngga-status>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="status === 'finished'">
                        <div class="w-full flex items-center gap-4">
                            <x-zyngga-button 
                                type="button"
                                variant="secondary" 
                                class="flex-1 !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                                size="l"
                                icon="download"
                                iconPosition="left"
                                label="Unduh Nota"
                                @click="window.dispatchEvent(new CustomEvent('open-modal-download'))"
                            />
                            <x-zyngga-button 
                                type="button"
                                size="l"
                                variant="primary" 
                                class="flex-1 !h-12 font-medium"
                                label="Ulangi Pesanan"
                            />
                        </div>
                    </template>
                </div>
            </div>
        </main>

        <x-zyngga-toast />

        {{-- MODAL DOWNLOAD NOTA --}}
        <x-zyngga-selection-modal 
            id="modal-download" 
            openEvent="open-modal-download"
        >
            <div class="flex flex-col items-center text-center">
                <div class="space-y-2 mb-8 px-2">
                    <x-zyngga-text variant="lg" weight="bold" color="neutral-900" class="!text-[20px] leading-snug">
                        Simpan Nota Transaksi
                    </x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="!text-[#717171] leading-relaxed">
                        Kamu bisa mengunduh atau membagikan nota ini sebagai bukti transaksi.
                    </x-zyngga-text>
                </div>

                <div class="flex flex-col gap-3 w-full">
                    <x-zyngga-button 
                        variant="secondary" 
                        size="l" 
                        class="w-full !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                        icon="send"
                        iconPosition="left"
                        label="Bagikan Nota"
                        @click="isOpen = false"
                    />
                    <x-zyngga-button 
                        variant="primary" 
                        size="l" 
                        class="w-full !h-12"
                        icon="download"
                        iconPosition="left"
                        label="Unduh Nota"
                        @click="isOpen = false"
                    />
                </div>
            </div>
        </x-zyngga-selection-modal>
    </div>

    @livewireScripts
    <script>
        function initFeather() {
            if (typeof feather !== 'undefined') {
                feather.replace();
                console.log('Feather replaced');
            } else {
                console.error('Feather is not defined');
            }
        }

        // Jalankan dengan delay kecil untuk memastikan DOM sudah stabil
        document.addEventListener('DOMContentLoaded', () => setTimeout(initFeather, 100));
        document.addEventListener('livewire:navigated', () => setTimeout(initFeather, 100));
        document.addEventListener('livewire:initialized', () => {
            initFeather();
            Livewire.hook('morph.updated', (el, component) => {
                initFeather();
            });
        });

        // Polling sebagai cadangan terakhir
        let count = 0;
        let interval = setInterval(() => {
            initFeather();
            if (++count > 5) clearInterval(interval);
        }, 500);
    </script>
</body>
</html>