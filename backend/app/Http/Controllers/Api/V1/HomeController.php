<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ActivityResource;
use App\Http\Resources\V1\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends BaseController
{
    /**
     * Page d'accueil.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Activité des follows (5 dernières)
        $friendsActivity = $this->getFriendsActivity($user);

        // 6 derniers livres ajoutés au catalogue
        $latestBooks = Book::with(['authors', 'genres'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // 6 livres les mieux notés
        $popularBooks = $this->getPopularBooks();

        return $this->success([
            'friends_activity' => $friendsActivity,
            'latest_books' => BookResource::collection($latestBooks),
            'popular_books' => BookResource::collection($popularBooks),
        ]);
    }

    /**
     * Activité récente des utilisateurs suivis.
     */
    protected function getFriendsActivity($user)
    {
        $followedIds = $user->following()->pluck('users.id');

        if ($followedIds->isEmpty()) {
            return [];
        }

        $activities = DB::table('user_books')
            ->join('books', 'books.id', '=', 'user_books.book_id')
            ->join('users', 'users.id', '=', 'user_books.user_id')
            ->whereIn('user_books.user_id', $followedIds)
            ->select(
                'user_books.id',
                'user_books.user_id',
                'users.name as user_name',
                'users.avatar_url as user_avatar_url',
                'user_books.book_id',
                'books.title as book_title',
                'books.cover_variant as book_cover_variant',
                'user_books.status',
                'user_books.rating',
                'user_books.user_comment',
                'user_books.created_at',
                'user_books.updated_at'
            )
            ->orderByDesc('user_books.updated_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $type = $item->created_at === $item->updated_at
                    ? 'added_to_library'
                    : ($item->status === 'read' ? 'reading_finished' : 'status_changed');

                return (object) [
                    'type' => $type,
                    'book' => [
                        'id' => $item->book_id,
                        'title' => $item->book_title,
                        'cover_variant' => $item->book_cover_variant,
                    ],
                    'user' => [
                        'id' => $item->user_id,
                        'name' => $item->user_name,
                        'avatar_url' => $item->user_avatar_url,
                    ],
                    'status' => $item->status,
                    'timestamp' => $item->updated_at,
                ];
            });

        return ActivityResource::collection($activities);
    }

    /**
     * Livres les mieux notés.
     */
    protected function getPopularBooks()
    {
        return Book::with(['authors', 'genres'])
            ->join('user_books', 'books.id', '=', 'user_books.book_id')
            ->select('books.*', DB::raw('AVG(user_books.rating) as avg_rating'), DB::raw('COUNT(user_books.rating) as ratings_count'))
            ->whereNotNull('user_books.rating')
            ->groupBy('books.id')
            ->orderByDesc('avg_rating')
            ->limit(6)
            ->get();
    }
}