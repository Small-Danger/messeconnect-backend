<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketSupportStatutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut' => ['required', Rule::in(['ouvert', 'en_cours', 'resolu', 'ferme'])],
            'reponse' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
