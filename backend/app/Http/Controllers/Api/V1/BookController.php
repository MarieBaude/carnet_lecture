<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\BookCollection;
use App\Http\Resources\V1\BookDetailResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\V1\BookStatsResource;
use App\Http\Resources\V1\CommentResource;
use Illuminate\Support\Facades\DB;

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
}
