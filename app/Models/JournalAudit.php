<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalAudit extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'acteur_type',
        'acteur_id',
        'action',
        'details',
        'ip_address',
    ];

    public function acteur(): MorphTo
    {
        return $this->morphTo();
    }
}
