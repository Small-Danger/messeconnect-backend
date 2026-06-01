<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\LogsAdminAction;
use App\Http\Controllers\Api\Concerns\AppliesInsensitiveSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreDioceseRequest;
use App\Http\Requests\Api\Admin\UpdateDioceseRequest;
use App\Http\Resources\Api\Admin\DioceseResource;
use App\Models\Diocese;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DioceseController extends Controller
{
    use AppliesInsensitiveSearch, LogsAdminAction;

    public function index(Request $request): JsonResponse
    {
        $query = Diocese::query()->withCount('paroisses');

        if ($request->filled('q')) {
            $this->whereInsensitive($query, 'nom', $request->string('q')->toString());
        }

        if ($request->has('actif')) {
            $query->where('actif', $request->boolean('actif'));
        }

        $dioceses = $query->orderBy('nom')->get();

        return response()->json([
            'dioceses' => DioceseResource::collection($dioceses),
        ]);
    }

    public function store(StoreDioceseRequest $request): JsonResponse
    {
        $diocese = Diocese::query()->create([
            ...$request->validated(),
            'actif' => $request->boolean('actif', true),
        ]);

        $this->logAdminAction($request, 'diocese.created', ['diocese_id' => $diocese->id]);

        return response()->json([
            'message' => 'Diocèse créé.',
            'diocese' => new DioceseResource($diocese),
        ], 201);
    }

    public function show(Diocese $diocese): JsonResponse
    {
        $diocese->loadCount('paroisses');

        return response()->json([
            'diocese' => new DioceseResource($diocese),
        ]);
    }

    public function update(UpdateDioceseRequest $request, Diocese $diocese): JsonResponse
    {
        $diocese->update($request->validated());

        $this->logAdminAction($request, 'diocese.updated', ['diocese_id' => $diocese->id]);

        return response()->json([
            'message' => 'Diocèse mis à jour.',
            'diocese' => new DioceseResource($diocese->fresh()->loadCount('paroisses')),
        ]);
    }

    public function destroy(Request $request, Diocese $diocese): JsonResponse
    {
        abort_if($diocese->paroisses()->exists(), 422, 'Ce diocèse est lié à des paroisses.');

        $dioceseId = $diocese->id;
        $diocese->delete();

        $this->logAdminAction($request, 'diocese.deleted', ['diocese_id' => $dioceseId]);

        return response()->json([
            'message' => 'Diocèse supprimé.',
        ]);
    }
}
