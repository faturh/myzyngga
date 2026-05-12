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
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; min-height: 100%; }
        [x-cloak] { display: none !important; }

        /* ── custom styles ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.delivery { background: #EBF5FF; color: #1660C1; }
        .status-badge.pickup { background: #EBF5FF; color: #1660C1; }
        .status-badge.unpaid { background: #FFF1F0; color: #F5222D; }
        .status-badge.paid { background: #F6FFED; color: #52C41A; }

        .progress-container {
            width: 100%;
            height: 6px;
            background: #E8EFF9;
            border-radius: 3px;
            overflow: hidden;
            margin: 12px 0;
        }
        .progress-bar {
            height: 100%;
            background: #1660C1;
            transition: width 0.3s ease;
        }

        #sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #F4F4F4;
            border-radius: 16px 16px 0 0;
            padding: 16px 20px calc(16px + env(safe-area-inset-bottom, 0px));
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body class="bg-[#e8eff9] pb-32">

    <div class="min-h-screen flex flex-col" x-data="{ 
        status: '{{ $order['status'] }}',
        showProgressDetail: false,
        showPaymentDetail: false
    }">
        {{-- HEADER --}}
        <x-dashboard-header 
            title="Detail Pesanan" 
            :backUrl="route('order.history')" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="flex-1 w-full max-w-5xl mx-auto px-5 mt-2 space-y-3">
            
            {{-- 1. HEADER CARD (Service & Main Info) --}}
            <x-zyngga-card gap="p-5">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-zyngga-yellow-50 flex items-center justify-center">
                            <i data-feather="zap" class="w-5 h-5 text-zyngga-yellow-300"></i>
                        </div>
                        <div>
                            <x-zyngga-text variant="base" weight="semibold">{{ $order['service_type'] }}</x-zyngga-text>
                            <div class="flex items-center gap-1 mt-0.5">
                                <x-zyngga-text variant="xs" color="neutral-400">{{ $order['id'] }}</x-zyngga-text>
                                <button class="p-1 hover:bg-gray-100 rounded" onclick="navigator.clipboard.writeText('{{ $order['id'] }}')">
                                    <i data-feather="copy" class="w-3 h-3 text-zyngga-neutral-400"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="status-badge delivery">
                        <i data-feather="{{ $order['status'] === 'finished' ? 'shopping-bag' : 'truck' }}" class="w-3.5 h-3.5"></i>
                        <span>{{ $order['status_label'] }}</span>
                    </div>
                </div>

                <x-zyngga-divider class="my-4" />

                <div class="space-y-4">
                    {{-- Customer --}}
                    <div>
                        <x-zyngga-text variant="sm" weight="semibold">{{ $order['customer_name'] }}</x-zyngga-text>
                        <x-zyngga-text variant="xs" color="neutral-500">{{ $order['customer_phone'] }}</x-zyngga-text>
                    </div>

                    {{-- Address (Only Ongoing) --}}
                    @if($order['status'] === 'ongoing')
                    <div>
                        <x-zyngga-text variant="sm" weight="semibold">{{ $order['address'] }}</x-zyngga-text>
                        <x-zyngga-text variant="xs" color="neutral-500">{{ $order['address_detail'] }}</x-zyngga-text>
                    </div>
                    @endif

                    {{-- Dates --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-zyngga-text variant="xs" color="neutral-500">Tanggal Pemesanan</x-zyngga-text>
                            <x-zyngga-text variant="xs" weight="medium" class="text-right mt-1" style="float:right">{{ explode('|', $order['order_date'])[0] }} | {{ explode('|', $order['order_date'])[1] }}</x-zyngga-text>
                        </div>
                        <div class="clear-both">
                            <x-zyngga-text variant="xs" color="neutral-500">Estimasi Selesai</x-zyngga-text>
                            <x-zyngga-text variant="xs" weight="medium" class="text-right mt-1" style="float:right">{{ explode('|', $order['estimated_finished'])[0] }} | {{ explode('|', $order['estimated_finished'])[1] }}</x-zyngga-text>
                        </div>
                    </div>
                </div>
            </x-zyngga-card>

            {{-- 2. STATUS PENGERJAAN --}}
            <x-zyngga-card title="Status Pengerjaan">
                <x-slot:headerAction>
                    <div class="bg-zyngga-blue-300 text-white px-2 py-0.5 rounded-full text-[10px] font-bold">
                        {{ $order['progress'] }}%
                    </div>
                </x-slot:headerAction>

                @if($order['status'] === 'ongoing')
                <div class="space-y-1 mb-4">
                    <x-zyngga-text variant="xs" weight="semibold">{{ $order['logs'][0]['date'] }}</x-zyngga-text>
                    <div class="flex gap-3">
                        <x-zyngga-text variant="xs" color="neutral-400">{{ $order['logs'][0]['time'] }}</x-zyngga-text>
                        <div class="flex-1 bg-zyngga-neutral-50 rounded-xl p-3">
                            <x-zyngga-text variant="xs" weight="medium">{{ $order['logs'][0]['note'] }}</x-zyngga-text>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex justify-center">
                    <button @click="showProgressDetail = !showProgressDetail" class="flex items-center gap-1 group">
                        <x-zyngga-text variant="xs" weight="semibold" color="primary">Lihat Detail</x-zyngga-text>
                        <i data-feather="chevron-down" class="w-3.5 h-3.5 text-zyngga-blue-300 transition-transform" :class="showProgressDetail ? 'rotate-180' : ''"></i>
                    </button>
                </div>

                {{-- Progress Timeline (Hidden) --}}
                <div x-show="showProgressDetail" x-cloak class="mt-4 space-y-4">
                    {{-- Dummy timeline --}}
                    @foreach($order['logs'] as $log)
                    <div class="flex gap-3 relative">
                        <div class="flex flex-col items-center">
                            <div class="w-2 h-2 rounded-full bg-zyngga-blue-300 z-10"></div>
                            <div class="w-0.5 h-full bg-zyngga-blue-50 absolute top-2"></div>
                        </div>
                        <div class="pb-4">
                            <x-zyngga-text variant="xs" weight="medium">{{ $log['note'] }}</x-zyngga-text>
                            <x-zyngga-text variant="xs" color="neutral-400">{{ $log['date'] }} | {{ $log['time'] }}</x-zyngga-text>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-zyngga-card>

            {{-- 3. RINCIAN PEMBAYARAN --}}
            <x-zyngga-card title="Rincian Pembayaran">
                <x-slot:headerAction>
                    <div class="status-badge {{ $order['payment_status'] === 'Lunas' ? 'paid' : 'unpaid' }}">
                        {{ $order['payment_status'] }}
                    </div>
                </x-slot:headerAction>

                <div class="space-y-4">
                    <div class="flex justify-between">
                        <div>
                            <x-zyngga-text variant="sm" weight="semibold">{{ $order['service_type'] }}</x-zyngga-text>
                            <x-zyngga-text variant="xs" color="neutral-500">{{ $order['items'][0]['qty'] }} x Rp{{ number_format($order['items'][0]['price'], 0, ',', '.') }}</x-zyngga-text>
                        </div>
                        <x-zyngga-text variant="sm" weight="bold">Rp{{ number_format($order['total'], 0, ',', '.') }}</x-zyngga-text>
                    </div>

                    <div class="flex justify-center">
                        <button @click="showPaymentDetail = !showPaymentDetail" class="flex items-center gap-1 group">
                            <x-zyngga-text variant="xs" weight="semibold" color="primary">Lihat Detail</x-zyngga-text>
                            <i data-feather="chevron-down" class="w-3.5 h-3.5 text-zyngga-blue-300 transition-transform" :class="showPaymentDetail ? 'rotate-180' : ''"></i>
                        </button>
                    </div>

                    <div x-show="showPaymentDetail" x-cloak class="pt-2 space-y-2">
                        <div class="flex justify-between">
                            <x-zyngga-text variant="xs" color="neutral-500">Subtotal</x-zyngga-text>
                            <x-zyngga-text variant="xs" weight="medium">Rp{{ number_format($order['total'], 0, ',', '.') }}</x-zyngga-text>
                        </div>
                        <div class="flex justify-between">
                            <x-zyngga-text variant="xs" color="neutral-500">Biaya Pengiriman</x-zyngga-text>
                            <x-zyngga-text variant="xs" weight="medium">Gratis</x-zyngga-text>
                        </div>
                        <x-zyngga-divider />
                        <div class="flex justify-between">
                            <x-zyngga-text variant="sm" weight="bold">Total Pembayaran</x-zyngga-text>
                            <x-zyngga-text variant="sm" weight="bold" color="primary">Rp{{ number_format($order['total'], 0, ',', '.') }}</x-zyngga-text>
                        </div>
                    </div>
                </div>
            </x-zyngga-card>

            {{-- 4. BANTUAN/LAYANAN --}}
            <x-zyngga-card title="Bantuan/Layanan">
                <div class="flex flex-col gap-3">
                    @if($order['status'] === 'ongoing')
                        <x-zyngga-button type="button" variant="secondary" class="!justify-between !px-4 w-full">
                            <div class="flex items-center gap-3">
                                <i data-feather="arrow-up-circle" class="w-[18px] h-[18px]"></i>
                                <span>Upgrade Layanan</span>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                        </x-zyngga-button>
                        <x-zyngga-button type="button" variant="secondary" class="!justify-between !px-4 w-full">
                            <div class="flex items-center gap-3">
                                <i data-feather="help-circle" class="w-[18px] h-[18px]"></i>
                                <span>Ubah Metode Pembayaran</span>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                        </x-zyngga-button>
                    @else
                        <x-zyngga-button type="button" variant="secondary" class="!justify-between !px-4 w-full">
                            <div class="flex items-center gap-3">
                                <i data-feather="message-circle" class="w-[18px] h-[18px]"></i>
                                <span>Hubungi Kami</span>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                        </x-zyngga-button>
                    @endif
                    <x-zyngga-button type="button" variant="secondary" class="!justify-between !px-4 w-full">
                        <div class="flex items-center gap-3">
                            <i data-feather="alert-circle" class="w-[18px] h-[18px]"></i>
                            <span>Ajukan Komplain</span>
                        </div>
                        <i data-feather="chevron-right" class="w-4 h-4"></i>
                    </x-zyngga-button>
                </div>
            </x-zyngga-card>

            {{-- 5. SYARAT & KETENTUAN --}}
            <x-zyngga-card title="Syarat dan Ketentuan" gap="p-5">
                <ol class="list-decimal ml-4 space-y-1">
                    <li><x-zyngga-text variant="xs" color="neutral-500">Pengambilan barang harap disertai nota</x-zyngga-text></li>
                    <li><x-zyngga-text variant="xs" color="neutral-500">Barang yang tidak diambil selama 1 bulan, hilang/rusak tidak diganti</x-zyngga-text></li>
                    <li><x-zyngga-text variant="xs" color="neutral-500">Barang hilang/rusak karena proses pengerjaan diganti maksimal 5x biaya</x-zyngga-text></li>
                    <li><x-zyngga-text variant="xs" color="neutral-500">Klaim luntur tidak dipisah di luar tanggunggan</x-zyngga-text></li>
                    <li><x-zyngga-text variant="xs" color="neutral-500">Hak klaim berlaku 1x24 jam setelah barang diambil</x-zyngga-text></li>
                    <li><x-zyngga-text variant="xs" color="neutral-500">Setiap konsumen dianggap setuju dengan poin tersebut di atas</x-zyngga-text></li>
                </ol>
            </x-zyngga-card>

        </main>

        {{-- STICKY FOOTER --}}
        <div id="sticky-footer">
            <div class="max-w-5xl mx-auto flex items-center gap-3">
                @if($order['status'] === 'ongoing')
                    <x-zyngga-button type="button" variant="secondary" class="flex-1 !h-12">
                        <div class="flex items-center gap-2">
                            <i data-feather="message-circle" class="w-4 h-4"></i>
                            <span>Chat</span>
                        </div>
                    </x-zyngga-button>
                    <x-zyngga-button type="button" variant="primary" label="Bayar Sekarang" class="flex-[2] !h-12" />
                @else
                    <x-zyngga-button type="button" variant="secondary" class="flex-1 !h-12">
                        <div class="flex items-center gap-2">
                            <i data-feather="download" class="w-4 h-4"></i>
                            <span>Unduh Nota</span>
                        </div>
                    </x-zyngga-button>
                    <x-zyngga-button type="button" variant="primary" label="Ulangi Pesanan" class="flex-1 !h-12" />
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
