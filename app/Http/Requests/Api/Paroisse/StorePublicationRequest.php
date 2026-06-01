<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicationRequest extends FormRequest
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
            'contenu' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:2048'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['string', 'max:2048'],
            'type' => ['required', Rule::in(['annonce', 'evenement', 'information', 'appel_don'])],
            'date_publication' => ['nullable', 'date'],
            'date_expiration' => ['nullable', 'date', 'after_or_equal:date_publication'],
            'visible' => ['sometimes', 'boolean'],
        ];
    }
}
