<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konfirmasi Pengantaran – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; }

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

        /* ── addon row ── */
        .addon-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 32px;
            cursor: pointer;
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

    <div class="min-h-screen flex flex-col" x-data="{ isDirty: false }">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Konfirmasi Pengantaran" 
            :backUrl="route('order.request.delivery', $order['id'])" 
            :backAction="'window.location.href=\'' . route('order.request.delivery', $order['id']) . '\''"
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[88px] pt-4">
                {{-- ── FORM CONTENT ──────────────────────────────────── --}}
                <form method="POST" action="{{ route('order.request.delivery.store', $order['id']) }}" id="page-content" class="flex-1 flex flex-col space-y-4">
                    @csrf
                    
                    <input type="hidden" name="address" value="{{ $address }}">
                    <input type="hidden" name="detail_address" value="{{ $detailAddress }}">
                    <input type="hidden" name="lat" value="{{ $lat }}">
                    <input type="hidden" name="lng" value="{{ $lng }}">

                    @php
                        $todayCarbon = \Carbon\Carbon::now('Asia/Jakarta');
                        $currentHour = $todayCarbon->hour;
                        $isTodayDisabled = $currentHour >= 17;
                        $defaultDate = $isTodayDisabled ? 'tomorrow' : 'today';
                    @endphp
                    <input type="hidden" name="pickup_date" id="pickup_date" value="{{ $defaultDate }}">
                    <input type="hidden" name="pickup_time" id="pickup_time" value="Standard">
                    <input type="hidden" name="catatan" id="note" value="">

                    {{-- ── LOKASI PENGANTARAN ────────────────────────────── --}}
                    <x-zyngga-card title="Lokasi Pengantaran">
                        <x-slot:headerAction>
                            <x-zyngga-button 
                                type="a"
                                href="{{ route('order.request.delivery', $order['id']) }}?lat={{ $lat }}&lng={{ $lng }}&address={{ urlencode($address) }}"
                                variant="secondary"
                                size="s"
                                label="Ubah"
                            />
                        </x-slot:headerAction>
                        
                        {{-- Map thumbnail ── --}}
                        <div id="map-thumb" class="mb-4 relative">
                            <iframe
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps/embed/v1/search?key={{ config('services.google.maps_key') }}&q={{ $lat }},{{ $lng }}&zoom=18&maptype=roadmap"
                                style="pointer-events:none;"
                            ></iframe>
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
                                @if($detailAddress)
                                    <x-zyngga-text variant="xs" color="neutral-700" class="mt-1 font-medium">
                                        Detail: {{ $detailAddress }}
                                    </x-zyngga-text>
                                @endif
                            </div>
                        </div>
                    </x-zyngga-card>

                    {{-- ── JADWAL PENGANTARAN ──────────────────────────────── --}}
                    <x-zyngga-card title="Jadwal Pengantaran">
                        <div class="space-y-4">
                            <div class="flex gap-2">
                                @php
                                    $tomorrowCarbon = $todayCarbon->copy()->addDay();
                                @endphp
                                <button type="button" id="date-today" class="date-btn {{ !$isTodayDisabled ? 'selected' : 'opacity-40 bg-gray-50 pointer-events-none' }}" 
                                        onclick="selectDate('today')" {{ $isTodayDisabled ? 'disabled' : '' }}>
                                    <x-zyngga-text variant="sm" weight="medium" class="m-0">Hari ini</x-zyngga-text>
                                    <x-zyngga-text variant="xs" color="neutral-500" class="m-0 mt-0.5">{{ $todayCarbon->isoFormat('D MMM YYYY') }}</x-zyngga-text>
                                </button>
                                <button type="button" id="date-tomorrow" class="date-btn {{ $isTodayDisabled ? 'selected' : '' }}" 
                                        onclick="selectDate('tomorrow')">
                                    <x-zyngga-text variant="sm" weight="medium" class="m-0">Besok</x-zyngga-text>
                                    <x-zyngga-text variant="xs" color="neutral-500" class="m-0 mt-0.5">{{ $tomorrowCarbon->isoFormat('D MMM YYYY') }}</x-zyngga-text>
                                </button>
                            </div>
                        </div>
                    </x-zyngga-card>

                    {{-- ── CATATAN OPERASIONAL ───────────────────────────── --}}
                    <x-zyngga-card>
                        <div class="addon-row flex items-center justify-between gap-2 overflow-hidden" onclick="openCatatan()">
                            <x-zyngga-text variant="sm" weight="regular" class="m-0 shrink-0">Catatan Driver (Opsional)</x-zyngga-text>
                            <div class="flex items-center gap-1 min-w-0 flex-1 justify-end max-w-[50%]">
                                <x-zyngga-text id="catatan-label" variant="sm" color="neutral-500" class="m-0 truncate text-right">Buat catatan</x-zyngga-text>
                                <i data-feather="chevron-right" class="w-4 h-4 text-[#808080] shrink-0"></i>
                            </div>
                        </div>
                    </x-zyngga-card>
                </form>
            </div>
        </main>

        {{-- ── STICKY FOOTER ──────────────────────────────────────── --}}
        <div id="sticky-footer">
            <div class="max-w-5xl mx-auto w-full px-5 flex items-center justify-between">
                <div>
                    <x-zyngga-text variant="base" weight="medium" class="m-0">Konfirmasi Alamat</x-zyngga-text>
                    <x-zyngga-text variant="xs" color="neutral-500" class="m-0">Harap pastikan alamat sudah benar</x-zyngga-text>
                </div>

                <x-zyngga-button 
                    type="button"
                    variant="primary"
                    size="l"
                    label="Konfirmasi Pengantaran"
                    class="ml-4"
                    onclick="submitDeliveryForm()"
                />
            </div>
        </div>

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
                        placeholder="Tambahkan instruksi pengiriman khusus (misal: titip satpam)"
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
    </div>

    <script>
        function selectDate(val) {
            document.getElementById('pickup_date').value = val;
            const todayBtn = document.getElementById('date-today');
            const tomorrowBtn = document.getElementById('date-tomorrow');

            if (val === 'today') {
                todayBtn.classList.add('selected');
                tomorrowBtn.classList.remove('selected');
            } else {
                tomorrowBtn.classList.add('selected');
                todayBtn.classList.remove('selected');
            }
        }

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
                label.classList.add('text-zyngga-neutral-900');
            } else {
                label.textContent = 'Buat catatan';
                label.classList.add('text-zyngga-neutral-400');
                label.classList.remove('text-zyngga-neutral-900');
            }

            if (noteInput) {
                noteInput.value = val;
            }

            closeCatatan();
        }

        function submitDeliveryForm() {
            document.getElementById('page-content').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
