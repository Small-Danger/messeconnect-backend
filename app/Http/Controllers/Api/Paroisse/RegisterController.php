<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\RegisterParoisseRequest;
use App\Http\Resources\Api\Paroisse\ParoisseResource;
use App\Http\Resources\Api\Paroisse\UserParoisseResource;
use App\Models\Diocese;
use App\Models\Paroisse;
use App\Models\UserParoisse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function dioceses(): JsonResponse
    {
        $dioceses = Diocese::query()
            ->where('actif', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'ville']);

        return response()->json([
            'dioceses' => $dioceses,
        ]);
    }

    public function store(RegisterParoisseRequest $request): JsonResponse
    {
        $result = DB::transaction(function () use ($request) {
            $paroisseData = $request->input('paroisse');
            $responsableData = $request->input('responsable');

            $paroisse = Paroisse::query()->create([
                ...$paroisseData,
                'statut' => 'en_attente',
                'actif' => true,
            ]);

            $user = UserParoisse::query()->create([
                'paroisse_id' => $paroisse->id,
                'nom' => $responsableData['nom'],
                'email' => $responsableData['email'],
                'password' => $responsableData['password'],
                'role' => 'admin',
                'actif' => true,
            ]);

            return compact('paroisse', 'user');
        });

        $result['paroisse']->load('diocese');

        return response()->json([
            'message' => 'Inscription enregistrée. Votre paroisse sera validée par MesseConnect.',
            'paroisse' => new ParoisseResource($result['paroisse']),
            'responsable' => new UserParoisseResource($result['user']),
        ], 201);
    }
}
