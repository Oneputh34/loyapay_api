<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\RentSchedule;
use Illuminate\Http\Request;

class RentScheduleController extends Controller
{
    /**
     * Liste des échéances du bailleur connecté.
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

        $schedules = RentSchedule::whereHas('lease', function ($query) use ($user) {
            $query->where('landlord_id', $user->id);
        })
        ->with([
            'lease.tenant.user',
            'lease.property',
            'lease.unit'
        ])
        ->orderBy('due_date')
        ->get();

        return response()->json([
            'success' => true,
            'rent_schedules' => $schedules
        ]);
    }

    /**
     * Créer une échéance manuellement.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'landlord') {
            return response()->json([
                'success' => false,
                'message' => 'Seul un bailleur peut créer une échéance.'
            ], 403);
        }

        $validated = $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'period' => 'required|date',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
        ]);

        // Vérifier que le contrat appartient au bailleur connecté
        $lease = Lease::where('id', $validated['lease_id'])
            ->where('landlord_id', $user->id)
            ->first();

        if (!$lease) {
            return response()->json([
                'success' => false,
                'message' => 'Contrat introuvable ou non autorisé.'
            ], 403);
        }

        // Éviter de créer deux fois la même échéance
        $existingSchedule = RentSchedule::where('lease_id', $lease->id)
            ->where('period', $validated['period'])
            ->first();

        if ($existingSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'Une échéance existe déjà pour cette période.'
            ], 409);
        }

        $schedule = RentSchedule::create([
            'lease_id' => $lease->id,
            'period' => $validated['period'],
            'due_date' => $validated['due_date'],
            'amount' => $validated['amount'],
            'status' => $validated['status'] ?? 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Échéance créée avec succès.',
            'rent_schedule' => $schedule
        ], 201);
    }

    /**
     * Afficher une échéance.
     */
    public function show($id, Request $request)
    {
        $user = $request->user();

        $schedule = RentSchedule::with([
            'lease.tenant.user',
            'lease.property',
            'lease.unit'
        ])->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Échéance introuvable.'
            ], 404);
        }

        // Vérifier que l'échéance appartient au bailleur
        if ($schedule->lease->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'rent_schedule' => $schedule
        ]);
    }
}