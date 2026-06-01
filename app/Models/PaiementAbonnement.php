<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementAbonnement extends Model
{
    use UsesUuid;

    protected $fillable = [
        'abonnement_id',
        'moyen_paiement_id',
        'montant',
        'frais_techniques',
        'devise',
        'reference_interne',
        'reference_fournisseur',
        'status',
        'statut_fournisseur',
        'telephone_payeur',
        'url_checkout',
        'payload_webhook',
        'date_paiement',
        'date_expiration',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'frais_techniques' => 'decimal:2',
            'payload_webhook' => 'array',
            'date_paiement' => 'datetime',
            'date_expiration' => 'datetime',
        ];
    }

    public function abonnement(): BelongsTo
    {
        return $this->belongsTo(Abonnement::class);
    }

    public function moyenPaiement(): BelongsTo
    {
        return $this->belongsTo(MoyenPaiement::class);
    }
}
