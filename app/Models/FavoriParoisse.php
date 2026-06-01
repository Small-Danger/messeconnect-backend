<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriParoisse extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'fidele_id',
        'paroisse_id',
    ];

    public function fidele(): BelongsTo
    {
        return $this->belongsTo(Fidele::class);
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }
}
