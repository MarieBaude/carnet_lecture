<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ActivityResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends BaseController
{
    /**
     * Fil d'activité de l'utilisateur connecté.
     */
    public function me(Request $request)
    {
        return $this->buildResponse($request, $request->user()->id);
    }

    /**
     * Fil d'activité d'un utilisateur (public).
     */
    public function user(Request $request, $id)
    {
        return $this->buildResponse($request, $id);
    }

    /**
     * Construit la réponse paginée du fil d'activité.
     */
    protected function buildResponse(Request $request, int $userId)
    {
        $query = DB::table('user_books')
            ->join('books', 'books.id', '=', 'user_books.book_id')
            ->join('users', 'users.id', '=', 'user_books.user_id')
            ->where('user_books.user_id', $userId)
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
            );

        // Filtre par type
        if ($request->has('type')) {
            $query->where(function ($q) use ($request) {
                $type = $request->get('type');
                match ($type) {
                    'added_to_library' => $q->whereColumn('user_books.created_at', 'user_books.updated_at'),
                    'removed_from_library' => $q->whereNotNull('user_books.deleted_at'), // jamais atteint avec cette requête
                    'reading_finished' => $q->where('user_books.status', 'read'),
                    'rated' => $q->whereNotNull('user_books.rating'),
                    'commented' => $q->whereNotNull('user_books.user_comment'),
                    'status_changed' => $q->whereColumn('user_books.created_at', '!=', 'user_books.updated_at')
                        ->whereNotIn('user_books.status', ['read']),
                    default => null,
                };
            });
        }

        $activities = $query
            ->orderByDesc('user_books.updated_at')
            ->paginate(20);

        $activities->getCollection()->transform(function ($item) {
            return $this->enrichActivity($item);
        });

        return response()->json([
            'success' => true,
            'data' => ActivityResource::collection($activities),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    /**
     * Détermine le type d'activité et enrichit l'entrée.
     */
    protected function enrichActivity(object $item): object
    {
        $type = $this->determineType($item);

        $metadata = [];
        if ($type === 'rated') {
            $metadata['rating'] = (int) $item->rating;
        }
        if ($type === 'commented') {
            $metadata['comment_excerpt'] = mb_strimwidth($item->user_comment, 0, 100, '...');
        }

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
            'metadata' => $metadata,
            'timestamp' => $item->updated_at,
        ];
    }

    /**
     * Détermine le type d'activité.
     */
    protected function determineType(object $item): string
    {
        // Si created_at = updated_at, c'est un ajout
        if ($item->created_at === $item->updated_at) {
            return 'added_to_library';
        }

        // Si status === read, c'est une lecture terminée
        if ($item->status === 'read') {
            return 'reading_finished';
        }

        // Si rating est non null et a été modifié (si updated_at != created_at)
        if ($item->rating !== null) {
            return 'rated';
        }

        // Si user_comment est non null
        if ($item->user_comment !== null) {
            return 'commented';
        }

        // Sinon, changement de statut générique
        return 'status_changed';
    }
}