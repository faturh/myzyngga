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
            'payment' => ['required', 'string', Rule::in(['cash', 'qris', 'transfer'])],
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
            parfum: $data['parfum'] ?? null,
            catatan: $data['catatan'] ?? null,
            paymentMethod: $data['payment'],
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
            return back()->withErrors([
                'query' => 'Pesanan tidak ditemukan atau nomor WhatsApp tidak cocok.'
            ])->withInput();
        }

        return back()->with('orders', $orders->all())->withInput();
    }

    private function mapOrderCard(Transaksi $order): array
    {
        $statusLabel = $this->statusLabel($order);
        $isRoundtrip = (bool) $order->is_roundtrip;

        return [
            'id' => (string) $order->id,
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

        return [
            'id' => (string) $order->id,
            'service_type' => $this->serviceName($order),
            'status' => $isFinished ? 'finished' : 'ongoing',
            'status_label' => $isFinished ? 'Ambil di Outlet' : $this->statusLabel($order),
            'is_roundtrip' => (bool) $order->is_roundtrip,
            'customer_name' => $order->pelanggan->nama ?? '-',
            'customer_phone' => $order->pelanggan->telepon ?? '-',
            'address' => $order->pickup_address ?: ($order->pelanggan->alamat ?? '-'),
            'address_detail' => $order->pickup_detail_address ?: '-',
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
            'cash' => (float) ($order->bayar ?: $total),
            'change' => (float) ($order->kembalian ?: 0),
            'items' => $this->mapOrderItems($order),
            'logs' => $this->mapOrderLogs($order),
            'raw_status' => (string) $order->status,
            'can_upgrade' => $this->canBeUpgraded($order),
            'upgrade_fee' => (float) $order->total_biaya_prioritas,
            'snap_token' => $this->getSnapToken($order),
        ];
    }

    private function getSnapToken(Transaksi $order): ?string
    {
        $unpaidAmount = max(0, (float) $order->total_bayar_akhir - (float) $order->bayar);
        
        if ($unpaidAmount <= 0) {
            return null;
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $order->id . '-' . time(),
                'gross_amount' => $unpaidAmount,
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
                $serviceBase = $order->layananPrioritas->nama ?? 'Reguler';
                $name = "{$serviceBase} ({$daysStr}) - " . $this->formatQuantity($qty) . "Kg";
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

        return [[
            'name' => $this->serviceName($order),
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
            'Baru' => 'Baru',
            'Proses' => 'Diproses',
            default => (string) $order->status,
        };
    }

    private function currentStep(Transaksi $order): string
    {
        return match ((string) $order->status) {
            'Selesai' => 'Selesai',
            'Proses' => 'Pesanan sedang diproses',
            'Baru' => 'Pesanan diterima',
            default => (string) $order->status,
        };
    }

    private function progressForStatus(string $status): int
    {
        return match ($status) {
            'Selesai' => 100,
            'Proses' => 56,
            'Baru' => 20,
            default => 10,
        };
    }

    private function isFinished(Transaksi $order): bool
    {
        return $order->status === 'Selesai';
    }

    private function canBeUpgraded(Transaksi $order): bool
    {
        if ($order->status === 'Selesai') {
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
            $durationHours = $this->getLayananDurationHours((int) $upgrade->prioritas);
            $newFinishTime = $baseDate->copy()->addHours($durationHours);
            $bufferHours = strtolower($upgrade->nama) === 'kilat' ? 1 : 5;
            
            if (now()->lte($newFinishTime->copy()->subHours($bufferHours))) {
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

    private function formatEstimatedFinished(Transaksi $order): string
    {
        $baseDate = $order->pickup_date ?? $order->waktu;

        if (! $baseDate) {
            return '-';
        }

        $priority = (int) ($order->layananPrioritas->prioritas ?? 1);
        $days = match (true) {
            $priority >= 99 => 0,
            $priority >= 3 => 1,
            $priority >= 2 => 1,
            default => 3,
        };

        return $baseDate->copy()->addDays($days)->locale('id')->isoFormat('dddd, D MMM | HH.mm');
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

        if ($order->status === 'Selesai') {
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

            $bufferHours = strtolower($upgrade->nama) === 'kilat' ? 1 : 5;
            $isAvailable = now()->lte($newFinishTime->copy()->subHours($bufferHours));

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
        if (!$order || $order->status === 'Selesai') {
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

        $order->layanan_prioritas_id = $newPriority->id;
        $order->total_biaya_prioritas = (float) $order->total_biaya_prioritas + $totalPriceDiff;
        $order->total_bayar_akhir = (float) $order->total_bayar_akhir + $totalPriceDiff;

        if ($paymentMethod && $this->isPaid($order)) {
            $order->jenis_pembayaran = match(strtolower($paymentMethod)) {
                'qris' => 'QRIS',
                'transfer' => 'Transfer',
                default => 'Tunai',
            };
        }

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
}
