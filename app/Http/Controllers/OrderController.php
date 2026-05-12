<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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

        $serviceLabels = [
            'kilat'    => 'Kilat',
            'regular'  => 'Regular',
            'quick'    => 'Quick',
            'express'  => 'Express',
            'satuan'   => 'Satuan',
        ];

        $serviceLabel = $serviceLabels[$service] ?? ucfirst($service);

        $savedAddresses = $user ? $user->addresses()->get() : collect();

        return view('order.pickup-location', compact('service', 'serviceLabel', 'savedAddresses'));
    }

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
        if ($addressId) {
            $existingAddress = auth()->user()->addresses()->find($addressId);
        }

        return view('order.pickup-details', compact('service', 'lat', 'lng', 'address', 'existingAddress'));
    }

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
                if ($user->addresses()->count() < 3) {
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
     * Store pickup location and redirect to booking page.
     */
    public function storePickupLocation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service'        => ['required', 'string'],
            'address'        => ['required', 'string', 'max:500'],
            'detail_address' => ['nullable', 'string', 'max:255'],
            'lat'            => ['nullable', 'numeric'],
            'lng'            => ['nullable', 'numeric'],
        ]);

        // Store in session to pass to booking page
        session([
            'order.service'        => $data['service'],
            'order.address'        => $data['address'],
            'order.detail_address' => $data['detail_address'] ?? '',
            'order.lat'            => $data['lat'] ?? '',
            'order.lng'            => $data['lng'] ?? '',
        ]);

        return redirect()->route('order.booking');
    }

    /**
     * Show the full booking form (Figma 77:301).
     */
    public function booking(Request $request)
    {
        // Require pickup location to have been set
        if (! session()->has('order.address')) {
            return auth()->check() ? redirect()->route('home') : redirect()->route('landing');
        }

        $serviceLabels = [
            'kilat'    => 'Kilat',
            'regular'  => 'Regular',
            'quick'    => 'Quick',
            'express'  => 'Express',
            'satuan'   => 'Satuan',
        ];

        $service       = session('order.service', 'regular');
        $serviceLabel  = $serviceLabels[$service] ?? ucfirst($service);
        $address       = session('order.address', '');
        $detailAddress = session('order.detail_address', '');
        $lat           = session('order.lat', '');
        $lng           = session('order.lng', '');
        
        // Additional state from session
        $parfum      = session('order.parfum', 'Lavender');
        $note        = session('order.note', '');
        $pickupDate  = session('order.pickup_date', '');
        $pickupTime  = session('order.pickup_time', '');

        return view('order.booking', compact(
            'service', 'serviceLabel', 'address', 'detailAddress', 'lat', 'lng',
            'parfum', 'note', 'pickupDate', 'pickupTime'
        ));
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
    public function confirm(Request $request): RedirectResponse
    {
        $isGuest = !auth()->check();

        $rules = [
            'service'          => ['required', 'string'],
            'address'          => ['required', 'string'],
            'detail_address'   => ['nullable', 'string'],
            'lat'              => ['nullable', 'numeric'],
            'lng'              => ['nullable', 'numeric'],
            'selected_service_id' => ['required', 'string'],
            'pickup_date'      => ['required', 'string'],
            'pickup_time'      => ['required', 'string'],
            'parfum'           => ['nullable', 'string'],
            'note'             => ['nullable', 'string'],
            'payment'          => ['required', 'string'],
        ];

        if ($isGuest) {
            $rules['customer_name']  = ['required', 'string', 'max:255'];
            $rules['customer_phone'] = ['required', 'string', 'max:20'];
            $rules['customer_email'] = ['required', 'email', 'max:255'];
        }

        $data = $request->validate($rules);

        // TODO: persist order to DB here
        // If guest, you might want to create a guest record or just save info in order table

        // Clear order session
        session()->forget(['order.service', 'order.address', 'order.detail_address', 'order.lat', 'order.lng']);

        return redirect()->route('order.detail')
            ->with('success', 'Pesanan Anda berhasil dibuat!');
    }

    /**
     * Show order detail page (Figma 95:10 & 221:719).
     */
    public function detail(Request $request)
    {
        return view('order.detail');
    }

    /**
     * Show order history page (Figma 110:15).
     */
    public function history(Request $request)
    {
        return view('order.history');
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

            // Logic for searching and verifying order
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
                    ],
                    [
                        'id' => 'ZYG-67890ABC',
                        'customer_name' => 'Rafi Syihan',
                        'phone_last_4' => '7890',
                        'service' => 'Satuan',
                        'date' => 'Minggu, 24 Feb | 12.09',
                        'status' => 'Diproses',
                        'progress' => 56,
                        'total' => 25000
                    ]
                ];
                
                // Redirect back with results in session flash
                return back()->with('orders', $orders)->withInput();
            }

            return back()->withErrors([
                'query' => 'Pesanan tidak ditemukan. Cek kembali Nama/ID atau 4 digit nomor WhatsApp kamu.'
            ])->withInput();
        }

        // Get orders from session (if any)
        $orders = session('orders', []);

        return view('order.check', compact('orders'));
    }
}
