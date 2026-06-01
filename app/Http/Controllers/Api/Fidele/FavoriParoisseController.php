<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Fidele\ParoisseResource;
use App\Models\FavoriParoisse;
use App\Models\Paroisse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriParoisseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\Fidele $fidele */
        $fidele = $request->user();

        $paroisses = Paroisse::query()
            ->whereIn('id', FavoriParoisse::query()
                ->where('fidele_id', $fidele->id)
                ->pluck('paroisse_id'))
            ->where('statut', 'validee')
            ->where('actif', true)
            ->with('diocese')
            ->orderBy('nom')
            ->get()
            ->each(fn (Paroisse $p) => $p->est_favori = true);

        return response()->json([
            'paroisses' => ParoisseResource::collection($paroisses),
        ]);
    }

    public function store(Request $request, Paroisse $paroisse): JsonResponse
    {
        abort_unless($paroisse->statut === 'validee' && $paroisse->actif, 404);

        /** @var \App\Models\Fidele $fidele */
        $fidele = $request->user();

        FavoriParoisse::query()->firstOrCreate([
            'fidele_id' => $fidele->id,
            'paroisse_id' => $paroisse->id,
        ]);

        return response()->json([
            'message' => 'Paroisse ajoutée aux favoris.',
        ], 201);
    }

    public function destroy(Request $request, Paroisse $paroisse): JsonResponse
    {
        /** @var \App\Models\Fidele $fidele */
        $fidele = $request->user();

        FavoriParoisse::query()
            ->where('fidele_id', $fidele->id)
            ->where('paroisse_id', $paroisse->id)
            ->delete();

        return response()->json([
            'message' => 'Paroisse retirée des favoris.',
        ]);
    }
}
