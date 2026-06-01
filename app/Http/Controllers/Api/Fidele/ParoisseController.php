<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Fidele\CampagneCollecteResource;
use App\Http\Resources\Api\Fidele\MesseResource;
use App\Http\Resources\Api\Fidele\MoyenPaiementResource;
use App\Http\Resources\Api\Fidele\ParoisseResource;
use App\Http\Resources\Api\Fidele\PublicationResource;
use App\Http\Resources\Api\Fidele\TypeOffrandeResource;
use App\Models\FavoriParoisse;
use App\Models\Paroisse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParoisseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Paroisse::query()
            ->where('statut', 'validee')
            ->where('actif', true)
            ->with('diocese');

        if ($request->filled('q')) {
            $terme = '%'.$request->string('q').'%';
            $this->appliquerRechercheInsensible($query, 'nom', $terme);
        }

        if ($request->filled('ville')) {
            $terme = '%'.$request->string('ville').'%';
            $this->appliquerRechercheInsensible($query, 'ville', $terme);
        }

        if ($request->filled('pays')) {
            $terme = '%'.$request->string('pays').'%';
            $this->appliquerRechercheInsensible($query, 'pays', $terme);
        }

        $paroisses = $query->orderBy('nom')->get();

        $fidele = auth('fidele')->user();
        if ($fidele !== null) {
            $favoris = FavoriParoisse::query()
                ->where('fidele_id', $fidele->id)
                ->pluck('paroisse_id')
                ->flip();

            $paroisses->each(function (Paroisse $paroisse) use ($favoris) {
                $paroisse->est_favori = $favoris->has($paroisse->id);
            });
        }

        return response()->json([
            'paroisses' => ParoisseResource::collection($paroisses),
        ]);
    }

    public function show(Request $request, Paroisse $paroisse): JsonResponse
    {
        $this->ensureParoissePublique($paroisse);
        $paroisse->load(['diocese', 'medias' => fn ($q) => $q->orderBy('ordre')]);
        $paroisse->enrichProfilPublic();

        $fidele = auth('fidele')->user();
        if ($fidele !== null) {
            $paroisse->est_favori = FavoriParoisse::query()
                ->where('fidele_id', $fidele->id)
                ->where('paroisse_id', $paroisse->id)
                ->exists();
        }

        return response()->json([
            'paroisse' => new ParoisseResource($paroisse),
        ]);
    }

    public function messes(Paroisse $paroisse): JsonResponse
    {
        $this->ensureParoissePublique($paroisse);

        $messes = $paroisse->messes()
            ->where('visible', true)
            ->where('reservable', true)
            ->where('statut', '!=', 'annulee')
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('heure')
            ->get();

        return response()->json([
            'messes' => MesseResource::collection($messes),
        ]);
    }

    public function typeOffrandes(Paroisse $paroisse): JsonResponse
    {
        $this->ensureParoissePublique($paroisse);

        $types = $paroisse->typeOffrandes()
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return response()->json([
            'type_offrandes' => TypeOffrandeResource::collection($types),
        ]);
    }

    public function moyenPaiements(Paroisse $paroisse): JsonResponse
    {
        $this->ensureParoissePublique($paroisse);

        $moyens = $paroisse->moyenPaiements()
            ->where('actif', true)
            ->orderBy('type')
            ->get();

        return response()->json([
            'moyen_paiements' => MoyenPaiementResource::collection($moyens),
        ]);
    }

    public function publications(Paroisse $paroisse): JsonResponse
    {
        $this->ensureParoissePublique($paroisse);

        $publications = $paroisse->publications()
            ->where('visible', true)
            ->where(function ($query) {
                $query->whereNull('date_expiration')
                    ->orWhere('date_expiration', '>=', now());
            })
            ->latest('date_publication')
            ->get();

        return response()->json([
            'publications' => PublicationResource::collection($publications),
        ]);
    }

    public function campagnes(Paroisse $paroisse): JsonResponse
    {
        $this->ensureParoissePublique($paroisse);

        $campagnes = $paroisse->campagneCollectes()
            ->where(function ($query) {
                $query->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>=', now()->toDateString());
            })
            ->latest()
            ->get();

        return response()->json([
            'campagnes' => CampagneCollecteResource::collection($campagnes),
        ]);
    }

    private function ensureParoissePublique(Paroisse $paroisse): void
    {
        abort_unless(
            $paroisse->statut === 'validee' && $paroisse->actif,
            404
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Paroisse>  $query
     */
    private function appliquerRechercheInsensible($query, string $colonne, string $terme): void
    {
        if ($query->getConnection()->getDriverName() === 'pgsql') {
            $query->where($colonne, 'ilike', $terme);

            return;
        }

        $query->whereRaw('LOWER('.$colonne.') LIKE ?', [mb_strtolower($terme)]);
    }
}
