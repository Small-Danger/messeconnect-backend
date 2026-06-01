<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'nom' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'telephone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'adresse' => ['sometimes', 'nullable', 'string', 'max:255'],
            'ville' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pays' => ['sometimes', 'nullable', 'string', 'max:255'],
            'site_web' => ['sometimes', 'nullable', 'string', 'max:255'],
            'horaires' => ['sometimes', 'array'],
            'horaires.*' => ['string', 'max:255'],
            'logo' => ['sometimes', 'nullable', 'string', 'max:255'],
            'banniere' => ['sometimes', 'nullable', 'string', 'max:255'],
            'couleur_principale' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
