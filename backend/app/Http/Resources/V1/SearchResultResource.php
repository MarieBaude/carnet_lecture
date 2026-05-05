<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover_variant' => $this->cover_variant,
            'language' => $this->language,
            'published_date' => $this->published_date,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
        ];
    }
}