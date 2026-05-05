<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'average_rating' => $this->average_rating
                ? round((float) $this->average_rating, 1)
                : null,
            'ratings_count' => (int) $this->ratings_count,
            'reviews_count' => (int) $this->reviews_count,
            'readers_count' => (int) $this->readers_count,
        ];
    }
}