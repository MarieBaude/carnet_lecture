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
    public function index(Request $request)
    {
        $user = $request->user();

        $activities = DB::table('user_books')
            ->join('books', 'books.id', '=', 'user_books.book_id')
            ->where('user_books.user_id', $user->id)
            ->select(
                'user_books.status',
                'user_books.updated_at',
                'books.id as book_id',
                'books.title',
                'books.cover_variant'
            )
            ->orderByDesc('user_books.updated_at')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'type' => $this->determineType($item->status),
                    'status' => $item->status,
                    'book_id' => $item->book_id,
                    'title' => $item->title,
                    'cover_variant' => $item->cover_variant,
                    'updated_at' => $item->updated_at,
                ];
            });

        return $this->success(ActivityResource::collection($activities));
    }

    protected function determineType(string $status): string
    {
        return match ($status) {
            'read' => 'finished',
            'reading' => 'status_change',
            'wishlist' => 'added',
            'owned' => 'added',
            'dropped' => 'status_change',
            default => 'added',
        };
    }
}