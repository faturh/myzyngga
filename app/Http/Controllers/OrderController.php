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

        return view('order.pickup-location', compact('service', 'serviceLabel'));
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
            return auth()->check() ? redirect()->route('dashboard') : redirect()->route('landing');
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

        return view('order.booking', compact(
            'service', 'serviceLabel', 'address', 'detailAddress', 'lat', 'lng'
        ));
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

        return ($isGuest ? redirect()->route('landing') : redirect()->route('dashboard'))
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
}
