<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\LogsAdminAction;
use App\Http\Controllers\Api\Concerns\AppliesInsensitiveSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateFideleActifRequest;
use App\Http\Resources\Api\Admin\FideleDetailResource;
use App\Http\Resources\Api\Admin\FideleResource;
use App\Models\Fidele;
use App\Models\Paiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FideleController extends Controller
{
    use AppliesInsensitiveSearch, LogsAdminAction;

    public function index(Request $request): JsonResponse
    {
        $query = Fidele::query()->withCount('demandes');

        if ($request->filled('q')) {
            $terme = $request->string('q')->toString();
            $query->where(function ($sub) use ($terme) {
                $this->whereInsensitive($sub, 'nom', $terme);
                $sub->orWhere(function ($or) use ($terme) {
                    $this->whereInsensitive($or, 'prenom', $terme);
                });
                $sub->orWhere(function ($or) use ($terme) {
                    $this->whereInsensitive($or, 'email', $terme);
                });
                $sub->orWhere(function ($or) use ($terme) {
                    $this->whereInsensitive($or, 'telephone', $terme);
                });
            });
        }

        if ($request->has('actif')) {
            $query->where('actif', $request->boolean('actif'));
        }

        $fideles = $query->orderBy('nom')->orderBy('prenom')->get();

        return response()->json([
            'fideles' => FideleResource::collection($fideles),
        ]);
    }

    public function show(Fidele $fidele): JsonResponse
    {
        $fidele->load([
            'demandes' => fn ($query) => $query->with('paroisse')->latest(),
            'favoris.paroisse',
            'journalAudits' => fn ($query) => $query->latest()->limit(20),
        ]);
        $fidele->loadCount('demandes');

        $paiements = Paiement::query()
            ->whereHas('demandeMesse', fn ($query) => $query->where('fidele_id', $fidele->id))
            ->with('moyenPaiement')
            ->latest()
            ->get();

        $fidele->setRelation('paiements', $paiements);

        return response()->json([
            'fidele' => new FideleDetailResource($fidele),
        ]);
    }

    public function updateActif(UpdateFideleActifRequest $request, Fidele $fidele): JsonResponse
    {
        $fidele->update(['actif' => $request->boolean('actif')]);

        $this->logAdminAction($request, 'fidele.actif', [
            'fidele_id' => $fidele->id,
            'actif' => $fidele->actif,
        ]);

        return response()->json([
            'message' => 'État du fidèle mis à jour.',
            'fidele' => new FideleResource($fidele->fresh()->loadCount('demandes')),
        ]);
    }
}
