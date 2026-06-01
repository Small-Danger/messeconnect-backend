<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paroisse extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'diocese_id',
        'nom',
        'description',
        'telephone',
        'email',
        'adresse',
        'ville',
        'pays',
        'site_web',
        'logo',
        'banniere',
        'couleur_principale',
        'statut',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    public function diocese(): BelongsTo
    {
        return $this->belongsTo(Diocese::class);
    }

    public function userParoisses(): HasMany
    {
        return $this->hasMany(UserParoisse::class);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(ConfigurationParoisse::class);
    }

    /**
     * @return list<string>
     */
    public function horairesSecretariat(): array
    {
        $valeur = $this->configurations()
            ->where('cle', 'horaires_secretariat')
            ->value('valeur');

        if ($valeur === null || $valeur === '') {
            return [];
        }

        $decoded = json_decode($valeur, true);

        return is_array($decoded) ? array_values(array_filter($decoded, fn ($line) => is_string($line) && trim($line) !== '')) : [];
    }

    public function enrichProfilPublic(): self
    {
        $this->setAttribute('horaires_secretariat', $this->horairesSecretariat());

        return $this;
    }

    public function moyenPaiements(): HasMany
    {
        return $this->hasMany(MoyenPaiement::class);
    }

    public function typeOffrandes(): HasMany
    {
        return $this->hasMany(TypeOffrande::class);
    }

    public function medias(): HasMany
    {
        return $this->hasMany(MediaParoisse::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    public function campagneCollectes(): HasMany
    {
        return $this->hasMany(CampagneCollecte::class);
    }

    public function abonnements(): HasMany
    {
        return $this->hasMany(Abonnement::class);
    }

    public function modeleMesses(): HasMany
    {
        return $this->hasMany(ModeleMesse::class);
    }

    public function messes(): HasMany
    {
        return $this->hasMany(Messe::class);
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(DemandeMesse::class);
    }

    public function ticketsSupport(): HasMany
    {
        return $this->hasMany(TicketSupport::class);
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(FavoriParoisse::class);
    }

    public function fideles(): BelongsToMany
    {
        return $this->belongsToMany(Fidele::class, 'favori_paroisses')
            ->withPivot('id');
    }
}
