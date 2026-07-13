<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konfirmasi Delivery – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; }

        /* ── map thumbnail ── */
        #map-thumb {
            width: 100%;
            height: 160px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        #map-thumb iframe { width:100%; height:100%; border:0; }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{ isDirty: false }">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Pengajuan Delivery" 
            :backUrl="route('order.detail', ['id' => $order['nota_layanan']])" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[88px]">
                <form method="POST" action="{{ route('order.delivery.store', ['id' => $order['nota_layanan']]) }}" id="page-content" class="flex-1 flex flex-col">
                    @csrf
                    @if ($errors->any())
                        <div x-init="$dispatch('toast', { message: '{{ $errors->first() }}', type: 'error' })"></div>
                    @endif
                    <input type="hidden" name="address" value="{{ $address }}">
                    <input type="hidden" name="lat"     value="{{ $lat }}">
                    <input type="hidden" name="lng"     value="{{ $lng }}">

                    {{-- ── LOKASI DELIVERY ─────────────────────────────────── --}}
                    <x-zyngga-card title="Lokasi Delivery">
                        <x-slot:headerAction>
                            <x-zyngga-button 
                                type="a"
                                href="{{ route('order.request.delivery', ['id' => $order['nota_layanan'], 'change' => 1]) }}"
                                variant="secondary"
                                size="s"
                                label="Ubah"
                            />
                        </x-slot:headerAction>

                        <div id="map-thumb" class="mb-4 relative h-[144px] w-full rounded-xl overflow-hidden border border-zyngga-neutral-100">
                            @if($lat && $lng)
                                <iframe 
                                    loading="lazy"
                                    allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_key', '') }}&q={{ $lat }},{{ $lng }}&zoom=17"
                                    style="pointer-events:none;"
                                    class="w-full h-full border-0 pointer-events-none"
                                ></iframe>
                                <a
                                    href="{{ route('order.request.delivery', ['id' => $order['nota_layanan'], 'change' => 1]) }}"
                                    class="absolute inset-0 z-10 block cursor-pointer"
                                    aria-label="Edit lokasi delivery"
                                    title="Edit lokasi delivery"
                                ></a>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-500">Peta tidak tersedia</div>
                            @endif
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-zyngga-blue-50">
                                <i data-feather="map-pin" class="w-5 h-5 text-zyngga-blue-300"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <x-zyngga-text variant="sm" weight="medium">
                                    {{ explode(',', $address)[0] ?? 'Alamat' }}
                                </x-zyngga-text>
                                <x-zyngga-text variant="xs" color="neutral-500" class="overflow-hidden text-overflow-ellipsis line-clamp-2">
                                    {{ $address ?: 'Alamat tidak ditemukan' }}
                                </x-zyngga-text>
                            </div>
                        </div>

                        {{-- Catatan Patokan --}}
                        <div class="mt-3">
                            <x-zyngga-input 
                                name="detail_address" 
                                id="note" 
                                placeholder="Contoh: Rumah warna biru, pagar putih..."
                                value="{{ $note }}"
                            >
                                <x-slot:iconRight>
                                    <i data-feather="edit-2" class="w-4 h-4 text-zyngga-neutral-900 pointer-events-none"></i>
                                </x-slot:iconRight>
                            </x-zyngga-input>
                        </div>
                    </x-zyngga-card>

                    {{-- ── RINCIAN PEMBAYARAN ─────────────────────────────────── --}}
                    <x-zyngga-card title="Rincian Pembayaran">
                        <div class="space-y-4">
                            <div class="space-y-1">
                                @if(isset($order['items']) && count($order['items']) > 0)
                                    @foreach($order['items'] as $item)
                                    <div class="flex justify-between items-center">
                                        <x-zyngga-text variant="sm" color="neutral-900">{{ $item['name'] }}</x-zyngga-text>
                                        <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ $order['payment_status'] === 'Lunas' ? '0' : number_format($item['subtotal'], 0, ',', '.') }}</x-zyngga-text>
                                    </div>
                                    @endforeach
                                @endif

                                @if(isset($order['upgrade_fee']) && $order['upgrade_fee'] > 0)
                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Biaya Upgrade</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ $order['payment_status'] === 'Lunas' ? '0' : number_format($order['upgrade_fee'], 0, ',', '.') }}</x-zyngga-text>
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
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ isset($order['discount']) ? number_format($order['discount'], 0, ',', '.') : '0' }}</x-zyngga-text>
                                </div>
                                <div class="flex justify-between">
                                    <x-zyngga-text variant="sm" color="neutral-900">Pajak</x-zyngga-text>
                                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ isset($order['tax']) ? number_format($order['tax'], 0, ',', '.') : '0' }}</x-zyngga-text>
                                </div>
                            </div>
                                
                            <x-zyngga-divider />

                            <div class="flex justify-between">
                                <x-zyngga-text variant="sm" color="neutral-900">Total</x-zyngga-text>
                                <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Rp{{ $order['payment_status'] === 'Lunas' ? '0' : (isset($order['total']) ? number_format($order['total'], 0, ',', '.') : '0') }}</x-zyngga-text>
                            </div>
                        </div>
                    </x-zyngga-card>

                    {{-- ── STICKY FOOTER ─────────────────────────────────────── --}}
                    <div id="sticky-footer" class="fixed bottom-0 left-0 right-0 bg-white border-t border-zyngga-neutral-200 p-4 z-40">
                        <div class="w-full max-w-5xl mx-auto">
                            @php
                                $isLunas = $order['payment_status'] === 'Lunas' || (isset($order['total']) && $order['total'] <= 0);
                            @endphp
                            <x-zyngga-button 
                                type="button"
                                variant="primary"
                                size="l"
                                class="w-full"
                                onclick="submitDelivery()"
                                label="{{ $isLunas ? 'Ajukan Pengantaran' : 'Bayar Sekarang' }}"
                            />
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    {{-- ── TOAST CONTAINER ───────────────────────────────────────── --}}
    <div id="toast-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] flex flex-col gap-2 w-[calc(100%-40px)] max-w-[400px]"></div>

    {{-- ── MODAL: PAYMENT SUCCESS ───────────────────────────── --}}
    <x-zyngga-selection-modal 
        id="payment-success-modal" 
        openEvent="open-payment-success-modal"
        closeEvent="close-payment-success-modal"
    >
        <x-zyngga-confirm-view 
            :image="asset('images/illustrations/confirm_order.png')"
            title="Pembayaran Berhasil!"
            description="Terima kasih, pembayaran pengajuan pengantaran Anda telah berhasil."
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            feather.replace();
        });

        // Toast handling
        window.addEventListener('toast', (e) => {
            const { message, type = 'success' } = e.detail;
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const isError = type === 'error';
            const bgColor = isError ? 'bg-[#FEF2F2]' : 'bg-[#F0FDF4]';
            const borderColor = isError ? 'border-[#FEE2E2]' : 'border-[#DCFCE7]';
            const iconColor = isError ? 'text-[#EF4444]' : 'text-[#22C55E]';
            const textColor = isError ? 'text-[#991B1B]' : 'text-[#166534]';
            const iconName = isError ? 'alert-circle' : 'check-circle';

            toast.className = `flex items-start gap-3 p-4 rounded-xl border ${bgColor} ${borderColor} shadow-sm transition-all duration-300 translate-y-[-100%] opacity-0`;
            toast.innerHTML = `
                <i data-feather="${iconName}" class="w-5 h-5 ${iconColor} shrink-0 mt-0.5"></i>
                <p class="text-sm font-medium ${textColor} flex-1 leading-snug">${message}</p>
                <button onclick="this.parentElement.remove()" class="p-1 hover:bg-black/5 rounded-full transition-colors shrink-0">
                    <i data-feather="x" class="w-4 h-4 ${iconColor} opacity-70"></i>
                </button>
            `;

            container.appendChild(toast);
            feather.replace();

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-[-100%]', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        });

        function submitDelivery() {
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
                    @if($order['payment_status'] === 'Lunas' || (isset($order['total']) && $order['total'] <= 0))
                        window.location.href = data.redirect || '{{ route('order.detail', $order['nota_layanan']) }}';
                    @else
                        window.location.href = '{{ route('order.payment-method', $order['nota_layanan']) }}';
                    @endif
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Gagal mengajukan pengantaran.', type: 'error' } }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Terjadi kesalahan sistem.', type: 'error' } }));
            });
        }

        function rollbackDelivery(reload = true) {
            fetch('{{ route('order.delivery.rollback', $order['nota_layanan']) }}', {
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
