<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\UpdateProfileRequest;
use App\Http\Resources\V1\BookResource;
use App\Http\Resources\V1\UserProfileResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    /**
     * Profil public d'un utilisateur.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Stats de lecture
        $totalBooks = DB::table('user_books')->where('user_id', $user->id)->count();
        $pagesRead = DB::table('user_books')
            ->join('books', 'books.id', '=', 'user_books.book_id')
            ->where('user_books.user_id', $user->id)
            ->where('user_books.status', 'read')
            ->sum('books.page_count');

        // Genres préférés (top 5)
        $topGenres = DB::table('genres')
            ->join('book_genre', 'genres.id', '=', 'book_genre.genre_id')
            ->join('user_books', 'book_genre.book_id', '=', 'user_books.book_id')
            ->where('user_books.user_id', $user->id)
            ->where('user_books.status', 'read')
            ->select('genres.name', DB::raw('count(*) as count'))
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Derniers livres lus
        $recentBooks = $user->library()
            ->wherePivotIn('status', ['read', 'reading'])
            ->orderByPivot('updated_at', 'desc')
            ->limit(5)
            ->get();

        return $this->success([
            'user' => new UserProfileResource($user),
            'stats' => [
                'total_books' => $totalBooks,
                'pages_read' => (int) $pagesRead,
                'top_genres' => $topGenres,
            ],
            'recent_books' => BookResource::collection($recentBooks),
        ]);
    }

    /**
     * Profil connecté complet.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        $featured = $user->library()
            ->wherePivot('featured', true)
            ->with(['authors', 'genres'])
            ->first();

        $palCount = $user->library()->wherePivot('status', 'wishlist')->count();

        return $this->success([
            'user' => new UserProfileResource($user),
            'featured_reading' => $featured ? [
                'id' => $featured->id,
                'title' => $featured->title,
                'cover_variant' => $featured->cover_variant,
                'page_count' => $featured->page_count,
                'current_page' => $featured->pivot->current_page,
                'progress_percent' => $featured->pivot->current_page && $featured->page_count
                    ? round(($featured->pivot->current_page / $featured->page_count) * 100)
                    : 0,
                'authors' => $featured->authors->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
            ] : null,
            'pal_count' => $palCount,
        ]);
    }

    /**
     * Mettre à jour son profil.
     */
    public function updateMe(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());

        return $this->success(
            ['user' => new UserProfileResource($user)],
            'Profil mis à jour'
        );
    }

    /**
     * Suivre un utilisateur.
     */
    public function follow(Request $request, $id)
    {
        $user = $request->user();

        if ($user->id == $id) {
            return $this->error('Vous ne pouvez pas vous suivre vous-même.', 422);
        }

        if ($user->following()->where('followed_id', $id)->exists()) {
            return $this->error('Vous suivez déjà cet utilisateur.', 422);
        }

        $user->following()->attach($id);

        return $this->success(null, 'Vous suivez maintenant cet utilisateur');
    }

    /**
     * Ne plus suivre un utilisateur.
     */
    public function unfollow(Request $request, $id)
    {
        $user = $request->user();
        $user->following()->detach($id);

        return $this->success(null, 'Vous ne suivez plus cet utilisateur');
    }

    /**
     * Liste des followers.
     */
    public function followers(Request $request)
    {
        $user = $request->user();
        $followers = $user->followers()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => UserProfileResource::collection($followers),
            'meta' => [
                'current_page' => $followers->currentPage(),
                'last_page' => $followers->lastPage(),
                'per_page' => $followers->perPage(),
                'total' => $followers->total(),
            ],
        ]);
    }

    /**
     * Liste des suivis.
     */
    public function following(Request $request)
    {
        $user = $request->user();
        $following = $user->following()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => UserProfileResource::collection($following),
            'meta' => [
                'current_page' => $following->currentPage(),
                'last_page' => $following->lastPage(),
                'per_page' => $following->perPage(),
                'total' => $following->total(),
            ],
        ]);
    }
}
