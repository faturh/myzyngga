<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        return redirect()->route('profile');
    }

    public function create()
    {
        return view('pelanggan.profile.addresses.create');
    }

    public function createDetails(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $address = $request->query('address');
        $service = $request->query('service');

        if (!$lat || !$lng || !$address) {
            return redirect()->route('addresses.create')->with('error', 'Silakan pilih lokasi terlebih dahulu.');
        }

        $addressCount = Auth::user()->addresses()->count();

        return view('pelanggan.profile.addresses.details', compact('lat', 'lng', 'address', 'service', 'addressCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'address_detail' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'note' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        // Limit to 3 addresses
        if ($user->addresses()->count() >= 3) {
            return back()->with('error', 'Kamu hanya bisa menyimpan maksimal 3 alamat.');
        }

        // Determine if this address should be primary
        $isPrimaryRequest = $request->boolean('is_primary');
        $isFirstAddress = $user->addresses()->count() === 0;
        
        $shouldBePrimary = $isPrimaryRequest || $isFirstAddress;

        if ($shouldBePrimary) {
            // Set all others to false if this one is going to be primary
            $user->addresses()->update(['is_primary' => false]);
        } else if ($user->addresses()->count() === 0) {
            // First address MUST be primary
            $shouldBePrimary = true;
        }

        $address = $user->addresses()->create([
            'label' => $request->label,
            'address_detail' => $request->address_detail,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'note' => $request->note,
            'is_primary' => $shouldBePrimary,
        ]);

        if ($request->has('service')) {
            session([
                'order.service'        => $request->service,
                'order.address'        => $address->address_detail,
                'order.detail_address' => $address->note ?? '',
                'order.lat'            => (string) $address->latitude ?? '',
                'order.lng'            => (string) $address->longitude ?? '',
            ]);
            return redirect()->route('order.booking');
        }

        return redirect()->route('profile')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
        
        $user = Auth::user();
        $addressCount = $user->addresses()->count();
        
        // Handle override from map picker
        if (request()->has('lat')) {
            $address->latitude = request()->query('lat');
            $address->longitude = request()->query('lng');
            $address->address_detail = request()->query('address');
        }

        // Check if coming from order flow
        $service = request()->query('service');
        
        return view('pelanggan.profile.addresses.edit', compact('address', 'service', 'addressCount'));
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'label' => 'required|string|max:255',
            'address_detail' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'note' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $isPrimaryRequest = $request->boolean('is_primary');
        $currentAddressCount = $user->addresses()->count();

        // If it's the only address, it MUST be primary
        if ($currentAddressCount === 1) {
            $isPrimaryRequest = true;
        }

        if ($isPrimaryRequest) {
            // Set all others to false
            $user->addresses()->where('id', '!=', $address->id)->update(['is_primary' => false]);
        } else if ($address->is_primary) {
            // If we are turning OFF the primary status of the currently primary address,
            // we must set another one as primary.
            $nextAddress = $user->addresses()
                ->where('id', '!=', $address->id)
                ->orderBy('updated_at', 'desc') // Pick the most recently updated one as fallback
                ->first();
            
            if ($nextAddress) {
                $nextAddress->update(['is_primary' => true]);
            } else {
                // No other addresses, keep this one as primary
                $isPrimaryRequest = true;
            }
        }

        $address->update([
            'label' => $request->label,
            'address_detail' => $request->address_detail,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'note' => $request->note,
            'is_primary' => $isPrimaryRequest,
        ]);

        if ($request->has('service')) {
            session([
                'order.service'        => $request->service,
                'order.address'        => $address->address_detail,
                'order.detail_address' => $address->note ?? '',
                'order.lat'            => (string) $address->latitude ?? '',
                'order.lng'            => (string) $address->longitude ?? '',
            ]);
            return redirect()->route('order.booking');
        }

        return redirect()->route('profile')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasPrimary = $address->is_primary;
        $address->delete();

        // If primary was deleted, set another one as primary if available
        if ($wasPrimary) {
            $nextAddress = Auth::user()->addresses()->first();
            if ($nextAddress) {
                $nextAddress->update(['is_primary' => true]);
            }
        }

        return redirect()->route('profile')->with('success', 'Alamat berhasil dihapus.');
    }

    public function setPrimary(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // Set all others to false
        Auth::user()->addresses()->update(['is_primary' => false]);
        
        // Set this one to true
        $address->update(['is_primary' => true]);

        return redirect()->route('profile')->with('success', 'Alamat utama berhasil diatur.');
    }
}
