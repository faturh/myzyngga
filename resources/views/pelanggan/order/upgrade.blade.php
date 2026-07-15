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
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

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
            padding: 16px 0 calc(16px + env(safe-area-inset-bottom, 0px));
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

        upgradesData: {{ json_encode(array_map(function($u) {
            $originalEta = match(strtolower($u['name'])) {
                'kilat' => 0,
                'express' => 1,
                'quick' => 2,
                default => 3,
            };
            $workingHours = match(strtolower($u['name'])) {
                'kilat' => 5,
                'express' => 10,
                'quick' => 20,
                default => 30,
            };
            return ['id' => (string) $u['id'], 'name' => $u['name'], 'eta' => $originalEta, 'workingHours' => $workingHours, 'price_diff' => $u['price_diff'], 'is_available' => $u['is_available']];
        }, $upgrades)) }},
        totalWeight: {{ $totalWeightKg }},
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
            let daysLabel = this.selectedData.eta === 0 ? '5 jam' : this.selectedData.eta + ' hari';
            return this.selectedData.name + ' (' + daysLabel + ')';
        },
        calculateETA(startDate, hoursToAdd) {
            let date = new Date(startDate);
            if (date.getHours() < 8) {
                date.setHours(8, 0, 0, 0);
            } else if (date.getHours() >= 18) {
                date.setDate(date.getDate() + 1);
                date.setHours(8, 0, 0, 0);
            }
            while (hoursToAdd > 0) {
                let endOfDay = new Date(date);
                endOfDay.setHours(18, 0, 0, 0);
                let minutesLeftToday = Math.floor((endOfDay - date) / 60000);
                if (minutesLeftToday <= 0) {
                    date.setDate(date.getDate() + 1);
                    date.setHours(8, 0, 0, 0);
                    continue;
                }
                let minutesToAdd = hoursToAdd * 60;
                if (minutesToAdd <= minutesLeftToday) {
                    date.setMinutes(date.getMinutes() + minutesToAdd);
                    hoursToAdd = 0;
                } else {
                    date.setDate(date.getDate() + 1);
                    date.setHours(8, 0, 0, 0);
                    hoursToAdd -= (minutesLeftToday / 60);
                }
            }
            return date;
        },
        get selectedServiceETA() {
            if (!this.selectedData) return 'Pilih layanan untuk melihat estimasi';
            let d = this.calculateETA('{{ $baseDate }}', this.selectedData.workingHours);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const formattedTime = d.getHours().toString().padStart(2, '0') + '.' + d.getMinutes().toString().padStart(2, '0');
            return 'Selesai: ' + days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' | ' + formattedTime;
        }
    }">
        <x-dashboard-header 
            title="Upgrade Layanan" 
            backUrl="{{ route('order.detail', $order['nota_layanan']) }}" 
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
                
                <form id="page-content" action="{{ route('order.upgrade.process', $order['nota_layanan']) }}" method="POST" class="flex-1 flex flex-col">
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
                                $maxElapsedHours = match(strtolower($upgrade['name'])) {
                                    'kilat' => 3,
                                    'express' => 12,
                                    'quick' => 24,
                                    default => 24,
                                };
                                $toastMsg = "Layanan " . $upgrade['name'] . " hanya tersedia jika pesanan diproses kurang dari " . $maxElapsedHours . " jam yang lalu.";
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

                    @if(!in_array($order['raw_status'], ['Baru', 'created', 'Perlu Diproses']))
                    {{-- CARD 2: RINCIAN PEMBAYARAN --}}
                    <x-zyngga-card title="Rincian Pembayaran">
                        <div class="space-y-4">
                            {{-- Group 1: Layanan, Upgrade, Pengiriman --}}
                            <div class="space-y-1">
                                @foreach($order['items'] as $item)
                                <div class="flex justify-between items-center">
                                    <x-zyngga-text variant="sm" color="neutral-900">{{ $item['name'] }}</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ $order['payment_status'] === 'Lunas' ? '0' : number_format($item['subtotal'], 0, ',', '.') }}</x-zyngga-text>
                                </div>
                                @endforeach

                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Biaya Upgrade</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(priceDiffTotal || 0)"></x-zyngga-text>
                                </div>
                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Biaya Pengiriman</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['delivery_fee'] ?? 0, 0, ',', '.') }}</x-zyngga-text>
                                </div>
                            </div>

                            {{-- Group 2: Diskon --}}
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Diskon</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['discount'], 0, ',', '.') }}</x-zyngga-text>
                                </div>
                            </div>

                            <div class="divider"></div>

                            {{-- Group 4: Total --}}
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-900">Total</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(newTotal)"></x-zyngga-text>
                            </div>
                        </div>
                    </x-zyngga-card>


                    @endif
                </form>

            </div>

            {{-- STICKY FOOTER --}}
            <div id="sticky-footer">
                <div class="max-w-5xl mx-auto w-full px-4 flex items-center justify-between">
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
                        @click="submitUpgrade()"
                        label="Bayar Sekarang"
                    />
                </div>
            </div>
        </main>

        <x-zyngga-toast />

        {{-- ── MODAL: PAYMENT SUCCESS ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="payment-success-modal" 
            openEvent="open-payment-success-modal"
            closeEvent="close-payment-success-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/confirm_order.png')"
                title="Pembayaran Berhasil!"
                description="Terima kasih, pembayaran upgrade layanan Anda telah berhasil."
                primaryLabel="Lihat Detail Pesanan"
                primaryAction="window.location.href = window.redirectUrl"
            />
        </x-zyngga-selection-modal>

        {{-- ── MODAL: PAYMENT FAILED ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="payment-failed-modal" 
            openEvent="open-payment-failed-modal"
            closeEvent="close-payment-failed-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/cancel_order.png')"
                title="Pembayaran Dibatalkan"
                description="Proses pembayaran tidak diselesaikan. Pengajuan Anda telah dibatalkan."
                primaryLabel="Tutup"
                primaryAction="window.location.reload()"
            />
        </x-zyngga-selection-modal>
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

        function submitUpgrade() {
            const form = document.getElementById('page-content');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to native payment method selection
                    window.location.href = '{{ route('order.payment-method', $order['nota_layanan']) }}';
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Gagal memproses upgrade.', type: 'error' } }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Terjadi kesalahan sistem.', type: 'error' } }));
            });
        }

        function rollbackUpgrade(reload = true) {
            fetch('{{ route('order.upgrade.rollback', $order['nota_layanan']) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            }).then(() => {
                if (reload) window.location.reload();
            });
        }
    </script>
</body>
</html>
