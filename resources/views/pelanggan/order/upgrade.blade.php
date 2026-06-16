<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upgrade Layanan – Zyngga</title>
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
            padding: 16px 20px calc(16px + env(safe-area-inset-bottom, 0px));
            display: flex;
            align-items: center;
            justify-content: space-between;
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

    <div class="min-h-screen flex flex-col" x-data="{ 
        selectedService: '',
        paymentMethod: '{{ strtolower($order['payment_method']) === 'qris' ? 'qris' : 'cash' }}',
        upgradesData: {{ json_encode(array_map(function($u) {
            $days = match(strtolower($u['name'])) {
                'kilat' => 0,
                'express' => 1,
                'quick' => 2,
                default => 3,
            };
            return ['id' => (string) $u['id'], 'name' => $u['name'], 'eta' => $days, 'price_diff' => $u['price_diff'], 'is_available' => $u['is_available']];
        }, $upgrades)) }},
        totalWeight: {{ collect($order['items'])->sum(fn($i) => (float) $i['qty']) }},
        baseTotal: {{ $order['payment_status'] === 'Lunas' ? 0 : $order['total'] }},
        init() {
            const firstAvailable = this.upgradesData.find(u => u.is_available);
            if (firstAvailable) {
                this.selectedService = firstAvailable.id;
            }
        },
        get selectedData() {
            return this.upgradesData.find(u => u.id === this.selectedService) || null;
        },
        get priceDiffTotal() {
            if (!this.selectedData || !this.selectedData.price_diff) return 0;
            return this.selectedData.price_diff * this.totalWeight;
        },
        get newTotal() {
            return this.baseTotal + this.priceDiffTotal;
        },
        get selectedServiceName() {
            if (!this.selectedData) return 'Pilih Layanan';
            let daysLabel = this.selectedData.eta === 0 ? 'Hari yang sama' : this.selectedData.eta + ' hari';
            return this.selectedData.name + ' (' + daysLabel + ')';
        },
        get selectedServiceETA() {
            if (!this.selectedData) return 'Pilih layanan untuk melihat estimasi';
            let d = new Date('{{ $baseDate }}');
            d.setDate(d.getDate() + this.selectedData.eta);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return 'Estimasi Selesai: ' + days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()];
        }
    }">
        <x-dashboard-header 
            title="Upgrade Layanan" 
            backUrl="{{ route('order.detail', $order['id']) }}" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[87px]">
                
                @if($errors->has('order'))
                    <div x-init="$dispatch('toast', { message: '{{ $errors->first('order') }}', type: 'error' })"></div>
                @endif
                
                <form id="page-content" action="{{ route('order.upgrade.process', $order['id']) }}" method="POST" class="flex-1 flex flex-col">
                    @csrf
                    
                    {{-- CARD 1: PILIHAN UPGRADE --}}
                    <x-zyngga-card padding="p-4" gap="py-[6px]">
                        @php
                            $currentDesc = match($currentService) {
                                'Reguler', 'Regular' => 'Layanan 3 hari (72 jam)',
                                'Quick' => 'Layanan 2 hari (48 jam)',
                                'Express' => 'Layanan 1 hari (24 jam)',
                                'Kilat' => 'Layanan 5 jam',
                                default => 'Layanan Saat Ini',
                            };
                        @endphp
                        {{-- Current Service --}}
                        <div class="flex items-center justify-between h-[56px] py-[6px]">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#FFF4E6] rounded-full flex items-center justify-center">
                                    <x-zyngga-service-icon service="{{ strtolower($currentService) }}" class="w-[18px] h-[18px] text-[#F59E0B]" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-neutral-900 leading-snug">{{ $currentService }}</span>
                                    <span class="text-xs text-neutral-500 leading-snug mt-0.5">{{ $currentDesc }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-regular text-neutral-500 leading-snug">Layanan saat ini</span>
                            </div>
                        </div>
                        
                        <div class="relative flex items-center justify-center my-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-[#F4F4F4]"></div>
                            </div>
                            <div class="relative bg-white px-3">
                                <x-zyngga-text variant="sm" weight="medium" color="primary" class="flex items-center gap-1">
                                    <i data-feather="arrow-down" class="w-4 h-4"></i> Upgrade Layanan
                                </x-zyngga-text>
                            </div>
                        </div>

                        {{-- Available Upgrades --}}
                        @foreach($upgrades as $index => $upgrade)
                            @php
                                $minHours = strtolower($upgrade['name']) === 'kilat' ? '1' : '5';
                                $toastMsg = "Waktu tidak cukup. Layanan " . $upgrade['name'] . " membutuhkan sisa waktu pemrosesan setidaknya " . $minHours . " jam.";
                            @endphp
                            <div {!! !$upgrade['is_available'] ? '@click="$dispatch(\'toast\', { message: \''.$toastMsg.'\', type: \'error\' })"' : '' !!}>
                                <x-zyngga-radio-row 
                                    x-model="selectedService"
                                    name="new_service_id"
                                    id="service-{{ $upgrade['id'] }}"
                                    value="{{ $upgrade['id'] }}"
                                    :disabled="!$upgrade['is_available']"
                                    :label="$upgrade['name']"
                                    :description="$upgrade['desc']"
                                    :additional="$upgrade['price_diff'] > 0 ? '+Rp' . number_format($upgrade['price_diff'], 0, ',', '.') . '/kg' : ''"
                                />
                            </div>
                            @if ($index < count($upgrades) - 1)
                                <div class="divider !my-[6px]"></div>
                            @endif
                        @endforeach
                    </x-zyngga-card>

                    @if(!in_array($order['raw_status'], ['Baru', 'created']))
                    {{-- CARD 2: RINCIAN PEMBAYARAN --}}
                    <x-zyngga-card title="Rincian Pembayaran">
                        <div class="space-y-4">
                            @foreach($order['items'] as $item)
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" color="neutral-900">{{ $item['name'] }}</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ $order['payment_status'] === 'Lunas' ? '0' : number_format($item['subtotal'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                            @endforeach

                            <div class="space-y-3 pt-2">
                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Biaya Upgrade</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(priceDiffTotal || 0)"></x-zyngga-text>
                                </div>
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

                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Total</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(newTotal)"></x-zyngga-text>
                                </div>
                            </div>
                        </div>
                    </x-zyngga-card>


                    @endif
                </form>

            </div>

            {{-- STICKY FOOTER --}}
            <div id="sticky-footer">
                <div class="max-w-5xl mx-auto w-full px-5 flex items-center justify-between">
                    <div>
                        <x-zyngga-text x-text="selectedServiceName" variant="base" weight="medium" class="m-0"></x-zyngga-text>
                        <div class="flex items-center gap-1.5 mt-1">
                            <i data-feather="info" class="w-3.5 h-3.5 text-[#1660C1]"></i>
                            <x-zyngga-text x-text="selectedServiceETA" variant="xs" color="primary" class="m-0"></x-zyngga-text>
                        </div>
                    </div>

                    <x-zyngga-button 
                        type="button"
                        variant="primary"
                        size="l"
                        class="ml-4"
                        x-bind:disabled="!selectedService"
                        @click="window.dispatchEvent(new CustomEvent('open-confirm-modal'))"
                        label="Ubah Layanan"
                    />
                </div>
            </div>
        </main>
        
        {{-- ── MODAL: KONFIRMASI UPGRADE ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="confirm-modal-root" 
            openEvent="open-confirm-modal"
            closeEvent="close-confirm-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/confirm_order.png')"
                title="Yakin ingin mengubah layanan ini?"
                description="Apakah Anda yakin ingin melanjutkan? Periksa detailnya sebelum melanjutkan."
                primaryLabel="Ubah Layanan"
                secondaryLabel="Batalkan"
                primaryAction="document.getElementById('page-content').submit()"
                secondaryAction="isOpen=false"
            />
        </x-zyngga-selection-modal>

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
