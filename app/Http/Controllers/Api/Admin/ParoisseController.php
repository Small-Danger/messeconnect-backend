<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\LogsAdminAction;
use App\Http\Controllers\Api\Concerns\AppliesInsensitiveSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateParoisseActifRequest;
use App\Http\Requests\Api\Admin\UpdateParoisseStatutRequest;
use App\Http\Resources\Api\Admin\ParoisseDetailResource;
use App\Http\Resources\Api\Admin\ParoisseResource;
use App\Models\Paiement;
use App\Models\Paroisse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParoisseController extends Controller
{
    use AppliesInsensitiveSearch, LogsAdminAction;

    public function index(Request $request): JsonResponse
    {
        $query = Paroisse::query()->with('diocese')->withCount(['demandes', 'favoris']);

        if ($request->filled('q')) {
            $terme = $request->string('q')->toString();
            $query->where(function ($sub) use ($terme) {
                $this->whereInsensitive($sub, 'nom', $terme);
                $sub->orWhere(function ($or) use ($terme) {
                    $this->whereInsensitive($or, 'ville', $terme);
                });
                $sub->orWhere(function ($or) use ($terme) {
                    $this->whereInsensitive($or, 'email', $terme);
                });
                $sub->orWhereHas('diocese', function ($diocese) use ($terme) {
                    $this->whereInsensitive($diocese, 'nom', $terme);
                });
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        if ($request->has('actif')) {
            $query->where('actif', $request->boolean('actif'));
        }

        $paroisses = $query->orderBy('nom')->get();
        $this->attachMontantsCollecte($paroisses);

        return response()->json([
            'paroisses' => ParoisseResource::collection($paroisses),
        ]);
    }

    public function show(Paroisse $paroisse): JsonResponse
    {
        $paroisse->load([
            'diocese',
            'userParoisses',
            'medias' => fn ($query) => $query->orderBy('ordre'),
            'demandes' => fn ($query) => $query->with('fidele')->latest()->limit(10),
            'campagneCollectes' => fn ($query) => $query->latest()->limit(5),
            'publications' => fn ($query) => $query->latest()->limit(5),
        ])->loadCount(['demandes', 'favoris']);

        $paiements = Paiement::query()
            ->with('moyenPaiement')
            ->where('statut', 'reussi')
            ->where(function ($query) use ($paroisse) {
                $query->whereHas('demandeMesse', fn ($demande) => $demande->where('paroisse_id', $paroisse->id))
                    ->orWhereHas('campagneCollecte', fn ($campagne) => $campagne->where('paroisse_id', $paroisse->id));
            })
            ->latest()
            ->limit(10)
            ->get();

        $paroisse->setRelation('paiements', $paiements);
        $this->attachMontantsCollecte(new Collection([$paroisse]));

        return response()->json([
            'paroisse' => new ParoisseDetailResource($paroisse),
        ]);
    }

    public function updateStatut(UpdateParoisseStatutRequest $request, Paroisse $paroisse): JsonResponse
    {
        $statutPrecedent = $paroisse->statut;
        $paroisse->update(['statut' => $request->validated('statut')]);

        $this->logAdminAction($request, 'paroisse.statut', [
            'paroisse_id' => $paroisse->id,
            'statut_precedent' => $statutPrecedent,
            'statut' => $paroisse->statut,
            'commentaire' => $request->validated('commentaire'),
        ]);

        return response()->json([
            'message' => 'Statut de la paroisse mis à jour.',
            'paroisse' => new ParoisseResource($paroisse->fresh()->load('diocese')),
        ]);
    }

    public function updateActif(UpdateParoisseActifRequest $request, Paroisse $paroisse): JsonResponse
    {
        $paroisse->update(['actif' => $request->boolean('actif')]);

        $this->logAdminAction($request, 'paroisse.actif', [
            'paroisse_id' => $paroisse->id,
            'actif' => $paroisse->actif,
        ]);

        return response()->json([
            'message' => 'État de la paroisse mis à jour.',
            'paroisse' => new ParoisseResource($paroisse->fresh()->load('diocese')),
        ]);
    }

    /**
     * @param  Collection<int, Paroisse>  $paroisses
     */
    private function attachMontantsCollecte(Collection $paroisses): void
    {
        if ($paroisses->isEmpty()) {
            return;
        }

        $ids = $paroisses->pluck('id');

        $fromDemandes = Paiement::query()
            ->select('demande_messes.paroisse_id as paroisse_id', DB::raw('SUM(paiements.montant) as total'))
            ->join('demande_messes', 'demande_messes.id', '=', 'paiements.demande_messe_id')
            ->where('paiements.statut', 'reussi')
            ->whereIn('demande_messes.paroisse_id', $ids)
            ->groupBy('demande_messes.paroisse_id')
            ->pluck('total', 'paroisse_id');

        $fromCampagnes = Paiement::query()
            ->select('campagne_collectes.paroisse_id as paroisse_id', DB::raw('SUM(paiements.montant) as total'))
            ->join('campagne_collectes', 'campagne_collectes.id', '=', 'paiements.campagne_collecte_id')
            ->where('paiements.statut', 'reussi')
            ->whereIn('campagne_collectes.paroisse_id', $ids)
            ->groupBy('campagne_collectes.paroisse_id')
            ->pluck('total', 'paroisse_id');

        foreach ($paroisses as $paroisse) {
            $paroisse->setAttribute(
                'montant_collecte',
                (float) ($fromDemandes[$paroisse->id] ?? 0) + (float) ($fromCampagnes[$paroisse->id] ?? 0),
            );
        }
    }
}
