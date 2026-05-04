<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\GenreResource;
use App\Models\Genre;

class GenreController extends BaseController
{
    public function index()
    {
        $genres = Genre::withCount('books')->orderBy('name')->get();

        return $this->success(
            GenreResource::collection($genres)
        );
    }
}