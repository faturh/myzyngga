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
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

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
        showClothingDetail: false,
        snapToken: '{{ $order['snap_token'] ?? '' }}',
        pay() {
            if (!this.snapToken) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Pembayaran sudah lunas atau token tidak tersedia.', type: 'error' } }));
                return;
            }
            window.location.href = '{{ route('order.payment-method', $order['nota_layanan']) }}';
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
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['nota_layanan'] }}</x-zyngga-text>
                                <button 
                                    @click="navigator.clipboard.writeText('{{ $order['nota_layanan'] }}'); $dispatch('toast', { message: 'ID Pesanan berhasil disalin', type: 'success' })"
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
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Kasir</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['cashier_name'] ?? 'Azhep' }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Tanggal Pemesanan</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['order_date'] }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Estimasi Selesai</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['estimated_finished'] }}</x-zyngga-text>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div class="space-y-2.5">
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Parfum</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500">{{ $order['perfume'] ?? 'Lavender' }}</x-zyngga-text>
                            </div>
                            <div class="flex justify-between items-start">
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Catatan</x-zyngga-text>
                                <x-zyngga-text variant="sm" color="neutral-500" class="text-right max-w-[60%]">{{ $order['notes'] ?? '-' }}</x-zyngga-text>
                            </div>
                        </div>
                    </div>
                </x-zyngga-card>

                {{-- CARD 1.5: GALERI --}}
                <x-zyngga-card title="Galeri">
                    @if(isset($order['gallery']) && count($order['gallery']) > 0)
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($order['gallery'] as $image)
                                <div class="aspect-square rounded-xl overflow-hidden bg-zyngga-neutral-100">
                                    <img src="{{ Str::startsWith($image, 'http') ? $image : asset('storage/' . $image) }}" alt="Galeri" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-4 gap-2 text-center">
                            <div class="w-12 h-12 bg-[#F4F4F4] rounded-full flex items-center justify-center mx-auto">
                                <img src="{{ asset('assets/images/image.svg') }}" alt="Tidak ada Gambar" width="24" height="24">
                            </div>
                            <x-zyngga-text variant="sm" color="neutral-500" class="leading-[1.6]">Tidak ada Gambar</x-zyngga-text>
                        </div>
                    @endif
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

                        @if(count($order['logs']) > 1)
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
                        @endif
                    </div>
                </x-zyngga-card>

                @if(!in_array($order['raw_status'], ['Baru', 'created', 'Perlu Diproses']))
                {{-- CARD 2.5: RINCIAN PAKAIAN --}}
                <x-zyngga-card title="Rincian Pakaian">
                    @php
                        $clothingItems = (isset($order['clothing_items']) && count($order['clothing_items']) > 0) 
                            ? $order['clothing_items'] 
                            : [
                                ['name' => 'Kemeja', 'qty' => 1],
                                ['name' => 'Kaos', 'qty' => 2],
                                ['name' => 'Jeans', 'qty' => 1]
                            ];
                        $totalClothing = collect($clothingItems)->sum('qty');
                    @endphp

                    <div x-show="showClothingDetail" x-collapse x-cloak>
                        <div class="space-y-3 mb-4">
                            @foreach($clothingItems as $item)
                            <div class="flex justify-between items-center">
                                <x-zyngga-text variant="sm" color="neutral-900">{{ $item['name'] }}</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $item['qty'] }}</x-zyngga-text>
                            </div>
                            @endforeach
                        </div>
                        <div class="divider"></div>
                    </div>

                    <div class="flex justify-between items-center" :class="showClothingDetail ? 'pt-2' : ''">
                        <x-zyngga-text variant="sm" color="neutral-900">Total</x-zyngga-text>
                        <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $totalClothing }} Items</x-zyngga-text>
                    </div>

                    <div class="flex justify-center pt-4">
                        <x-zyngga-button 
                            variant="tertiary" 
                            size="m" 
                            icon="chevron-down"
                            iconPosition="right"
                            ::class="showClothingDetail ? 'rotate-icon' : ''"
                            @click="showClothingDetail = !showClothingDetail"
                        >
                            <span x-text="showClothingDetail ? 'Sembunyikan' : 'Lihat Detail'"></span>
                        </x-zyngga-button>
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
                            <div class="space-y-1">
                                @if(isset($order['items']) && count($order['items']) > 0)
                                    @foreach($order['items'] as $item)
                                    <div class="flex justify-between items-center">
                                        <x-zyngga-text variant="sm" color="neutral-900">{{ $item['name'] }}</x-zyngga-text>
                                        <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</x-zyngga-text>
                                    </div>
                                    @endforeach
                                @endif

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
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Diskon</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['discount'], 0, ',', '.') }}</x-zyngga-text>
                                </div>
                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Pajak</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['tax'], 0, ',', '.') }}</x-zyngga-text>
                                </div>
                            </div>
                            
                            <x-zyngga-divider />
                            
                            @if($order['payment_status'] === 'Lunas' || $order['status'] === 'finished')
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-900">Metode Pembayaran</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $order['payment_method'] }}</x-zyngga-text>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-900">Total</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ number_format($order['total'], 0, ',', '.') }}</x-zyngga-text>
                            </div>
                        </div>
                </x-zyngga-card>
                @endif

                @if(!in_array($order['raw_status'], ['Baru', 'created', 'Perlu Diproses']) && ($order['status'] === 'finished' || $order['can_upgrade'] || !$order['is_roundtrip']))
                {{-- CARD 4: BANTUAN/LAYANAN --}}
                <x-zyngga-card title="Bantuan/Layanan">
                    <div class="flex flex-col gap-3">
                        <template x-if="status !== 'finished'">
                            <div class="flex flex-col gap-3">
                                @if($order['can_upgrade'])
                                <x-zyngga-dropdown-item type="a" href="{{ route('order.upgrade', $order['nota_layanan']) }}" icon="arrow-up-circle" size="M">Upgrade Layanan</x-zyngga-dropdown-item>
                                @endif

                                @if(!$order['is_roundtrip'])
                                <x-zyngga-dropdown-item 
                                    type="a" 
                                    href="{{ route('order.request.delivery', $order['nota_layanan']) }}" 
                                    icon="truck" 
                                    size="M"
                                >Ajukan Delivery</x-zyngga-dropdown-item>
                                @endif
                            </div>
                        </template>
                        <template x-if="status === 'finished'">
                            <div class="flex flex-col gap-3">
                                <x-zyngga-dropdown-item 
                                    type="a" 
                                    href="https://wa.me/6282125322500" 
                                    target="_blank" 
                                    icon="message-square" 
                                    size="M"
                                >Hubungi Kami</x-zyngga-dropdown-item>
                                
                                @if(!$order['is_roundtrip'])
                                <x-zyngga-dropdown-item 
                                    type="a" 
                                    href="{{ route('order.request.delivery', $order['nota_layanan']) }}" 
                                    icon="truck" 
                                    size="M"
                                >Ajukan Pengantaran</x-zyngga-dropdown-item>
                                @endif

                                @if($order['has_complaint'] ?? false)
                                    <x-zyngga-dropdown-item type="a" href="{{ route('profile.complaint.detail', $order['complaint_id']) }}" icon="alert-circle" size="M">Lihat Detail Komplain</x-zyngga-dropdown-item>
                                @else
                                    <x-zyngga-dropdown-item type="a" href="{{ route('order.complaint', $order['nota_layanan']) }}" icon="alert-circle" size="M">Ajukan Komplain</x-zyngga-dropdown-item>
                                @endif
                            </div>
                        </template>
                    </div>
                </x-zyngga-card>
                @endif

                {{-- CARD 5: SYARAT DAN KETENTUAN --}}
                <x-zyngga-card title="Syarat dan Ketentuan">
                    <div>
                        @foreach(['Harap membawa bon saat pengambilan', 'Ganti maksimal: Satuan x5, Kiloan x2', 'Batas komplain 1x24 jam', 'Gratis pickup dan delivery', 'Diluar tanggung jawab kami cucian lebih 6 bulan'] as $index => $text)
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
                        <div class="w-full">
                            {{-- Kondisi 1: Belum diproses --}}
                            <template x-if="['Baru', 'created', 'Perlu Diproses'].includes(rawStatus)">
                                <x-zyngga-button 
                                    type="a"
                                    href="https://wa.me/6282125322500"
                                    target="_blank"
                                    variant="secondary" 
                                    class="w-full !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                                    size="l"
                                    icon="message-circle"
                                    iconPosition="left"
                                    label="Chat"
                                />
                            </template>
                            
                            {{-- Kondisi 2: Sudah diproses dan belum bayar --}}
                            <template x-if="!['Baru', 'created', 'Perlu Diproses'].includes(rawStatus) && !isPaid">
                                <div class="w-full flex gap-4">
                                    <x-zyngga-button 
                                        type="a"
                                        href="https://wa.me/6282125322500"
                                        target="_blank"
                                        variant="secondary" 
                                        class="flex-1 !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                                        size="l"
                                        icon="message-circle"
                                        iconPosition="left"
                                        label="Chat"
                                    />
                                    <x-zyngga-button 
                                        type="button"
                                        size="l"
                                        variant="primary" 
                                        class="flex-1 !h-12 font-medium"
                                        @click="pay()"
                                        label="Bayar Sekarang"
                                    />
                                </div>
                            </template>

                            {{-- Kondisi 3: Sudah diproses dan dibayar --}}
                            <template x-if="!['Baru', 'created', 'Perlu Diproses'].includes(rawStatus) && isPaid">
                                <div class="w-full flex gap-4">
                                    <x-zyngga-button 
                                        type="button"
                                        variant="secondary" 
                                        class="flex-1 !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                                        size="l"
                                        icon="download"
                                        iconPosition="left"
                                        label="Unduh Nota"
                                        onclick="window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Nota berhasil diunduh!' } })); window.location.href='{{ route('order.download-receipt', $order['nota_layanan']) }}';"
                                    />
                                    <x-zyngga-button 
                                        type="a"
                                        href="https://wa.me/6282125322500"
                                        target="_blank"
                                        variant="secondary" 
                                        class="flex-1 !h-12 border-zyngga-blue-300 text-zyngga-blue-300"
                                        size="l"
                                        icon="message-circle"
                                        iconPosition="left"
                                        label="Chat"
                                    />
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Kondisi 4: Sudah selesai --}}
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
                                onclick="window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Nota berhasil diunduh!' } })); window.location.href='{{ route('order.download-receipt', $order['nota_layanan']) }}';"
                            />
                            <x-zyngga-button 
                                type="a"
                                href="{{ route('order.repeat', $order['nota_layanan']) }}"
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

        {{-- ── MODAL: PAYMENT SUCCESS ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="payment-success-modal" 
            openEvent="open-payment-success-modal"
            closeEvent="close-payment-success-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/confirm_order.png')"
                title="Pembayaran Berhasil!"
                description="Terima kasih, pembayaran untuk pesanan Anda telah berhasil diverifikasi."
                primaryLabel="Tutup"
                primaryAction="window.location.reload()"
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
                description="Proses pembayaran tidak diselesaikan atau dibatalkan."
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
