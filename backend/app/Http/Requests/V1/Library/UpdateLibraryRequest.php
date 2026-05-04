<?php

namespace App\Http\Requests\V1\Library;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLibraryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:wishlist,owned,reading,read,dropped',
            'rating' => 'nullable|integer|min:1|max:5',
            'current_page' => 'nullable|integer|min:0',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
            'user_comment' => 'nullable|string|max:5000',
            'shelf' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Le statut doit être wishlist, owned, reading, read ou dropped.',
            'rating.min' => 'La note doit être entre 1 et 5.',
            'rating.max' => 'La note doit être entre 1 et 5.',
        ];
    }
}