<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instruksi Pembayaran – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; min-height: 100%; }
        #qrcode canvas, #qrcode img {
            width: 100% !important;
            height: auto !important;
            max-width: 100%;
            object-fit: contain;
            mix-blend-mode: multiply;
        }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="paymentInstruction">
        <x-dashboard-header 
            title="Pembayaran" 
            :backUrl="'javascript:void(0)'" 
            :backAction="'window.dispatchEvent(new CustomEvent(\'open-back-confirm-modal\')); return false;'"
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="flex-1 flex flex-col relative w-full max-w-5xl mx-auto px-5 pb-[87px]">
            {{-- CARD 1: BAYAR SEBELUM & TOTAL BAYAR --}}
            <x-zyngga-card padding="p-4" gap="py-[6px]">
                <div class="flex items-center justify-between mb-3">
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-900">Bayar Sebelum</x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900" x-text="countdown">--:--:--</x-zyngga-text>
                </div>
                <div class="w-full h-px bg-[#F4F4F4] my-3"></div>
                <div class="flex items-center justify-between mt-3">
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-900">Total Bayar</x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="medium" color="blue-300">
                        Rp{{ number_format((float)($instruction['gross_amount'] ?? 0), 0, ',', '.') }}
                    </x-zyngga-text>
                </div>
            </x-zyngga-card>

            {{-- CARD 2: INSTRUKSI PEMBAYARAN (QRIS / VA) --}}
            <x-zyngga-card padding="p-4" class="text-center">
                @if(isset($instruction['va_number']))
                    <div class="flex items-center justify-between mb-6">
                        @php
                            $bankName = $instruction['bank'] ?? 'Bank';
                            $isOther = strtolower($bankName) === 'other';
                            $isCimb = strtolower($bankName) === 'cimb';
                        @endphp
                        <x-zyngga-text variant="base" weight="medium" color="neutral-900">
                            {{ $isOther ? 'Bank Lainnya' : ($isCimb ? 'Bank CIMB Niaga' : 'Bank ' . strtoupper($bankName)) }}
                        </x-zyngga-text>
                        @if($isOther)
                            <div class="w-8 h-8 bg-zyngga-neutral-100 rounded-lg flex items-center justify-center">
                                <i data-feather="grid" class="w-4 h-4 text-zyngga-neutral-400"></i>
                            </div>
                        @else
                            <img src="{{ asset('images/logos/' . strtolower($bankName) . '.png') }}" alt="{{ strtoupper($bankName) }}" class="h-6 object-contain" onerror="this.style.display='none'">
                        @endif
                    </div>
                    <div class="text-left">
                        @if(isset($instruction['biller_code']))
                            <x-zyngga-text variant="sm" weight="regular" color="neutral-900" class="mb-2">Kode Perusahaan</x-zyngga-text>
                            <div class="flex items-center justify-between bg-gray-50 rounded-xl py-3 px-4 mb-4">
                                <x-zyngga-text variant="base" weight="medium" color="neutral-900">{{ $instruction['biller_code'] }}</x-zyngga-text>
                                <x-zyngga-button 
                                    variant="tertiary" 
                                    size="m" 
                                    label="Salin" 
                                    icon="copy" 
                                    iconPosition="right" 
                                    onclick="navigator.clipboard.writeText('{{ $instruction['biller_code'] }}'); window.dispatchEvent(new CustomEvent('toast', {detail:{message:'Kode perusahaan disalin!', type:'success'}}))"
                                />
                            </div>
                        @endif

                        @if($isCimb)
                            <x-zyngga-text variant="sm" weight="regular" color="neutral-900" class="mb-2">Kode Bank</x-zyngga-text>
                            <div class="flex items-center justify-between bg-gray-50 rounded-xl py-3 px-4 mb-4">
                                <x-zyngga-text variant="base" weight="medium" color="neutral-900">022 - CIMB Niaga</x-zyngga-text>
                            </div>
                        @endif

                        <x-zyngga-text variant="sm" weight="regular" color="neutral-900" class="mb-2">Nomor Akun Virtual</x-zyngga-text>
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl py-3 px-4">
                            <x-zyngga-text variant="base" weight="medium" color="neutral-900">{{ $instruction['va_number'] }}</x-zyngga-text>
                            <x-zyngga-button 
                                variant="tertiary" 
                                size="m" 
                                label="Salin" 
                                icon="copy" 
                                iconPosition="right" 
                                onclick="navigator.clipboard.writeText('{{ $instruction['va_number'] }}'); window.dispatchEvent(new CustomEvent('toast', {detail:{message:'Nomor berhasil disalin!', type:'success'}}))"
                            />
                        </div>
                    </div>
                @elseif(isset($instruction['deeplink_url']))
                    <div class="flex flex-col items-center mt-2">
                        <div class="w-16 h-16 bg-zyngga-blue-50 text-zyngga-blue-500 rounded-full flex items-center justify-center mb-4">
                            <i data-feather="smartphone" class="w-8 h-8"></i>
                        </div>
                        <x-zyngga-text variant="base" weight="medium" color="neutral-900" class="mb-1">Buka Aplikasi Pembayaran</x-zyngga-text>
                        <x-zyngga-text variant="sm" color="neutral-500" class="mb-6">Silakan lanjutkan pembayaran melalui aplikasi di HP Anda.</x-zyngga-text>
                        
                        <a href="{{ $instruction['deeplink_url'] }}" target="_blank" class="block w-full py-3 bg-zyngga-blue-500 text-white rounded-full font-medium text-center mb-4">Buka Aplikasi Sekarang</a>
                    </div>

                    @if(isset($instruction['qr_image_url']) || isset($instruction['qr_string']))
                        <div class="mt-4 border-t border-zyngga-neutral-200 pt-6">
                            <x-zyngga-text variant="sm" weight="medium" color="neutral-500" class="mb-4">Atau scan QR Code (Jika di Desktop)</x-zyngga-text>
                            <div class="relative w-full max-w-[240px] aspect-square mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                                <div class="relative z-10 w-full h-full p-4 flex flex-col">
                                    <div id="qrcode" class="flex-1 w-full flex items-center justify-center bg-white overflow-hidden">
                                        @if(isset($instruction['qr_image_url']))
                                            <img src="{{ $instruction['qr_image_url'] }}" alt="QR Code" class="w-full h-full object-contain mix-blend-multiply">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <script>
                        // Auto-redirect for mobile devices
                        document.addEventListener('DOMContentLoaded', function() {
                            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                                window.open("{!! $instruction['deeplink_url'] !!}", "_blank");
                            }
                        });
                    </script>

                @elseif(isset($instruction['qr_image_url']) || isset($instruction['qr_string']))
                    <div class="relative w-full max-w-[340px] aspect-square mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                        {{-- Red Triangle Left --}}
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-0 h-0 border-t-[30px] border-t-transparent border-l-[30px] border-l-[#ED1C24] border-b-[30px] border-b-transparent"></div>
                        
                        {{-- Red Triangle Bottom Right --}}
                        <div class="absolute right-0 bottom-0 w-0 h-0 border-l-[80px] border-l-transparent border-b-[80px] border-b-[#ED1C24]"></div>

                        <div class="relative z-10 w-full h-full p-5 flex flex-col">
                            {{-- Header QRIS --}}
                            <div class="flex justify-between items-start mb-2">
                                <img src="{{ asset('images/logos/qris.png') }}" alt="QRIS" class="h-8">
                                <div class="text-right text-[11px] font-bold text-neutral-900 leading-tight">
                                    QR Code Standar<br>Pembayaran Nasional
                                </div>
                            </div>
                            
                            {{-- QR Code Container --}}
                            <div id="qrcode" class="flex-1 w-full flex items-center justify-center bg-white overflow-hidden">
                                @if(isset($instruction['qr_image_url']))
                                    <img src="{{ $instruction['qr_image_url'] }}" alt="QR Code" class="w-full h-full object-contain mix-blend-multiply">
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-4">
                        <a href="{{ $instruction['actions'][0]['url'] ?? '#' }}" target="_blank" class="block w-full py-3 bg-zyngga-blue-500 text-white rounded-full font-medium text-center">Buka Aplikasi Pembayaran</a>
                    </div>
                @endif
            </x-zyngga-card>

            
            {{-- CARD 3: BANTUAN/LAYANAN --}}
            <x-zyngga-card title="Bantuan/Layanan">
                <div class="flex flex-col gap-3">
                    <x-zyngga-dropdown-item 
                    type="a" 
                    href="{{ route('order.payment-method', $order['nota_layanan']) }}" 
                    icon="help-circle" 
                    size="M"
                    >Ubah Metode Pembayaran</x-zyngga-dropdown-item>
                    
                    <x-zyngga-dropdown-item 
                    type="a" 
                    href="#" 
                    icon="message-circle" 
                    size="M"
                    >Hubungi Kami</x-zyngga-dropdown-item>
                </div>
            </x-zyngga-card>
            
            {{-- CARD 4: ALUR PEMBAYARAN --}}
            <x-zyngga-card padding="p-4">
                <x-zyngga-text variant="base" weight="medium" color="neutral-900" class="mb-4">Cara Pembayaran</x-zyngga-text>
                <div class="flex flex-col">
                    @if(isset($instruction['steps']) && is_array($instruction['steps']))
                        @php 
                            $isAssoc = count(array_filter(array_keys($instruction['steps']), 'is_string')) > 0;
                            $loopCount = 0;
                        @endphp

                        @if($isAssoc)
                            <div class="flex flex-col text-left" x-data="{ activeChannel: '{{ array_key_first($instruction['steps']) }}' }">
                                @foreach($instruction['steps'] as $channel => $channelSteps)
                                    <div>
                                        <div class="flex items-center justify-between cursor-pointer py-2" @click="activeChannel = activeChannel === '{{ $channel }}' ? null : '{{ $channel }}'">
                                            <x-zyngga-text variant="sm" weight="medium" color="neutral-900">{{ $channel }}</x-zyngga-text>
                                            <svg class="w-5 h-5 text-zyngga-neutral-500 transition-transform duration-200" :class="{'rotate-180': activeChannel === '{{ $channel }}'}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                        <div x-show="activeChannel === '{{ $channel }}'" x-collapse class="mt-2 pl-2">
                                            <ol class="list-decimal pl-4 space-y-2 marker:text-zyngga-neutral-400 text-sm text-zyngga-neutral-400">
                                                @foreach($channelSteps as $step)
                                                    <li class="pl-1">
                                                        {!! $step !!}
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>
                                    @php $loopCount++; @endphp
                                    @if(!$loop->last)
                                        <x-zyngga-divider class="my-4" />
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-left">
                                <ol class="list-decimal pl-5 space-y-2 marker:text-zyngga-neutral-400 text-sm text-zyngga-neutral-400">
                                    @foreach($instruction['steps'] as $step)
                                        <li class="pl-1">
                                            {!! $step !!}
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    @else
                        <x-zyngga-text variant="sm" color="neutral-500">Instruksi pembayaran tidak tersedia.</x-zyngga-text>
                    @endif
                </div>
            </x-zyngga-card>
            
        </main>

        <div id="sticky-footer" class="fixed bottom-0 left-0 right-0 bg-white border-t border-zyngga-neutral-200 py-4 z-40">
            <div class="w-full max-w-5xl mx-auto px-5 flex items-center justify-center h-12">
                @if(isset($instruction['va_number']))
                    <x-zyngga-button 
                        type="button"
                        size="l"
                        variant="primary" 
                        class="w-full h-full font-medium"
                        label="Salin Nomor VA"
                        onclick="navigator.clipboard.writeText('{{ $instruction['va_number'] }}'); window.dispatchEvent(new CustomEvent('toast', {detail:{message:'Nomor berhasil disalin!', type:'success'}}))"
                    />
                @elseif(isset($instruction['qr_image_url']) || isset($instruction['qr_string']))
                    <x-zyngga-button 
                        type="button"
                        size="l"
                        variant="primary" 
                        class="w-full h-full font-medium"
                        label="Unduh Kode QR"
                        onclick="downloadQR()"
                    />
                @else
                    <x-zyngga-button 
                        type="button"
                        size="l"
                        variant="primary" 
                        class="w-full h-full font-medium !bg-red-500 !border-red-500"
                        label="Batalkan Pembayaran"
                        @click="cancelPayment"
                    />
                @endif
            </div>
        </div>

        {{-- ── MODAL: PAYMENT SUCCESS ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="payment-success-modal" 
            openEvent="open-payment-success-modal"
            closeEvent="close-payment-success-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/payment_success.png')"
                title="Pembayaran Berhasil!"
                description="Terima kasih, pembayaran untuk pesanan Anda telah berhasil diverifikasi."
                primaryLabel="Lihat Detail Pesanan"
                primaryAction="window.location.href = '{{ route('order.detail', $order['nota_layanan']) }}'"
            />
            <div class="mt-4 text-center" 
                 x-data="{ count: 5 }" 
                 @open-payment-success-modal.window="
                     let intervalId = setInterval(() => { 
                         count--; 
                         if (count <= 0) {
                             clearInterval(intervalId);
                             window.location.href = '{{ route('order.detail', $order['nota_layanan']) }}'; 
                         }
                     }, 1000)
                 ">
                <x-zyngga-text variant="sm" color="neutral-500">
                    Dialihkan otomatis dalam <span x-text="count">5</span> detik...
                </x-zyngga-text>
            </div>
        </x-zyngga-selection-modal>

        {{-- ── MODAL: PAYMENT FAILED ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="payment-failed-modal" 
            openEvent="open-payment-failed-modal"
            closeEvent="close-payment-failed-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/payment_failed.png')"
                title="Pembayaran Gagal"
                description="Pembayaran Anda gagal diproses atau dibatalkan."
                primaryLabel="Tutup"
                primaryAction="window.location.href = '{{ route('order.detail', $order['nota_layanan']) }}'"
            />
        </x-zyngga-selection-modal>

        {{-- ── MODAL: PAYMENT TIMEOUT ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="payment-timeout-modal" 
            openEvent="open-payment-timeout-modal"
            closeEvent="close-payment-timeout-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/payment_timeout.png')"
                title="Pembayaran Kadaluarsa"
                description="Batas waktu pembayaran telah habis."
                primaryLabel="Tutup"
                primaryAction="window.location.href = '{{ route('order.detail', $order['nota_layanan']) }}'"
            />
        </x-zyngga-selection-modal>
        {{-- ── MODAL: CONFIRM BACK ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="back-confirm-modal" 
            openEvent="open-back-confirm-modal"
            closeEvent="close-back-confirm-modal"
        >
            <x-zyngga-confirm-view 
                :image="asset('images/illustrations/cancel_order.png')"
                title="Batalkan Pembayaran?"
                description="Apakah Anda yakin ingin kembali? Proses pembayaran ini akan dibatalkan."
                primaryLabel="Ya, Batalkan"
                primaryAction="cancelPayment()"
                secondaryLabel="Kembali"
                secondaryAction="window.dispatchEvent(new CustomEvent('close-back-confirm-modal'))"
            />
        </x-zyngga-selection-modal>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('paymentInstruction', () => ({
                status: '{{ $instruction['status'] }}',
                checkInterval: null,
                countdown: '--:--:--',
                expiryTime: '{{ $instruction['expiry_time'] ?? '' }}',
                countdownInterval: null,
                init() {
                    this.checkInterval = setInterval(() => this.checkStatus(), 3000);
                    if (this.expiryTime) {
                        this.updateCountdown();
                        this.countdownInterval = setInterval(() => this.updateCountdown(), 1000);
                    }
                    @if(isset($instruction['qr_string']) && !isset($instruction['qr_image_url']))
                        setTimeout(() => {
                            new QRCode(document.getElementById('qrcode'), {
                                text: '{!! $instruction['qr_string'] ?? '' !!}',
                                width: 256,
                                height: 256,
                                colorDark : '#000000',
                                colorLight : '#ffffff',
                                correctLevel : QRCode.CorrectLevel.H
                            });
                        }, 100);
                    @endif
                },
                updateCountdown() {
                    if (!this.expiryTime) return;
                    let expiryDate = new Date(this.expiryTime.replace(' ', 'T')); // Handle Safari parsing if needed
                    let now = new Date();
                    let diff = expiryDate - now;
                    if (diff <= 0) {
                        this.countdown = '00:00:00';
                        if (this.countdownInterval) clearInterval(this.countdownInterval);
                        return;
                    }
                    let h = String(Math.floor(diff / 3600000)).padStart(2, '0');
                    let m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                    let s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
                    this.countdown = `${h}:${m}:${s}`;
                },
                checkStatus() {
                    fetch('{{ route('order.payment-status', $order['nota_layanan']) }}')
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === 'settlement' || data.status === 'capture' || data.status === 'paid') {
                                clearInterval(this.checkInterval);
                                window.dispatchEvent(new CustomEvent('open-payment-success-modal'));
                            } else if (data.status === 'cancel' || data.status === 'deny') {
                                clearInterval(this.checkInterval);
                                window.dispatchEvent(new CustomEvent('open-payment-failed-modal'));
                            } else if (data.status === 'expire') {
                                clearInterval(this.checkInterval);
                                window.dispatchEvent(new CustomEvent('open-payment-timeout-modal'));
                            }
                        });
                },
                cancelPayment() {
                    fetch('{{ route('order.payment-cancel', $order['nota_layanan']) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    }).then(() => {
                        window.location.href = '{{ route('order.detail', $order['nota_layanan']) }}';
                    });
                }
            }));
        });

        // Function to truly download the QR code image
        function downloadQR() {
            let canvas = document.querySelector('#qrcode canvas');
            if (canvas) {
                let link = document.createElement('a');
                link.download = 'QR_Code_Pembayaran.png';
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.dispatchEvent(new CustomEvent('toast', {detail:{message:'Kode QR berhasil diunduh!', type:'success'}}));
                return;
            }
            
            let img = document.querySelector('#qrcode img');
            if (img) {
                // Gunakan proxy backend untuk memotong batas CORS
                let proxyUrl = '{{ route('download.image') }}?url=' + encodeURIComponent(img.src);
                let link = document.createElement('a');
                link.href = proxyUrl;
                link.download = 'QR_Code_Pembayaran.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.dispatchEvent(new CustomEvent('toast', {detail:{message:'Kode QR berhasil diunduh!', type:'success'}}));
            }
        }

        document.addEventListener('DOMContentLoaded', () => { feather.replace(); });
    </script>
    <x-zyngga-toast />
</body>
</html>
