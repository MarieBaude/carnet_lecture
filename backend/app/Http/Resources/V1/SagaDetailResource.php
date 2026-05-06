<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SagaDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'books' => $this->whenLoaded('books', function () {
                return $this->books->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'cover_url' => $book->cover_url,
                        'cover_variant' => $book->cover_variant,
                        'tome_number' => $book->pivot->tome_number,
                        'authors' => $book->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
                    ];
                });
            }),
            'created_at' => $this->created_at,
        ];
    }
}