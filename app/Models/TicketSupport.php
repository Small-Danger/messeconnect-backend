<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSupport extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $table = 'ticket_supports';

    protected $fillable = [
        'paroisse_id',
        'user_paroisse_id',
        'sujet',
        'message',
        'reponse_admin',
        'reponse_admin_at',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'reponse_admin_at' => 'datetime',
        ];
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }

    public function userParoisse(): BelongsTo
    {
        return $this->belongsTo(UserParoisse::class);
    }
}
