<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePublicationRequest extends FormRequest
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
            'contenu' => ['sometimes', 'nullable', 'string'],
            'image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'images' => ['sometimes', 'nullable', 'array', 'max:10'],
            'images.*' => ['string', 'max:2048'],
            'type' => ['sometimes', Rule::in(['annonce', 'evenement', 'information', 'appel_don'])],
            'date_publication' => ['sometimes', 'nullable', 'date'],
            'date_expiration' => ['sometimes', 'nullable', 'date'],
            'visible' => ['sometimes', 'boolean'],
        ];
    }
}
