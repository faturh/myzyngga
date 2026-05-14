<?php

namespace App\Modules\Order\Presentation\Web\Controllers;

use App\Modules\Order\Application\Services\OrderService;
use App\Modules\Order\Application\Services\OrderWebService;
use Illuminate\Http\Request;

class OrderPageController
{
    public function __construct(
        private readonly OrderWebService $webService,
        private readonly OrderService $orderService,
    ) {
    }

    public function pickupLocation(Request $request, string $service)
    {
        $user = auth()->user();
        $primaryAddress = $user ? $user->addresses()->where('is_primary', true)->first() : null;

        // Only auto-redirect if NOT forced (e.g. not clicking "Ubah" from booking)
        if ($primaryAddress && !$request->has('force')) {
            session([
                'order.service'        => $service,
                'order.address'        => $primaryAddress->address_detail,
                'order.detail_address' => $primaryAddress->note ?? '',
                'order.lat'            => (string) $primaryAddress->latitude,
                'order.lng'            => (string) $primaryAddress->longitude,
            ]);

            return redirect()->route('order.booking');
        }

        $data = $this->webService->pickupLocationData($service);
        $data['from'] = $request->query('from') ?? $request->query('amp;from');

        return view('pelanggan.order.pickup-location', $data);
    }

    public function storePickupLocation(Request $request)
    {
        return $this->webService->storePickupLocation($request);
    }

    public function booking(Request $request)
    {
        $payload = $this->webService->bookingData();
        if ($payload === null) {
            return redirect()->route('dashboard');
        }

        return view('pelanggan.order.booking', $payload);
    }

    public function confirm(Request $request)
    {
        return $this->webService->confirmOrder($request, $this->orderService);
    }

    public function pickupDetails(Request $request, string $service)
    {
        $data = $this->webService->pickupDetailsData($request, $service);
        if (empty($data)) {
            return redirect()->route('order.pickup', $service)->with('error', 'Silakan pilih lokasi terlebih dahulu.');
        }

        return view('pelanggan.order.pickup-details', $data);
    }

    public function storePickupDetails(Request $request)
    {
        return $this->webService->storePickupDetails($request);
    }

    public function updateSession(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            session(["order.$key" => $value]);
        }
        return response()->json(['status' => 'success']);
    }

    public function detail(Request $request, ?string $id = null)
    {
        $status = $request->query('status', 'ongoing'); // 'ongoing' or 'finished'
        $orderId = $id ?: 'IJK902H8MAHD';
        
        $order = [
            'id' => $orderId,
            'service_type' => 'Express',
            'status' => $status === 'finished' ? 'finished' : 'ongoing',
            'status_label' => $status === 'finished' ? 'Ambil di Outlet' : 'Delivery',
            'customer_name' => 'Rafi Syihan',
            'customer_phone' => '0812 3456 7890',
            'address' => 'Telkom University',
            'address_detail' => 'Jl. Telekomunikasi No.1, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung',
            'order_date' => 'Minggu, 12 Mei | 12.00',
            'estimated_finished' => 'Senin, 13 Mei | 12.00',
            'progress' => $status === 'finished' ? 100 : 56,
            'current_step' => $status === 'finished' ? 'Selesai' : 'Mengerjakan Tahap Pengeringan',
            'payment_status' => $status === 'finished' ? 'Lunas' : 'Belum Bayar',
            'payment_method' => 'QRIS',
            'subtotal' => 33000,
            'discount' => 0,
            'tax' => 0,
            'total' => 33000,
            'cash' => 33000,
            'change' => 0,
            'items' => [
                ['name' => 'Express', 'qty' => '3.3', 'price' => 10000, 'subtotal' => 33000]
            ],
            'logs' => [
                ['time' => '08:30', 'date' => 'Senin, 18 Feb', 'note' => 'Mengerjakan Tahap Pengeringan'],
                ['time' => '12:30', 'date' => 'Minggu, 19 Feb', 'note' => 'Mengerjakan Tahap Pencucian'],
                ['time' => '08:30', 'date' => 'Minggu, 19 Feb', 'note' => 'Menerima Pesanan'],
            ]
        ];

        return view('pelanggan.order.detail', compact('order'));
    }

    public function history(Request $request)
    {
        return view('pelanggan.order.history');
    }

    public function cancel(Request $request)
    {
        session()->forget('order');
        return redirect()->route('home');
    }

    public function check(Request $request)
    {
        if ($request->isMethod('post')) {
            return $this->webService->checkOrder($request);
        }

        $orders = session('orders', []);
        return view('pelanggan.order.check', compact('orders'));
    }
}

