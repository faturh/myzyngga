<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Customer\Domain\Repositories\CustomerRepositoryInterface;
use App\Modules\Order\Application\DTO\CreateOrderData;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderWebService
{
    private const SERVICE_LABELS = [
        'kilat' => 'Kilat',
        'regular' => 'Regular',
        'quick' => 'Quick',
        'express' => 'Express',
        'satuan' => 'Satuan',
    ];

    private const SERVICE_ESTIMATED_TOTALS = [
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

        // Simple logic for searching and verifying order (placeholder)
        if (str_contains(strtolower($request->input('query')), 'rafi') || str_contains(strtoupper($request->input('query')), 'ZYG-12345')) {
            $orders = [
                [
                    'id' => 'IJK902H8MAHD',
                    'customer_name' => 'Rafi Syihan',
                    'phone_last_4' => '7890',
                    'service' => 'Express',
                    'date' => 'Minggu, 24 Feb | 12.09',
                    'status' => 'Diproses',
                    'progress' => 56,
                    'total' => 33000
                ]
            ];
            return back()->with('orders', $orders)->withInput();
        }

        return back()->withErrors([
            'query' => 'Pesanan tidak ditemukan.'
        ])->withInput();
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
}
