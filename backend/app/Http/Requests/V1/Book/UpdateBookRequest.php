<?php

namespace App\Http\Requests\V1\Book;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'isbn' => 'nullable|string|max:13',
            'cover_variant' => 'nullable|integer|min:1|max:10',
            'publisher' => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'page_count' => 'nullable|integer|min:1',
            'language' => 'nullable|string|size:2',
            'summary' => 'nullable|string',
            'authors' => 'sometimes|array|min:1',
            'authors.*' => ['required'],
            'genres' => 'nullable|array',
            'genres.*' => 'integer|exists:genres,id',
            'saga_id' => 'nullable|integer|exists:sagas,id',
            'tome_number' => 'nullable|integer|min:1',
        ];
    }
}