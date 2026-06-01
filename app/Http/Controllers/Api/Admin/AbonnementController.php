<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\LogsAdminAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreAbonnementRequest;
use App\Http\Requests\Api\Admin\UpdateAbonnementRequest;
use App\Http\Resources\Api\Admin\AbonnementResource;
use App\Models\Abonnement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AbonnementController extends Controller
{
    use LogsAdminAction;

    public function index(Request $request): JsonResponse
    {
        $query = Abonnement::query()->with('paroisse')->latest();

        if ($request->filled('paroisse_id')) {
            $query->where('paroisse_id', $request->string('paroisse_id'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        $abonnements = $query->get();

        return response()->json([
            'abonnements' => AbonnementResource::collection($abonnements),
        ]);
    }

    public function store(StoreAbonnementRequest $request): JsonResponse
    {
        $abonnement = Abonnement::query()->create([
            ...$request->validated(),
            'statut' => $request->validated('statut', 'actif'),
        ]);

        $this->logAdminAction($request, 'abonnement.created', ['abonnement_id' => $abonnement->id]);

        return response()->json([
            'message' => 'Abonnement créé.',
            'abonnement' => new AbonnementResource($abonnement->load('paroisse')),
        ], 201);
    }

    public function show(Abonnement $abonnement): JsonResponse
    {
        $abonnement->load('paroisse');

        return response()->json([
            'abonnement' => new AbonnementResource($abonnement),
        ]);
    }

    public function update(UpdateAbonnementRequest $request, Abonnement $abonnement): JsonResponse
    {
        $abonnement->update($request->validated());

        $this->logAdminAction($request, 'abonnement.updated', ['abonnement_id' => $abonnement->id]);

        return response()->json([
            'message' => 'Abonnement mis à jour.',
            'abonnement' => new AbonnementResource($abonnement->fresh()->load('paroisse')),
        ]);
    }
}
