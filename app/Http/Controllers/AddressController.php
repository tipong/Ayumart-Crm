<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerAddress;

class AddressController extends Controller
{
    /**
     * Display addresses
     */
    public function index()
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        $addresses = CustomerAddress::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelanggan.addresses.index', compact('addresses'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('pelanggan.addresses.create');
    }

    /**
     * Store new address
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return redirect()->route('home')->with('error', 'Data pelanggan tidak ditemukan');
        }

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'alamat_lengkap' => 'required|string',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'nama_penerima' => 'required|string|max:100',
            'no_telp_penerima' => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean'
        ]);

        $validated['id_pelanggan'] = $pelanggan->id_pelanggan;

        // If this is the first address or marked as default, set as default
        $hasAddresses = CustomerAddress::where('id_pelanggan', $pelanggan->id_pelanggan)->exists();
        if (!$hasAddresses || ($request->has('is_default') && $request->is_default)) {
            // Unset all other defaults
            CustomerAddress::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
        }

        $address = CustomerAddress::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil ditambahkan',
                'address' => $address
            ]);
        }

        return redirect()->route('pelanggan.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        $address = CustomerAddress::where('id', $id)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->firstOrFail();

        return view('pelanggan.addresses.edit', compact('address'));
    }

    /**
     * Update address
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        $address = CustomerAddress::where('id', $id)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->firstOrFail();

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'alamat_lengkap' => 'required|string',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'nama_penerima' => 'required|string|max:100',
            'no_telp_penerima' => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean'
        ]);

        if ($request->has('is_default') && $request->is_default) {
            // Unset all other defaults
            CustomerAddress::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $address->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diperbarui',
                'address' => $address
            ]);
        }

        return redirect()->route('pelanggan.addresses.index')
            ->with('success', 'Alamat berhasil diperbarui');
    }

    /**
     * Delete address
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        $address = CustomerAddress::where('id', $id)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->firstOrFail();

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set first remaining address as default
        if ($wasDefault) {
            $firstAddress = CustomerAddress::where('id_pelanggan', $pelanggan->id_pelanggan)->first();
            if ($firstAddress) {
                $firstAddress->update(['is_default' => true]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus'
            ]);
        }

        return redirect()->route('pelanggan.addresses.index')
            ->with('success', 'Alamat berhasil dihapus');
    }

    /**
     * Set address as default
     */
    public function setDefault($id)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        $address = CustomerAddress::where('id', $id)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->firstOrFail();

        $address->setAsDefault();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alamat default berhasil diubah'
            ]);
        }

        return redirect()->route('pelanggan.addresses.index')
            ->with('success', 'Alamat default berhasil diubah');
    }

    /**
     * Get addresses for checkout (AJAX)
     */
    public function getForCheckout()
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan'], 404);
        }

        $addresses = CustomerAddress::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'addresses' => $addresses
        ]);
    }

    /**
     * Get address details (AJAX)
     */
    public function getAddressDetails($id)
    {
        $user = Auth::user();
        $pelanggan = $user->getOrCreatePelanggan();

        if (!$pelanggan) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan'], 404);
        }

        $address = CustomerAddress::where('id', $id)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'address' => [
                'id' => $address->id,
                'label' => $address->label,
                'nama_penerima' => $address->nama_penerima,
                'no_telp_penerima' => $address->no_telp_penerima,
                'alamat_lengkap' => $address->alamat_lengkap,
                'kota' => $address->kota,
                'kecamatan' => $address->kecamatan,
                'kode_pos' => $address->kode_pos,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'formatted_address' => $address->formatted_address,
                'is_default' => $address->is_default,
            ]
        ]);
    }
}
