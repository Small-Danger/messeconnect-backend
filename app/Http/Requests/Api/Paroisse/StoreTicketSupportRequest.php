<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketSupportRequest extends FormRequest
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
            'sujet' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ];
    }
}
