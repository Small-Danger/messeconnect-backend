<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\StoreMesseRequest;
use App\Http\Requests\Api\Paroisse\UpdateMesseRequest;
use Carbon\Carbon;
use App\Http\Resources\Api\Paroisse\DemandeMesseResource;
use App\Http\Resources\Api\Paroisse\MesseResource;
use App\Models\Messe;
use App\Services\Paroisse\DemandeMesseStatutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MesseController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $query = $this->paroisse($request)
            ->messes()
            ->with('modeleMesse')
            ->orderBy('date')
            ->orderBy('heure');

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->string('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->string('to'));
        }

        $messes = $query->get();

        return response()->json([
            'messes' => MesseResource::collection($messes),
        ]);
    }

    public function store(StoreMesseRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $pretre = $validated['pretre'] ?? null;
        $lieu = $validated['lieu'] ?? null;
        unset($validated['pretre'], $validated['lieu']);

        $meta = [];
        if ($pretre) {
            $meta[] = "Prêtre: {$pretre}";
        }
        if ($lieu) {
            $meta[] = "Lieu: {$lieu}";
        }
        if ($meta !== []) {
            $existing = trim((string) ($validated['description'] ?? ''));
            $validated['description'] = trim($existing."\n".implode("\n", $meta));
        }

        $validated['heure'] = Carbon::parse($validated['heure'])->format('H:i:s');
        $validated['statut'] = 'planifiee';
        $validated['reservable'] = $validated['reservable'] ?? true;
        $validated['visible'] = $validated['visible'] ?? true;
        $validated['places_reservees'] = 0;

        $messe = $this->paroisse($request)->messes()->create($validated);
        $messe->load('modeleMesse');

        return response()->json([
            'message' => 'Messe programmée.',
            'messe' => new MesseResource($messe),
        ], 201);
    }

    public function show(Request $request, Messe $messe): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $messe);
        $messe->load([
            'modeleMesse',
            'demandes.typeOffrande',
            'demandes.fidele',
            'demandes.paiements.moyenPaiement',
        ])->loadCount('demandes');

        return response()->json([
            'messe' => new MesseResource($messe),
        ]);
    }

    public function update(UpdateMesseRequest $request, Messe $messe): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $messe);

        $validated = $request->validated();
        if (isset($validated['heure'])) {
            $validated['heure'] = Carbon::parse($validated['heure'])->format('H:i:s');
        }

        $messe->update($validated);
        $messe->load('modeleMesse');

        return response()->json([
            'message' => 'Messe mise à jour.',
            'messe' => new MesseResource($messe),
        ]);
    }

    public function destroy(Request $request, Messe $messe): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $messe);

        if ($messe->demandes()->exists()) {
            $count = $messe->demandes()->count();

            return response()->json([
                'message' => "Impossible de supprimer ce créneau : {$count} intention(s) y sont enregistrée(s).",
            ], 422);
        }

        $messe->delete();

        return response()->json([
            'message' => 'Créneau supprimé.',
        ]);
    }

    public function annuler(
        Request $request,
        Messe $messe,
        DemandeMesseStatutService $statutService,
    ): JsonResponse {
        $this->ensureBelongsToParoisse($request, $messe);

        if ($messe->statut === 'celebree') {
            return response()->json([
                'message' => 'Impossible d\'annuler une messe déjà célébrée.',
            ], 422);
        }

        if ($messe->statut === 'annulee') {
            return response()->json([
                'message' => 'Ce créneau est déjà annulé.',
            ], 422);
        }

        DB::transaction(function () use ($messe, $statutService) {
            $messe->update([
                'statut' => 'annulee',
                'reservable' => false,
                'visible' => false,
            ]);

            $messe->demandes()
                ->whereNotIn('statut', ['annulee', 'celebree'])
                ->each(function ($demande) use ($statutService) {
                    $statutService->mettreAJour($demande, 'annulee', 'Messe annulée par la paroisse');
                });
        });

        $messe->load([
            'modeleMesse',
            'demandes.typeOffrande',
            'demandes.fidele',
            'demandes.paiements.moyenPaiement',
        ])->loadCount('demandes');

        return response()->json([
            'message' => 'Messe annulée. Les intentions concernées ont été marquées comme annulées.',
            'messe' => new MesseResource($messe),
        ]);
    }

    public function celebrer(
        Request $request,
        Messe $messe,
        DemandeMesseStatutService $statutService,
    ): JsonResponse {
        $this->ensureBelongsToParoisse($request, $messe);

        DB::transaction(function () use ($messe, $statutService) {
            $messe->update(['statut' => 'celebree']);

            $messe->demandes()
                ->whereIn('statut', ['confirmee', 'payee'])
                ->each(function ($demande) use ($statutService) {
                    $statutService->mettreAJour($demande, 'celebree', 'Messe célébrée');
                });
        });

        $messe->load([
            'modeleMesse',
            'demandes.typeOffrande',
            'demandes.fidele',
            'demandes.paiements.moyenPaiement',
        ])->loadCount('demandes');

        return response()->json([
            'message' => 'Messe marquée comme célébrée.',
            'messe' => new MesseResource($messe),
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, Messe $messe): void
    {
        abort_unless(
            $messe->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }
}
