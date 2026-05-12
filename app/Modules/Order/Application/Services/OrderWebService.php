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
        return [
            'service' => $service,
            'serviceLabel' => self::SERVICE_LABELS[$service] ?? ucfirst($service),
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
        $pelanggan = $this->customerRepository->upsertProfileForUser($user, [
            'nama' => $user->name,
            'jenis_kelamin' => 'L',
            'telepon' => '-',
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

        $orderService->createOrder(new CreateOrderData(
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

        session()->forget(['order.service', 'order.address', 'order.detail_address', 'order.lat', 'order.lng']);

        return redirect()->route('dashboard')->with('success', 'Pesanan Anda berhasil dibuat!');
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
