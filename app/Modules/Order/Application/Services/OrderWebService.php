<?php

namespace App\Modules\Order\Application\Services;

use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Customer\Domain\Repositories\CustomerRepositoryInterface;
use App\Modules\Order\Application\DTO\CreateOrderData;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class OrderWebService
{
    private const SERVICE_LABELS = [
        'reguler' => 'Reguler',
        'kilat' => 'Kilat',
        'regular' => 'Regular',
        'quick' => 'Quick',
        'express' => 'Express',
        'satuan' => 'Satuan',
    ];

    private const SERVICE_ESTIMATED_TOTALS = [
        'reguler' => 4850,
        'regular' => 4850,
        'quick' => 6000,
        'express' => 6250,
        'kilat' => 7850,
        'satuan' => 10000,
    ];

    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function pickupLocationData(string $service): array
    {
        $user = auth()->user();
        $savedAddresses = $user ? $user->addresses : collect();

        return [
            'service' => $service,
            'serviceLabel' => self::SERVICE_LABELS[$service] ?? ucfirst($service),
            'savedAddresses' => $savedAddresses,
        ];
    }

    public function storePickupLocation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service' => ['required', 'string', Rule::in(array_keys(self::SERVICE_LABELS))],
            'address' => ['required', 'string', 'max:500'],
            'detail_address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);

        session([
            'order.service' => $data['service'],
            'order.address' => $data['address'],
            'order.detail_address' => $data['detail_address'] ?? '',
            'order.lat' => $data['lat'] ?? '',
            'order.lng' => $data['lng'] ?? '',
        ]);

        return redirect()->route('order.booking');
    }

    public function bookingData(): ?array
    {
        if (! session()->has('order.address')) {
            return null;
        }

        $service = session('order.service', 'regular');

        return [
            'service' => $service,
            'serviceLabel' => self::SERVICE_LABELS[$service] ?? ucfirst($service),
            'address' => session('order.address', ''),
            'detailAddress' => session('order.detail_address', ''),
            'lat' => session('order.lat', ''),
            'lng' => session('order.lng', ''),
            'pickupDate' => session('order.pickup_date', ''),
            'pickupTime' => session('order.pickup_time', ''),
            'parfum' => session('order.parfum', ''),
            'note' => session('order.note', ''),
            'customerName' => session('order.customer_name', ''),
            'customerPhone' => session('order.customer_phone', ''),
            'customerEmail' => session('order.customer_email', ''),
            'isRoundtrip' => session('order.is_roundtrip', true),
        ];
    }

    public function dashboardData(?User $user): array
    {
        $pelanggan = $user ? $this->customerRepository->findByUser($user) : null;

        if (! $pelanggan) {
            return [
                'activeOrder' => null,
                'latestOrder' => null,
            ];
        }

        $activeOrder = $this->orderRepository->latestByPelangganId((int) $pelanggan->id, false);
        $latestOrder = $this->orderRepository->latestByPelangganId((int) $pelanggan->id, true);

        return [
            'activeOrder' => $activeOrder ? $this->mapOrderCard($activeOrder) : null,
            'latestOrder' => $latestOrder ? $this->mapOrderCard($latestOrder) : null,
        ];
    }

    public function historyData(User $user, int $perPage = 10): array
    {
        $pelanggan = $this->customerRepository->findByUser($user);

        if (! $pelanggan) {
            return [
                'orders' => collect(),
                'paginator' => null,
            ];
        }

        $paginator = $this->orderRepository->paginateByPelangganId((int) $pelanggan->id, $perPage);

        return [
            'orders' => $paginator->getCollection()->map(fn (Transaksi $order) => $this->mapOrderCard($order)),
            'paginator' => $paginator,
        ];
    }

    public function detailData(?string $id, ?User $user): ?array
    {
        $order = $id ? $this->orderRepository->findById($id) : null;

        if (! $order && $user) {
            $pelanggan = $this->customerRepository->findByUser($user);
            if ($pelanggan) {
                $order = $this->orderRepository->latestByPelangganId((int) $pelanggan->id, false)
                    ?? $this->orderRepository->latestByPelangganId((int) $pelanggan->id);
            }
        }

        return $order ? $this->mapOrderDetail($order) : null;
    }

    public function notificationData(?User $user): array
    {
        $pelanggan = $user ? $this->customerRepository->findByUser($user) : null;

        if (! $pelanggan) {
            return ['notifications' => collect()];
        }

        $orders = $this->orderRepository
            ->paginateByPelangganId((int) $pelanggan->id, 5)
            ->getCollection();

        return [
            'notifications' => $orders->flatMap(fn (Transaksi $order) => $this->mapOrderNotifications($order))->values(),
        ];
    }

    public function confirmOrder(Request $request, OrderService $orderService): RedirectResponse
    {
        $data = $request->validate([
            'service' => ['required', 'string', Rule::in(array_keys(self::SERVICE_LABELS))],
            'address' => ['required', 'string'],
            'detail_address' => ['nullable', 'string'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'selected_service_id' => ['required', 'string', Rule::in(array_keys(self::SERVICE_ESTIMATED_TOTALS))],
            'pickup_date' => ['required', 'string', Rule::in(['today', 'tomorrow'])],
            'pickup_time' => ['required', 'string'],
            'parfum' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'payment' => ['nullable', 'string', Rule::in(['cash', 'qris', 'transfer'])],

            'is_roundtrip' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $pelanggan = $this->customerRepository->upsertProfile($user, [
            'nama' => $user ? $user->name : $request->input('customer_name'),
            'jenis_kelamin' => 'L',
            'telepon' => $user ? ($user->phone ?? '-') : $request->input('customer_phone'),
            'alamat' => $data['address'],
        ]);

        $layananPrioritasId = $this->orderRepository->firstAvailableLayananPrioritasId();
        if (! $layananPrioritasId) {
            return redirect()->back()->withErrors(['layanan' => 'Layanan prioritas belum tersedia.']);
        }

        $cabangId = $this->orderRepository->firstAvailableCabangId();
        if (! $cabangId) {
            return redirect()->back()->withErrors(['cabang' => 'Cabang belum tersedia.']);
        }

        $order = $orderService->createOrder(new CreateOrderData(
            pelangganId: (int) $pelanggan->id,
            cabangId: (int) $cabangId,
            layananPrioritasId: (int) $layananPrioritasId,
            pickupAddress: $data['address'],
            pickupDetailAddress: $data['detail_address'] ?? null,
            pickupDate: $this->resolvePickupDate($data['pickup_date']),
            pickupTime: $data['pickup_time'],
            pickupLat: isset($data['lat']) && $data['lat'] !== '' ? (float) $data['lat'] : null,
            pickupLng: isset($data['lng']) && $data['lng'] !== '' ? (float) $data['lng'] : null,
            parfum: $data['parfum'] ?? null,
            catatan: $data['catatan'] ?? null,
            paymentMethod: $data['payment'] ?? 'qris',
            estimatedTotal: $this->resolveEstimatedTotal($data['selected_service_id']),
            isRoundtrip: $request->boolean('is_roundtrip'),
        ));

        session()->forget('order');

        return redirect()->route('order.detail', ['id' => $order->id])->with('success', 'Pesanan Anda berhasil dibuat!');
    }

    public function pickupDetailsData(Request $request, string $service): array
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $address = $request->query('address');
        $addressId = $request->query('address_id');

        if (!$lat || !$lng || !$address) {
            return [];
        }

        $existingAddress = null;
        $user = auth()->user();
        if ($addressId && $user) {
            $existingAddress = $user->addresses()->find($addressId);
        }

        return compact('service', 'lat', 'lng', 'address', 'existingAddress');
    }

    public function storePickupDetails(Request $request): RedirectResponse
    {
        $request->validate([
            'service'        => 'required|string',
            'label'          => 'required|string|max:255',
            'address_detail' => 'required|string',
            'latitude'       => 'required|numeric',
            'longitude'      => 'required|numeric',
            'note'           => 'nullable|string|max:255',
            'address_id'     => 'nullable|exists:addresses,id',
            'save_address'   => 'nullable|boolean',
        ]);

        $user = auth()->user();

        if ($user && ($request->boolean('save_address') || $request->filled('address_id'))) {
            if ($request->filled('address_id')) {
                $address = $user->addresses()->find($request->address_id);
                if ($address) {
                    $address->update([
                        'label' => $request->label,
                        'address_detail' => $request->address_detail,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'note' => $request->note,
                    ]);
                }
            } else {
                if ($user->addresses()->count() < 5) {
                    $user->addresses()->create([
                        'label' => $request->label,
                        'address_detail' => $request->address_detail,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'note' => $request->note,
                        'is_primary' => $user->addresses()->count() === 0,
                    ]);
                }
            }
        }

        session([
            'order.service'        => $request->service,
            'order.address'        => $request->address_detail,
            'order.detail_address' => $request->note ?? '',
            'order.lat'            => (string) $request->latitude,
            'order.lng'            => (string) $request->longitude,
        ]);

        return redirect()->route('order.booking');
    }

    public function storeRequestDelivery(Request $request, string $id): void
    {
        $request->validate([
            'address'        => 'required|string',
            'detail_address' => 'nullable|string',
        ]);

        $order = $this->orderRepository->findById($id);
        if (!$order) {
            throw new \Exception('Pesanan tidak ditemukan.');
        }

        // Biaya pengantaran ditetapkan server-side — tidak dari input client.
        $deliveryFee = (float) config('laundry.delivery_fee', 0);

        $existingMeta = json_decode($order->payment_metadata, true) ?? [];
        $existingMeta['pending_delivery'] = [
            'address'        => $request->address,
            'detail_address' => $request->detail_address,
            'is_roundtrip'   => true,
            'delivery_fee'   => $deliveryFee,
        ];

        $order->payment_metadata = json_encode($existingMeta);
        $order->save();
    }

    public function checkOrder(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'phone_last_4' => 'required|digits:4',
        ], [
            'query.required' => 'Nama atau ID Delivery tidak boleh kosong.',
            'phone_last_4.required' => '4 digit terakhir nomor WhatsApp tidak boleh kosong.',
            'phone_last_4.digits' => 'Masukkan tepat 4 digit terakhir nomor WhatsApp.',
        ]);

        $orders = $this->orderRepository
            ->searchForPublicCheck($request->input('query'), $request->input('phone_last_4'))
            ->map(fn (Transaksi $order) => $this->mapOrderCard($order));

        if ($orders->isEmpty()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan atau nomor WhatsApp tidak cocok.'
                ], 404);
            }
            return back()->withErrors([
                'query' => 'Pesanan tidak ditemukan atau nomor WhatsApp tidak cocok.'
            ])->withInput();
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $orders->all()
            ], 200);
        }

        return back()->with('orders', $orders->all())->withInput();
    }

    private function mapOrderCard(Transaksi $order): array
    {
        $statusLabel = $this->statusLabel($order);
        $isRoundtrip = (bool) $order->is_roundtrip;

        return [
            'id' => (string) $order->id,
            'nota_layanan' => $order->nota_layanan,
            'customer_name' => $order->pelanggan->nama ?? '-',
            'phone_last_4' => substr((string) ($order->pelanggan->telepon ?? ''), -4),
            'service' => $this->serviceName($order),
            'date' => $this->formatDateTime($order->waktu),
            'status' => $statusLabel,
            'status_icon' => $statusLabel === 'Selesai' ? 'check' : ($statusLabel === 'Belum Bayar' ? 'credit-card' : 'loader'),
            'delivery_status' => $isRoundtrip ? 'Delivery' : 'Ambil di Outlet',
            'delivery_icon' => $isRoundtrip ? 'truck' : 'shopping-bag',
            'is_roundtrip' => $isRoundtrip,
            'progress' => $this->progressForStatus((string) $order->status),
            'total' => (float) $order->total_bayar_akhir,
            'items_count' => (int) $order->detailTransaksi->sum('total_pakaian'),
            'weight' => $this->formatQuantity($order->detailTransaksi->sum('total_pakaian')),
        ];
    }

    private function mapOrderDetail(Transaksi $order): array
    {
        $isFinished = $this->isFinished($order);
        $paymentStatus = $this->isPaid($order) || $isFinished ? 'Lunas' : 'Belum Bayar';
        $total = (float) $order->total_bayar_akhir;
        $upgradeFee = (float) $order->total_biaya_prioritas;

        // Apply pending changes for display
        $meta = json_decode($order->payment_metadata, true) ?? [];
        if (isset($meta['pending_upgrade'])) {
            $total += (float) ($meta['pending_upgrade']['price_diff'] ?? 0);
            $upgradeFee += (float) ($meta['pending_upgrade']['price_diff'] ?? 0);
        }

        return [
            'id' => (string) $order->id,
            'nota_layanan' => $order->nota_layanan,
            'service_type' => $this->serviceName($order),
            'status' => $isFinished ? 'finished' : 'ongoing',
            'status_label' => $isFinished ? 'Ambil di Outlet' : $this->statusLabel($order),
            'is_roundtrip' => (bool) $order->is_roundtrip || isset($meta['pending_delivery']),
            'customer_name' => $order->pelanggan->nama ?? '-',
            'customer_phone' => $order->pelanggan->telepon ?? '-',
            'address' => $order->pickup_address ?: ($order->pelanggan->alamat ?? '-'),
            'address_detail' => $order->pickup_detail_address ?: '-',
            'lat' => $order->pickup_lat,
            'lng' => $order->pickup_lng,
            'order_date' => $this->formatDateTime($order->waktu),
            'estimated_finished' => $this->formatEstimatedFinished($order),
            'progress' => $this->progressForStatus((string) $order->status),
            'current_step' => $this->currentStep($order),
            'payment_status' => $paymentStatus,
            'payment_method' => strtoupper((string) ($this->latestPayment($order)?->method ?? $order->jenis_pembayaran ?? 'cash')),
            'subtotal' => (float) ($order->total_biaya_layanan ?: $total),
            'discount' => 0,
            'tax' => 0,
            'total' => $total,
            'cash' => (float) $order->bayar,
            'change' => (float) ($order->kembalian ?: 0),
            'items' => $this->mapOrderItems($order),
            'logs' => $this->mapOrderLogs($order),
            'raw_status' => (string) $order->status,
            'can_upgrade' => $this->canBeUpgraded($order),
            'upgrade_fee' => $upgradeFee,
            'snap_token' => $this->getSnapToken($order),
            'has_complaint' => \App\Models\Complaint::where('transaksi_id', $order->id)->exists(),
            'clothing_details' => $order->timbangan && $order->timbangan->items ? $order->timbangan->items->map(function ($item) {
                return [
                    'nama' => $item->jenisPakaian->nama ?? '-',
                    'qty' => $item->qty,
                ];
            })->all() : [],
        ];
    }

    private function getSnapToken(Transaksi $order): ?string
    {
        if ($this->isUnweighed($order)) {
            return null;
        }

        $unpaidAmount = max(0, (float) $order->total_bayar_akhir - (float) $order->bayar);
        
        if ($unpaidAmount <= 0) {
            return null;
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
        \Midtrans\Config::$overrideNotifUrl = url('api/v1/payment/notification');

        $params = [
            'transaction_details' => [
                'order_id' => $order->id . '-' . time(),
                'gross_amount' => (int) round($unpaidAmount),
            ],
            'customer_details' => [
                'first_name' => $order->pelanggan->nama ?? 'Pelanggan Zyngga',
                'phone' => $order->pelanggan->telepon ?? '081234567890',
            ],
        ];

        try {
            return \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return null;
        }
    }
    public function processCoreApiPayment(string $id, string $method)
    {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            throw new \Exception('Order tidak ditemukan.');
        }

        if ($this->isUnweighed($order)) {
            throw new \Exception('Pesanan Anda belum ditimbang oleh operator.');
        }

        $existingMeta = json_decode($order->payment_metadata, true) ?? [];
        $pendingUpgrade = $existingMeta['pending_upgrade'] ?? null;
        $pendingDelivery = $existingMeta['pending_delivery'] ?? null;

        $unpaidAmount = max(0, (float) $order->total_bayar_akhir - (float) $order->bayar);
        
        if ($pendingUpgrade) {
            $unpaidAmount += (float) ($pendingUpgrade['price_diff'] ?? 0);
        }

        if ($unpaidAmount <= 0) {
            throw new \Exception('Pesanan sudah lunas.');
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $midtransOrderId = $order->id . '-' . time();

        $params = [
            'payment_type' => $this->mapPaymentMethod($method),
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) round($unpaidAmount),
            ],
            'customer_details' => [
                'first_name' => $order->pelanggan->nama ?? 'Pelanggan Zyngga',
                'phone' => $order->pelanggan->telepon ?? '081234567890',
            ],
        ];

        $bank = explode('_', $method)[0];
        $supportedNativeBanks = ['bca', 'bni', 'bri', 'permata', 'cimb'];

        if (in_array($method, ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'cimb_va', 'danamon_va', 'bsi_va', 'seabank_va', 'saqu_va', 'other_va'])) {
            $midtransBank = in_array($bank, $supportedNativeBanks) ? $bank : ($method === 'other_va' ? 'cimb' : 'bni');
            $params['bank_transfer'] = [
                'bank' => $midtransBank
            ];
        } elseif ($method === 'mandiri_va') {
            $params['echannel'] = [
                'bill_info1' => 'Payment for Zyngga',
                'bill_info2' => 'Order ID',
            ];
        }

        try {
            $response = \Midtrans\CoreApi::charge($params);

            if ($pendingUpgrade) $response->pending_upgrade = $pendingUpgrade;
            if ($pendingDelivery) $response->pending_delivery = $pendingDelivery;
            $response->original_bank = $bank;

            // Save the exact transaction id to retrieve status later
            $order->midtrans_order_id = $midtransOrderId;
            $order->payment_metadata = json_encode($response);
            $order->save();

            return $response;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans CoreApi Error: ' . $e->getMessage());
            throw new \Exception('Gagal memproses pembayaran dengan metode yang dipilih.');
        }
    }

    private function mapPaymentMethod(string $method): string
    {
        if (in_array($method, ['bca_va', 'bni_va', 'bri_va'])) {
            return 'bank_transfer';
        }
        if ($method === 'mandiri_va') {
            return 'echannel';
        }
        if (in_array($method, ['qris', 'dana'])) {
            // For Sandbox purposes, map DANA to qris if direct dana is not available
            return 'qris';
        }
        if (in_array($method, ['gopay', 'shopeepay'])) {
            return $method;
        }
        return 'bank_transfer';
    }

    public function getPaymentInstruction(string $id)
    {
        $order = $this->orderRepository->findById($id);
        if (!$order || !$order->midtrans_order_id || !$order->payment_metadata) {
            return null;
        }

        $meta = json_decode($order->payment_metadata, true);
        
        $instruction = [
            'type' => $meta['payment_type'] ?? null,
            'status' => $meta['transaction_status'] ?? null,
            'gross_amount' => $meta['gross_amount'] ?? null,
            'expiry_time' => $meta['expiry_time'] ?? null,
        ];

        if (isset($meta['va_numbers']) && count($meta['va_numbers']) > 0) {
            $instruction['bank'] = $meta['original_bank'] === 'other' ? ($meta['va_numbers'][0]['bank'] ?? 'cimb') : ($meta['original_bank'] ?? $meta['va_numbers'][0]['bank'] ?? null);
            $instruction['va_number'] = $meta['va_numbers'][0]['va_number'] ?? null;
        } elseif (isset($meta['permata_va_number'])) {
            $instruction['bank'] = $meta['original_bank'] === 'other' ? 'permata' : ($meta['original_bank'] ?? 'permata');
            $instruction['va_number'] = $meta['permata_va_number'];
        }

        if (isset($meta['qr_string'])) {
            $instruction['qr_string'] = $meta['qr_string'];
        }

        if (isset($meta['actions'])) {
            $instruction['actions'] = $meta['actions'];
            foreach ($meta['actions'] as $action) {
                if ($action['name'] === 'generate-qr-code') {
                    $instruction['qr_image_url'] = $action['url'];
                }
                if ($action['name'] === 'deeplink-redirect') {
                    $instruction['deeplink_url'] = $action['url'];
                }
            }
        }

        // Handle Mandiri echannel
        if (isset($meta['bill_key']) && isset($meta['biller_code'])) {
            $instruction['bank'] = 'mandiri';
            $instruction['biller_code'] = $meta['biller_code'];
            $instruction['va_number'] = $meta['bill_key'];
        }

        // Generate dynamic payment steps
        $bank = strtolower($instruction['bank'] ?? '');
        $type = $instruction['type'] ?? '';

        $va_number = $instruction['va_number'] ?? '';
        $steps = [];
        if ($type === 'qris' || $type === 'gopay') {
            $steps = [
                'Buka aplikasi e-wallet atau m-banking (GoPay, DANA, OVO, dll).',
                'Pilih menu Scan QR.',
                'Scan kode QR yang tertera pada halaman ini.',
                'Periksa detail tagihan dan selesaikan pembayaran.'
            ];
        } elseif ($bank === 'bca') {
            $steps = [
                'm-BCA' => [
                    'Login ke aplikasi m-BCA.',
                    'Pilih menu m-Transfer.',
                    'Pilih BCA Virtual Account.',
                    'Masukkan nomor Virtual Account ' . $va_number . '.',
                    'Periksa detail transaksi dan masukkan PIN m-BCA.',
                    'Pembayaran selesai.'
                ],
                'KlikBCA' => [
                    'Login ke KlikBCA Individual.',
                    'Pilih menu Transfer Dana > Transfer ke BCA Virtual Account.',
                    'Masukkan nomor Virtual Account ' . $va_number . '.',
                    'Masukkan respon KeyBCA Appli 1.',
                    'Pembayaran selesai.'
                ],
                'ATM BCA' => [
                    'Masukkan Kartu ATM BCA & PIN.',
                    'Pilih menu Transaksi Lainnya > Transfer > ke Rekening BCA Virtual Account.',
                    'Masukkan nomor Virtual Account ' . $va_number . '.',
                    'Pastikan detail pembayaran sudah sesuai dan pilih Benar.',
                    'Pembayaran selesai.'
                ]
            ];
        } elseif ($bank === 'bni') {
            $steps = [
                'BNI Mobile Banking' => [
                    'Buka aplikasi BNI Mobile Banking dan login.',
                    'Pilih menu Transfer > Virtual Account Billing.',
                    'Masukkan nomor Virtual Account ' . $va_number . '.',
                    'Konfirmasi pembayaran dan masukkan Password Transaksi.',
                    'Pembayaran selesai.'
                ],
                'ATM BNI' => [
                    'Masukkan Kartu ATM BNI & PIN.',
                    'Pilih Menu Lainnya > Transfer > Virtual Account Billing.',
                    'Masukkan nomor Virtual Account ' . $va_number . '.',
                    'Periksa detail pembayaran dan konfirmasi.',
                    'Pembayaran selesai.'
                ],
                'Internet Banking BNI' => [
                    'Login ke BNI Internet Banking.',
                    'Pilih menu Transfer > Virtual Account Billing.',
                    'Masukkan nomor Virtual Account ' . $va_number . '.',
                    'Masukkan token otentikasi.',
                    'Pembayaran selesai.'
                ]
            ];
        } elseif ($bank === 'mandiri' || $type === 'echannel') {
            $steps = [
                'Livin\' by Mandiri' => [
                    'Buka aplikasi Livin\' by Mandiri dan login.',
                    'Pilih menu Bayar > E-Commerce.',
                    'Pilih penyedia jasa Midtrans (70012).',
                    'Masukkan nomor Virtual Account ' . $va_number . '.',
                    'Konfirmasi tagihan dan masukkan PIN.',
                    'Pembayaran selesai.'
                ],
                'ATM Mandiri' => [
                    'Masukkan Kartu ATM Mandiri & PIN.',
                    'Pilih menu Bayar/Beli > Lainnya > Lainnya > E-Commerce.',
                    'Masukkan kode perusahaan Midtrans (70012).',
                    'Masukkan nomor Virtual Account ' . $va_number . '.',
                    'Konfirmasi pembayaran dengan menekan angka 1 dan Ya.',
                    'Pembayaran selesai.'
                ]
            ];
        } elseif ($bank === 'bri') {
            $steps = [
                'BRImo' => [
                    'Buka aplikasi BRImo dan login.',
                    'Pilih menu BRIVA.',
                    'Masukkan nomor BRIVA ' . $va_number . '.',
                    'Konfirmasi pembayaran dan masukkan PIN.',
                    'Pembayaran selesai.'
                ],
                'ATM BRI' => [
                    'Masukkan Kartu ATM BRI & PIN.',
                    'Pilih menu Transaksi Lain > Pembayaran > Lainnya > BRIVA.',
                    'Masukkan nomor BRIVA ' . $va_number . '.',
                    'Konfirmasi pembayaran dengan menekan Ya.',
                    'Pembayaran selesai.'
                ]
            ];
        } elseif ($bank === 'cimb') {
            $steps = [
                'ATM Prima' => [
                    'Pilih menu Transaksi Lainnya pada menu utama.',
                    'Pilih menu Transfer.',
                    'Pilih menu Ke Rek Bank Lain.',
                    'Masukkan 022 sebagai kode bank CIMB Niaga.',
                    'Masukkan nominal pembayaran sesuai tagihan.',
                    'Masukkan nomor Virtual Account ' . $va_number . ' lalu konfirmasi.',
                    'Pembayaran selesai.'
                ],
                'ATM Bersama' => [
                    'Pilih menu Transaksi Lainnya pada menu utama.',
                    'Pilih menu Transfer.',
                    'Pilih menu Transfer ke Bank Lain.',
                    'Masukkan 022 sebagai kode bank CIMB Niaga.',
                    'Masukkan nominal pembayaran sesuai tagihan.',
                    'Masukkan nomor Virtual Account ' . $va_number . ' lalu konfirmasi.',
                    'Pembayaran selesai.'
                ],
                'Jaringan ALTO' => [
                    'Pilih menu Transaksi Lainnya pada menu utama.',
                    'Pilih menu Transfer.',
                    'Pilih menu Transfer ke Bank Lain.',
                    'Masukkan 022 sebagai kode bank CIMB Niaga.',
                    'Masukkan nominal pembayaran sesuai tagihan.',
                    'Masukkan nomor Virtual Account ' . $va_number . ' lalu konfirmasi.',
                    'Pembayaran selesai.'
                ]
            ];
        } else {
            $steps = [
                'Salin nomor pembayaran / Virtual Account di atas.',
                'Buka aplikasi m-banking atau e-wallet pilihan Anda.',
                'Pilih menu transfer ke Virtual Account.',
                'Selesaikan pembayaran sesuai nominal tagihan.'
            ];
        }
        $instruction['steps'] = $steps;

        return $instruction;
    }

    public function checkPaymentStatus(string $id): string
    {
        $order = $this->orderRepository->findById($id);
        if (!$order || !$order->midtrans_order_id) {
            return 'unknown';
        }

        $meta = json_decode($order->payment_metadata, true) ?? [];
        $targetAmount = (float) $order->total_bayar_akhir;
        if (isset($meta['pending_upgrade'])) {
            $targetAmount += (float) ($meta['pending_upgrade']['price_diff'] ?? 0);
        }
        if (isset($meta['pending_delivery'])) {
            $targetAmount += (float) ($meta['pending_delivery']['delivery_fee'] ?? 0);
        }

        if ((float) $order->bayar >= $targetAmount && $targetAmount > 0) {
            return 'paid'; // The webhook probably already caught it
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $status = \Midtrans\Transaction::status($order->midtrans_order_id);
            if ($status && isset($status->transaction_status)) {
                return $status->transaction_status;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Check Status Error: ' . $e->getMessage());
        }

        return 'pending';
    }

    public function cancelCoreApiPayment(string $id): void
    {
        $order = $this->orderRepository->findById($id);
        if (!$order || !$order->midtrans_order_id) {
            throw new \Exception('Transaksi pembayaran tidak ditemukan.');
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            \Midtrans\Transaction::cancel($order->midtrans_order_id);
            $order->midtrans_order_id = null;
            $order->payment_metadata = null;
            $order->save();
        } catch (\Exception $e) {
            // It might already be expired or canceled.
            $order->midtrans_order_id = null;
            $order->payment_metadata = null;
            $order->save();
        }
    }
    private function mapOrderItems(Transaksi $order): array
    {
        $items = $order->detailTransaksi->map(function ($detail) use ($order) {
            $serviceNames = $detail->detailLayananTransaksi
                ->map(fn ($serviceDetail) => $serviceDetail->hargaJenisLayanan?->jenisLayanan?->nama)
                ->filter()
                ->unique()
                ->implode(' + ');

            $firstServiceDetail = $detail->detailLayananTransaksi->first();
            $clothingName = $firstServiceDetail?->hargaJenisLayanan?->jenisPakaian?->nama;
            
            $qty = (float) $detail->total_pakaian;
            $subtotal = (float) ($detail->total_biaya_layanan ?: 0);
            $price = $qty > 0 ? $subtotal / $qty : (float) $detail->harga_layanan_akhir;

            $priority = (int) ($order->layananPrioritas->prioritas ?? 1);
            $daysStr = match (true) {
                $priority >= 99 => 'Hari ini',
                $priority >= 3 => '1 hari',
                $priority >= 2 => '2 hari',
                default => '3 hari',
            };

            $isSatuan = $firstServiceDetail?->hargaJenisLayanan && strtolower($firstServiceDetail->hargaJenisLayanan->jenis_satuan) !== 'kg';
            
            if ($isSatuan && $clothingName) {
                $name = "Satuan - " . $clothingName;
            } else {
                $originalService = $order->upgradeLayanans->first()?->layananAsal ?? $order->layananPrioritas;
                $serviceBase = $originalService->nama ?? 'Reguler';
                
                // Keep the days string based on original priority
                $origPriority = (int) ($originalService->prioritas ?? 1);
                $origDaysStr = match (true) {
                    $origPriority >= 99 => 'Hari ini',
                    $origPriority >= 3 => '1 hari',
                    $origPriority >= 2 => '2 hari',
                    default => '3 hari',
                };
                
                $name = "{$serviceBase} ({$origDaysStr}) - " . $this->formatQuantity($qty) . "Kg";
            }

            return [
                'name' => $name,
                'qty' => $this->formatQuantity($qty),
                'price' => $price,
                'subtotal' => $subtotal,
            ];
        })->filter(fn (array $item) => $item['subtotal'] > 0 || $item['qty'] !== '0');

        if ($items->isNotEmpty()) {
            return $items->values()->all();
        }

        $total = (float) $order->total_bayar_akhir;
        $originalService = $order->upgradeLayanans->first()?->layananAsal ?? $order->layananPrioritas;

        return [[
            'name' => $originalService->nama ?? 'Layanan Laundry',
            'qty' => '1',
            'price' => $total,
            'subtotal' => $total,
        ]];
    }

    private function mapOrderLogs(Transaksi $order): array
    {
        $createdAt = $order->waktu ?? $order->created_at ?? now();
        $logs = [[
            'time' => $createdAt->format('H:i'),
            'date' => $createdAt->locale('id')->isoFormat('dddd, D MMM'),
            'note' => 'Pesanan diterima',
        ]];

        if (in_array($order->status, ['Proses', 'Selesai'], true)) {
            $logs[] = [
                'time' => optional($order->updated_at)->format('H:i') ?: $createdAt->copy()->addHour()->format('H:i'),
                'date' => optional($order->updated_at)->locale('id')->isoFormat('dddd, D MMM') ?: $createdAt->locale('id')->isoFormat('dddd, D MMM'),
                'note' => 'Pesanan sedang diproses',
            ];
        }

        if ($this->isFinished($order)) {
            $logs[] = [
                'time' => optional($order->updated_at)->format('H:i') ?: now()->format('H:i'),
                'date' => optional($order->updated_at)->locale('id')->isoFormat('dddd, D MMM') ?: now()->locale('id')->isoFormat('dddd, D MMM'),
                'note' => 'Pesanan selesai dan siap diambil',
            ];
        }

        return array_reverse($logs);
    }

    private function mapOrderNotifications(Transaksi $order): Collection
    {
        $notifications = collect();
        $dateLabel = $this->relativeDate($order->updated_at ?? $order->waktu);
        $service = $this->serviceName($order);

        if ($this->isFinished($order)) {
            $notifications->push([
                'category' => 'Status',
                'title' => 'Pesanan Selesai',
                'message' => "Pesanan {$service} Anda ({$order->id}) telah selesai dan siap diambil.",
                'time' => $dateLabel,
                'timestamp' => $order->updated_at ?? $order->waktu,
                'icon' => 'check-circle',
                'box_class' => 'bg-[#E8EFF9]',
                'icon_class' => 'text-zyngga-blue-300',
            ]);
        } else {
            $notifications->push([
                'category' => 'Status',
                'title' => $this->statusLabel($order),
                'message' => "Pesanan {$service} Anda ({$order->id}) sedang dalam status {$order->status}.",
                'time' => $dateLabel,
                'timestamp' => $order->updated_at ?? $order->waktu,
                'icon' => 'truck',
                'box_class' => 'bg-[#E8EFF9]',
                'icon_class' => 'text-zyngga-blue-300',
            ]);
        }

        if (! $this->isPaid($order) && ! $this->isFinished($order)) {
            $notifications->push([
                'category' => 'Transaksi',
                'title' => 'Tagihan Tersedia',
                'message' => 'Tagihan pesanan sebesar Rp'.number_format((float) $order->total_bayar_akhir, 0, ',', '.').' menunggu pembayaran.',
                'time' => $dateLabel,
                'timestamp' => $order->updated_at ?? $order->waktu,
                'icon' => 'file-text',
                'box_class' => 'bg-[#E9F7EE]',
                'icon_class' => 'text-zyngga-status-success',
            ]);
        }

        return $notifications;
    }

    private function serviceName(Transaksi $order): string
    {
        $hasSatuan = $order->detailTransaksi->contains(function ($detail) {
            $firstServiceDetail = $detail->detailLayananTransaksi->first();
            $satuan = $firstServiceDetail?->hargaJenisLayanan?->jenis_satuan;
            return $satuan && strtolower($satuan) !== 'kg';
        });
        
        $hasKiloan = $order->detailTransaksi->contains(function ($detail) {
            $firstServiceDetail = $detail->detailLayananTransaksi->first();
            $satuan = $firstServiceDetail?->hargaJenisLayanan?->jenis_satuan;
            return !$satuan || strtolower($satuan) === 'kg';
        });

        if ($hasSatuan && !$hasKiloan) {
            return 'Satuan';
        }
        
        if ($hasSatuan && $hasKiloan) {
             return ($order->layananPrioritas->nama ?? 'Reguler') . ' & Satuan';
        }

        return $order->layananPrioritas->nama ?? 'Reguler';
    }

    private function statusLabel(Transaksi $order): string
    {
        if ($this->isFinished($order)) {
            return 'Selesai';
        }

        if (! $this->isPaid($order)) {
            return 'Belum Bayar';
        }

        return match ((string) $order->status) {
            'Baru', 'Perlu Diproses' => 'Baru',
            'Proses', 'Menunggu Pembayaran', 'Perlu Dikerjakan', 'Proses Pengerjaan' => 'Diproses',
            default => (string) $order->status,
        };
    }

    private function currentStep(Transaksi $order): string
    {
        return match ((string) $order->status) {
            'Selesai', 'Pesanan Selesai' => 'Selesai',
            'Proses', 'Menunggu Pembayaran', 'Perlu Dikerjakan', 'Proses Pengerjaan' => 'Pesanan sedang diproses',
            'Baru', 'Perlu Diproses' => 'Pesanan diterima',
            default => (string) $order->status,
        };
    }

    private function progressForStatus(string $status): int
    {
        return match ($status) {
            'Selesai', 'Pesanan Selesai' => 100,
            'Proses', 'Menunggu Pembayaran', 'Perlu Dikerjakan', 'Proses Pengerjaan' => 56,
            'Baru', 'Perlu Diproses' => 20,
            default => 10,
        };
    }

    private function isFinished(Transaksi $order): bool
    {
        return $order->list_status_pengerjaan_id == 5 || $order->status === 'Selesai' || $order->status === 'Pesanan Selesai';
    }

    private function isUnweighed(Transaksi $order): bool
    {
        return in_array($order->status, ['Baru', 'created', 'Perlu Diproses']);
    }

    private function canBeUpgraded(Transaksi $order): bool
    {
        if ($this->isFinished($order)) {
            return false;
        }

        $currentPriority = $order->layananPrioritas;
        if (!$currentPriority) {
            return false;
        }

        $availableUpgrades = \App\Models\LayananPrioritas::where('cabang_id', $currentPriority->cabang_id)
            ->where('prioritas', '>', $currentPriority->prioritas)
            ->get();

        if ($availableUpgrades->isEmpty()) {
            return false;
        }

        $baseDate = \Carbon\Carbon::parse($order->waktu ?? now());
        foreach ($availableUpgrades as $upgrade) {
            $maxElapsedHours = match(strtolower($upgrade->nama)) {
                'kilat' => 3,
                'express' => 12,
                'quick' => 24,
                default => 24,
            };
            
            if (now()->lte($baseDate->copy()->addHours($maxElapsedHours))) {
                return true;
            }
        }

        return false;
    }

    private function isPaid(Transaksi $order): bool
    {
        return $order->payment_status === 'paid'
            || $this->latestPayment($order)?->status === 'paid';
    }

    private function latestPayment(Transaksi $order): ?object
    {
        return $order->payments->sortByDesc('created_at')->first();
    }

    private function formatDateTime($date): string
    {
        if (! $date) {
            return '-';
        }

        return $date->locale('id')->isoFormat('dddd, D MMM | HH.mm');
    }

    private function calculateWorkingHoursETA(\Carbon\Carbon $startDate, int $hoursToAdd): \Carbon\Carbon
    {
        $date = $startDate->copy();

        if ($date->hour < 8) {
            $date->setTime(8, 0, 0);
        } elseif ($date->hour >= 18) {
            $date->addDay()->setTime(8, 0, 0);
        }

        while ($hoursToAdd > 0) {
            $endOfDay = $date->copy()->setTime(18, 0, 0);
            $minutesLeftToday = $date->diffInMinutes($endOfDay, false);
            
            // If it's somehow past 18:00 (e.g. edge cases), move to next day
            if ($minutesLeftToday <= 0) {
                $date->addDay()->setTime(8, 0, 0);
                continue;
            }

            $minutesToAdd = $hoursToAdd * 60;

            if ($minutesToAdd <= $minutesLeftToday) {
                $date->addMinutes($minutesToAdd);
                $hoursToAdd = 0;
            } else {
                $date->addDay()->setTime(8, 0, 0);
                $hoursToAdd -= ($minutesLeftToday / 60);
            }
        }

        return $date;
    }

    private function formatEstimatedFinished(Transaksi $order): string
    {
        $baseDate = $order->pickup_date ?? $order->waktu;

        if (! $baseDate) {
            return '-';
        }

        $priority = (int) ($order->layananPrioritas->prioritas ?? 1);
        $workingHours = match (true) {
            $priority >= 99 => 5, // Kilat
            $priority >= 3 => 10, // Express
            $priority >= 2 => 20, // Quick
            default => 30, // Reguler
        };

        $etaDate = $this->calculateWorkingHoursETA($baseDate, $workingHours);
        return $etaDate->locale('id')->isoFormat('dddd, D MMM | HH.mm');
    }

    private function formatQuantity(float|int $quantity): string
    {
        $formatted = rtrim(rtrim(number_format((float) $quantity, 1, '.', ''), '0'), '.');

        return $formatted === '' ? '0' : $formatted;
    }

    private function relativeDate($date): string
    {
        return $date ? $date->locale('id')->diffForHumans() : '-';
    }

    private function resolvePickupDate(string $pickupDate): string
    {
        return match ($pickupDate) {
            'today' => now()->toDateString(),
            'tomorrow' => now()->addDay()->toDateString(),
            default => $pickupDate,
        };
    }

    private function resolveEstimatedTotal(string $serviceId): float
    {
        return (float) (self::SERVICE_ESTIMATED_TOTALS[$serviceId] ?? self::SERVICE_ESTIMATED_TOTALS['regular']);
    }

    public function upgradeData(string $id, ?User $user): array
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            throw new \Exception('Pesanan tidak ditemukan.');
        }

        if ($this->isFinished($order)) {
            throw new \Exception('Pesanan yang sudah selesai tidak dapat di-upgrade.');
        }

        $currentPriority = $order->layananPrioritas;
        if (!$currentPriority) {
            throw new \Exception('Layanan pesanan tidak valid.');
        }

        $availableUpgrades = \App\Models\LayananPrioritas::where('cabang_id', $currentPriority->cabang_id)
            ->where('prioritas', '>', $currentPriority->prioritas)
            ->get();

        $upgrades = collect();
        $baseDate = \Carbon\Carbon::parse($order->waktu ?? now());
        
        foreach ($availableUpgrades as $upgrade) {
            $durationHours = $this->getLayananDurationHours((int) $upgrade->prioritas);
            $newFinishTime = $baseDate->copy()->addHours($durationHours);

            $desc = match ($upgrade->nama) {
                'Reguler', 'Regular' => 'Layanan 3 hari (72 jam)',
                'Quick' => 'Layanan 2 hari (48 jam)',
                'Express' => 'Layanan 1 hari (24 jam)',
                'Kilat' => 'Layanan 5 jam',
                default => 'Layanan ' . $upgrade->nama,
            };

            $maxElapsedHours = match(strtolower($upgrade->nama)) {
                'kilat' => 3,
                'express' => 12,
                'quick' => 24,
                default => 24,
            };
            
            $isAvailable = now()->lte($baseDate->copy()->addHours($maxElapsedHours));

            $upgrades->push([
                'id' => $upgrade->id,
                'name' => $upgrade->nama,
                'desc' => $desc,
                'price' => (float) $upgrade->harga,
                'is_available' => $isAvailable,
            ]);
        }

        if ($upgrades->where('is_available', true)->isEmpty()) {
            throw new \Exception('Tidak ada layanan upgrade yang memenuhi syarat waktu untuk pesanan ini.');
        }

        $upgrades = $upgrades->map(function($item) use ($currentPriority) {
            $diff = $item['price'] - (float) $currentPriority->harga;
            $item['price_diff'] = $diff > 0 ? $diff : 0;
            return $item;
        })->sortBy([
            ['is_available', 'desc'],
            ['price', 'asc'],
        ]);

        return [
            'order' => $this->mapOrderDetail($order),
            'currentService' => $currentPriority->nama,
            'upgrades' => $upgrades->values()->all(),
            'baseDate' => $baseDate->toIso8601String(),
        ];
    }

    public function paymentData(string $id, ?User $user): array
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            throw new \Exception('Pesanan tidak ditemukan.');
        }

        if ($this->isFinished($order) || $this->isPaid($order)) {
            throw new \Exception('Metode pembayaran tidak dapat diubah karena pesanan sudah lunas atau selesai.');
        }

        if ($this->isUnweighed($order)) {
            throw new \Exception('Pesanan Anda belum ditimbang oleh operator.');
        }

        return [
            'order' => $this->mapOrderDetail($order),
        ];
    }

    public function updatePayment(string $id, string $method, ?User $user): void
    {
        $order = $this->orderRepository->findById($id);

        if (!$order || $this->isFinished($order) || $this->isPaid($order)) {
            throw new \Exception('Metode pembayaran tidak dapat diubah.');
        }

        $order->jenis_pembayaran = match(strtolower($method)) {
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
            default => 'Tunai',
        };
        $order->save();
    }

    public function processUpgrade(string $id, int $newServiceId, ?User $user, ?string $paymentMethod = null): void
    {
        $order = $this->orderRepository->findById($id);
        if (!$order || $this->isFinished($order)) {
            throw new \Exception('Pesanan yang sudah selesai tidak dapat di-upgrade.');
        }

        $currentPriority = $order->layananPrioritas;
        $newPriority = \App\Models\LayananPrioritas::find($newServiceId);

        if (!$newPriority || $newPriority->cabang_id !== $currentPriority->cabang_id || $newPriority->prioritas <= $currentPriority->prioritas) {
            throw new \Exception('Layanan upgrade tidak valid.');
        }

        $baseDate = \Carbon\Carbon::parse($order->waktu ?? now());
        $durationHours = $this->getLayananDurationHours((int) $newPriority->prioritas);
        $newFinishTime = $baseDate->copy()->addHours($durationHours);

        $bufferHours = strtolower($newPriority->nama) === 'kilat' ? 1 : 5;
        if (now()->gt($newFinishTime->copy()->subHours($bufferHours))) {
            throw new \Exception('Waktu pesanan sudah melebihi batas untuk upgrade ke layanan ini.');
        }

        $priceDiff = max(0, (float) $newPriority->harga - (float) $currentPriority->harga);
        $totalWeight = $order->detailTransaksi->sum('total_pakaian') ?: 1; // Fallback to 1 if empty or 0
        $totalPriceDiff = $priceDiff * $totalWeight;

        $existingMeta = json_decode($order->payment_metadata, true) ?? [];
        $existingMeta['pending_upgrade'] = [
            'new_service_id' => $newPriority->id,
            'price_diff' => $totalPriceDiff
        ];

        $order->payment_metadata = json_encode($existingMeta);
        $order->save();
    }

    private function getLayananDurationHours(int $prioritas): int
    {
        return match (true) {
            $prioritas >= 99 => 5,
            $prioritas >= 3 => 24, 
            $prioritas >= 2 => 48, 
            default => 72, 
        };
    }

    public function storeComplaint(string $id, Request $request, ?User $user): void
    {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            throw new \Exception('Pesanan tidak ditemukan.');
        }

        $pelanggan = $user ? $this->customerRepository->findByUser($user) : null;
        if (!$pelanggan) {
            $pelanggan = $order->pelanggan;
        }

        if (!$pelanggan) {
            throw new \Exception('Pelanggan tidak valid.');
        }

        $imagePath = null;
        if ($request->hasFile('issue_image')) {
            $imagePath = $request->file('issue_image')->storeOnCloudinary('complaints')->getSecurePath();
        }

        $content = $request->input('issue_description') ?? $request->input('content');
        if (empty($content)) {
            throw new \Exception('Deskripsi masalah harus diisi.');
        }

        \App\Models\Complaint::create([
            'transaksi_id' => $order->id,
            'pelanggan_id' => $pelanggan->id,
            'content' => $content,
            'issue_types' => $request->input('issue_types') ?? ['lainnya'],
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);
    }

    public function requestDeliveryConfirmData(string $id, Request $request): array
    {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            throw new \Exception('Pesanan tidak ditemukan.');
        }

        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $address = $request->query('address');
        $detailAddress = $request->query('detail_address');

        if (!$lat || !$lng || !$address) {
            throw new \Exception('Lokasi pengantaran tidak lengkap.');
        }

        return [
            'order' => $this->mapOrderDetail($order),
            'service' => $this->serviceName($order),
            'serviceLabel' => $this->serviceName($order),
            'address' => $address,
            'detailAddress' => $detailAddress ?? '',
            'lat' => $lat,
            'lng' => $lng,
            'pickupDate' => session('order.pickup_date', ''),
            'pickupTime' => session('order.pickup_time', ''),
            'parfum' => session('order.parfum', 'Lavender'),
            'note' => session('order.note', ''),
        ];
    }

    public function storeDeliveryDetails(string $id, Request $request, ?User $user): void
    {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            throw new \Exception('Pesanan tidak ditemukan.');
        }

        $request->validate([
            'address' => 'required|string',
            'detail_address' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'pickup_date' => 'nullable|string',
            'pickup_time' => 'nullable|string',
            'parfum' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $order->pickup_address = $request->input('address');
        $order->pickup_detail_address = $request->input('detail_address');
        $order->is_roundtrip = true;
        
        if ($request->filled('pickup_date')) {
            $order->pickup_date = $this->resolvePickupDate($request->input('pickup_date'));
        }
        if ($request->filled('pickup_time')) {
            $order->pickup_time = $request->input('pickup_time');
        }
        if ($request->filled('parfum')) {
            $order->parfum = $request->input('parfum');
        }
        if ($request->filled('catatan')) {
            $order->catatan = $request->input('catatan');
        }

        $order->save();
    }

    public function complaintsHistoryData(User $user): Collection
    {
        $pelanggan = $this->customerRepository->findByUser($user);
        if (!$pelanggan) {
            return collect();
        }

        return \App\Models\Complaint::with('transaksi')
            ->where('pelanggan_id', $pelanggan->id)
            ->latest()
            ->get();
    }

    public function complaintDetailData(string $id, User $user): \App\Models\Complaint
    {
        $pelanggan = $this->customerRepository->findByUser($user);
        if (!$pelanggan) {
            throw new \Exception('Pelanggan tidak valid.');
        }

        return \App\Models\Complaint::with('transaksi')
            ->where('pelanggan_id', $pelanggan->id)
            ->findOrFail($id);
    }
}

