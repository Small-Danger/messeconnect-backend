<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModeleMesseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titre' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'jour_semaine' => ['sometimes', 'integer', 'min:0', 'max:6'],
            'heure' => ['sometimes', 'date_format:H:i'],
            'reservable' => ['sometimes', 'boolean'],
            'capacite_max' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'date_debut' => ['sometimes', 'nullable', 'date'],
            'date_fin' => ['sometimes', 'nullable', 'date', 'after_or_equal:date_debut'],
            'actif' => ['sometimes', 'boolean'],
        ];
    }
}
