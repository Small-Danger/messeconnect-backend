<?php

namespace App\Http\Requests\Api\Fidele;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'moyen_paiement_id' => ['required', 'uuid', 'exists:moyen_paiements,id'],
            'telephone_payeur' => ['nullable', 'string', 'max:255'],
        ];
    }
}
