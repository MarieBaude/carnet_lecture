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
            'book' => $this->book,
            'user' => $this->user,
            'status' => $this->status,
            'metadata' => $this->metadata ?? [],
            'timestamp' => $this->timestamp,
        ];
    }
}