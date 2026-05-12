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
            left: 0;
            right: 0;
            width: 100%;
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
        /* Remove centering on desktop */
        @media (min-width: 768px) {
            footer {
                left: 0;
                right: 0;
                transform: none;
            }
        }
    </style>
</head>
<body x-data="{ 
    isPaid: {{ request('status') === 'paid' ? 'true' : 'false' }},
    isOutlet: true,
    showStatusDetail: false,
    showPaymentDetail: false 
}" class="bg-zyngga-blue-50 min-h-screen">

    <div class="min-h-screen flex flex-col">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Detail Pesanan" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[100px]">

        {{-- ── CARD 1: ORDER INFO ──────────────────────────────────── --}}
        <x-zyngga-card>
            {{-- Top Row: Service & Status --}}
            <div class="flex items-start justify-between">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-zyngga-yellow-50 rounded-full flex items-center justify-center shrink-0">
                            <x-zyngga-service-icon service="Express" class="w-[18px] h-[18px] text-zyngga-yellow-300" />
                        </div>
                        <x-zyngga-text variant="xl" weight="semibold">Express</x-zyngga-text>
                    </div>
                    {{-- Order ID --}}
                    <div class="flex items-center gap-1.5">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="regular">IJK902H8MAHD</x-zyngga-text>
                        <button class="text-zyngga-blue-300 hover:text-zyngga-blue-400 transition-colors">
                            <i data-feather="copy" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                
                <template x-if="isOutlet">
                    <x-zyngga-status type="secondary" size="M" icon="shopping-bag" label="Ambil di Outlet" class="!bg-[#E8EFF9] !text-zyngga-blue-300 !border-none px-3" />
                </template>
                <template x-if="!isOutlet">
                    <x-zyngga-status type="secondary" size="L" icon="truck" label="Delivery" />
                </template>
            </div>

            <x-zyngga-divider class="my-4" />
            
            {{-- Name & Phone --}}
            <div class="space-y-1">
                <x-zyngga-text variant="base" weight="semibold">Rafi Syihan</x-zyngga-text>
                <x-zyngga-text variant="sm" color="neutral-500" weight="regular">0812 3456 7890</x-zyngga-text>
            </div>

            <template x-if="!isOutlet">
                <div>
                    <x-zyngga-divider class="my-4" />
                    {{-- Location & Address --}}
                    <div class="space-y-1">
                        <x-zyngga-text variant="base" weight="semibold">Telkom University</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" weight="regular" class="leading-relaxed">
                            Jl. Telekomunikasi No.1, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung
                        </x-zyngga-text>
                    </div>
                </div>
            </template>

            <x-zyngga-divider class="my-4" />

            {{-- Dates Section --}}
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Tanggal Pemesanan</x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Minggu, 12 Mei | 12.00</x-zyngga-text>
                </div>
                <div class="flex justify-between items-center">
                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Estimasi Selesai</x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Senin, 13 Mei | 12.00</x-zyngga-text>
                </div>
            </div>
        </x-zyngga-card>

        {{-- ── CARD 2: STATUS PENGERJAAN ───────────────────────────── --}}
        <x-zyngga-card title="Status Pengerjaan">
            <x-slot:headerAction>
                <x-zyngga-status type="primary" size="L" class="!px-3 !bg-zyngga-blue-300 !text-white !border-none">
                    <span x-text="isPaid ? '100%' : '56%'">100%</span>
                </x-zyngga-status>
            </x-slot:headerAction>

            <div class="flex flex-col gap-4 mt-4">
                {{-- Date Group: Current --}}
                <div class="flex flex-col gap-2">
                    <x-zyngga-text variant="sm" weight="semibold">Senin, 18 Feb</x-zyngga-text>
                    <div class="flex gap-2 items-center">
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="w-[60px] shrink-0">08:30</x-zyngga-text>
                        <div class="bg-zyngga-neutral-200 flex-1 px-4 py-3 rounded-lg">
                            <x-zyngga-text variant="sm" weight="medium">Mengerjakan Tahap Pengeringan</x-zyngga-text>
                        </div>
                    </div>
                </div>

                {{-- Expanded Items --}}
                <div x-show="showStatusDetail" x-transition x-cloak class="flex flex-col gap-4">
                    <x-zyngga-divider />
                    
                    <div class="flex flex-col gap-2">
                        <x-zyngga-text variant="sm" weight="semibold">Minggu, 19 Feb</x-zyngga-text>
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 items-center">
                                <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="w-[60px] shrink-0">12:30</x-zyngga-text>
                                <div class="bg-zyngga-neutral-200 flex-1 px-4 py-3 rounded-lg">
                                    <x-zyngga-text variant="sm" weight="medium">Mengerjakan Tahap Pencucian</x-zyngga-text>
                                </div>
                            </div>
                            <div class="flex gap-2 items-center">
                                <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="w-[60px] shrink-0">08:30</x-zyngga-text>
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
                size="m"
                icon="chevron-down"
                iconPosition="right"
                @click="showStatusDetail = !showStatusDetail"
                class="w-full mt-4 hover:bg-transparent !text-zyngga-blue-300 !text-[14px]"
                ::class="showStatusDetail ? '[&_svg]:rotate-180' : ''"
            >
                <span x-text="showStatusDetail ? 'Sembunyikan' : 'Lihat Detail'">Lihat Detail</span>
            </x-zyngga-button>
        </x-zyngga-card>

        {{-- ── CARD 3: RINCIAN PEMBAYARAN ───────────────────────────── --}}
        <x-zyngga-card title="Rincian Pembayaran">
            <x-slot:headerAction>
                <x-zyngga-status x-show="isPaid" type="success" size="L" label="Lunas" class="!bg-[#E9F7EE] !text-zyngga-status-success !border-none !px-4" />
                <x-zyngga-status x-show="!isPaid" type="error" size="L" label="Belum Bayar" class="!bg-[#FEE7E6] !text-zyngga-status-error !border-none !px-4" />
            </x-slot:headerAction>

            <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col gap-1">
                        <x-zyngga-text variant="sm" weight="semibold">Express</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-500">3.3 x Rp10.000</x-zyngga-text>
                    </div>
                    <x-zyngga-text variant="sm" weight="semibold">Rp33.000</x-zyngga-text>
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
                </div>
            </div>

            <x-zyngga-button 
                variant="tertiary"
                size="m"
                icon="chevron-down"
                iconPosition="right"
                @click="showPaymentDetail = !showPaymentDetail"
                class="w-full mt-4 hover:bg-transparent !text-zyngga-blue-300 !text-[14px]"
                ::class="showPaymentDetail ? '[&_svg]:rotate-180' : ''"
            >
                <span x-text="showPaymentDetail ? 'Sembunyikan' : 'Lihat Detail'">Lihat Detail</span>
            </x-zyngga-button>
        </x-zyngga-card>

        {{-- ── CARD 4: BANTUAN/LAYANAN ───────────────────────────── --}}
        <x-zyngga-card title="Bantuan/Layanan">
            <div class="space-y-3">
                <a href="https://wa.me/+6281297673318" target="_blank" class="action-item !border-[#CCCCCC] !rounded-xl !py-4 px-5">
                    <div class="flex items-center gap-3">
                        <i data-feather="message-circle" class="w-5 h-5 text-zyngga-neutral-500"></i>
                        <x-zyngga-text variant="sm" weight="medium">Hubungi Kami</x-zyngga-text>
                    </div>
                    <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                </a>

                <div class="action-item !border-[#CCCCCC] !rounded-xl !py-4 px-5">
                    <div class="flex items-center gap-3">
                        <i data-feather="alert-circle" class="w-5 h-5 text-zyngga-neutral-500"></i>
                        <x-zyngga-text variant="sm" weight="medium">Ajukan Komplain</x-zyngga-text>
                    </div>
                    <i data-feather="chevron-right" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>
            </div>
        </x-zyngga-card>

        {{-- ── CARD 5: SYARAT DAN KETENTUAN ───────────────────────────── --}}
        <x-zyngga-card title="Syarat dan Ketentuan">
            <div class="space-y-3">
                @foreach([
                    'Pengambilan barang harap disertai nota',
                    'Barang yang tidak diambil selama 1 bulan, hilang/rusak tidak diganti',
                    'Barang hilang/rusak karena proses pengerjaan diganti maksimal 5x biaya',
                    'Klaim luntur tidak dipisah di luar tanggungan',
                    'Hak klaim berlaku 1x24 jam setelah barang diambil',
                    'Setiap konsumen dianggap setuju dengan poin tersebut di atas'
                ] as $index => $item)
                    <div class="flex gap-3">
                        <x-zyngga-text variant="sm" color="neutral-500" weight="regular">{{ $index + 1 }}.</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" weight="regular" class="leading-relaxed">{{ $item }}</x-zyngga-text>
                    </div>
                @endforeach
            </div>
        </x-zyngga-card>

        {{-- ── FOOTER ─────────────────────────────────────────────── --}}
        <footer>
            <div class="max-w-5xl mx-auto w-full px-5 flex items-center gap-4">
                <x-zyngga-button 
                    variant="secondary"
                    size="l"
                    icon="download"
                    iconPosition="left"
                    label="Unduh Nota"
                    class="flex-1"
                    onclick="window.dispatchEvent(new CustomEvent('open-download-modal'))"
                />
                <x-zyngga-button 
                    variant="primary"
                    size="l"
                    label="Ulangi Pesanan"
                    class="flex-1 !bg-zyngga-blue-300"
                />
            </div>
        </footer>

        {{-- ── MODAL: DOWNLOAD NOTA ────────────────────────────── --}}
        <x-zyngga-selection-modal id="download-modal" openEvent="open-download-modal">
            <div class="flex flex-col items-center text-center">
                <x-zyngga-text variant="lg" weight="medium" color="neutral-900" class="mb-2 !text-[#0F0F0F]">Simpan Nota Transaksi</x-zyngga-text>
                <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="mb-8 px-4 !text-[#717171]">
                    Kamu bisa mengunduh atau membagikan nota ini sebagai bukti transaksi.
                </x-zyngga-text>
                
                <div class="flex flex-col gap-3 w-full">
                    <x-zyngga-button variant="secondary" size="m" icon="send" iconPosition="left" label="Bagikan Nota" class="w-full" />
                    <x-zyngga-button variant="primary" size="m" icon="download" iconPosition="left" label="Unduh Nota" class="w-full !bg-zyngga-blue-300" />
                </div>
            </div>
        </x-zyngga-selection-modal>

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
