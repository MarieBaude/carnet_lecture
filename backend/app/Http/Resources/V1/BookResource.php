<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover_variant' => $this->cover_variant,
            'cover_url' => $this->cover_url,
            'language' => $this->language,
            'published_date' => $this->published_date,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'saga' => $this->whenLoaded('sagas', function () {
                $saga = $this->sagas->first();
                return $saga ? [
                    'id' => $saga->id,
                    'name' => $saga->name,
                    'tome_number' => $saga->pivot->tome_number ?? null,
                ] : null;
            }),
        ];
    }
}
