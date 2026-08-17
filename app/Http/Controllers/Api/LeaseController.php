<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\RentSchedule;
use Carbon\Carbon;

class LeaseController extends Controller
{
    /**
     * Liste des contrats du bailleur connecté.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'landlord') {
            return response()->json([
                'success' => false,
                'message' => 'Accès réservé aux bailleurs.'
            ], 403);
        }

        $leases = Lease::where('landlord_id', $user->id)
            ->with(['tenant.user', 'property', 'unit'])
            ->get();

        return response()->json([
            'success' => true,
            'leases' => $leases
        ]);
    }

    /**
     * Créer un contrat de location.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'landlord') {
            return response()->json([
                'success' => false,
                'message' => 'Seul un bailleur peut créer un contrat.'
            ], 403);
        }

        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'property_id' => 'required|exists:properties,id',
            'unit_id' => 'required|exists:units,id',
            'rent_amount' => 'required|numeric|min:0',
            'due_day' => 'required|integer|min:1|max:31',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|in:active,ended,terminated',
        ]);

        /*
         * Vérifier que la propriété appartient
         * bien au bailleur connecté.
         */
        $property = Property::where('id', $validated['property_id'])
            ->where('owner_id', $user->id)
            ->first();

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Cette propriété ne vous appartient pas.'
            ], 403);
        }

        /*
         * Vérifier que le logement appartient
         * à cette propriété.
         */
        $unit = Unit::where('id', $validated['unit_id'])
            ->where('property_id', $property->id)
            ->first();

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'Ce logement n’appartient pas à cette propriété.'
            ], 422);
        }

        /*
         * Vérifier que le tenant existe.
         */
        $tenant = Tenant::find($validated['tenant_id']);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant introuvable.'
            ], 404);
        }

        /*
         * Vérifier que le logement n'est pas déjà occupé
         * par un contrat actif.
         */
        $existingLease = Lease::where('unit_id', $unit->id)
            ->where('status', 'active')
            ->exists();

        if ($existingLease) {
            return response()->json([
                'success' => false,
                'message' => 'Ce logement possède déjà un contrat actif.'
            ], 422);
        }

        $lease = Lease::create([
            'landlord_id' => $user->id,
            'tenant_id' => $validated['tenant_id'],
            'property_id' => $validated['property_id'],
            'unit_id' => $validated['unit_id'],
            'rent_amount' => $validated['rent_amount'],
            'due_day' => $validated['due_day'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        /*
 * Générer automatiquement les échéances de loyer.
 */
$startDate = Carbon::parse($lease->start_date);

for ($i = 0; $i < 12; $i++) {

    $period = $startDate->copy()->addMonths($i)->startOfMonth();

    /*
     * Construire la date d'échéance.
     * Exemple : due_day = 5
     * => 05/09/2026
     */
    $daysInMonth = $period->daysInMonth;

    $dueDay = min($lease->due_day, $daysInMonth);

    $dueDate = $period->copy()->day($dueDay);

    /*
     * Ne pas générer une échéance
     * après la date de fin du contrat.
     */
    if (
        $lease->end_date &&
        $dueDate->greaterThan(Carbon::parse($lease->end_date))
    ) {
        break;
    }

    RentSchedule::create([
        'lease_id' => $lease->id,
        'period' => $period->toDateString(),
        'due_date' => $dueDate->toDateString(),
        'amount' => $lease->rent_amount,
        'status' => 'pending',
    ]);
}

        /*
         * Mettre le logement comme occupé.
         */
        $unit->update([
            'status' => 'occupied'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contrat de location créé avec succès.',
            'lease' => $lease->load([
                'tenant.user',
                'property',
                'unit'
            ])
        ], 201);
    }

    /**
     * Afficher un contrat.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $lease = Lease::where('id', $id)
            ->where('landlord_id', $user->id)
            ->with(['tenant.user', 'property', 'unit'])
            ->first();

        if (!$lease) {
            return response()->json([
                'success' => false,
                'message' => 'Contrat introuvable.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'lease' => $lease
        ]);
    }

    /**
     * Modifier un contrat.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $lease = Lease::where('id', $id)
            ->where('landlord_id', $user->id)
            ->first();

        if (!$lease) {
            return response()->json([
                'success' => false,
                'message' => 'Contrat introuvable.'
            ], 404);
        }

        $validated = $request->validate([
            'rent_amount' => 'sometimes|required|numeric|min:0',
            'due_day' => 'sometimes|required|integer|min:1|max:31',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'sometimes|required|in:active,ended,terminated',
        ]);

        $lease->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contrat modifié avec succès.',
            'lease' => $lease->load([
                'tenant.user',
                'property',
                'unit'
            ])
        ]);
    }

    /**
     * Terminer un contrat.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $lease = Lease::where('id', $id)
            ->where('landlord_id', $user->id)
            ->first();

        if (!$lease) {
            return response()->json([
                'success' => false,
                'message' => 'Contrat introuvable.'
            ], 404);
        }

        $lease->update([
            'status' => 'terminated',
            'end_date' => now()->toDateString(),
        ]);

        /*
         * Le logement redevient vacant.
         */
        $unit = Unit::find($lease->unit_id);

        if ($unit) {
            $unit->update([
                'status' => 'vacant'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contrat terminé avec succès.'
        ]);
    }
}