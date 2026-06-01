<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMesseRequest extends FormRequest
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
            'date' => ['sometimes', 'date'],
            'heure' => ['sometimes', 'date_format:H:i'],
            'reservable' => ['sometimes', 'boolean'],
            'capacite_max' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'visible' => ['sometimes', 'boolean'],
            'statut' => ['sometimes', Rule::in(['planifiee', 'en_cours', 'celebree', 'annulee'])],
        ];
    }
}
