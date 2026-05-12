<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Order\Presentation\Web\Controllers\OrderPageController;

class OrderController extends Controller
{
    /**
     * @deprecated gunakan App\Modules\Order\Presentation\Web\Controllers\OrderPageController
     */
    public function __construct(
        private readonly OrderPageController $controller,
    ) {
    }

    /**
     * Show the pickup location selection page.
     */
    public function pickupLocation(Request $request, string $service)
    {
        $user = auth()->user();
        
        // If user has a primary address, skip the map selection
        // BUT if 'force' is present, they want to choose manually
        if ($user) {
            $primaryAddress = $user->addresses()->where('is_primary', true)->first();
            if ($primaryAddress && !$request->has('force')) {
                session([
                    'order.service'        => $service,
                    'order.address'        => $primaryAddress->address_detail,
                    'order.detail_address' => $primaryAddress->note ?? '',
                    'order.lat'            => (string) $primaryAddress->latitude ?? '',
                    'order.lng'            => (string) $primaryAddress->longitude ?? '',
                ]);
                return redirect()->route('order.booking');
            }
        }

        return $this->controller->pickupLocation($request, $service);
    }

    /**
     * Store pickup location and redirect to booking page.
     */
    public function storePickupLocation(Request $request)
    {
        return $this->controller->storePickupLocation($request);
    }

    /**
     * Show pickup details page.
     */
    public function pickupDetails(Request $request, string $service)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $address = $request->query('address');
        $addressId = $request->query('address_id');

        if (!$lat || !$lng || !$address) {
            return redirect()->route('order.pickup', $service)->with('error', 'Silakan pilih lokasi terlebih dahulu.');
        }

        // If it's an existing address, we might want to show its current labels
        $existingAddress = null;
        if ($addressId && auth()->check()) {
            $existingAddress = auth()->user()->addresses()->find($addressId);
        }

        return view('order.pickup-details', compact('service', 'lat', 'lng', 'address', 'existingAddress'));
    }

    /**
     * Store pickup details.
     */
    public function storePickupDetails(Request $request)
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

        // If the user checked "Simpan Alamat" or if it was an existing address
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
                // Check limit before saving new
                if ($user->addresses()->count() < 5) { // Increased to 5 as per recent updates
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

        // Store in session for booking
        session([
            'order.service'        => $request->service,
            'order.address'        => $request->address_detail,
            'order.detail_address' => $request->note ?? '',
            'order.lat'            => (string) $request->latitude,
            'order.lng'            => (string) $request->longitude,
        ]);

        return redirect()->route('order.booking');
    }

    /**
     * Show the full booking form.
     */
    public function booking(Request $request)
    {
        return $this->controller->booking($request);
    }

    /**
     * Update order session via AJAX.
     */
    public function updateSession(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            session(["order.$key" => $value]);
        }
        return response()->json(['status' => 'success']);
    }

    /**
     * Confirm and place the order.
     */
    public function confirm(Request $request)
    {
        return $this->controller->confirm($request);
    }

    /**
     * Show order detail page.
     */
    public function detail(Request $request)
    {
        return $this->controller->detail($request);
    }

    /**
     * Show order history page.
     */
    public function history(Request $request)
    {
        return $this->controller->history($request);
    }

    /**
     * Show order check page.
     */
    public function check(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'query' => 'required|string',
                'phone_last_4' => 'required|digits:4',
            ], [
                'query.required' => 'Nama atau ID Delivery tidak boleh kosong.',
                'phone_last_4.required' => '4 digit terakhir nomor WhatsApp tidak boleh kosong.',
                'phone_last_4.digits' => 'Masukkan tepat 4 digit terakhir nomor WhatsApp.',
            ]);

            // Simple logic for searching and verifying order (placeholder)
            $orders = [];
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

        $orders = session('orders', []);
        return view('order.check', compact('orders'));
    }
}
