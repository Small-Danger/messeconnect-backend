<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use UsesUuid;

    protected $fillable = [
        'fidele_id',
        'demande_messe_id',
        'type',
        'titre',
        'contenu',
        'statut',
        'date_envoi',
    ];

    protected function casts(): array
    {
        return [
            'date_envoi' => 'datetime',
        ];
    }

    public function fidele(): BelongsTo
    {
        return $this->belongsTo(Fidele::class);
    }

    public function demandeMesse(): BelongsTo
    {
        return $this->belongsTo(DemandeMesse::class);
    }
}
