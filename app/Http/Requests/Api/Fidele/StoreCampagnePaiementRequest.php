<?php

namespace App\Http\Requests\Api\Fidele;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampagnePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'montant' => ['required', 'numeric', 'min:1'],
            'moyen_paiement_id' => ['required', 'uuid', 'exists:moyen_paiements,id'],
            'telephone_payeur' => ['nullable', 'string', 'max:255'],
        ];
    }
}
