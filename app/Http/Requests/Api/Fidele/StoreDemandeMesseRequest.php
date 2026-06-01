<?php

namespace App\Http\Requests\Api\Fidele;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandeMesseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fidele = auth('fidele')->user();

        return [
            'paroisse_id' => ['required', 'uuid', 'exists:paroisses,id'],
            'messe_id' => ['required', 'uuid', 'exists:messes,id'],
            'type_offrande_id' => ['required', 'uuid', 'exists:type_offrandes,id'],
            'est_anonyme' => ['sometimes', 'boolean'],
            'nom_demandeur' => [$fidele ? 'nullable' : 'required', 'string', 'max:255'],
            'email_demandeur' => ['nullable', 'email', 'max:255'],
            'telephone_demandeur' => [$fidele ? 'nullable' : 'required', 'string', 'max:255'],
            'intention' => ['nullable', 'string'],
            'nom_personne_concernee' => ['nullable', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:1'],
        ];
    }
}
