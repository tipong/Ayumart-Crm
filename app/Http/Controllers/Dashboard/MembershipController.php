<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MembershipController extends Controller
{
    public function index()
    {
        try {
            $memberships = Membership::with(['user', 'user.pelanggan'])  // FIXED: Added eager loading for pelanggan
                                    ->orderBy('id', 'desc')  // FIXED: Changed from created_at to id
                                    ->paginate(15);

            $tiers = ['bronze', 'silver', 'gold', 'platinum'];
            $tierCounts = [
                'bronze' => Membership::where('tier', 'bronze')->where('is_active', true)->count(),
                'silver' => Membership::where('tier', 'silver')->where('is_active', true)->count(),
                'gold' => Membership::where('tier', 'gold')->where('is_active', true)->count(),
                'platinum' => Membership::where('tier', 'platinum')->where('is_active', true)->count(),
            ];

            // Get customers without membership (id_role = 5)
            $customersWithoutMembership = User::with('pelanggan')  // FIXED: Added eager loading
                                            ->where('id_role', 5)  // FIXED: Changed from role_id to id_role
                                            ->whereDoesntHave('membership')
                                            ->get();

            return view('admin.memberships.index', compact('memberships', 'tiers', 'tierCounts', 'customersWithoutMembership'));
        } catch (\Exception $e) {
            Log::error('Error loading memberships: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat memuat data membership.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id_user',  // FIXED: Changed from id to id_user (new PK)
                'points' => 'required|integer|min:0',  // FIXED: Added points validation
                'valid_from' => 'required|date',
                'valid_until' => 'required|date|after:valid_from',
            ]);

            // Set default active status
            $validated['is_active'] = true;

            // Tier and discount will be auto-calculated by model based on points
            // No need to manually set tier and discount_percentage

            Membership::create($validated);

            return redirect()->route('admin.memberships.index')
                           ->with('success', 'Membership berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating membership: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat menambahkan membership.')
                       ->withInput();
        }
    }

    public function update(Request $request, Membership $membership)
    {
        try {
            $validated = $request->validate([
                'points' => 'required|integer|min:0',
                'valid_from' => 'required|date',
                'valid_until' => 'required|date|after:valid_from',
                'is_active' => 'required|boolean',
            ]);

            // Tier and discount will be auto-updated by model's boot method when points change
            $membership->update($validated);

            return redirect()->route('admin.memberships.index')
                           ->with('success', 'Membership berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating membership: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat mengupdate membership.')
                       ->withInput();
        }
    }

    public function destroy(Membership $membership)
    {
        try {
            $membership->delete();
            return redirect()->route('admin.memberships.index')
                           ->with('success', 'Membership berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting membership: ' . $e->getMessage());
            return back()->with('error', 'Maaf, terjadi kesalahan saat menghapus membership.');
        }
    }
}
