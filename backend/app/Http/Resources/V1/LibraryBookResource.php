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
            'page_count' => $this->page_count,
            'language' => $this->language,
            'published_date' => $this->published_date,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'library' => [
                'status' => $this->pivot->status,
                'rating' => $this->pivot->rating,
                'current_page' => $this->pivot->current_page,
                'progress_percent' => $this->pivot->current_page && $this->page_count
                    ? round(($this->pivot->current_page / $this->page_count) * 100)
                    : 0,
                'started_at' => $this->pivot->started_at,
                'finished_at' => $this->pivot->finished_at,
                'user_comment' => $this->pivot->user_comment,
                'shelf' => $this->pivot->shelf,
                'added_at' => $this->pivot->created_at,
            ],
        ];
    }
}