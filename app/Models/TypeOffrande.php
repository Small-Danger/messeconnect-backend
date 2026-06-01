<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeOffrande extends Model
{
    use UsesUuid;

    protected $fillable = [
        'paroisse_id',
        'nom',
        'description',
        'montant_propose',
        'image',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'montant_propose' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(DemandeMesse::class);
    }
}
