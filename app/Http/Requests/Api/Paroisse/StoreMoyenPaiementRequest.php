<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMoyenPaiementRequest extends FormRequest
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
            'type' => ['required', Rule::in(['orange_money', 'wave', 'moov_money', 'autre'])],
            'environment' => ['sometimes', Rule::in(['sandbox', 'production'])],
            'numero' => ['nullable', 'string', 'max:255'],
            'identifiant_marchand' => ['nullable', 'string', 'max:255'],
            'client_id' => ['nullable', 'string', 'max:255'],
            'api_key' => ['nullable', 'string'],
            'secret_key' => ['nullable', 'string'],
            'webhook_secret' => ['nullable', 'string'],
            'callback_url' => ['nullable', 'url', 'max:255'],
            'notify_url' => ['nullable', 'url', 'max:255'],
            'metadata' => ['nullable', 'array'],
            'actif' => ['sometimes', 'boolean'],
        ];
    }
}
