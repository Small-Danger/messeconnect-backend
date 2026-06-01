<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIntentionGuichetRequest extends FormRequest
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
            'messe_id' => ['required', 'uuid', 'exists:messes,id'],
            'type_offrande_id' => ['required', 'uuid', 'exists:type_offrandes,id'],
            'est_anonyme' => ['sometimes', 'boolean'],
            'nom_demandeur' => ['required_if:est_anonyme,false', 'nullable', 'string', 'max:255'],
            'telephone_demandeur' => ['required', 'string', 'max:255'],
            'intention' => ['required', 'string', 'max:2000'],
            'montant' => ['required', 'numeric', 'min:1'],
            'moyen_paiement_id' => ['required', 'uuid', 'exists:moyen_paiements,id'],
            'paiement_recu' => ['required', 'boolean'],
        ];
    }
}
