<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\UpdateDemandeMesseStatutRequest;
use App\Http\Resources\Api\Paroisse\DemandeMesseResource;
use App\Models\DemandeMesse;
use App\Services\Paroisse\DemandeMesseStatutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DemandeMesseController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $query = $this->paroisse($request)
            ->demandes()
            ->with(['messe', 'typeOffrande', 'fidele', 'paiements'])
            ->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        if ($request->filled('messe_id')) {
            $query->where('messe_id', $request->string('messe_id'));
        }

        if ($request->filled('from')) {
            $query->whereHas('messe', fn ($q) => $q->whereDate('date', '>=', $request->string('from')));
        }

        if ($request->filled('to')) {
            $query->whereHas('messe', fn ($q) => $q->whereDate('date', '<=', $request->string('to')));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', $term)
                    ->orWhere('intention', 'like', $term)
                    ->orWhereHas('fidele', function ($fideleQuery) use ($term) {
                        $fideleQuery->where('nom', 'like', $term)
                            ->orWhere('prenom', 'like', $term);
                    });
            });
        }

        match ($request->string('vue')->toString()) {
            'a_venir' => $query->whereHas('messe', fn ($q) => $q->whereDate('date', '>=', now()->toDateString()))
                ->where('statut', '!=', 'annulee'),
            'celebre' => $query->where('statut', 'celebree'),
            'annulee' => $query->where('statut', 'annulee'),
            'historique' => $query->where(function ($q) {
                $q->whereIn('statut', ['celebree', 'annulee'])
                    ->orWhereHas('messe', fn ($m) => $m->whereDate('date', '<', now()->toDateString()));
            }),
            default => null,
        };

        return response()->json([
            'demandes' => DemandeMesseResource::collection($query->get()),
        ]);
    }

    public function show(Request $request, DemandeMesse $demandeMesse): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $demandeMesse);
        $demandeMesse->load(['messe', 'typeOffrande', 'fidele', 'paiements']);

        return response()->json([
            'demande' => new DemandeMesseResource($demandeMesse),
        ]);
    }

    public function updateStatut(
        UpdateDemandeMesseStatutRequest $request,
        DemandeMesse $demandeMesse,
        DemandeMesseStatutService $statutService,
    ): JsonResponse {
        $this->ensureBelongsToParoisse($request, $demandeMesse);

        $demande = $statutService->mettreAJour(
            $demandeMesse,
            $request->validated('statut'),
            $request->validated('commentaire'),
        );

        $demande->load(['messe', 'typeOffrande', 'fidele', 'paiements']);

        return response()->json([
            'message' => 'Statut de la demande mis à jour.',
            'demande' => new DemandeMesseResource($demande),
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, DemandeMesse $demandeMesse): void
    {
        abort_unless(
            $demandeMesse->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }
}
