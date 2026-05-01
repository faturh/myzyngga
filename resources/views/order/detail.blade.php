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
        .status-paid { background: #E9F7EE; color: #21B557; }
        .status-unpaid { background: #FEE7E6; color: #EC0F04; }

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
            max-width: 768px; /* Tablet width */
            background: white;
            padding: 20px;
            display: flex;
            gap: 16px;
            z-index: 50;
            box-shadow: 0 -4px 24px rgba(0,0,0,0.08);
            border-radius: 16px 16px 0 0;
            transition: all 0.3s ease;
        }

        /* Adjust footer position when sidebar is present on desktop */
        @media (min-width: 768px) {
            footer {
                left: 50%;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body x-data="{ 
    isPaid: {{ request('status') === 'paid' ? 'true' : 'false' }},
    showStatusDetail: false,
    showPaymentDetail: false 
}" class="bg-zyngga-blue-50 min-h-screen">

    <div class="min-h-screen flex flex-col">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Detail Pesanan" 
            :backUrl="route('dashboard')" 
            :maxWidth="'max-w-3xl'"
            :showPoints="false"
            :showMenu="true"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-3xl mx-auto px-5 pb-[100px]">

        {{-- ── CARD 1: ORDER INFO ──────────────────────────────────── --}}
        <x-zyngga-card>
            {{-- Top Row: Service & Status --}}
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                        <x-zyngga-service-icon service="Express" class="w-[18px] h-[18px] text-zyngga-yellow-300" />
                    </div>
                    <x-zyngga-text variant="xl" weight="medium">Express</x-zyngga-text>
                </div>
                <x-zyngga-status type="secondary" size="L" icon="truck" label="Delivery" />
            </div>

            {{-- Order ID --}}
            <div class="flex items-center gap-1.5 mb-4 px-1">
                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">IJK902H8MAHD</x-zyngga-text>
                <button class="text-zyngga-blue-300 hover:text-zyngga-blue-400 transition-colors">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                </button>
            </div>

            <x-zyngga-divider class="my-4" />
            
            {{-- Name & Phone --}}
            <div class="space-y-1">
                <x-zyngga-text variant="base" weight="medium">Rafi Syihan</x-zyngga-text>
                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">0812 3456 7890</x-zyngga-text>
            </div>

            <x-zyngga-divider class="my-4" />

            {{-- Location & Address --}}
            <div class="space-y-1">
                <x-zyngga-text variant="base" weight="medium">Telkom University</x-zyngga-text>
                <x-zyngga-text variant="sm" color="neutral-500" weight="regular" class="leading-relaxed">
                    Jl. Telekomunikasi No.1, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung
                </x-zyngga-text>
            </div>
        </x-zyngga-card>

        {{-- ── CARD 2: STATUS PENGERJAAN ───────────────────────────── --}}
        <x-zyngga-card title="Status Pengerjaan">
            <x-slot:headerAction>
                <x-zyngga-status type="primary" size="M" class="!px-3">
                    <span x-text="isPaid ? '100%' : '56%'">56%</span>
                </x-zyngga-status>
            </x-slot:headerAction>

            <div class="flex flex-col gap-4 mt-4">
                {{-- Date Group: Current --}}
                <div class="flex flex-col gap-2">
                    <x-zyngga-text variant="sm" weight="medium">Senin, 18 Feb</x-zyngga-text>
                    <div class="flex gap-2 items-center">
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="w-[60px] shrink-0">08:30</x-zyngga-text>
                        <div class="bg-zyngga-neutral-200 flex-1 px-4 py-3 rounded-lg">
                            <x-zyngga-text variant="sm" weight="regular">Mengerjakan Tahap Pengeringan</x-zyngga-text>
                        </div>
                    </div>
                </div>

                {{-- Expanded Items --}}
                <div x-show="showStatusDetail" x-transition x-cloak class="flex flex-col gap-4">
                    <x-zyngga-divider />
                    
                    <div class="flex flex-col gap-2">
                        <x-zyngga-text variant="sm" weight="medium">Minggu, 19 Feb</x-zyngga-text>
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 items-center">
                                <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="w-[60px] shrink-0">12:30</x-zyngga-text>
                                <div class="bg-zyngga-neutral-200 flex-1 px-4 py-3 rounded-lg">
                                    <x-zyngga-text variant="sm" weight="regular">Mengerjakan Tahap Pencucian</x-zyngga-text>
                                </div>
                            </div>
                            <div class="flex gap-2 items-center">
                                <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="w-[60px] shrink-0">08:30</x-zyngga-text>
                                <div class="bg-zyngga-neutral-200 flex-1 px-4 py-3 rounded-lg">
                                    <x-zyngga-text variant="sm" weight="regular">Menerima Pesanan</x-zyngga-text>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-zyngga-button 
                variant="tertiary"
                size="m"
                icon="chevron-down"
                iconPosition="right"
                @click="showStatusDetail = !showStatusDetail"
                class="w-full mt-4"
                ::class="showStatusDetail ? '[&_svg]:rotate-180' : ''"
            >
                <span x-text="showStatusDetail ? 'Sembunyikan' : 'Lihat Detail'">Lihat Detail</span>
            </x-zyngga-button>
        </x-zyngga-card>

        {{-- ── CARD 3: RINCIAN PEMBAYARAN ───────────────────────────── --}}
        <x-zyngga-card title="Rincian Pembayaran">
            <x-slot:headerAction>
                <x-zyngga-status x-show="isPaid" type="success" size="M" label="Lunas" />
                <x-zyngga-status x-show="!isPaid" type="error" size="M" label="Belum Bayar" />
            </x-slot:headerAction>

            <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col gap-1">
                        <x-zyngga-text variant="sm" weight="medium">Express</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500">3.3 x Rp10.000</x-zyngga-text>
                    </div>
                    <x-zyngga-text variant="sm" weight="medium">Rp33.000</x-zyngga-text>
                </div>

                <div x-show="showPaymentDetail" x-transition x-cloak class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Subtotal</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="medium">Rp33.000</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Diskon</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="medium">Rp0</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Pajak</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="medium">Rp0</x-zyngga-text>
                    </div>

                    <x-zyngga-divider class="my-2" />

                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Metode Pembayaran</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="medium" x-text="isPaid ? 'QRIS' : 'Cash'">Cash</x-zyngga-text>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Total</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="medium">Rp33.000</x-zyngga-text>
                    </div>
                    
                    <template x-if="isPaid">
                        <div class="flex flex-col gap-2">
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Tunai</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium">Rp33.000</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Kembalian</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium">Rp0</x-zyngga-text>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <x-zyngga-button 
                variant="tertiary"
                size="m"
                icon="chevron-down"
                iconPosition="right"
                @click="showPaymentDetail = !showPaymentDetail"
                class="w-full mt-4"
                ::class="showPaymentDetail ? '[&_svg]:rotate-180' : ''"
            >
                <span x-text="showPaymentDetail ? 'Sembunyikan' : 'Lihat Detail'">Lihat Detail</span>
            </x-zyngga-button>
        </x-zyngga-card>

        {{-- ── CARD 4: BANTUAN/LAYANAN ───────────────────────────── --}}
        <x-zyngga-card title="Bantuan/Layanan">
            <div class="space-y-3">
                <div class="action-item">
                    <div class="flex items-center gap-3">
                        <i data-feather="trending-up" class="w-5 h-5 text-zyngga-neutral-500"></i>
                        <x-zyngga-text variant="sm" weight="regular">Upgrade Layanan</x-zyngga-text>
                    </div>
                    <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>

                <div class="action-item">
                    <div class="flex items-center gap-3">
                        <i data-feather="credit-card" class="w-5 h-5 text-zyngga-neutral-500"></i>
                        <x-zyngga-text variant="sm" weight="regular">Ubah Metode Pembayaran</x-zyngga-text>
                    </div>
                    <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>

                <div class="action-item">
                    <div class="flex items-center gap-3">
                        <i data-feather="alert-circle" class="w-5 h-5 text-zyngga-neutral-500"></i>
                        <x-zyngga-text variant="sm" weight="regular">Ajukan Komplain</x-zyngga-text>
                    </div>
                    <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>
            </div>
        </x-zyngga-card>

        {{-- ── CARD 5: SYARAT DAN KETENTUAN ───────────────────────────── --}}
        <x-zyngga-card title="Syarat dan Ketentuan">
            <ol class="list-decimal list-inside space-y-3">
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="regular" as="span" class="ms-2">Pengambilan barang harap disertai nota</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="regular" as="span" class="ms-2">Barang yang tidak diambil selama 1 bulan, hilang/rusak tidak diganti</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="regular" as="span" class="ms-2">Barang hilang/rusak karena proses pengerjaan diganti maksimal 5x biaya</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="regular" as="span" class="ms-2">Klaim luntur tidak dipisah di luar tanggungan</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="regular" as="span" class="ms-2">Hak klaim berlaku 1x24 jam setelah barang diambil</x-zyngga-text></li>
                <li class="ms-1"><x-zyngga-text variant="sm" color="neutral-500" weight="regular" as="span" class="ms-2">Setiap konsumen dianggap setuju dengan poin tersebut di atas</x-zyngga-text></li>
            </ol>
        </x-zyngga-card>

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
                <x-zyngga-text variant="base" weight="medium" color="white" x-text="isPaid ? 'Sudah Dibayar' : 'Bayar Sekarang'">Bayar Sekarang</x-zyngga-text>
            </x-zyngga-button>
        </footer>

            </div>
        </main>
</div>
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
