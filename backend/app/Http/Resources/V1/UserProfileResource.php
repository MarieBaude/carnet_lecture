<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
            'created_at' => $this->created_at,
            $this->mergeWhen($this->isOwner($request), [
                'email' => $this->email,
                'role' => $this->role,
            ]),
        ];
    }

    protected function isOwner(Request $request): bool
    {
        return $request->user() && $request->user()->id === $this->id;
    }
}