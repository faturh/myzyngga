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
    <!-- Midtrans Snap -->
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{ 
        status: '{{ $order['status'] }}',
        rawStatus: '{{ $order['raw_status'] }}',
        isPaid: {{ $order['payment_status'] === 'Lunas' ? 'true' : 'false' }},
        paymentMethod: '{{ strtoupper($order['payment_method'] ?? 'CASH') }}',
        showStatusDetail: false,
        showPaymentDetail: false,
        snapToken: '{{ $order['snap_token'] ?? '' }}',
        pay() {
            if (!this.snapToken) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Pembayaran sudah lunas atau token tidak tersedia.', type: 'error' } }));
                return;
            }
            snap.pay(this.snapToken, {
                onSuccess: function(result) {
                    window.location.reload();
                },
                onPending: function(result) {
                    window.location.reload();
                },
                onError: function(result) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Pembayaran gagal!', type: 'error' } }));
                },
                onClose: function() {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Pembayaran dibatalkan.', type: 'error' } }));
                }
            });
        }
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
                
                @if($errors->has('order'))
                    <div x-init="$dispatch('toast', { message: '{{ $errors->first('order') }}', type: 'error' })"></div>
                @endif
                @if(session('success'))
                    <div x-init="$dispatch('toast', { message: '{{ session('success') }}', type: 'success' })"></div>
                @endif
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
                            :icon="$order['is_roundtrip'] ? 'truck' : 'shopping-bag'" 
                            :label="$order['is_roundtrip'] ? 'Delivery' : 'Ambil di Outlet'" 
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
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ explode(',', $order['address'])[0] }}</x-zyngga-text>
                            <x-zyngga-text variant="sm" color="neutral-500" class="leading-relaxed">
                                {{ $order['address'] }}
                                @if($order['address_detail'] !== '-')
                                    (Patokan: {{ $order['address_detail'] }})
                                @endif
                            </x-zyngga-text>
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
                            {{ $order['progress'] }}%
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

                @if(!in_array($order['raw_status'], ['Baru', 'created']))
                {{-- CARD 3: RINCIAN PEMBAYARAN --}}
                <x-zyngga-card title="Rincian Pembayaran">
                    <x-slot:headerAction>
                        <x-zyngga-status :type="($order['payment_status'] === 'Lunas' || $order['status'] === 'finished') ? 'success' : 'error'" size="L">
                            <span x-text="(isPaid || status === 'finished') ? 'Lunas' : 'Belum Bayar'"></span>
                        </x-zyngga-status>
                    </x-slot:headerAction>

                    <div class="space-y-4">
                        @foreach($order['items'] as $item)
                        <div class="flex justify-between items-center">
                            <x-zyngga-text variant="sm" color="neutral-900">{{ $item['name'] }}</x-zyngga-text>
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</x-zyngga-text>
                        </div>
                        @endforeach

                        <div x-show="showPaymentDetail" x-collapse x-cloak class="space-y-3 pt-2">
                            @if(isset($order['upgrade_fee']) && $order['upgrade_fee'] > 0)
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-900">Biaya Upgrade</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['upgrade_fee'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-900">Biaya Pengiriman</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp0</x-zyngga-text>
                            </div>
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-900">Diskon</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['discount'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between pb-2">
                                <x-zyngga-text variant="sm" color="neutral-900">Pajak</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['tax'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <div class="flex justify-between pt-2">
                                <x-zyngga-text variant="sm" color="neutral-900">Metode Pembayaran</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $order['payment_method'] }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-900">Total</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['total'], 0, ',', '.') }}</x-zyngga-text>
                            </div>

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
                @endif

                @if(!in_array($order['raw_status'], ['Baru', 'created']) && ($order['status'] === 'finished' || $order['can_upgrade'] || !$order['is_roundtrip']))
                {{-- CARD 4: BANTUAN/LAYANAN --}}
                <x-zyngga-card title="Bantuan/Layanan">
                    <div class="flex flex-col gap-3">
                        <template x-if="status !== 'finished'">
                            <div class="flex flex-col gap-3">
                                @if($order['can_upgrade'])
                                <x-zyngga-dropdown-item type="a" href="{{ route('order.upgrade', $order['id']) }}" icon="arrow-up-circle" size="M">Upgrade Layanan</x-zyngga-dropdown-item>
                                @endif

                                @if(!$order['is_roundtrip'])
                                <x-zyngga-dropdown-item 
                                    type="a" 
                                    href="{{ route('order.request.delivery', $order['id']) }}" 
                                    icon="truck" 
                                    size="M"
                                >Ajukan Pengantaran</x-zyngga-dropdown-item>
                                @endif
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
                                
                                @if(!$order['is_roundtrip'])
                                <x-zyngga-dropdown-item 
                                    type="a" 
                                    href="{{ route('order.request.delivery', $order['id']) }}" 
                                    icon="truck" 
                                    size="M"
                                >Ajukan Pengantaran</x-zyngga-dropdown-item>
                                @endif

                                <x-zyngga-dropdown-item type="a" href="{{ route('order.complaint', $order['id']) }}" icon="alert-circle" size="M">Ajukan Komplain</x-zyngga-dropdown-item>
                            </div>
                        </template>
                    </div>
                </x-zyngga-card>
                @endif

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
                                        x-bind:disabled="paymentMethod === 'CASH' || ['Baru', 'created'].includes(rawStatus)"
                                        x-bind:class="paymentMethod === 'CASH' || ['Baru', 'created'].includes(rawStatus) ? 'opacity-50 cursor-not-allowed' : ''"
                                        @click="pay()"
                                    >
                                        <x-zyngga-text variant="base" weight="medium" color="white">Bayar Sekarang</x-zyngga-text>
                                    </x-zyngga-button>
                                </template>
                                <template x-if="isPaid">
                                    <x-zyngga-button 
                                        type="button"
                                        size="l"
                                        variant="primary" 
                                        class="w-full h-full font-medium opacity-50 cursor-not-allowed"
                                        disabled
                                    >
                                        <x-zyngga-text variant="base" weight="medium" color="white">Sudah Dibayar</x-zyngga-text>
                                    </x-zyngga-button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="status === 'finished'">
                        @php
                            $repeatService = 'reguler';
                            $serviceLower = strtolower($order['service_type'] ?? 'reguler');
                            if (str_contains($serviceLower, 'kilat')) {
                                $repeatService = 'kilat';
                            } elseif (str_contains($serviceLower, 'express') || str_contains($serviceLower, 'quick')) {
                                $repeatService = 'express';
                            } elseif (str_contains($serviceLower, 'satuan')) {
                                $repeatService = 'satuan';
                            }
                        @endphp
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
                                type="a"
                                href="{{ route('order.pickup', $repeatService) }}"
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
                    <x-zyngga-text variant="lg" weight="medium" color="neutral-900" class="!text-[20px] leading-snug">
                        Simpan Nota Transaksi
                    </x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="!text-[#717171] leading-relaxed">
                        Kamu bisa mengunduh atau membagikan nota ini sebagai bukti transaksi.
                    </x-zyngga-text>
                </div>

                <div class="flex flex-col gap-3 w-full">
                    <x-zyngga-button 
                        variant="secondary" 
                        size="m" 
                        class="w-full !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                        icon="send"
                        iconPosition="left"
                        label="Bagikan Nota"
                        @click="
                            isOpen = false;
                            const shareUrl = '{{ route('public.cetak-struk', $order['id']) }}';
                            if (navigator.share) {
                                navigator.share({
                                    title: 'Nota Transaksi Zyngga',
                                    text: 'Berikut adalah nota transaksi Zyngga dengan ID {{ $order['id'] }}',
                                    url: shareUrl
                                }).catch(err => console.log(err));
                            } else {
                                navigator.clipboard.writeText(shareUrl);
                                $dispatch('toast', { message: 'Link nota berhasil disalin ke clipboard', type: 'success' });
                            }
                        "
                    />
                    <x-zyngga-button 
                        type="a"
                        href="{{ route('public.cetak-struk', $order['id']) }}"
                        target="_blank"
                        variant="primary" 
                        size="m" 
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
