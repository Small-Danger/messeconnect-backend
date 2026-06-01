<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigurationParoisse extends Model
{
    use UsesUuid;

    public const CREATED_AT = null;

    protected $fillable = [
        'paroisse_id',
        'cle',
        'valeur',
    ];

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }
}
