<?php

namespace App\Http\Requests\V1;

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
            'name' => 'sometimes|string|max:100',
            'bio' => 'nullable|string|max:5000',
            'avatar_url' => 'nullable|url|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'bio.max' => 'La bio ne peut pas dépasser 5000 caractères.',
            'avatar_url.url' => 'L\'URL de l\'avatar n\'est pas valide.',
        ];
    }
}