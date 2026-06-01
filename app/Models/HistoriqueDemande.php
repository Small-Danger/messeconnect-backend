<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueDemande extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'demande_messe_id',
        'statut_precedent',
        'nouveau_statut',
        'commentaire',
    ];

    public function demandeMesse(): BelongsTo
    {
        return $this->belongsTo(DemandeMesse::class);
    }
}
