<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Library\AddToLibraryRequest;
use App\Http\Requests\V1\Library\UpdateLibraryRequest;
use App\Http\Resources\V1\LibraryBookResource;
use App\Http\Resources\V1\LibraryCollection;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibraryController extends BaseController
{
    /**
     * Ajouter un livre à sa bibliothèque.
     */
    public function store(AddToLibraryRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        // Vérifier unicité
        $exists = $user->library()->where('book_id', $data['book_id'])->exists();
        if ($exists) {
            return $this->error('Ce livre est déjà dans votre bibliothèque.', 422);
        }

        // Auto-dates
        if (($data['status'] ?? '') === 'reading' && empty($data['started_at'])) {
            $data['started_at'] = now()->toDateString();
        }
        if (($data['status'] ?? '') === 'read' && empty($data['finished_at'])) {
            $data['finished_at'] = now()->toDateString();
        }

        $user->library()->attach($data['book_id'], $data);

        $book = $user->library()->where('book_id', $data['book_id'])->with(['authors', 'genres'])->first();

        return $this->success(
            ['book' => new LibraryBookResource($book)],
            'Livre ajouté à votre bibliothèque',
            201
        );
    }

    /**
     * Lister sa bibliothèque.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->library()->with(['authors', 'genres']);

        // Filtre par statut
        if ($request->has('status')) {
            $query->wherePivot('status', $request->get('status'));
        }

        // Filtre par étagère
        if ($request->has('shelf')) {
            $query->wherePivot('shelf', $request->get('shelf'));
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $allowedSorts = ['created_at', 'title', 'rating', 'finished_at'];

        if ($sortField === 'title') {
            $query->orderBy('title', $sortOrder);
        } elseif (in_array($sortField, ['created_at', 'rating', 'finished_at'])) {
            $query->orderByPivot($sortField, $sortOrder);
        }

        $perPage = min($request->get('per_page', 20), 50);
        $books = $query->paginate($perPage);

        return new LibraryCollection($books);
    }

    /**
     * Modifier un livre de sa bibliothèque.
     */
    public function update(UpdateLibraryRequest $request, $bookId)
    {
        $user = $request->user();
        $book = $user->library()->where('book_id', $bookId)->first();

        if (!$book) {
            return $this->error('Livre non trouvé dans votre bibliothèque.', 404);
        }

        $data = $request->validated();

        // Auto-dates
        if (($data['status'] ?? $book->pivot->status) === 'reading' && empty($data['started_at'])) {
            $data['started_at'] = $book->pivot->started_at ?? now()->toDateString();
        }
        if (($data['status'] ?? $book->pivot->status) === 'read' && empty($data['finished_at'])) {
            $data['finished_at'] = $book->pivot->finished_at ?? now()->toDateString();
        }

        $user->library()->updateExistingPivot($bookId, $data);

        $book = $user->library()->where('book_id', $bookId)->with(['authors', 'genres'])->first();

        return $this->success(
            ['book' => new LibraryBookResource($book)],
            'Bibliothèque mise à jour'
        );
    }

    /**
     * Retirer un livre de sa bibliothèque.
     */
    public function destroy(Request $request, $bookId)
    {
        $user = $request->user();
        $exists = $user->library()->where('book_id', $bookId)->exists();

        if (!$exists) {
            return $this->error('Livre non trouvé dans votre bibliothèque.', 404);
        }

        $user->library()->detach($bookId);

        return $this->success(null, 'Livre retiré de votre bibliothèque');
    }

    /**
     * Statistiques de la bibliothèque.
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $library = $user->library();

        $byStatus = DB::table('user_books')
            ->where('user_id', $user->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalPagesRead = DB::table('user_books')
            ->join('books', 'books.id', '=', 'user_books.book_id')
            ->where('user_books.user_id', $user->id)
            ->where('user_books.status', 'read')
            ->sum('books.page_count');

        $averageRating = DB::table('user_books')
            ->where('user_id', $user->id)
            ->whereNotNull('rating')
            ->avg('rating');

        return $this->success([
            'total' => (int) $library->count(),
            'wishlist' => (int) ($byStatus['wishlist'] ?? 0),
            'owned' => (int) ($byStatus['owned'] ?? 0),
            'reading' => (int) ($byStatus['reading'] ?? 0),
            'read' => (int) ($byStatus['read'] ?? 0),
            'dropped' => (int) ($byStatus['dropped'] ?? 0),
            'pages_read' => (int) $totalPagesRead,
            'average_rating' => $averageRating ? round($averageRating, 2) : null,
        ]);
    }

    /**
     * Liste des étagères de l'utilisateur.
     */
    public function shelves(Request $request)
    {
        $user = $request->user();

        $shelves = DB::table('user_books')
            ->where('user_id', $user->id)
            ->whereNotNull('shelf')
            ->select('shelf', DB::raw('count(*) as count'))
            ->groupBy('shelf')
            ->orderBy('shelf')
            ->get();

        return $this->success($shelves);
    }
}