<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pesanan – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; color: #0F0F0F; }
        
        [x-cloak] { display: none !important; }

        .section-card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin: 6px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .status-badge {
            padding: 8px 12px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .status-ongoing { background: #e8eff9; color: #1660C1; }
        .status-paid { background: rgba(33, 181, 87, 0.1); color: #21B557; }
        .status-unpaid { background: rgba(236, 15, 4, 0.1); color: #EC0F04; }

        .timeline-item {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }
        .timeline-time {
            width: 60px;
            font-size: 14px;
            font-weight: 500;
            color: #808080;
            flex-shrink: 0;
        }
        .timeline-content {
            background: #F4F4F4;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            flex: 1;
        }

        .action-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border: 1px solid #CCCCCC;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .action-item:hover { border-color: #1660C1; background: #e8eff9; }

        footer {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 425px;
            background: white;
            padding: 20px;
            display: flex;
            gap: 16px;
            z-index: 50;
            box-shadow: 0 -4px 24px rgba(0,0,0,0.08);
            border-radius: 16px 16px 0 0;
        }
    </style>
</head>
<body x-data="{ 
    isPaid: {{ request('status') === 'paid' ? 'true' : 'false' }},
    showStatusDetail: false,
    showPaymentDetail: false 
}">
    <div class="w-full max-w-[425px] mx-auto min-h-screen flex flex-col pb-[100px]">

        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <div class="sticky top-0 z-40 bg-white rounded-b-2xl shadow-[0_4px_12px_rgba(0,0,0,0.04)] px-5 py-5 mb-[6px]">
            <div class="flex items-center gap-3">
                <x-zyngga-button 
                    type="a"
                    href="{{ route('dashboard') }}"
                    variant="neutral"
                    size="m"
                    icon="arrow-left"
                    iconPosition="only"
                    aria-label="Kembali"
                />
                <x-zyngga-text variant="lg" weight="semibold" as="h1">Detail Pesanan</x-zyngga-text>
            </div>
        </div>

        {{-- ── CARD 1: ORDER INFO ──────────────────────────────────── --}}
        <div class="section-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="bg-zyngga-yellow-50 p-1.5 rounded-full flex items-center justify-center shrink-0">
                            <x-zyngga-service-icon service="Express" class="w-3.5 h-3.5 text-zyngga-yellow-300" />
                        </div>
                        <x-zyngga-text variant="lg" weight="semibold" as="span">Express</x-zyngga-text>
                    </div>
                    <div class="flex items-center gap-1">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="medium" as="span">IJK902H8MAHD</x-zyngga-text>
                        <button class="p-1 hover:bg-zyngga-neutral-200 rounded">
                            <i data-feather="copy" class="w-4 h-4 text-zyngga-neutral-400"></i>
                        </button>
                    </div>
                </div>
                <x-zyngga-status type="secondary" size="L" icon="package" label="Delivery" />
            </div>

            <div class="h-[1px] bg-zyngga-neutral-200 my-4"></div>

            <div class="space-y-4">
                <div>
                    <x-zyngga-text variant="xs" color="neutral-500" weight="medium" class="tracking-tight">Nama</x-zyngga-text>
                    <x-zyngga-text variant="base" weight="medium" class="mt-1">Rafi Syihan</x-zyngga-text>
                </div>
                
                <div class="h-[1px] bg-zyngga-neutral-200"></div>

                <div>
                    <x-zyngga-text variant="base" weight="medium">Telkom University</x-zyngga-text>
                    <x-zyngga-text variant="sm" color="neutral-500" class="mt-1 leading-snug">Jl. Telekomunikasi No.1, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung</x-zyngga-text>
                </div>
            </div>
        </div>

        {{-- ── CARD 2: STATUS PENGERJAAN ───────────────────────────── --}}
        <div class="section-card">
            <div class="flex items-center justify-between mb-2">
                <x-zyngga-text variant="base" weight="semibold" as="span">Status Pengerjaan</x-zyngga-text>
                <x-zyngga-status type="primary" size="M" class="!px-3">
                    <span x-text="isPaid ? '100%' : '56%'">56%</span>
                </x-zyngga-status>
            </div>

            <div class="flex flex-col gap-4 mt-4">
                {{-- Date Group: Current --}}
                <div class="flex flex-col gap-2">
                    <x-zyngga-text variant="sm" weight="semibold">Senin, 18 Feb</x-zyngga-text>
                    <div class="flex gap-2 items-center">
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-500" class="w-[60px] shrink-0">08:30</x-zyngga-text>
                        <div class="bg-zyngga-neutral-200 flex-1 px-4 py-3 rounded-lg">
                            <x-zyngga-text variant="sm" weight="medium">Mengerjakan Tahap Pengeringan</x-zyngga-text>
                        </div>
                    </div>
                </div>

                {{-- Expanded Items --}}
                <div x-show="showStatusDetail" x-transition x-cloak class="flex flex-col gap-4">
                    <div class="h-[1px] bg-zyngga-neutral-200"></div>
                    
                    <div class="flex flex-col gap-2">
                        <x-zyngga-text variant="sm" weight="semibold">Minggu, 19 Feb</x-zyngga-text>
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-500" class="w-[60px] shrink-0">12:30</x-zyngga-text>
                                <div class="bg-zyngga-neutral-200 flex-1 px-4 py-3 rounded-lg">
                                    <x-zyngga-text variant="sm" weight="medium">Mengerjakan Tahap Pencucian</x-zyngga-text>
                                </div>
                            </div>
                            <div class="flex gap-2 items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-500" class="w-[60px] shrink-0">08:30</x-zyngga-text>
                                <div class="bg-zyngga-neutral-200 flex-1 px-4 py-3 rounded-lg">
                                    <x-zyngga-text variant="sm" weight="medium">Menerima Pesanan</x-zyngga-text>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-zyngga-button 
                variant="tertiary"
                size="s"
                icon="chevron-down"
                iconPosition="right"
                @click="showStatusDetail = !showStatusDetail"
                class="w-full mt-4"
                ::class="showStatusDetail ? '[&_svg]:rotate-180' : '[&_svg]:rotate-0'"
            >
                <x-zyngga-text variant="xs" weight="semibold" x-text="showStatusDetail ? 'Sembunyikan' : 'Lihat Detail'">Lihat Detail</x-zyngga-text>
            </x-zyngga-button>
        </div>

        {{-- ── CARD 3: RINCIAN PEMBAYARAN ───────────────────────────── --}}
        <div class="section-card">
            <div class="flex items-center justify-between mb-4">
                <x-zyngga-text variant="base" weight="semibold" as="span">Rincian Pembayaran</x-zyngga-text>
                <x-zyngga-status x-show="isPaid" type="success" size="M" label="Lunas" />
                <x-zyngga-status x-show="!isPaid" type="error" size="M" label="Belum Bayar" />
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col gap-1">
                        <x-zyngga-text variant="sm" weight="semibold">Express</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-500">3.3 x Rp10.000</x-zyngga-text>
                    </div>
                    <x-zyngga-text variant="sm" weight="semibold">Rp33.000</x-zyngga-text>
                </div>

                <div x-show="showPaymentDetail" x-transition x-cloak class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-500">Subtotal</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">Rp33.000</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-500">Diskon</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">Rp0</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-500">Pajak</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">Rp0</x-zyngga-text>
                    </div>

                    <div class="h-[1px] bg-zyngga-neutral-200 my-2"></div>

                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-500">Metode Pembayaran</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold" x-text="isPaid ? 'QRIS' : 'Cash'">Cash</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-500">Total</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="semibold">Rp33.000</x-zyngga-text>
                    </div>
                    
                    <template x-if="isPaid">
                        <div class="flex flex-col gap-2">
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-500">Tunai</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="semibold">Rp33.000</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-500">Kembalian</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="semibold">Rp0</x-zyngga-text>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <x-zyngga-button 
                variant="tertiary"
                size="s"
                icon="chevron-down"
                iconPosition="right"
                @click="showPaymentDetail = !showPaymentDetail"
                class="w-full mt-4"
                ::class="showPaymentDetail ? '[&_svg]:rotate-180' : '[&_svg]:rotate-0'"
            >
                <x-zyngga-text variant="xs" weight="semibold" x-text="showPaymentDetail ? 'Sembunyikan' : 'Lihat Detail'">Lihat Detail</x-zyngga-text>
            </x-zyngga-button>
        </div>

        {{-- ── CARD 4: BANTUAN/LAYANAN ───────────────────────────── --}}
        <div class="section-card space-y-3">
            <x-zyngga-text variant="base" weight="semibold" class="block mb-1">Bantuan/Layanan</x-zyngga-text>
            
            <div class="action-item">
                <div class="flex items-center gap-3">
                    <i data-feather="trending-up" class="w-5 h-5 text-zyngga-neutral-500"></i>
                    <x-zyngga-text variant="sm" weight="medium">Upgrade Layanan</x-zyngga-text>
                </div>
                <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
            </div>

            <div class="action-item">
                <div class="flex items-center gap-3">
                    <i data-feather="credit-card" class="w-5 h-5 text-zyngga-neutral-500"></i>
                    <x-zyngga-text variant="sm" weight="medium">Ubah Metode Pembayaran</x-zyngga-text>
                </div>
                <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
            </div>

            <div class="action-item">
                <div class="flex items-center gap-3">
                    <i data-feather="alert-circle" class="w-5 h-5 text-zyngga-neutral-500"></i>
                    <x-zyngga-text variant="sm" weight="medium">Ajukan Komplain</x-zyngga-text>
                </div>
                <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
            </div>
        </div>

        {{-- ── CARD 5: SYARAT DAN KETENTUAN ───────────────────────────── --}}
        <div class="section-card">
            <x-zyngga-text variant="base" weight="semibold" class="block mb-4">Syarat dan Ketentuan</x-zyngga-text>
            <ol class="list-decimal list-inside space-y-3">
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="medium" as="span" class="ms-2">Pengambilan barang harap disertai nota</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="medium" as="span" class="ms-2">Barang yang tidak diambil selama 1 bulan, hilang/rusak tidak diganti</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="medium" as="span" class="ms-2">Barang hilang/rusak karena proses pengerjaan diganti maksimal 5x biaya</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="medium" as="span" class="ms-2">Klaim luntur tidak dipisah di luar tanggungan</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="medium" as="span" class="ms-2">Hak klaim berlaku 1x24 jam setelah barang diambil</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="medium" as="span" class="ms-2">Setiap konsumen dianggap setuju dengan poin tersebut di atas</x-zyngga-text></li>
            </ol>
        </div>

        {{-- ── FOOTER ─────────────────────────────────────────────── --}}
        <footer>
            <x-zyngga-button 
                variant="secondary"
                size="l"
                icon="message-square"
                iconPosition="left"
                label="Chat"
                class="flex-1"
            />
            <x-zyngga-button 
                variant="primary"
                size="l"
                class="flex-[2]"
                ::disabled="isPaid"
            >
                <x-zyngga-text variant="base" weight="semibold" color="white" x-text="isPaid ? 'Sudah Dibayar' : 'Bayar Sekarang'">Bayar Sekarang</x-zyngga-text>
            </x-zyngga-button>
        </footer>

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
