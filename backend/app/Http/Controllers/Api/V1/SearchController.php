<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\AuthorResource;
use App\Http\Resources\V1\LibraryBookResource;
use App\Http\Resources\V1\SearchResultResource;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends BaseController
{
    /**
     * Recherche avancée multi-critères.
     */
    public function search(Request $request)
    {
        $filters = [
            'q' => $request->get('q'),
            'type' => $request->get('type'),
            'tags' => $request->get('tags', []),
            'language' => $request->get('language'),
            'status' => $request->get('status'),
            'sort' => $request->get('sort', 'relevance'),
        ];

        $result = [];

        // Recherche auteurs
        if (!$request->has('type') || $request->get('type') === 'author') {
            $result['authors'] = $this->searchAuthors($request);
        }

        // Recherche livres
        if (!$request->has('type') || $request->get('type') === 'book') {
            if ($request->has('status') && $request->user()) {
                $result['books'] = $this->searchLibrary($request);
            } else {
                $result['books'] = $this->searchBooks($request);
            }
        }

        return $this->success($result, 'Résultats de recherche', 200);
    }

    /**
     * Recherche de livres dans le catalogue.
     */
    protected function searchBooks(Request $request): array
    {
        $query = Book::with(['authors', 'genres']);

        // Full-text search
        if ($request->has('q')) {
            $query->search($request->get('q'));
        }

        // Filtre par genre (tags)
        if ($request->has('tags')) {
            $tags = (array) $request->get('tags');
            foreach ($tags as $slug) {
                $query->whereHas('genres', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            }
        }

        // Filtre par langue
        if ($request->has('language')) {
            $query->where('language', $request->get('language'));
        }

        // Tri
        $this->applySort($query, $request);

        $perPage = min($request->get('per_page', 20), 50);
        $results = $query->paginate($perPage);

        return [
            'items' => SearchResultResource::collection($results),
            'total' => $results->total(),
            'page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
        ];
    }

    /**
     * Recherche dans la bibliothèque personnelle.
     */
    protected function searchLibrary(Request $request): array
    {
        $user = $request->user();
        $query = $user->library()->with(['authors', 'genres']);

        // Filtre par statut
        if ($request->has('status')) {
            $query->wherePivot('status', $request->get('status'));
        }

        // Full-text search
        if ($request->has('q')) {
            $query->search($request->get('q'));
        }

        // Tri
        $sort = $request->get('sort', 'updated_at');
        $order = $request->get('order', 'desc');
        if ($sort === 'title') {
            $query->orderBy('title', $order);
        } else {
            $query->orderByPivot('updated_at', $order);
        }

        $perPage = min($request->get('per_page', 20), 50);
        $results = $query->paginate($perPage);

        return [
            'items' => LibraryBookResource::collection($results),
            'total' => $results->total(),
            'page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
        ];
    }

    /**
     * Recherche d'auteurs.
     */
    protected function searchAuthors(Request $request): array
    {
        $query = Author::query()->orderBy('name');

        if ($request->has('q')) {
            $query->where('name', 'ilike', '%' . $request->get('q') . '%');
        }

        $perPage = min($request->get('per_page', 20), 50);
        $results = $query->paginate($perPage);

        return [
            'items' => AuthorResource::collection($results),
            'total' => $results->total(),
            'page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
        ];
    }

    /**
     * Applique le tri.
     */
    protected function applySort($query, Request $request): void
    {
        $sort = $request->get('sort', 'relevance');
        $order = $request->get('order', 'desc');

        match ($sort) {
            'title' => $query->orderBy('title', $order),
            'published_date' => $query->orderBy('published_date', $order),
            'created_at' => $query->orderBy('created_at', $order),
            default => $query->orderBy('created_at', 'desc'),
        };
    }
}