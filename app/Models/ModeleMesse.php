<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeleMesse extends Model
{
    use UsesUuid;

    protected $fillable = [
        'paroisse_id',
        'titre',
        'description',
        'jour_semaine',
        'heure',
        'reservable',
        'capacite_max',
        'date_debut',
        'date_fin',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'jour_semaine' => 'integer',
            'reservable' => 'boolean',
            'capacite_max' => 'integer',
            'date_debut' => 'date',
            'date_fin' => 'date',
            'actif' => 'boolean',
        ];
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }

    public function messes(): HasMany
    {
        return $this->hasMany(Messe::class);
    }
}
