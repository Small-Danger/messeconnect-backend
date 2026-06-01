<?php

namespace App\Http\Requests\Api\Paroisse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaParoisseRequest extends FormRequest
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
            'type' => ['sometimes', Rule::in(['image', 'video'])],
            'url' => ['sometimes', 'string', 'max:2048'],
            'ordre' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
