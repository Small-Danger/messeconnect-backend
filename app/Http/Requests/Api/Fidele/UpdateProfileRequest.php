<?php

namespace App\Http\Requests\Api\Fidele;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'string', 'max:255'],
            'prenom' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:fideles,email,'.$this->user()->id],
            'telephone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'ville' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pays' => ['sometimes', 'nullable', 'string', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ];
    }
}
