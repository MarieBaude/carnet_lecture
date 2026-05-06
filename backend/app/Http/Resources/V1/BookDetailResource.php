<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'cover_variant' => $this->cover_variant,
            'cover_url' => $this->cover_url,
            'publisher' => $this->publisher,
            'published_date' => $this->published_date,
            'page_count' => $this->page_count,
            'language' => $this->language,
            'summary' => $this->summary,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'saga' => $this->whenLoaded('sagas', function () {
                $saga = $this->sagas->first();
                return $saga ? [
                    'id' => $saga->id,
                    'name' => $saga->name,
                    'tome_number' => $saga->pivot->tome_number,
                ] : null;
            }),
            'stats' => [
                'readers_count' => 0,
                'average_rating' => null,
                'reviews_count' => 0,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}