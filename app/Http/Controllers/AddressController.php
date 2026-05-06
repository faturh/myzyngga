<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_primary', 'desc')->get();
        return view('profile.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('profile.addresses.create');
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
        
        // Limit to 5 addresses
        if ($user->addresses()->count() >= 5) {
            return back()->with('error', 'Kamu hanya bisa menyimpan maksimal 5 alamat.');
        }

        // If it's the first address, make it primary
        $isPrimary = $user->addresses()->count() === 0;

        $user->addresses()->create([
            'label' => $request->label,
            'address_detail' => $request->address_detail,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'note' => $request->note,
            'is_primary' => $isPrimary,
        ]);

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
        return view('profile.addresses.edit', compact('address'));
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

        $address->update($request->only('label', 'address_detail', 'latitude', 'longitude', 'note'));

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil diperbarui.');
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

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil dihapus.');
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

        return redirect()->route('addresses.index')->with('success', 'Alamat utama berhasil diatur.');
    }
}
