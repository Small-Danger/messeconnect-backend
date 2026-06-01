<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemandeMesse extends Model
{
    use UsesUuid;

    protected $fillable = [
        'fidele_id',
        'paroisse_id',
        'messe_id',
        'type_offrande_id',
        'reference',
        'est_anonyme',
        'nom_demandeur',
        'email_demandeur',
        'telephone_demandeur',
        'intention',
        'nom_personne_concernee',
        'montant',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'est_anonyme' => 'boolean',
            'montant' => 'decimal:2',
        ];
    }

    public function fidele(): BelongsTo
    {
        return $this->belongsTo(Fidele::class);
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }

    public function messe(): BelongsTo
    {
        return $this->belongsTo(Messe::class);
    }

    public function typeOffrande(): BelongsTo
    {
        return $this->belongsTo(TypeOffrande::class);
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueDemande::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
