<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\StoreTypeOffrandeRequest;
use App\Http\Requests\Api\Paroisse\UpdateTypeOffrandeRequest;
use App\Http\Resources\Api\Paroisse\TypeOffrandeResource;
use App\Models\TypeOffrande;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TypeOffrandeController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $types = $this->paroisse($request)
            ->typeOffrandes()
            ->orderBy('nom')
            ->get();

        return response()->json([
            'type_offrandes' => TypeOffrandeResource::collection($types),
        ]);
    }

    public function store(StoreTypeOffrandeRequest $request): JsonResponse
    {
        $type = $this->paroisse($request)
            ->typeOffrandes()
            ->create($request->validated());

        return response()->json([
            'message' => 'Type d\'offrande créé.',
            'type_offrande' => new TypeOffrandeResource($type),
        ], 201);
    }

    public function show(Request $request, TypeOffrande $typeOffrande): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $typeOffrande);

        return response()->json([
            'type_offrande' => new TypeOffrandeResource($typeOffrande),
        ]);
    }

    public function update(UpdateTypeOffrandeRequest $request, TypeOffrande $typeOffrande): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $typeOffrande);
        $typeOffrande->update($request->validated());

        return response()->json([
            'message' => 'Type d\'offrande mis à jour.',
            'type_offrande' => new TypeOffrandeResource($typeOffrande),
        ]);
    }

    public function destroy(Request $request, TypeOffrande $typeOffrande): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $typeOffrande);
        $typeOffrande->delete();

        return response()->json([
            'message' => 'Type d\'offrande supprimé.',
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, TypeOffrande $typeOffrande): void
    {
        abort_unless(
            $typeOffrande->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }
}
