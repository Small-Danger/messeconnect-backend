<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    use UsesUuid;

    protected $fillable = [
        'demande_messe_id',
        'campagne_collecte_id',
        'moyen_paiement_id',
        'montant',
        'frais_techniques',
        'devise',
        'statut',
        'statut_fournisseur',
        'reference_interne',
        'reference_fournisseur',
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

    public function demandeMesse(): BelongsTo
    {
        return $this->belongsTo(DemandeMesse::class);
    }

    public function campagneCollecte(): BelongsTo
    {
        return $this->belongsTo(CampagneCollecte::class);
    }

    public function moyenPaiement(): BelongsTo
    {
        return $this->belongsTo(MoyenPaiement::class);
    }
}
