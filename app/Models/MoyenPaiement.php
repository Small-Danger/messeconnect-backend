<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoyenPaiement extends Model
{
    use UsesUuid;

    protected $fillable = [
        'paroisse_id',
        'type',
        'environment',
        'numero',
        'identifiant_marchand',
        'client_id',
        'api_key',
        'secret_key',
        'webhook_secret',
        'callback_url',
        'notify_url',
        'metadata',
        'actif',
    ];

    protected $hidden = [
        'api_key',
        'secret_key',
        'webhook_secret',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'actif' => 'boolean',
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

    public function paiementAbonnements(): HasMany
    {
        return $this->hasMany(PaiementAbonnement::class);
    }
}
