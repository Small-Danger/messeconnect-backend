<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Admin\PaiementResource;
use App\Models\Paiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /** @var array<string, string> */
    private const METHODE_TYPES = [
        'orange money' => 'orange_money',
        'moov money' => 'moov_money',
        'wave' => 'wave',
        'espèces' => 'especes',
        'especes' => 'especes',
        'orange_money' => 'orange_money',
        'moov_money' => 'moov_money',
    ];

    public function index(Request $request): JsonResponse
    {
        $query = Paiement::query()
            ->with([
                'moyenPaiement',
                'demandeMesse.fidele',
                'demandeMesse.paroisse',
                'campagneCollecte.paroisse',
            ])
            ->latest();

        if ($request->filled('statut') && $request->string('statut') !== 'tous') {
            $query->where('statut', $request->string('statut'));
        }

        if ($request->filled('q')) {
            $terme = '%'.$request->string('q').'%';
            $query->where(function ($sub) use ($terme) {
                $sub->where('reference_interne', 'like', $terme)
                    ->orWhere('reference_fournisseur', 'like', $terme)
                    ->orWhereHas('demandeMesse', function ($demande) use ($terme) {
                        $demande->where('reference', 'like', $terme)
                            ->orWhereHas('fidele', function ($q) use ($terme) {
                                $q->where('nom', 'like', $terme)
                                    ->orWhere('prenom', 'like', $terme)
                                    ->orWhere('email', 'like', $terme);
                            })
                            ->orWhereHas('paroisse', fn ($q) => $q->where('nom', 'like', $terme));
                    })
                    ->orWhereHas('campagneCollecte', function ($campagne) use ($terme) {
                        $campagne->where('nom', 'like', $terme)
                            ->orWhereHas('paroisse', fn ($q) => $q->where('nom', 'like', $terme));
                    });
            });
        }

        if ($request->filled('methode') && $request->string('methode') !== 'tous') {
            $type = self::METHODE_TYPES[strtolower($request->string('methode')->toString())] ?? $request->string('methode');
            $query->whereHas('moyenPaiement', fn ($q) => $q->where('type', $type));
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->string('date_debut'));
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->string('date_fin'));
        }

        if ($request->filled('montant_min')) {
            $query->where('montant', '>=', $request->float('montant_min'));
        }

        if ($request->filled('montant_max')) {
            $query->where('montant', '<=', $request->float('montant_max'));
        }

        $paiements = $query->get();

        return response()->json([
            'paiements' => PaiementResource::collection($paiements),
            'synthese' => [
                'total' => $paiements->count(),
                'montant_total' => (float) $paiements->sum('montant'),
                'montant_reussi' => (float) $paiements->where('statut', 'reussi')->sum('montant'),
                'reussis' => $paiements->where('statut', 'reussi')->count(),
                'en_attente' => $paiements->where('statut', 'en_attente')->count(),
                'echoues' => $paiements->where('statut', 'echoue')->count(),
            ],
        ]);
    }

    public function show(Paiement $paiement): JsonResponse
    {
        $paiement->load([
            'moyenPaiement',
            'demandeMesse.fidele',
            'demandeMesse.paroisse',
            'campagneCollecte.paroisse',
        ]);

        return response()->json([
            'paiement' => new PaiementResource($paiement),
        ]);
    }
}
