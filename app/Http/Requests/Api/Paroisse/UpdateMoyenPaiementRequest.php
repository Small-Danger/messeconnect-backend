<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMoyenPaiementRequest extends FormRequest
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
            'type' => ['sometimes', Rule::in(['orange_money', 'wave', 'moov_money', 'autre'])],
            'environment' => ['sometimes', Rule::in(['sandbox', 'production'])],
            'numero' => ['sometimes', 'nullable', 'string', 'max:255'],
            'identifiant_marchand' => ['sometimes', 'nullable', 'string', 'max:255'],
            'client_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'api_key' => ['sometimes', 'nullable', 'string'],
            'secret_key' => ['sometimes', 'nullable', 'string'],
            'webhook_secret' => ['sometimes', 'nullable', 'string'],
            'callback_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'notify_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'metadata' => ['sometimes', 'nullable', 'array'],
            'actif' => ['sometimes', 'boolean'],
        ];
    }
}
