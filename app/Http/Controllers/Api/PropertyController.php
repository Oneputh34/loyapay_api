<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Liste des propriétés du propriétaire connecté
     */
    public function index(Request $request)
    {
        $properties = Property::where('owner_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'properties' => $properties
        ]);
    }

    /**
     * Créer une propriété
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $property = Property::create([
            'owner_id' => $request->user()->id,
            'name' => $validated['name'],
            'address' => $validated['address'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Propriété créée avec succès.',
            'property' => $property
        ], 201);
    }

    /**
     * Afficher une propriété
     */
    public function show(Request $request, Property $property)
    {
        if ($property->owner_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'property' => $property
        ]);
    }

    /**
     * Modifier une propriété
     */
    public function update(Request $request, Property $property)
    {
        if ($property->owner_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
        ]);

        $property->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Propriété mise à jour avec succès.',
            'property' => $property
        ]);
    }

    /**
     * Supprimer une propriété
     */
    public function destroy(Request $request, Property $property)
    {
        if ($property->owner_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        $property->delete();

        return response()->json([
            'success' => true,
            'message' => 'Propriété supprimée avec succès.'
        ]);
    }
}