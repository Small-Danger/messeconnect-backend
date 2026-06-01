<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampagneCollecte extends Model
{
    use UsesUuid;

    protected $fillable = [
        'paroisse_id',
        'nom',
        'description',
        'objectif_total',
        'montant_collecte',
        'image',
        'date_fin',
    ];

    protected function casts(): array
    {
        return [
            'objectif_total' => 'decimal:2',
            'montant_collecte' => 'decimal:2',
            'date_fin' => 'date',
        ];
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }
}
