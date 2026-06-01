<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Messe extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'paroisse_id',
        'modele_messe_id',
        'titre',
        'description',
        'date',
        'heure',
        'reservable',
        'capacite_max',
        'places_reservees',
        'visible',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'reservable' => 'boolean',
            'capacite_max' => 'integer',
            'places_reservees' => 'integer',
            'visible' => 'boolean',
        ];
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }

    public function modeleMesse(): BelongsTo
    {
        return $this->belongsTo(ModeleMesse::class);
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(DemandeMesse::class);
    }
}
