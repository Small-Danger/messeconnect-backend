<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Fidele extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UsesUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'google_id',
        'ville',
        'pays',
        'actif',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(DemandeMesse::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(FavoriParoisse::class);
    }

    public function paroisses(): BelongsToMany
    {
        return $this->belongsToMany(Paroisse::class, 'favori_paroisses')
            ->withPivot('id');
    }

    public function journalAudits(): MorphMany
    {
        return $this->morphMany(JournalAudit::class, 'acteur');
    }
}
