<?php

namespace App\Http\Requests\V1\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:13',
            'cover_variant' => 'nullable|integer|min:1|max:10',
            'cover_url' => 'nullable|string|max:2048',
            'publisher' => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'page_count' => 'nullable|integer|min:1',
            'language' => 'nullable|string|size:2',
            'summary' => 'nullable|string',
            'authors' => 'required|array|min:1',
            'authors.*' => 'nullable',
            'genres' => 'nullable',
            'saga_id' => 'nullable|integer|exists:sagas,id',
            'tome_number' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est requis.',
            'authors.required' => 'Au moins un auteur est requis.',
        ];
    }
}