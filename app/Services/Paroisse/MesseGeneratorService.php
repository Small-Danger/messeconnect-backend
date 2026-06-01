<?php

namespace App\Services\Paroisse;

use App\Models\Messe;
use App\Models\ModeleMesse;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class MesseGeneratorService
{
    /**
     * Génère les instances de messe à partir d'un modèle récurrent.
     *
     * jour_semaine : 0 = dimanche … 6 = samedi (convention PHP/Carbon).
     */
    public function generer(ModeleMesse $modele, int $semaines = 8): int
    {
        $debut = $modele->date_debut
            ? Carbon::parse($modele->date_debut)->startOfDay()
            : now()->startOfDay();

        $fin = $modele->date_fin
            ? Carbon::parse($modele->date_fin)->startOfDay()
            : $debut->copy()->addWeeks($semaines);

        if ($fin->lt($debut)) {
            return 0;
        }

        $heure = Carbon::parse($modele->heure)->format('H:i:s');
        $creees = 0;

        /** @var Carbon $date */
        foreach (CarbonPeriod::create($debut, $fin) as $date) {
            if ((int) $date->dayOfWeek !== (int) $modele->jour_semaine) {
                continue;
            }

            $existe = Messe::query()
                ->where('paroisse_id', $modele->paroisse_id)
                ->where('modele_messe_id', $modele->id)
                ->whereDate('date', $date->toDateString())
                ->where('heure', $heure)
                ->exists();

            if ($existe) {
                continue;
            }

            Messe::query()->create([
                'paroisse_id' => $modele->paroisse_id,
                'modele_messe_id' => $modele->id,
                'titre' => $modele->titre,
                'description' => $modele->description,
                'date' => $date->toDateString(),
                'heure' => $heure,
                'reservable' => $modele->reservable,
                'capacite_max' => $modele->capacite_max,
                'places_reservees' => 0,
                'visible' => true,
                'statut' => 'planifiee',
            ]);

            $creees++;
        }

        return $creees;
    }
}
