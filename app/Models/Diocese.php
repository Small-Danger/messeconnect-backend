<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diocese extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'nom',
        'ville',
        'pays',
        'description',
        'logo',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    public function paroisses(): HasMany
    {
        return $this->hasMany(Paroisse::class);
    }
}
