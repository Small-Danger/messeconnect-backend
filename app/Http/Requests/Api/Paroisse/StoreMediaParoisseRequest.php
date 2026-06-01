<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaParoisseRequest extends FormRequest
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
            'type' => ['required', Rule::in(['image', 'video'])],
            'url' => ['required', 'string', 'max:2048'],
            'ordre' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
