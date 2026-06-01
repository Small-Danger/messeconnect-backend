<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Models\Messe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanningIntentionController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $paroisse = $this->paroisse($request);
        $scope = $request->string('scope', 'upcoming')->toString();
        $today = now()->toDateString();

        $query = $paroisse->messes()
            ->where('statut', '!=', 'annulee');

        if ($scope === 'past') {
            $from = $request->string('from', now()->subMonths(6)->toDateString());
            $to = $request->string('to', $today);

            $query->whereDate('date', '>=', $from)
                ->whereDate('date', '<=', $to)
                ->where(function ($q) use ($today) {
                    $q->whereDate('date', '<', $today)
                        ->orWhere('statut', 'celebree');
                })
                ->orderByDesc('date')
                ->orderByDesc('heure');
        } elseif ($scope === 'range') {
            $from = $request->string('from', $today);
            $to = $request->string('to', now()->addWeeks(8)->toDateString());

            $query->whereDate('date', '>=', $from)
                ->whereDate('date', '<=', $to)
                ->orderBy('date')
                ->orderBy('heure');
        } else {
            $from = $request->string('from', $today);
            $to = $request->string('to', now()->addWeeks(8)->toDateString());

            $query->whereDate('date', '>=', $from)
                ->whereDate('date', '<=', $to)
                ->orderBy('date')
                ->orderBy('heure');
        }

        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('titre', 'like', $term)
                    ->orWhereHas('demandes', function ($demandeQuery) use ($term) {
                        $demandeQuery->where('statut', '!=', 'annulee')
                            ->where(function ($inner) use ($term) {
                                $inner->where('reference', 'like', $term)
                                    ->orWhere('intention', 'like', $term)
                                    ->orWhereHas('fidele', function ($fideleQuery) use ($term) {
                                        $fideleQuery->where('nom', 'like', $term)
                                            ->orWhere('prenom', 'like', $term);
                                    });
                            });
                    });
            });
        }

        $messes = $query->with(['demandes' => function ($query) {
            $query->where('statut', '!=', 'annulee')
                ->with(['paiements.moyenPaiement']);
        }])->get();

        $creneaux = $messes->map(function (Messe $messe) {
            $intentions = $messe->demandes;

            $montantCollecte = $intentions->sum(
                fn ($demande) => $demande->paiements->where('statut', 'reussi')->sum('montant')
            );

            $especesEnAttente = $intentions->sum(
                fn ($demande) => $demande->paiements->filter(
                    fn ($paiement) => $paiement->statut === 'en_attente'
                        && $paiement->moyenPaiement?->type === 'autre'
                )->count()
            );

            return [
                'id' => $messe->id,
                'titre' => $messe->titre,
                'date' => $messe->date?->format('Y-m-d') ?? $messe->date,
                'heure' => is_string($messe->heure) ? substr($messe->heure, 0, 5) : $messe->heure,
                'capacite_max' => $messe->capacite_max,
                'places_reservees' => $messe->places_reservees,
                'intentions_count' => $intentions->count(),
                'montant_collecte' => (float) $montantCollecte,
                'paiements_especes_en_attente' => $especesEnAttente,
                'statut' => $messe->statut,
            ];
        })->values();

        return response()->json([
            'creneaux' => $creneaux,
        ]);
    }
}
