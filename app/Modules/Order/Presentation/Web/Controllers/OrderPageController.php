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
        $order = $this->webService->detailData($id, $request->user());

        if (! $order) {
            return redirect()->route($request->user() ? 'order.history' : 'order.check')
                ->withErrors(['order' => 'Pesanan tidak ditemukan.']);
        }

        return view('pelanggan.order.detail', compact('order'));
    }

    public function downloadReceipt(Request $request, string $id)
    {
        $order = $this->webService->detailData($id, $request->user());

        if (! $order) {
            return redirect()->route($request->user() ? 'order.history' : 'order.check')
                ->withErrors(['order' => 'Pesanan tidak ditemukan.']);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pelanggan.order.receipt-pdf', compact('order'));
        // Set paper size for thermal receipt (e.g., 80mm width)
        $pdf->setPaper(array(0, 0, 226.77, 600), 'portrait'); // 80mm = ~226.77pt

        return $pdf->download('Nota_Zyngga_' . $order['nota_layanan'] . '.pdf');
    }

    public function repeat(Request $request, string $id)
    {
        $order = \App\Models\Transaksi::find($id);
        if (!$order) {
            return redirect()->route('dashboard');
        }

        // Determine service from order properties
        $service = $order->total_biaya_prioritas > 0 ? 'kilat' : 'reguler';

        if ($order->pickup_lat && $order->pickup_lng) {
            session([
                'order.service'        => $service,
                'order.address'        => $order->pickup_address,
                'order.detail_address' => $order->pickup_detail_address ?? '',
                'order.lat'            => (string) $order->pickup_lat,
                'order.lng'            => (string) $order->pickup_lng,
            ]);

            return redirect()->route('order.booking');
        }

        // Redirect to pickup selection if it was a drop-off or no coords are available
        return redirect()->route('order.pickup', ['service' => $service])
            ->with('info', 'Silakan pilih lokasi pengambilan baru karena pesanan sebelumnya dilakukan di outlet.');
    }

    public function history(Request $request)
    {
        return view('pelanggan.order.history', $this->webService->historyData($request->user()));
    }

    public function complaintsHistory(Request $request)
    {
        $complaints = \App\Models\Complaint::with('transaksi')->where('user_id', $request->user()->id)->latest()->get();
        return view('pelanggan.profile.complaints', compact('complaints'));
    }

    public function complaintDetail(Request $request, $id)
    {
        $complaint = \App\Models\Complaint::with('transaksi')->where('user_id', $request->user()->id)->findOrFail($id);
        return view('pelanggan.profile.complaint-detail', compact('complaint'));
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
    public function complaint(Request $request, string $id)
    {
        $order = $this->webService->detailData($id, $request->user());
        if (!$order) {
            return redirect()->route('home');
        }
        return view('pelanggan.order.complaint', compact('order'));
    }

    public function storeComplaint(Request $request, string $id)
    {
        $request->validate([
            'content' => 'nullable|string|max:1000',
            'issue_description' => 'nullable|string|max:1000',
            'issue_types' => 'nullable|array',
            'issue_types.*' => 'string|in:pakaian_rusak,masalah_pengantaran,status_pesanan,kendala_pembayaran,lainnya',
            'issue_image' => 'nullable|image|max:5120',
        ]);

        try {
            $this->webService->storeComplaint($id, $request, $request->user());
            return redirect()->route('order.detail', ['id' => $id])
                ->with('success', 'Komplain berhasil dikirim');
        } catch (\Exception $e) {
            return redirect()->route('order.detail', ['id' => $id])
                ->withErrors(['complaint' => $e->getMessage()]);
        }
    }

    public function requestDelivery(Request $request, string $id)
    {
        $order = $this->webService->detailData($id, $request->user());
        if (!$order) {
            return redirect()->route('home');
        }

        $address = $order['address'] ?? '';

        if (!empty($address) && $address !== '-' && !$request->has('change')) {
            $note = ($order['address_detail'] ?? '') !== '-' ? ($order['address_detail'] ?? '') : '';
            
            $lat = $order['lat'] ?? '';
            $lng = $order['lng'] ?? '';

            if ((empty($lat) || empty($lng)) && $request->user()) {
                $savedAddr = $request->user()->addresses()->where('address_detail', $address)->first();
                if ($savedAddr) {
                    $lat = $savedAddr->latitude;
                    $lng = $savedAddr->longitude;
                }
            }

            return redirect()->route('order.request.delivery.confirm', [
                'id' => $id,
                'address' => $address,
                'note' => $note,
                'lat' => $lat,
                'lng' => $lng,
            ]);
        }

        $savedAddresses = collect();
        if ($request->user()) {
            $savedAddresses = $request->user()->addresses()->get();
        }
        return view('pelanggan.order.request-delivery-location', [
            'order' => $order,
            'service' => $order['service_type'] ?? 'Reguler',
            'from' => 'detail',
            'savedAddresses' => $savedAddresses,
        ]);
    }

    public function storeRequestDelivery(Request $request, string $id)
    {
        $order = \App\Models\Transaksi::find($id);
        if ($order) {
            $oldState = [
                'pickup_address' => $order->pickup_address,
                'pickup_detail_address' => $order->pickup_detail_address,
                'pickup_lat' => $order->pickup_lat,
                'pickup_lng' => $order->pickup_lng,
                'is_roundtrip' => $order->is_roundtrip,
                'total_bayar_akhir' => $order->total_bayar_akhir,
            ];
            session()->put('pending_rollback_delivery_' . $id, $oldState);
        }

        try {
            $this->webService->storeRequestDelivery($request, $id);
            if ($request->ajax() || $request->wantsJson()) {
                $updatedOrder = $this->webService->detailData($id, $request->user());
                return response()->json([
                    'success' => true,
                    'snap_token' => $updatedOrder['snap_token'] ?? null,
                    'redirect' => route('order.detail', ['id' => $id])
                ]);
            }
            return redirect()->route('order.detail', ['id' => $id])
                ->with('success', 'Lokasi pengantaran berhasil diajukan');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rollbackDelivery(Request $request, string $id)
    {
        $oldState = session()->get('pending_rollback_delivery_' . $id);
        if ($oldState) {
            $order = \App\Models\Transaksi::find($id);
            if ($order) {
                $order->pickup_address = $oldState['pickup_address'];
                $order->pickup_detail_address = $oldState['pickup_detail_address'];
                $order->pickup_lat = $oldState['pickup_lat'];
                $order->pickup_lng = $oldState['pickup_lng'];
                $order->is_roundtrip = $oldState['is_roundtrip'];
                $order->total_bayar_akhir = $oldState['total_bayar_akhir'];
                $order->save();
            }
            session()->forget('pending_rollback_delivery_' . $id);
        }
        return response()->json(['success' => true]);
    }

    public function requestDeliveryConfirm(Request $request, string $id)
    {
        $order = $this->webService->detailData($id, $request->user());
        if (!$order) {
            return redirect()->route('home');
        }

        return view('pelanggan.order.request-delivery-confirm', [
            'order'   => $order,
            'lat'     => $request->query('lat'),
            'lng'     => $request->query('lng'),
            'address' => $request->query('address'),
            'note'    => $request->query('note', ''),
        ]);
    }

    public function upgrade(Request $request, string $id)
    {
        try {
            $data = $this->webService->upgradeData($id, $request->user());
            return view('pelanggan.order.upgrade', $data);
        } catch (\Exception $e) {
            return redirect()->route('order.detail', ['id' => $id])->withErrors(['order' => $e->getMessage()]);
        }
    }

    public function processUpgrade(Request $request, string $id)
    {
        $request->validate([
            'new_service_id' => 'required|integer',
            'payment_method' => 'nullable|string|in:cash,qris,transfer'
        ]);

        $order = \App\Models\Transaksi::find($id);
        if ($order) {
            $oldState = [
                'layanan_prioritas_id' => $order->layanan_prioritas_id,
                'total_biaya_prioritas' => $order->total_biaya_prioritas,
                'total_bayar_akhir' => $order->total_bayar_akhir,
                'jenis_pembayaran' => $order->jenis_pembayaran,
            ];
            session()->put('pending_rollback_upgrade_' . $id, $oldState);
        }

        try {
            $this->webService->processUpgrade($id, $request->new_service_id, $request->user(), $request->payment_method);
            if ($request->ajax() || $request->wantsJson()) {
                $updatedOrder = $this->webService->detailData($id, $request->user());
                return response()->json([
                    'success' => true,
                    'snap_token' => $updatedOrder['snap_token'] ?? null,
                    'redirect' => route('order.detail', ['id' => $id])
                ]);
            }
            return redirect()->route('order.detail', ['id' => $id])->with('success', 'Pesanan berhasil di-upgrade!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->route('order.detail', ['id' => $id])->withErrors(['order' => $e->getMessage()]);
        }
    }

    public function rollbackUpgrade(Request $request, string $id)
    {
        $oldState = session()->get('pending_rollback_upgrade_' . $id);
        if ($oldState) {
            $order = \App\Models\Transaksi::find($id);
            if ($order) {
                $order->layanan_prioritas_id = $oldState['layanan_prioritas_id'];
                $order->total_biaya_prioritas = $oldState['total_biaya_prioritas'];
                $order->total_bayar_akhir = $oldState['total_bayar_akhir'];
                $order->jenis_pembayaran = $oldState['jenis_pembayaran'];
                $order->save();
            }
            session()->forget('pending_rollback_upgrade_' . $id);
        }
        return response()->json(['success' => true]);
    }
    public function payment(Request $request, string $id)
    {
        try {
            $data = $this->webService->paymentData($id, $request->user());
            return view('pelanggan.order.payment', $data);
        } catch (\Exception $e) {
            return redirect()->route('order.detail', ['id' => $id])->withErrors(['order' => $e->getMessage()]);
        }
    }

    public function updatePayment(Request $request, string $id)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,qris,transfer'
        ]);

        try {
            $this->webService->updatePayment($id, $request->payment_method, $request->user());
            return redirect()->route('order.detail', ['id' => $id])->with('success', 'Metode pembayaran berhasil diubah!');
        } catch (\Exception $e) {
            return redirect()->route('order.detail', ['id' => $id])->withErrors(['order' => $e->getMessage()]);
        }
    }
    public function paymentMethod(Request $request, string $id)
    {
        $order = $this->webService->detailData($id, $request->user());
        if (!$order) {
            return redirect()->route('home');
        }
        return view('pelanggan.order.payment-method', ['order' => $order]);
    }

    public function paymentWaiting(Request $request, string $id)
    {
        $order = $this->webService->detailData($id, $request->user());
        if (!$order) {
            return redirect()->route('home');
        }
        return view('pelanggan.order.waiting', ['order' => $order]);
    }

    public function processPayment(Request $request, string $id)
    {
        $request->validate([
            'method' => 'required|string|in:bca_va,bni_va,bri_va,mandiri_va,permata_va,cimb_va,danamon_va,bsi_va,seabank_va,saqu_va,other_va,qris,gopay,shopeepay,dana'
        ]);

        try {
            $response = $this->webService->processCoreApiPayment($id, $request->method);
            
            $redirectUrl = route('order.payment-instruction', ['id' => $id]);
            if ($request->method === 'gopay') {
                $redirectUrl = route('order.payment.waiting', ['id' => $id]);
            }
            $deeplinkUrl = null;

            if (is_array($response) && isset($response['actions'])) {
                $actions = $response['actions'];
            } elseif (is_object($response) && isset($response->actions)) {
                $actions = $response->actions;
            } else {
                $actions = [];
            }
                
            foreach ($actions as $action) {
                $actionArr = (array) $action;
                if ($actionArr['name'] === 'deeplink-redirect') {
                    $deeplinkUrl = $actionArr['url'];
                    break;
                }
            }

            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl,
                'deeplink' => $deeplinkUrl
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function paymentInstruction(Request $request, string $id)
    {
        $order = $this->webService->detailData($id, $request->user());
        if (!$order) {
            return redirect()->route('home');
        }

        // Fetch the pending payment instructions from the database or cache.
        // For simplicity, we can fetch it via OrderWebService
        $instruction = $this->webService->getPaymentInstruction($id);

        if (!$instruction) {
            return redirect()->route('order.detail', ['id' => $id])->with('error', 'Instruksi pembayaran tidak ditemukan.');
        }

        return view('pelanggan.order.payment-instruction', [
            'order' => $order,
            'instruction' => $instruction
        ]);
    }

    public function paymentStatus(Request $request, string $id)
    {
        $status = $this->webService->checkPaymentStatus($id);
        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }

    public function paymentCancel(Request $request, string $id)
    {
        try {
            $this->webService->cancelCoreApiPayment($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
