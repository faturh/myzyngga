<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemesanan Pickup – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; }

        /* ── scrollable main content ── */
        #page-content {
            /* padding-bottom removed to allow cleaner structure */
        }


        /* ── service option ── */
        .service-option {
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            margin-bottom: 12px;
            height: 72px;
        }
        .service-option:last-child { margin-bottom: 0; }
        .service-option.selected {
            border-color: #1660C1;
            background: #e8eff9;
            height: 72px;
        }

        /* ── date button ── */
        .date-btn {
            flex: 1;
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            text-align: left;
            height: 72px;
        }
        .date-btn:hover {
            border-color: #1660C1;
            background: #e8eff9;
        }
        .date-btn.selected {
            border-color: #1660C1;
            background: #e8eff9;
        }

        /* ── time chip ── */
        .time-chip {
            flex: 1;
            height: 40px;
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #808080;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .time-chip:not(:disabled):hover {
            border-color: #1660C1;
            color: #1660C1;
            background: #e8eff9;
        }
        .time-chip.selected {
            border-color: #1660C1;
            background: #e8eff9;
            color: #0F0F0F;
            font-weight: 600;
        }

        /* ── addon row ── */
        .addon-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 32px;
            cursor: pointer;
        }

        /* ── payment radio ── */
        .payment-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            cursor: pointer;
        }
        .radio-circle {
            width: 20px; height: 20px;
            border-radius: 50%;
            border: 2px solid #e8eff9;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: border-color 0.15s;
        }
        .radio-circle.checked {
            border-color: #1660C1;
            background: #1660C1;
        }
        .radio-circle.checked::after {
            content: '';
            width: 6px; height: 6px;
            background: white;
            border-radius: 50%;
        }

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
        /* Remove specific centering on desktop */
        @media (min-width: 768px) {
            #sticky-footer {
                left: 0;
                right: 0;
                transform: none;
            }
        }

        /* ── map thumbnail ── */
        #map-thumb {
            width: 100%;
            height: 144px;
            border-radius: 8px;
            overflow: hidden;
        }
        #map-thumb iframe { width:100%; height:100%; border:0; }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data>
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Pemesanan Pickup" 
            :backUrl="route('home')" 
            :backAction="'if(isDirty) { window.dispatchEvent(new CustomEvent(\'open-back-modal\')); return false; }'"
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[88px]">
                {{-- ── SCROLLABLE CONTENT ──────────────────────────────────── --}}
                <form method="POST" action="{{ route('order.confirm') }}" id="page-content" class="flex-1 flex flex-col">
        @csrf
        @if ($errors->any())
            <div x-init="$dispatch('toast', { message: '{{ $errors->first() }}', type: 'error' })"></div>
        @endif
        <input type="hidden" name="service"        value="{{ $service }}">
        <input type="hidden" name="address"        value="{{ $address }}">
        <input type="hidden" name="lat"            value="{{ $lat }}">
        <input type="hidden" name="lng"            value="{{ $lng }}">
        <input type="hidden" name="selected_service_id" id="selected_service_id" value="{{ strtolower($serviceLabel) }}">
        @php
            $todayCarbon = \Carbon\Carbon::now('Asia/Jakarta');
            $currentHour = $todayCarbon->hour;
            $isTodayDisabled = $currentHour >= 17;
            $defaultDate = $isTodayDisabled ? 'tomorrow' : 'today';
        @endphp
        <input type="hidden" name="pickup_date"   id="pickup_date"   value="{{ $pickupDate ?: $defaultDate }}">
        <input type="hidden" name="pickup_time"   id="pickup_time"   value="{{ $pickupTime ?: 'Standard' }}">
        <input type="hidden" name="parfum"        id="parfum"        value="{{ $parfum ?: 'Lavender' }}">
        <input type="hidden" name="catatan"       id="note"          value="{{ $note }}">
        
        {{-- ── LOKASI PICKUP ─────────────────────────────────── --}}
        <x-zyngga-card title="Lokasi Pickup">
            <x-slot:headerAction>
                <x-zyngga-button 
                    type="a"
                    href="{!! route('order.pickup', ['service' => $service, 'force' => 1, 'from' => 'booking']) !!}"
                    variant="secondary"
                    size="s"
                    label="Ubah"
                    onclick="isDirty = false"
                />
            </x-slot:headerAction>
            
            {{-- Map thumbnail — clickable → edit pickup location --}}
            <div id="map-thumb" class="mb-4 relative">
                <iframe
                loading="lazy"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps/embed/v1/search?key={{ config('services.google.maps_key') }}&q={{ $lat }},{{ $lng }}&zoom=18&maptype=roadmap"
                style="pointer-events:none;"
                ></iframe>
                {{-- Transparent overlay captures click → navigate to edit page --}}
                <a
                href="{!! route('order.pickup', ['service' => $service, 'force' => 1, 'from' => 'booking']) !!}"
                class="absolute inset-0 z-10 block cursor-pointer"
                aria-label="Edit lokasi pickup"
                title="Edit lokasi pickup"
                ></a>
            </div>
            
            {{-- Address with Icon --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-blue-50">
                    <i data-feather="map-pin" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <x-zyngga-text variant="sm" weight="medium">
                        {{ explode(',', $address)[0] }}
                    </x-zyngga-text>
                    <x-zyngga-text variant="xs" color="neutral-500" class="overflow-hidden text-overflow-ellipsis line-clamp-2">
                        {{ $address }}
                    </x-zyngga-text>
                </div>
            </div>

            <div class="mt-3">
                <x-zyngga-input 
                    name="detail_address" 
                    id="detail_address" 
                    value="{{ $detailAddress }}" 
                    placeholder="Tambah detail lokasi" 
                >
                    <x-slot:iconRight>
                        <i data-feather="edit-2" class="w-4 h-4 text-zyngga-neutral-900 pointer-events-none"></i>
                    </x-slot:iconRight>
                </x-zyngga-input>
            </div>
        </x-zyngga-card>
        
                @guest
                {{-- ── DETAIL PELANGGAN ───────────────────────────────── --}}
                <x-zyngga-card title="Detail Pelanggan">
                    <div class="space-y-4">
                        <div>
                            <x-zyngga-text variant="sm" weight="regular" class="mb-1.5 block">Nama Lengkap</x-zyngga-text>
                            <x-zyngga-input 
                                name="customer_name"
                                id="customer_name"
                                value="{{ $customerName ?? '' }}"
                                placeholder="Masukkan nama pelanggan"
                                required
                            />
                            <span id="error-customer_name" class="text-xs text-red-500 mt-1 hidden"></span>
                        </div>
                        <div>
                            <x-zyngga-text variant="sm" weight="regular" class="mb-1.5 block">Nomor WhatsApp</x-zyngga-text>
                            <x-zyngga-input 
                                name="customer_phone"
                                id="customer_phone"
                                value="{{ $customerPhone ?? '' }}"
                                placeholder="Masukkan nomor WhatsApp"
                                required
                            />
                            <span id="error-customer_phone" class="text-xs text-red-500 mt-1 hidden"></span>
                        </div>
                        <div>
                            <x-zyngga-text variant="sm" weight="regular" class="mb-1.5 block">Email</x-zyngga-text>
                            <x-zyngga-input 
                                name="customer_email"
                                id="customer_email"
                                value="{{ $customerEmail ?? '' }}"
                                placeholder="Masukkan Email"
                                type="email"
                                required
                            />
                            <span id="error-customer_email" class="text-xs text-red-500 mt-1 hidden"></span>
                            <div class="flex items-center gap-2 mt-2">
                                <i data-feather="info" class="w-4 h-4 text-[#1660C1]"></i>
                                <x-zyngga-text variant="xs" color="primary">Email digunakan untuk mengirim notifikasi pesanan</x-zyngga-text>
                            </div>
                        </div>
                    </div>
                </x-zyngga-card>
                @endguest
        
        {{-- ── JADWAL PICKUP ──────────────────────────────────── --}}
        <x-zyngga-card>
            <div class="space-y-4">
                {{-- Antar-Jemput Toggle --}}
                <x-zyngga-switch 
                    name="is_roundtrip" 
                    id="is_roundtrip"
                    label="Antar-Jemput"
                    :checked="$isRoundtrip" 
                    value="1"
                    onchange="isDirty = true; updateOrderSession({ is_roundtrip: this.checked ? 1 : 0 })"
                >
                    <x-slot:description>Kurir jemput dan antar kembali pakaianmu</x-slot:description>
                </x-zyngga-switch>

                {{-- Date options --}}
                <div class="flex gap-2">
                    @php
                        $tomorrowCarbon = $todayCarbon->copy()->addDay();
                    @endphp
                    <button type="button" class="date-btn {{ !$isTodayDisabled ? 'selected' : 'opacity-40 bg-gray-50 pointer-events-none' }}" 
                            onclick="selectDate('today', this)" {{ $isTodayDisabled ? 'disabled' : '' }}>
                        <x-zyngga-text variant="sm" weight="medium" class="m-0">Hari ini</x-zyngga-text>
                        <x-zyngga-text variant="xs" color="neutral-500" class="m-0 mt-0.5">{{ $todayCarbon->isoFormat('D MMM YYYY') }}</x-zyngga-text>
                    </button>
                    <button type="button" class="date-btn {{ $isTodayDisabled ? 'selected' : '' }}" 
                            onclick="selectDate('tomorrow', this)">
                        <x-zyngga-text variant="sm" weight="medium" class="m-0">Besok</x-zyngga-text>
                        <x-zyngga-text variant="xs" color="neutral-500" class="m-0 mt-0.5">{{ $tomorrowCarbon->isoFormat('D MMM YYYY') }}</x-zyngga-text>
                    </button>
                </div>
            </div>
        </x-zyngga-card>

        {{-- ── JENIS LAYANAN ──────────────────────────────────── --}}
        <x-zyngga-card title="Jenis Layanan">
            <x-slot:headerAction>
                <x-zyngga-button 
                    type="button" 
                    onclick="window.dispatchEvent(new CustomEvent('open-service-modal'))"
                    variant="tertiary"
                    size="s"
                    label="Lihat semua"
                />
            </x-slot:headerAction>

            @php
                $allServices = [
                    ['id' => 'regular',  'name' => 'Regular',  'desc' => 'Layanan 3 hari (72 jam)',   'price' => 'Rp4.850/kg'],
                    ['id' => 'quick',    'name' => 'Quick',    'desc' => 'Layanan 2 hari (48 jam)',   'price' => 'Rp6.000/kg'],
                    ['id' => 'express',  'name' => 'Express',  'desc' => 'Layanan 1 hari (24 jam)',   'price' => 'Rp6.250/kg'],
                    ['id' => 'kilat',    'name' => 'Kilat',    'desc' => 'Layanan 5 jam',              'price' => 'Rp7.850/kg'],
                    ['id' => 'satuan',   'name' => 'Satuan',   'desc' => 'Selimut, Bed Cover, dll.',  'price' => 'Mulai Rp10.000'],
                ];
            @endphp

            {{-- Slot 0: selected service (updated by JS on selection) --}}
            <div class="service-option selected" id="card-slot-0" onclick="cardSlotClick(0)">
                <div>
                    <x-zyngga-text id="slot0-name" variant="sm" weight="medium" class="m-0"></x-zyngga-text>
                    <x-zyngga-text id="slot0-desc" variant="xs" color="neutral-500" class="m-0 mt-1"></x-zyngga-text>
                </div>
                <div class="flex items-center gap-3">
                    <x-zyngga-text id="slot0-price" variant="sm" weight="medium" class="shrink-0"></x-zyngga-text>
                </div>
            </div>

            {{-- Slot 1: alternative service (updated by JS on selection) --}}
            <div class="service-option" id="card-slot-1" onclick="cardSlotClick(1)">
                <div>
                    <x-zyngga-text id="slot1-name" variant="sm" weight="medium" class="m-0"></x-zyngga-text>
                    <x-zyngga-text id="slot1-desc" variant="xs" color="neutral-500" class="m-0 mt-1"></x-zyngga-text>
                </div>
                <div class="flex items-center gap-3">
                    <x-zyngga-text id="slot1-price" variant="sm" weight="medium" class="shrink-0"></x-zyngga-text>
                </div>
            </div>
        </x-zyngga-card>

        {{-- ════════════════════════════════════════════════════════
             POPUP — Jenis Layanan (Figma 268:481)
             Centered in page, backdrop rgba(0,0,0,0.10)
        ════════════════════════════════════════════════════════ --}}
        {{-- ── MODAL: JENIS LAYANAN ──────────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="service-modal-root" 
            title="Jenis Layanan"
            openEvent="open-service-modal"
            closeEvent="close-service-modal"
        >
            @foreach ($allServices as $i => $svc)
                <x-zyngga-radio-row 
                    name="modal_service"
                    id="modal-radio-{{ $svc['id'] }}"
                    value="{{ $svc['id'] }}"
                    :label="$svc['name']"
                    :description="$svc['desc']"
                    :additional="$svc['price']"
                    :checked="strtolower($serviceLabel) === $svc['id']"
                    onclick="selectServiceFromModal('{{ $svc['id'] }}')"
                />
                @if ($i < count($allServices) - 1)
                    <x-zyngga-divider class=" !my-[6px]" />
                @endif
            @endforeach
        </x-zyngga-selection-modal>

        {{-- ── TAMBAHAN ───────────────────────────────────────── --}}
        <x-zyngga-card>
            <div class="addon-row flex items-center justify-between gap-2 overflow-hidden" onclick="window.dispatchEvent(new CustomEvent('open-parfum-modal'))">
                <x-zyngga-text variant="sm" weight="regular" class="m-0 shrink-0">Pilihan parfum</x-zyngga-text>
                <div class="flex items-center gap-1 min-w-0 flex-1 justify-end max-w-[50%]">
                    <x-zyngga-text id="selected-parfum" variant="sm" class="m-0 truncate text-right">Lavender</x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-[#808080] shrink-0"></i>
                </div>
            </div>

            <x-zyngga-divider class=" my-2" />
            <div class="addon-row flex items-center justify-between gap-2 overflow-hidden" onclick="openCatatan()">
                <x-zyngga-text variant="sm" weight="regular" class="m-0 shrink-0">Catatan</x-zyngga-text>
                <div class="flex items-center gap-1 min-w-0 flex-1 justify-end max-w-[50%]">
                    <x-zyngga-text id="catatan-label" variant="sm" color="neutral-500" class="m-0 truncate text-right">Buat catatan</x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-[#808080] shrink-0"></i>
                </div>
            </div>
        </x-zyngga-card>

        {{-- ── METODE PEMBAYARAN ──────────────────────────────── --}}
        <x-zyngga-card title="Metode Pembayaran">
            @php
                $payments = [
                    ['id' => 'cash', 'label' => 'Tunai',  'desc' => 'Bayar tunai via kurir atau di outlet',
                     'feather' => 'dollar-sign', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"],
                    ['id' => 'qris', 'label' => 'Non tunai',  'desc' => 'Bayar via aplikasi (QRIS & transfer)',
                     'feather' => 'grid', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"],
                ];
            @endphp

            <div class="flex flex-col">
                @foreach($payments as $i => $pay)
                    <x-zyngga-radio-row 
                        name="payment"
                        id="payment-{{ $pay['id'] }}"
                        value="{{ $pay['id'] }}"
                        :label="$pay['label']"
                        :description="$pay['desc']"
                        :checked="$pay['id'] === 'cash'"
                    >
                        <x-slot:icon>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-yellow-50">
                                <i data-feather="{{ $pay['feather'] }}" class="w-[18px] h-[18px] text-zyngga-yellow-300"></i>
                            </div>
                        </x-slot:icon>
                    </x-zyngga-radio-row>
                    @if ($i < count($payments) - 1)
                        <x-zyngga-divider class=" my-2" />
                    @endif
                @endforeach
            </div>
        </x-zyngga-card>
    </form>

    {{-- ── STICKY FOOTER ──────────────────────────────────────── --}}
    <div id="sticky-footer">
        <div class="max-w-5xl mx-auto w-full px-5 flex items-center justify-between">
            <div>
                <x-zyngga-text id="footer-service-label" variant="base" weight="medium" class="m-0">Memuat...</x-zyngga-text>
                <div class="flex items-center gap-1.5 mt-1">
                    <i data-feather="info" class="w-3.5 h-3.5 text-[#1660C1]"></i>
                    <x-zyngga-text id="footer-eta" variant="xs" color="primary" class="m-0">Menghitung estimasi...</x-zyngga-text>
                </div>
            </div>

            <x-zyngga-button 
                type="button"
                variant="primary"
                size="l"
                label="Buat Pesanan"
                class="ml-4"
                onclick="validateAndConfirm()"
            />
        </div>
    </div>

            </div>
        </main>
</div>

{{-- ── MODAL: KONFIRMASI PESANAN ───────────────────────────── --}}
<x-zyngga-selection-modal 
    id="confirm-modal-root" 
    openEvent="open-confirm-modal"
    closeEvent="close-confirm-modal"
>
    <x-zyngga-confirm-view 
        :image="asset('images/illustrations/confirm_order.png')"
        title="Yakin ingin membuat pesanan ini?"
        description="Apakah Anda yakin ingin melanjutkan? Periksa detailnya sebelum melanjutkan."
        primaryLabel="Buat Pesanan"
        secondaryLabel="Batalkan"
        primaryAction="submitOrder()"
        secondaryAction="isOpen=false"
    />
</x-zyngga-selection-modal>

{{-- ── MODAL: KONFIRMASI KEMBALI ────────────────────────────── --}}
<x-zyngga-selection-modal 
    id="back-modal-root" 
    openEvent="open-back-modal"
    closeEvent="close-back-modal"
>
    <x-zyngga-confirm-view 
        :image="asset('images/illustrations/cancel_order.png')"
        title="Batal buat pesanan?"
        description="Data yang sudah Anda masukkan akan hilang jika Anda kembali ke halaman sebelumnya."
        primaryLabel="Ya, Batalkan"
        secondaryLabel="Tetap di Sini"
        primaryAction="window.location.href='{{ route('order.cancel') }}'"
        secondaryAction="isOpen=false"
    />
</x-zyngga-selection-modal>

{{-- ── MODAL: PILIH PARFUM ────────────────────────────────── --}}
<x-zyngga-selection-modal 
    id="parfum-modal-root" 
    title="Pilih Parfum"
    openEvent="open-parfum-modal"
    closeEvent="close-parfum-modal"
>
    @php $parfums = ['Lavender','Rose','Jasmine','Fresh','Unscented']; @endphp
    @foreach ($parfums as $i => $p)
        <x-zyngga-radio-row 
            name="modal_parfum"
            id="parfum-radio-row-{{ $p }}"
            value="{{ $p }}"
            :label="$p"
            size="M"
            :checked="$p === 'Lavender'"
            onclick="chooseParfum('{{ $p }}')"
        />
        @if ($i < count($parfums) - 1)
            <x-zyngga-divider class=" my-2" />
        @endif
    @endforeach
</x-zyngga-selection-modal>

{{-- ── MODAL: CATATAN ──────────────────────────────────────── --}}
<x-zyngga-selection-modal 
    id="catatan-modal-root" 
    title="Tambah Catatan"
    openEvent="open-catatan-modal"
    closeEvent="close-catatan-modal"
>
    <div class="space-y-4">
        <div class="relative">
            <textarea 
                id="modal-catatan-input"
                maxlength="60"
                class="w-full h-32 p-4 border-[1.5px] border-zyngga-blue-50 rounded-xl focus:border-zyngga-blue-300 focus:ring-0 outline-none transition-all duration-200 text-sm placeholder-zyngga-neutral-400"
                placeholder="Tambahkan catatan untuk pesananmu"
                oninput="document.getElementById('catatan-char-count').textContent = this.value.length"
            ></textarea>
            <div class="absolute bottom-3 right-4 text-xs text-zyngga-neutral-400">
                <span id="catatan-char-count">0</span>/60
            </div>
        </div>
        
        <x-zyngga-button 
            type="button"
            variant="primary"
            size="l"
            label="Simpan"
            class="w-full"
            onclick="saveCatatan()"
        />
    </div>
</x-zyngga-selection-modal>

<script>
    // ── Service catalogue (mirrors PHP $allServices) ───────────
    const ALL_SERVICES = [
        { id: 'regular', name: 'Regular', desc: 'Layanan 3 hari (72 jam)',   price: 'Rp4.850/kg',      eta: 3 },
        { id: 'quick',   name: 'Quick',   desc: 'Layanan 2 hari (48 jam)',   price: 'Rp6.000/kg',      eta: 2 },
        { id: 'express', name: 'Express', desc: 'Layanan 1 hari (24 jam)',   price: 'Rp6.250/kg',      eta: 1 },
        { id: 'kilat',   name: 'Kilat',   desc: 'Layanan 5 jam',              price: 'Rp7.850/kg',      eta: 0 },
        { id: 'satuan',  name: 'Satuan',  desc: 'Selimut, Bed Cover, dll.',  price: 'Mulai Rp10.000',  eta: 3 },
    ];

    // The service currently selected (read from the server-rendered initial value)
    let selectedId = document.getElementById('selected_service_id').value || 'regular';

    // ── Render both card slots ─────────────────────────────────
    function renderCardSlots() {
        const selIdx   = ALL_SERVICES.findIndex(s => s.id === selectedId);
        const selected = ALL_SERVICES[selIdx] || ALL_SERVICES[0];

        // Slot 1: the next service after selected (wraps around, skipping selected)
        const altIdx   = (selIdx + 1) % ALL_SERVICES.length;
        const alt      = ALL_SERVICES[altIdx];

        // Populate slot 0 (selected)
        fillSlot(0, selected, true);

        // Populate slot 1 (alternative — not selected)
        fillSlot(1, alt, false);
    }

    function fillSlot(slot, svc, isSelected) {
        document.getElementById(`slot${slot}-name`).textContent  = svc.name;
        document.getElementById(`slot${slot}-desc`).textContent  = svc.desc;
        document.getElementById(`slot${slot}-price`).textContent = svc.price;

        const el    = document.getElementById(`card-slot-${slot}`);

        // Store which service this slot represents for click handler
        el.dataset.serviceId = svc.id;

        if (isSelected) {
            el.classList.add('selected');
        } else {
            el.classList.remove('selected');
        }
    }

    // ── Clicking a card slot selects that slot's service ───────
    function cardSlotClick(slot) {
        const el = document.getElementById(`card-slot-${slot}`);
        if (!el) return;
        applySelection(el.dataset.serviceId);
    }

    // ── Core: apply selection state everywhere ─────────────────
    function applySelection(id) {
        selectedId = id;

        // 1. Hidden input
        document.getElementById('selected_service_id').value = id;

        // 2. Footer label & ETA
        updateFooterServiceLabel(id);

        // 3. Update Session
        isDirty = true;
        updateOrderSession({ service: id });
    }

    function updateFooterServiceLabel(id) {
        const svc = ALL_SERVICES.find(s => s.id === id) || ALL_SERVICES[0];
        
        // Label: Name (X hari) or Name (Hari yang sama)
        const daysLabel = svc.eta === 0 ? 'Hari yang sama' : `${svc.eta} hari`;
        document.getElementById('footer-service-label').textContent = `${svc.name} (${daysLabel})`;
        
        // ETA Date calculation based on Pickup Date
        const pickupDateVal = document.getElementById('pickup_date').value;
        const offset = (pickupDateVal === 'tomorrow') ? 1 : 0;

        const d = new Date();
        d.setDate(d.getDate() + svc.eta + offset);
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const formattedDate = `${dayNames[d.getDay()]}, ${d.getDate()} ${monthNames[d.getMonth()]}`;
        
        document.getElementById('footer-eta').textContent = `Estimasi Selesai: ${formattedDate}`;

        // 3. Re-render card slots: slot-0 = selected, slot-1 = alternative
        renderCardSlots();

        // 4. Sync modal radios
        document.querySelectorAll('input[name="modal_service"]').forEach(r => {
            r.checked = (r.value === id);
        });
    }

    // ── Called from in-card click (legacy wrapper, kept for safety)
    function selectService(id) { applySelection(id); }

    // ── Modal open / close ─────────────────────────────────────
    function openServiceModal() {
        window.dispatchEvent(new CustomEvent('open-service-modal'));
    }
    function closeServiceModal() {
        window.dispatchEvent(new CustomEvent('close-service-modal'));
    }

    // ── Called from modal row click ────────────────────────────
    function selectServiceFromModal(id) {
        applySelection(id);
        closeServiceModal();
    }

    const CURRENT_HOUR = {{ \Carbon\Carbon::now('Asia/Jakarta')->hour }};

    // ── Date selection ─────────────────────────────────────────
    function selectDate(val, el) {
        document.querySelectorAll('.date-btn').forEach(e => e.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('pickup_date').value = val;
        
        // Refresh ETA based on new date
        applySelection(selectedId);
        isDirty = true;
        updateOrderSession({ pickup_date: val });
    }

    // ── Init on page load ──────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        applySelection(selectedId);
        
        // Enforce 17:00 threshold for "today" button
        if (CURRENT_HOUR >= 17) {
            const todayBtn = document.querySelector('button[onclick*="selectDate(\'today\'"]');
            if (todayBtn) {
                todayBtn.disabled = true;
                todayBtn.classList.add('opacity-30', 'cursor-not-allowed');
                // Auto-select tomorrow if today is selected and now disabled
                if (todayBtn.classList.contains('selected')) {
                    const tomorrowBtn = document.querySelector('button[onclick*="selectDate(\'tomorrow\'"]');
                    if (tomorrowBtn) selectDate('tomorrow', tomorrowBtn);
                }
            }
        }
    });



    // ── Parfum picker ──────────────────────────────────────────
    function openParfumPicker() {
        window.dispatchEvent(new CustomEvent('open-parfum-modal'));
    }
    function closeParfumPicker() {
        window.dispatchEvent(new CustomEvent('close-parfum-modal'));
    }
    function chooseParfum(val) {
        // Update hidden input and label
        document.getElementById('selected-parfum').textContent = val;
        document.getElementById('parfum').value = val;
        isDirty = true;
        updateOrderSession({ parfum: val });

        // Sync radios
        document.querySelectorAll('input[name="modal_parfum"]').forEach(r => {
            r.checked = (r.value === val);
        });

        closeParfumPicker();
    }

    // ── Catatan ────────────────────────────────────────────────
    function openCatatan() {
        window.dispatchEvent(new CustomEvent('open-catatan-modal'));
    }
    function closeCatatan() {
        window.dispatchEvent(new CustomEvent('close-catatan-modal'));
    }
    function saveCatatan() {
        const val = document.getElementById('modal-catatan-input').value.trim();
        const label = document.getElementById('catatan-label');
        const noteInput = document.getElementById('note');

        if (val) {
            label.textContent = val;
            label.classList.remove('text-zyngga-neutral-400');
            label.classList.add('text-zyngga-neutral-900'); // Use dark color for filled note
        } else {
            label.textContent = 'Buat catatan';
            label.classList.add('text-zyngga-neutral-400');
            label.classList.remove('text-zyngga-neutral-900');
        }

        if (noteInput) {
            noteInput.value = val;
            isDirty = true;
            updateOrderSession({ note: val });
        }

        closeCatatan();
    }

    function updateOrderSession(data) {
        fetch('{{ route('order.update-session') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
    }

    // ── Validation Helpers ─────────────────────────────────────
    function showError(fieldId, message) {
        const errorEl = document.getElementById(`error-${fieldId}`);
        const inputEl = document.getElementById(fieldId);
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        }
        // Assuming your x-zyngga-input has an internal input we can style, 
        // but for now we focus on the text message.
    }

    function clearErrors() {
        document.querySelectorAll('[id^="error-"]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    function validateGuestDetails() {
        clearErrors();
        let isValid = true;

        const nameEl  = document.getElementById('customer_name');
        const phoneEl = document.getElementById('customer_phone');
        const emailEl = document.getElementById('customer_email');

        // If these elements don't exist (user is logged in), skip validation
        if (!nameEl) return true;

        const name  = nameEl.value.trim();
        const phone = phoneEl.value.trim();
        const email = emailEl.value.trim();

        // Validate Name
        if (!name) {
            showError('customer_name', 'Nama lengkap tidak boleh kosong');
            isValid = false;
        } else if (/\d/.test(name)) {
            showError('customer_name', 'Nama tidak boleh mengandung angka');
            isValid = false;
        }

        // Validate Phone
        if (!phone) {
            showError('customer_phone', 'Nomor WhatsApp tidak boleh kosong');
            isValid = false;
        }

        // Validate Email
        if (!email) {
            showError('customer_email', 'Alamat email tidak boleh kosong');
            isValid = false;
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('customer_email', 'Format email tidak valid');
                isValid = false;
            }
        }

        return isValid;
    }

    function validateAndConfirm() {
        if (validateGuestDetails()) {
            window.dispatchEvent(new CustomEvent('open-confirm-modal'));
        } else {
            // Scroll to top of the card if needed, or just focus first error
            const firstError = document.querySelector('[id^="error-"]:not(.hidden)');
            if (firstError) {
                firstError.previousElementSibling.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    // ── Prevent Refresh/Leave Warning ──────────────────────────
    let isDirty = false;

    // Monitor guest inputs for changes
    ['customer_name', 'customer_phone', 'customer_email'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            let debounceTimer;
            el.addEventListener('input', () => {
                isDirty = true;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    updateOrderSession({ [id]: el.value.trim() });
                }, 500);
            });
        }
    });

    // Also track other interactions like notes or parfum changes
    document.getElementById('modal-catatan-input')?.addEventListener('input', () => isDirty = true);

    // Trap the browser back button to show the beautiful modal
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        if (isDirty) {
            window.dispatchEvent(new CustomEvent('open-back-modal'));
            // Re-push to keep trapping
            history.pushState(null, null, location.href);
        } else {
            // If not dirty, actually go back
            window.location.href = "{{ route('home') }}";
        }
    };

    // ── Confirm Modal Submit ─────────────────────────────────────
    function submitOrder() {
        // Final check before submission
        if (validateGuestDetails()) {
            isDirty = false; // Disable warning on legitimate submit
            document.getElementById('page-content').submit();
        } else {
            window.dispatchEvent(new CustomEvent('close-confirm-modal'));
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        setTimeout(() => feather.replace(), 500);

        // ── Restore State from Session ──
        const initialNote = "{{ $note }}";
        if (initialNote) {
            const label = document.getElementById('catatan-label');
            if (label) {
                label.textContent = initialNote;
                label.classList.remove('text-zyngga-neutral-400');
                label.classList.add('text-zyngga-neutral-900');
            }
            const input = document.getElementById('modal-catatan-input');
            if (input) {
                input.value = initialNote;
                const count = document.getElementById('catatan-char-count');
                if (count) count.textContent = initialNote.length;
            }
        }

        const initialParfum = "{{ $parfum }}";
        if (initialParfum) {
            const pLabel = document.getElementById('selected-parfum');
            if (pLabel) pLabel.textContent = initialParfum;
            const radio = document.querySelector(`input[name="modal_parfum"][value="${initialParfum}"]`);
            if (radio) radio.checked = true;
        }

        const pDate = "{{ $pickupDate }}";
        if (pDate) {
            const dateBtn = document.querySelector(`button[onclick*="selectDate('${pDate}'"]`);
            if (dateBtn) selectDate(pDate, dateBtn);
        }


    });
    document.addEventListener('livewire:load', function () {
        feather.replace();
    });
    document.addEventListener('livewire:navigated', function () {
        feather.replace();
    });
</script>

@livewireScripts
</body>
</html>
