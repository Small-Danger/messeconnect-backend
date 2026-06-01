<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;

class StoreModeleMesseRequest extends FormRequest
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
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'jour_semaine' => ['required', 'integer', 'min:0', 'max:6'],
            'heure' => ['required', 'date_format:H:i'],
            'reservable' => ['sometimes', 'boolean'],
            'capacite_max' => ['nullable', 'integer', 'min:1'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'actif' => ['sometimes', 'boolean'],
        ];
    }
}
