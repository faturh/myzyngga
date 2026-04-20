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
        $serviceLabels = [
            'kilat'    => 'Kilat',
            'regular'  => 'Regular',
            'quick'    => 'Quick',
            'express'  => 'Express',
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
            return redirect()->route('dashboard');
        }

        $serviceLabels = [
            'kilat'    => 'Kilat',
            'regular'  => 'Regular',
            'quick'    => 'Quick',
            'express'  => 'Express',
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
        $data = $request->validate([
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
        ]);

        // TODO: persist order to DB here

        // Clear order session
        session()->forget(['order.service', 'order.address', 'order.detail_address', 'order.lat', 'order.lng']);

        return redirect()->route('dashboard')
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
