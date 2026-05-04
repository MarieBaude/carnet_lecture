<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LibraryBookResource extends JsonResource
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
            'library' => [
                'status' => $this->pivot->status,
                'rating' => $this->pivot->rating,
                'current_page' => $this->pivot->current_page,
                'started_at' => $this->pivot->started_at,
                'finished_at' => $this->pivot->finished_at,
                'user_comment' => $this->pivot->user_comment,
                'shelf' => $this->pivot->shelf,
                'added_at' => $this->pivot->created_at,
            ],
        ];
    }
}