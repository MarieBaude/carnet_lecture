<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\BookCollection;
use App\Http\Resources\V1\BookDetailResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends BaseController
{
    public function index(Request $request)
    {
        $query = Book::query()->with(['authors', 'genres']);

        // Full-text search
        if ($request->has('search')) {
            $query->search($request->get('search'));
        }

        // Filtre par genre (slug)
        if ($request->has('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('slug', $request->get('genre'));
            });
        }

        // Filtre par auteur
        if ($request->has('author')) {
            $query->whereHas('authors', function ($q) use ($request) {
                $q->where('authors.id', $request->get('author'));
            });
        }

        // Filtre par saga
        if ($request->has('saga')) {
            $query->whereHas('sagas', function ($q) use ($request) {
                $q->where('sagas.id', $request->get('saga'));
            });
        }

        // Filtre par langue
        if ($request->has('language')) {
            $query->where('language', $request->get('language'));
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $allowedSorts = ['title', 'published_date', 'created_at'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder);
        }

        $perPage = min($request->get('per_page', 20), 50);
        $books = $query->paginate($perPage);

        return new BookCollection($books);
    }

    public function show($id)
    {
        $book = Book::with(['authors', 'genres', 'sagas'])->findOrFail($id);

        return $this->success([
            'book' => new BookDetailResource($book),
        ]);
    }
}