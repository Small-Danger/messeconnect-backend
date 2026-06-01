<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Models\CampagneCollecte;
use App\Models\DemandeMesse;
use App\Models\FavoriParoisse;
use App\Models\Paiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $paroisse = $this->paroisse($request);
        $paroisseId = $paroisse->id;

        $demandesQuery = DemandeMesse::query()->where('paroisse_id', $paroisseId);

        $montantCollecte = Paiement::query()
            ->where('statut', 'reussi')
            ->where(function ($query) use ($paroisseId) {
                $query->whereHas('demandeMesse', fn ($q) => $q->where('paroisse_id', $paroisseId))
                    ->orWhereHas('campagneCollecte', fn ($q) => $q->where('paroisse_id', $paroisseId));
            })
            ->sum('montant');

        $campagnesActives = CampagneCollecte::query()
            ->where('paroisse_id', $paroisseId)
            ->where(function ($query) {
                $query->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>=', now()->toDateString());
            })
            ->count();

        return response()->json([
            'statistiques' => [
                'demandes' => [
                    'total' => (clone $demandesQuery)->count(),
                    'a_venir' => (clone $demandesQuery)->whereHas('messe', fn ($q) => $q->whereDate('date', '>=', now()->toDateString()))
                        ->where('statut', '!=', 'annulee')
                        ->count(),
                    'celebre' => (clone $demandesQuery)->where('statut', 'celebree')->count(),
                    'annulees' => (clone $demandesQuery)->where('statut', 'annulee')->count(),
                ],
                'montant_collecte' => $montantCollecte,
                'fideles' => FavoriParoisse::query()->where('paroisse_id', $paroisseId)->distinct('fidele_id')->count('fidele_id'),
                'campagnes_actives' => $campagnesActives,
            ],
        ]);
    }
}
