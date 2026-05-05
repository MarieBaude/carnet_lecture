<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'book' => [
                'id' => $this->book_id,
                'title' => $this->title,
                'cover_variant' => $this->cover_variant,
            ],
            'status' => $this->status,
            'date' => $this->updated_at,
        ];
    }
}