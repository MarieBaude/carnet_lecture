<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends BaseController
{
    public function index(Request $request)
    {
        $query = Author::query()->orderBy('name');

        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->get('search') . '%');
        }

        $perPage = min($request->get('per_page', 20), 50);
        $authors = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AuthorResource::collection($authors),
            'meta' => [
                'current_page' => $authors->currentPage(),
                'last_page' => $authors->lastPage(),
                'per_page' => $authors->perPage(),
                'total' => $authors->total(),
            ],
        ]);
    }
}