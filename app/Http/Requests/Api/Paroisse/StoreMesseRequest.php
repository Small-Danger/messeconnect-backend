<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;

class StoreMesseRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'heure' => ['required', 'date_format:H:i'],
            'pretre' => ['nullable', 'string', 'max:255'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'reservable' => ['sometimes', 'boolean'],
            'capacite_max' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'visible' => ['sometimes', 'boolean'],
        ];
    }
}
