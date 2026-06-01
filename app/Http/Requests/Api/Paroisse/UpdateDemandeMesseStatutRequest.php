<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDemandeMesseStatutRequest extends FormRequest
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
            'statut' => ['required', Rule::in(['en_attente', 'payee', 'confirmee', 'celebree', 'annulee'])],
            'commentaire' => ['nullable', 'string'],
        ];
    }
}
