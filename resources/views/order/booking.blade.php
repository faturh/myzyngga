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
            padding-bottom: 100px; /* space for sticky footer */
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
        .time-chip:hover {
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
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 768px; /* Tablet width */
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
                left: 50%;
                transform: translateX(-50%);
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

    <div class="min-h-screen flex flex-col">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Pemesanan Pickup" 
            :backUrl="route('order.pickup', ['service' => $service])" 
            :maxWidth="'max-w-3xl'"
            :showPoints="false"
            :showMenu="true"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-3xl mx-auto px-5">
                {{-- ── SCROLLABLE CONTENT ──────────────────────────────────── --}}
                <form method="POST" action="{{ route('order.confirm') }}" id="page-content" class="flex-1 flex flex-col">
        @csrf
        <input type="hidden" name="service"        value="{{ $service }}">
        <input type="hidden" name="address"        value="{{ $address }}">
        <input type="hidden" name="detail_address" value="{{ $detailAddress }}">
        <input type="hidden" name="lat"            value="{{ $lat }}">
        <input type="hidden" name="lng"            value="{{ $lng }}">
        <input type="hidden" name="selected_service_id" id="selected_service_id" value="{{ strtolower($serviceLabel) }}">
        <input type="hidden" name="pickup_date"   id="pickup_date"   value="today">
        <input type="hidden" name="pickup_time"   id="pickup_time"   value="10:00">
        <input type="hidden" name="parfum"        id="parfum"        value="Lavender">

        {{-- ── LOKASI PICKUP ─────────────────────────────────── --}}
        <x-zyngga-card title="Lokasi Pickup">
            <x-slot:headerAction>
                <x-zyngga-button 
                    type="a"
                    href="{{ route('order.pickup', ['service' => $service]) }}"
                    variant="secondary"
                    size="s"
                    label="Ubah"
                />
            </x-slot:headerAction>

            {{-- Map thumbnail — clickable → edit pickup location --}}
            <div id="map-thumb" class="mb-4 relative">
                <iframe
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_key') }}&q={{ urlencode($address) }}&zoom=15&maptype=roadmap"
                    style="pointer-events:none;"
                ></iframe>
                {{-- Transparent overlay captures click → navigate to edit page --}}
                <a
                    href="{{ route('order.pickup', ['service' => $service]) }}"
                    class="absolute inset-0 z-10 block cursor-pointer"
                    aria-label="Edit lokasi pickup"
                    title="Edit lokasi pickup"
                ></a>
            </div>

            {{-- Address --}}
            <div class="mb-3">
                <x-zyngga-text variant="sm" weight="medium" class="mb-1">
                    {{ $address }}
                </x-zyngga-text>
                @if($detailAddress)
                    <x-zyngga-text variant="xs" color="neutral-500">
                        {{ $detailAddress }}
                    </x-zyngga-text>
                @endif
            </div>

            {{-- Detail lokasi input --}}
            <x-zyngga-input 
                name="detail_address_edit"
                placeholder="Detail Lokasi"
                value="{{ $detailAddress }}"
                onkeydown="if(event.key === 'Enter') event.preventDefault();"
            >
                <x-slot:iconRight>
                    <i data-feather="edit-2" class="w-4 h-4 text-[#808080]"></i>
                </x-slot:iconRight>
            </x-zyngga-input>
        </x-zyngga-card>

        {{-- ── JENIS LAYANAN ──────────────────────────────────── --}}
        <x-zyngga-card title="Jenis Layanan">
            <x-slot:headerAction>
                <button type="button" onclick="openServiceModal()">
                    <x-zyngga-text variant="xs" weight="medium" color="primary">Lihat semua</x-zyngga-text>
                </button>
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
        <div
            id="service-modal"
            style="
                display: none;
                position: fixed;
                inset: 0;
                background: #F4F4F4;
                z-index: 100;
                align-items: center;
                justify-content: center;
            "
            onclick="closeServiceModal(event)"
        >
            <div
                id="service-modal-box"
                style="
                    width: 385px;
                    max-width: calc(100vw - 40px);
                    background: white;
                    border-radius: 16px;
                    padding: 20px;
                    box-shadow: 0 8px 40px rgba(0,0,0,0.16);
                    max-height: 90vh;
                    overflow-y: auto;
                "
                onclick="event.stopPropagation()"
            >
                {{-- Modal header --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
                    <x-zyngga-text variant="lg" weight="medium">Jenis Layanan</x-zyngga-text>
                    <button
                        type="button"
                        onclick="closeServiceModal()"
                        style="
                            width:32px; height:32px;
                            border:none; background:none;
                            cursor:pointer;
                            display:flex; align-items:center; justify-content:center;
                            border-radius:50%;
                            transition:background 0.15s;
                        "
                        onmouseover="this.style.background='#F4F4F4'"
                        onmouseout="this.style.background='none'"
                        aria-label="Tutup"
                    >
                        <i data-feather="x" class="w-5 h-5 text-[#0F0F0F]"></i>
                    </button>
                </div>

                {{-- Service list --}}
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
                        <x-zyngga-divider class="mx-1 my-1" />
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ── JADWAL PICKUP ──────────────────────────────────── --}}
        <x-zyngga-card title="Jadwal Pickup">
            <div class="space-y-3">
                {{-- Date options --}}
                <div class="flex gap-2">
                    @php
                        $today    = \Carbon\Carbon::now('Asia/Jakarta');
                        $tomorrow = $today->copy()->addDay();
                    @endphp
                    <div class="date-btn selected" onclick="selectDate('today', this)">
                        <x-zyngga-text variant="sm" weight="medium" class="m-0">Hari ini</x-zyngga-text>
                        <x-zyngga-text variant="xs" color="neutral-500" class="m-0 mt-0.5">{{ $today->isoFormat('D MMM YYYY') }}</x-zyngga-text>
                    </div>
                    <div class="date-btn" onclick="selectDate('tomorrow', this)">
                        <x-zyngga-text variant="sm" weight="medium" class="m-0">Besok</x-zyngga-text>
                        <x-zyngga-text variant="xs" color="neutral-500" class="m-0 mt-0.5">{{ $tomorrow->isoFormat('D MMM YYYY') }}</x-zyngga-text>
                    </div>
                </div>

                {{-- Time chips --}}
                <div class="flex gap-2">
                    @foreach(['10:00','12:00','16:00','18:00'] as $time)
                        <button
                            type="button"
                            class="time-chip {{ $time === '10:00' ? 'selected' : '' }}"
                            onclick="selectTime('{{ $time }}', this)"
                        >
                            <x-zyngga-text variant="sm" weight="regular" class="inherit-color">{{ $time }}</x-zyngga-text>
                        </button>
                    @endforeach
                </div>
            </div>
        </x-zyngga-card>

        {{-- ── TAMBAHAN ───────────────────────────────────────── --}}
        <x-zyngga-card title="Tambahan">
            <div class="addon-row" onclick="openParfumPicker()">
                <x-zyngga-text variant="sm" weight="regular" class="m-0">Pilihan parfum</x-zyngga-text>
                <div class="flex items-center gap-1">
                    <x-zyngga-text id="selected-parfum" variant="sm" color="neutral-500" class="m-0">Lavender</x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-[#808080]"></i>
                </div>
            </div>

            <x-zyngga-divider class="mx-1 my-2" />
            <div class="addon-row" onclick="openCatatan()">
                <x-zyngga-text variant="sm" weight="regular" class="m-0">Catatan</x-zyngga-text>
                <div class="flex items-center gap-1">
                    <x-zyngga-text id="catatan-label" variant="sm" color="neutral-500" class="m-0">Buat catatan</x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-[#808080]"></i>
                </div>
            </div>
        </x-zyngga-card>

        {{-- ── METODE PEMBAYARAN ──────────────────────────────── --}}
        <x-zyngga-card title="Metode Pembayaran">
            @php
                $payments = [
                    ['id' => 'cash', 'label' => 'Cash',  'desc' => 'Pembayaran dilakukan kepada kurir',
                     'feather' => 'dollar-sign', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"],
                    ['id' => 'qris', 'label' => 'QRIS',  'desc' => 'Pembayaran dilakukan melalui admin',
                     'feather' => 'grid', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"],
                    ['id' => 'transfer', 'label' => 'Transfer Bank', 'desc' => 'Pembayaran dilakukan melalui admin',
                     'feather' => 'home', 'color' => "theme('colors.zyngga.yellow.300')", 'bg' => "theme('colors.zyngga.yellow.50')"],
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
                        <x-zyngga-divider class="mx-1 my-2" />
                    @endif
                @endforeach
            </div>
        </x-zyngga-card>
    </form>

    {{-- ── STICKY FOOTER ──────────────────────────────────────── --}}
    <div id="sticky-footer">
        <div>
            <x-zyngga-text id="footer-service-label" variant="base" weight="medium" class="m-0">Memuat...</x-zyngga-text>
            <div class="flex items-center gap-1.5 mt-1">
                <i data-feather="info" class="w-3.5 h-3.5 text-[#1660C1]"></i>
                <x-zyngga-text id="footer-eta" variant="xs" color="primary" class="m-0">Menghitung estimasi...</x-zyngga-text>
            </div>
        </div>

        <x-zyngga-button 
            type="submit"
            form="page-content"
            variant="primary"
            size="l"
            label="Buat Pesanan"
            class="ml-4"
        />
    </div>

            </div>
        </main>
</div>

{{-- ── PARFUM MODAL ───────────────────────────────────── --}}
<div
    id="parfum-modal"
    style="
        display: none;
        position: fixed;
        inset: 0;
        background: #F4F4F4;
        z-index: 100;
        align-items: center;
        justify-content: center;
    "
    onclick="closeParfumPicker(event)"
>
    <div
        id="parfum-modal-box"
        style="
            width: 385px;
            max-width: calc(100vw - 40px);
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.16);
            max-height: 90vh;
            overflow-y: auto;
        "
        onclick="event.stopPropagation()"
    >
        {{-- Modal header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <p style="font-size:18px; font-weight:700; color:theme('colors.zyngga.neutral.500'); margin:0;">Pilih Parfum</p>
            <button
                type="button"
                onclick="closeParfumPicker()"
                style="
                    width:32px; height:32px;
                    border:none; background:none;
                    cursor:pointer;
                    display:flex; align-items:center; justify-content:center;
                    border-radius:50%;
                    transition:background 0.15s;
                "
                onmouseover="this.style.background='#F4F4F4'"
                onmouseout="this.style.background='none'"
                aria-label="Tutup"
            >
                <i data-feather="x" class="w-5 h-5 text-[#0F0F0F]"></i>
            </button>
        </div>

        {{-- Parfum list --}}
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
                <x-zyngga-divider class="mx-1 my-2" />
            @endif
        @endforeach
    </div>
</div>

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
        document.getElementById('service-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeServiceModal() {
        document.getElementById('service-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // ── Called from modal row click ────────────────────────────
    function selectServiceFromModal(id) {
        applySelection(id);
        closeServiceModal();
    }

    // ── Init on page load ──────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        applySelection(selectedId);
    });

    // ── Date selection ─────────────────────────────────────────
    function selectDate(val, el) {
        document.querySelectorAll('.date-btn').forEach(e => e.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('pickup_date').value = val;
        
        // Refresh ETA based on new date
        applySelection(selectedId);
    }

    // ── Time selection ─────────────────────────────────────────
    function selectTime(val, el) {
        document.querySelectorAll('.time-chip').forEach(e => e.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('pickup_time').value = val;
    }



    // ── Parfum picker ──────────────────────────────────────────
    function openParfumPicker() {
        document.getElementById('parfum-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeParfumPicker() {
        document.getElementById('parfum-modal').style.display = 'none';
        document.body.style.overflow = '';
    }
    function chooseParfum(val) {
        // Update hidden input and label
        document.getElementById('selected-parfum').textContent = val;
        document.getElementById('parfum').value = val;

        // Sync radios
        document.querySelectorAll('input[name="modal_parfum"]').forEach(r => {
            r.checked = (r.value === val);
        });

        closeParfumPicker();
    }

    // ── Catatan ────────────────────────────────────────────────
    function openCatatan() {
        const note = prompt('Tulis catatan untuk kurir:');
        if (note) document.getElementById('catatan-label').textContent = note;
    }

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
