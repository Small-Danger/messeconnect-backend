<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserParoisse extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UsesUuid;

    protected $fillable = [
        'paroisse_id',
        'nom',
        'email',
        'password',
        'role',
        'actif',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'actif' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }

    public function ticketsSupport(): HasMany
    {
        return $this->hasMany(TicketSupport::class);
    }

    public function journalAudits(): MorphMany
    {
        return $this->morphMany(JournalAudit::class, 'acteur');
    }
}
