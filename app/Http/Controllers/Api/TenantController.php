<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Afficher le profil du tenant connecté.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'tenant') {
            return response()->json([
                'success' => false,
                'message' => 'Accès réservé aux tenants.'
            ], 403);
        }

        $tenant = Tenant::where('user_id', $user->id)
            ->with('user')
            ->first();

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Profil tenant introuvable.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'tenant' => $tenant
        ]);
    }

    /**
     * Modifier le profil du tenant connecté.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'tenant') {
            return response()->json([
                'success' => false,
                'message' => 'Accès réservé aux tenants.'
            ], 403);
        }

        $tenant = Tenant::where('user_id', $user->id)->first();

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Profil tenant introuvable.'
            ], 404);
        }

        $validated = $request->validate([
            'identity_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
        ]);

        $tenant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil tenant mis à jour avec succès.',
            'tenant' => $tenant->load('user')
        ]);
    }
}