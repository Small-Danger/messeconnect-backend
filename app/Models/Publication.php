<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'paroisse_id',
        'titre',
        'contenu',
        'image',
        'images',
        'type',
        'date_publication',
        'date_expiration',
        'visible',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'date_publication' => 'datetime',
            'date_expiration' => 'datetime',
            'visible' => 'boolean',
        ];
    }

    public function paroisse(): BelongsTo
    {
        return $this->belongsTo(Paroisse::class);
    }
}
