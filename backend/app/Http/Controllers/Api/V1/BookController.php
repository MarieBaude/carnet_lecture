<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\BookCollection;
use App\Http\Resources\V1\BookDetailResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\V1\BookStatsResource;
use App\Http\Resources\V1\CommentResource;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\V1\Book\StoreBookRequest;
use App\Http\Requests\V1\Book\UpdateBookRequest;
use App\Models\Author;
use App\Models\Genre;

class BookController extends BaseController
{
    public function index(Request $request)
    {
        $query = Book::query()->with(['authors', 'genres', 'sagas']);

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

    /**
     * Statistiques d'un livre.
     */
    public function stats($id)
    {
        $stats = DB::table('user_books')
            ->where('book_id', $id)
            ->select(
                DB::raw('COALESCE(AVG(rating), 0) as average_rating'),
                DB::raw('COUNT(rating) as ratings_count'),
                DB::raw('COUNT(user_comment) as reviews_count'),
                DB::raw('SUM(CASE WHEN status = \'read\' THEN 1 ELSE 0 END) as readers_count')
            )
            ->first();

        return $this->success(new BookStatsResource($stats));
    }

    /**
     * Noter un livre.
     */
    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $user = $request->user();
        $book = Book::findOrFail($id);

        $exists = $user->library()->where('book_id', $id)->exists();
        if (!$exists) {
            return $this->error('Ajoutez d\'abord ce livre à votre bibliothèque pour le noter.', 422);
        }

        $user->library()->updateExistingPivot($id, ['rating' => $request->rating]);

        // Moyenne mise à jour
        $average = DB::table('user_books')
            ->where('book_id', $id)
            ->whereNotNull('rating')
            ->avg('rating');

        return $this->success([
            'rating' => (int) $request->rating,
            'average_rating' => round((float) $average, 1),
        ], 'Note enregistrée');
    }

    /**
     * Commentaires d'un livre.
     */
    public function comments(Request $request, $id)
    {
        $comments = DB::table('user_books')
            ->join('users', 'users.id', '=', 'user_books.user_id')
            ->where('user_books.book_id', $id)
            ->whereNotNull('user_books.user_comment')
            ->select(
                'user_books.id',
                'user_books.user_id',
                'users.name as user_name',
                'users.avatar_url as user_avatar_url',
                'user_books.user_comment',
                'user_books.rating',
                'user_books.created_at',
                'user_books.updated_at'
            )
            ->orderByDesc('user_books.created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => CommentResource::collection($comments),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Ajouter un commentaire à un livre.
     */
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $user = $request->user();
        $book = Book::findOrFail($id);

        $exists = $user->library()->where('book_id', $id)->exists();
        if (!$exists) {
            return $this->error('Ajoutez d\'abord ce livre à votre bibliothèque pour le commenter.', 422);
        }

        $user->library()->updateExistingPivot($id, ['user_comment' => $request->comment]);

        $comment = DB::table('user_books')
            ->join('users', 'users.id', '=', 'user_books.user_id')
            ->where('user_books.user_id', $user->id)
            ->where('user_books.book_id', $id)
            ->select(
                'user_books.id',
                'user_books.user_id',
                'users.name as user_name',
                'users.avatar_url as user_avatar_url',
                'user_books.user_comment',
                'user_books.rating',
                'user_books.created_at',
                'user_books.updated_at'
            )
            ->first();

        return $this->success(
            new CommentResource($comment),
            'Commentaire ajouté',
            201
        );
    }

    /**
     * Créer un livre.
     */
    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();


        // Normaliser les auteurs
        $authors = $data['authors'] ?? [];
        $authorIds = [];
        foreach ($authors as $author) {
            if (is_array($author)) {
                $author = $author['id'] ?? $author['name'] ?? reset($author);
            }
            if (is_numeric($author)) {
                $authorIds[] = (int) $author;
            } elseif (is_string($author) && !empty($author)) {
                $newAuthor = Author::firstOrCreate(['name' => $author]);
                $authorIds[] = $newAuthor->id;
            }
        }

        // Normaliser les genres
        $genres = $data['genres'] ?? [];
        if (!is_array($genres)) {
            $genres = $genres ? [$genres] : [];
        }
        $genreIds = [];
        foreach ($genres as $genre) {
            if (is_array($genre)) {
                $genre = $genre['id'] ?? $genre['slug'] ?? reset($genre);
            }
            if (is_numeric($genre)) {
                $genreIds[] = (int) $genre;
            } elseif (is_string($genre) && !empty($genre)) {
                $g = Genre::where('slug', $genre)->orWhere('name', $genre)->first();
                if ($g) {
                    $genreIds[] = $g->id;
                }
            }
        }

        // \Log::info('Store book data', $data);
        if (empty($data['cover_variant'])) {
            $data['cover_variant'] = rand(1, 10);
        }

        $bookData = collect($data)->except(['authors', 'genres', 'saga_id', 'tome_number'])->toArray();

        // Forcer les champs vides à null
        foreach ($bookData as $key => $value) {
            if (is_string($value) && trim($value) === '') {
                $bookData[$key] = null;
            }
        }
        $book = Book::create($bookData);

        if (!empty($authorIds)) {
            $book->authors()->attach($authorIds);
        }
        if (!empty($genreIds)) {
            $book->genres()->attach($genreIds);
        }
        
        // Attacher la saga
        if (isset($data['saga_id'])) {
            $book->sagas()->attach($data['saga_id'], [
                'tome_number' => $data['tome_number'] ?? null,
            ]);
        }

        $book->load(['authors', 'genres', 'sagas']);

        return $this->success(
            ['book' => new BookDetailResource($book)],
            'Livre créé avec succès',
            201
        );
    }

    /**
     * Mettre à jour un livre.
     */
    public function update(UpdateBookRequest $request, $id)
    {
        $book = Book::findOrFail($id);
        $data = $request->validated();

        $bookData = collect($data)->except(['authors', 'genres', 'saga_id', 'tome_number'])->toArray();
        $book = Book::create($bookData);

        // Sync auteurs
        if (isset($data['authors'])) {
            $authorIds = [];
            foreach ($data['authors'] as $author) {
                if (is_numeric($author)) {
                    $authorIds[] = (int) $author;
                } else {
                    $newAuthor = Author::create(['name' => $author]);
                    $authorIds[] = $newAuthor->id;
                }
            }
            $book->authors()->sync($authorIds);
        }

        // Sync genres
        if (isset($data['genres'])) {
            $genres = is_array($data['genres']) ? $data['genres'] : [$data['genres']];
            $book->genres()->sync($genres);
        }

        // Sync saga
        if (isset($data['saga_id'])) {
            $book->sagas()->sync([$data['saga_id'] => [
                'tome_number' => $data['tome_number'] ?? null,
            ]]);
        }

        $book->load(['authors', 'genres', 'sagas']);

        return $this->success(
            ['book' => new BookDetailResource($book)],
            'Livre mis à jour'
        );
    }

    /**
     * Supprimer un livre (soft delete).
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return $this->success(null, 'Livre supprimé');
    }
}
