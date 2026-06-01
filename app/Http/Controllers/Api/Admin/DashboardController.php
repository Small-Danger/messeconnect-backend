<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeMesse;
use App\Models\Fidele;
use App\Models\JournalAudit;
use App\Models\Paiement;
use App\Models\Paroisse;
use App\Models\TicketSupport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $paroissesQuery = Paroisse::query();
        $now = now();

        $montantCollecte = Paiement::query()
            ->where('statut', 'reussi')
            ->sum('montant');

        $montantMoisCourant = Paiement::query()
            ->where('statut', 'reussi')
            ->whereBetween('date_paiement', [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ])
            ->sum('montant');

        $montantMoisPrecedent = Paiement::query()
            ->where('statut', 'reussi')
            ->whereBetween('date_paiement', [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
            ])
            ->sum('montant');

        $croissanceMensuelle = $montantMoisPrecedent > 0
            ? round((($montantMoisCourant - $montantMoisPrecedent) / $montantMoisPrecedent) * 100, 1)
            : ($montantMoisCourant > 0 ? 100.0 : 0.0);

        $activitesAudit = JournalAudit::query()
            ->with('acteur')
            ->latest()
            ->limit(8)
            ->get();

        $activites = $activitesAudit->map(function (JournalAudit $entry) {
            $acteur = $entry->acteur;
            $utilisateur = $acteur instanceof User ? ($acteur->email ?? $acteur->nom ?? 'Admin') : 'Système';

            return [
                'id' => $entry->id,
                'date' => $entry->created_at?->toIso8601String(),
                'action' => $this->formatActionLabel($entry->action),
                'utilisateur' => $utilisateur,
                'type' => $this->resolveActionType($entry->action),
            ];
        })->values();

        if ($activites->isEmpty()) {
            $activites = $this->buildFallbackActivites();
        }

        return response()->json([
            'statistiques' => [
                'paroisses' => [
                    'total' => (clone $paroissesQuery)->count(),
                    'en_attente' => (clone $paroissesQuery)->where('statut', 'en_attente')->count(),
                    'validees' => (clone $paroissesQuery)->where('statut', 'validee')->count(),
                    'suspendues' => (clone $paroissesQuery)->where('statut', 'suspendue')->count(),
                ],
                'fideles' => [
                    'total' => Fidele::query()->count(),
                    'actifs' => Fidele::query()->where('actif', true)->count(),
                ],
                'demandes' => [
                    'total' => DemandeMesse::query()->count(),
                    'en_attente' => DemandeMesse::query()->where('statut', 'en_attente')->count(),
                    'confirmees' => DemandeMesse::query()->where('statut', 'confirmee')->count(),
                ],
                'montant_collecte' => $montantCollecte,
                'croissance_mensuelle' => $croissanceMensuelle,
                'tickets_ouverts' => TicketSupport::query()->whereIn('statut', ['ouvert', 'en_cours'])->count(),
            ],
            'activites' => $activites,
            'graphiques' => [
                'revenus_mensuels' => $this->revenusMensuels(),
                'inscriptions_mensuelles' => $this->inscriptionsMensuelles(),
            ],
        ]);
    }

    /**
     * @return list<array{label: string, value: float}>
     */
    private function revenusMensuels(): array
    {
        $debut = now()->subMonths(5)->startOfMonth();
        $dateColumn = $this->monthExpression('COALESCE(date_paiement, created_at)');

        $rows = Paiement::query()
            ->select(
                DB::raw("{$dateColumn} as periode"),
                DB::raw('SUM(montant) as total'),
            )
            ->where('statut', 'reussi')
            ->whereRaw('COALESCE(date_paiement, created_at) >= ?', [$debut])
            ->groupBy('periode')
            ->orderBy('periode')
            ->pluck('total', 'periode');

        return $this->buildMonthlySeries($debut, 6, $rows);
    }

    /**
     * @return list<array{label: string, value: int}>
     */
    private function inscriptionsMensuelles(): array
    {
        $debut = now()->subMonths(5)->startOfMonth();
        $dateColumn = $this->monthExpression('created_at');

        $rows = Fidele::query()
            ->select(
                DB::raw("{$dateColumn} as periode"),
                DB::raw('COUNT(*) as total'),
            )
            ->where('created_at', '>=', $debut)
            ->groupBy('periode')
            ->orderBy('periode')
            ->pluck('total', 'periode');

        return $this->buildMonthlySeries($debut, 6, $rows);
    }

    private function monthExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', {$column})",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    /**
     * @param  \Illuminate\Support\Collection<string, mixed>  $rows
     * @return list<array{label: string, value: float|int}>
     */
    private function buildMonthlySeries(Carbon $debut, int $mois, $rows): array
    {
        $series = [];
        $cursor = $debut->copy();

        for ($i = 0; $i < $mois; $i++) {
            $key = $cursor->format('Y-m');
            $series[] = [
                'label' => $cursor->translatedFormat('M'),
                'value' => (float) ($rows[$key] ?? 0),
            ];
            $cursor->addMonth();
        }

        return $series;
    }

    private function formatActionLabel(string $action): string
    {
        return match (true) {
            str_starts_with($action, 'paroisse.statut') => 'Statut paroisse modifié',
            str_starts_with($action, 'paroisse.actif') => 'État paroisse modifié',
            str_starts_with($action, 'fidele.actif') => 'Compte fidèle modifié',
            str_starts_with($action, 'ticket.statut') => 'Ticket support mis à jour',
            str_starts_with($action, 'publication.visible') => 'Publication modérée',
            str_starts_with($action, 'diocese.') => 'Diocèse mis à jour',
            str_starts_with($action, 'abonnement.') => 'Abonnement mis à jour',
            default => str_replace(['.', '_'], ' ', $action),
        };
    }

    private function resolveActionType(string $action): string
    {
        return match (true) {
            str_starts_with($action, 'paroisse.') => 'paroisse',
            str_starts_with($action, 'fidele.') => 'utilisateur',
            str_starts_with($action, 'ticket.') => 'support',
            str_starts_with($action, 'publication.') => 'moderation',
            str_starts_with($action, 'abonnement.') => 'abonnement',
            str_starts_with($action, 'diocese.') => 'diocese',
            default => 'admin',
        };
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function buildFallbackActivites()
    {
        $items = collect();

        Paroisse::query()->latest()->limit(3)->get()->each(function (Paroisse $paroisse) use ($items) {
            $items->push([
                'id' => 'paroisse-'.$paroisse->id,
                'date' => $paroisse->created_at?->toIso8601String(),
                'action' => $paroisse->statut === 'en_attente'
                    ? 'Nouvelle inscription paroisse'
                    : 'Paroisse enregistrée',
                'utilisateur' => $paroisse->nom,
                'type' => 'paroisse',
            ]);
        });

        Paiement::query()
            ->with('demandeMesse.fidele')
            ->where('statut', 'reussi')
            ->latest('date_paiement')
            ->limit(3)
            ->get()
            ->each(function (Paiement $paiement) use ($items) {
                $nom = $paiement->demandeMesse?->fidele?->nom ?? 'Payeur';
                $items->push([
                    'id' => 'paiement-'.$paiement->id,
                    'date' => ($paiement->date_paiement ?? $paiement->created_at)?->toIso8601String(),
                    'action' => 'Paiement confirmé',
                    'utilisateur' => $nom,
                    'type' => 'transaction',
                ]);
            });

        return $items->sortByDesc('date')->take(8)->values();
    }
}
