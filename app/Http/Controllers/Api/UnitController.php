<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Liste les logements d'une propriété.
     */
    public function index($propertyId)
    {
        $property = Property::where('id', $propertyId)
            ->where('owner_id', auth()->id())
            ->firstOrFail();

        $units = Unit::where('property_id', $property->id)->get();

        return response()->json([
            'success' => true,
            'property' => $property,
            'units' => $units
        ]);
    }

    /**
     * Créer un logement.
     */
    public function store(Request $request, $propertyId)
    {
        $property = Property::where('id', $propertyId)
            ->where('owner_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:vacant,occupied,maintenance',
        ]);

        $unit = Unit::create([
            'property_id' => $property->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'rent_amount' => $validated['rent_amount'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logement créé avec succès.',
            'unit' => $unit
        ], 201);
    }

    /**
     * Afficher un logement.
     */
    public function show($propertyId, $unitId)
    {
        $property = Property::where('id', $propertyId)
            ->where('owner_id', auth()->id())
            ->firstOrFail();

        $unit = Unit::where('id', $unitId)
            ->where('property_id', $property->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'unit' => $unit
        ]);
    }

    /**
     * Modifier un logement.
     */
    public function update(Request $request, $propertyId, $unitId)
    {
        $property = Property::where('id', $propertyId)
            ->where('owner_id', auth()->id())
            ->firstOrFail();

        $unit = Unit::where('id', $unitId)
            ->where('property_id', $property->id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'rent_amount' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:vacant,occupied,maintenance',
        ]);

        $unit->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Logement modifié avec succès.',
            'unit' => $unit
        ]);
    }

    /**
     * Supprimer un logement.
     */
    public function destroy($propertyId, $unitId)
    {
        $property = Property::where('id', $propertyId)
            ->where('owner_id', auth()->id())
            ->firstOrFail();

        $unit = Unit::where('id', $unitId)
            ->where('property_id', $property->id)
            ->firstOrFail();

        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logement supprimé avec succès.'
        ]);
    }
}