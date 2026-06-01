<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;

class RegisterParoisseRequest extends FormRequest
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
            'paroisse.nom' => ['required', 'string', 'max:255'],
            'paroisse.email' => ['required', 'email', 'max:255', 'unique:paroisses,email'],
            'paroisse.telephone' => ['nullable', 'string', 'max:255'],
            'paroisse.adresse' => ['nullable', 'string', 'max:255'],
            'paroisse.ville' => ['nullable', 'string', 'max:255'],
            'paroisse.pays' => ['nullable', 'string', 'max:255'],
            'paroisse.description' => ['nullable', 'string'],
            'paroisse.site_web' => ['nullable', 'string', 'max:255'],
            'paroisse.diocese_id' => ['nullable', 'uuid', 'exists:dioceses,id'],
            'responsable.nom' => ['required', 'string', 'max:255'],
            'responsable.email' => ['required', 'email', 'max:255', 'unique:user_paroisses,email'],
            'responsable.password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
