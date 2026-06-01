<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\GenererMessesRequest;
use App\Http\Requests\Api\Paroisse\StoreModeleMesseRequest;
use App\Http\Requests\Api\Paroisse\UpdateModeleMesseRequest;
use App\Http\Resources\Api\Paroisse\ModeleMesseResource;
use App\Models\ModeleMesse;
use App\Services\Paroisse\MesseGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModeleMesseController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $modeles = $this->paroisse($request)
            ->modeleMesses()
            ->withCount('messes')
            ->orderBy('jour_semaine')
            ->orderBy('heure')
            ->get();

        return response()->json([
            'modele_messes' => ModeleMesseResource::collection($modeles),
        ]);
    }

    public function store(StoreModeleMesseRequest $request): JsonResponse
    {
        $modele = $this->paroisse($request)
            ->modeleMesses()
            ->create($request->validated());

        return response()->json([
            'message' => 'Modèle de messe créé.',
            'modele_messe' => new ModeleMesseResource($modele),
        ], 201);
    }

    public function show(Request $request, ModeleMesse $modeleMesse): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $modeleMesse);
        $modeleMesse->loadCount('messes');

        return response()->json([
            'modele_messe' => new ModeleMesseResource($modeleMesse),
        ]);
    }

    public function update(UpdateModeleMesseRequest $request, ModeleMesse $modeleMesse): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $modeleMesse);
        $modeleMesse->update($request->validated());

        return response()->json([
            'message' => 'Modèle de messe mis à jour.',
            'modele_messe' => new ModeleMesseResource($modeleMesse),
        ]);
    }

    public function destroy(Request $request, ModeleMesse $modeleMesse): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $modeleMesse);
        $modeleMesse->delete();

        return response()->json([
            'message' => 'Modèle de messe supprimé.',
        ]);
    }

    public function generer(
        GenererMessesRequest $request,
        ModeleMesse $modeleMesse,
        MesseGeneratorService $generator,
    ): JsonResponse {
        $this->ensureBelongsToParoisse($request, $modeleMesse);

        $creees = $generator->generer(
            $modeleMesse,
            $request->integer('semaines', 8)
        );

        return response()->json([
            'message' => 'Génération des messes terminée.',
            'messes_creees' => $creees,
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, ModeleMesse $modeleMesse): void
    {
        abort_unless(
            $modeleMesse->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }
}
