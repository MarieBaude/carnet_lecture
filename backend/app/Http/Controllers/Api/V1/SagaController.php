<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\SagaDetailResource;
use App\Http\Resources\V1\SagaResource;
use App\Models\Book;
use App\Models\Saga;
use Illuminate\Http\Request;

class SagaController extends BaseController
{
    /**
     * Liste paginée des sagas.
     */
    public function index(Request $request)
    {
        $query = Saga::withCount('books')->orderBy('name');

        if ($request->has('q')) {
            $query->where('name', 'ilike', '%' . $request->get('q') . '%');
        }

        $sagas = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => SagaResource::collection($sagas),
            'meta' => [
                'current_page' => $sagas->currentPage(),
                'last_page' => $sagas->lastPage(),
                'per_page' => $sagas->perPage(),
                'total' => $sagas->total(),
            ],
        ]);
    }

    /**
     * Fiche saga avec ses livres triés par tome.
     */
    public function show($id)
    {
        $saga = Saga::with(['books' => function ($q) {
            $q->orderByPivot('tome_number')->with('authors');
        }])->findOrFail($id);

        return $this->success([
            'saga' => new SagaDetailResource($saga),
        ]);
    }

    /**
     * Créer une saga.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $saga = Saga::create($data);

        return $this->success(new SagaResource($saga), 'Saga créée', 201);
    }

    /**
     * Modifier une saga.
     */
    public function update(Request $request, $id)
    {
        $saga = Saga::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $saga->update($data);

        return $this->success(new SagaResource($saga), 'Saga mise à jour');
    }

    /**
     * Supprimer une saga.
     */
    public function destroy($id)
    {
        $saga = Saga::findOrFail($id);
        $saga->delete();

        return $this->success(null, 'Saga supprimée');
    }

    /**
     * Ajouter un livre à une saga.
     */
    public function addBook(Request $request, $id)
    {
        $saga = Saga::findOrFail($id);

        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'tome_number' => 'nullable|integer|min:1',
        ]);

        // Éviter les doublons
        if ($saga->books()->where('book_id', $data['book_id'])->exists()) {
            return $this->error('Ce livre est déjà dans cette saga.', 422);
        }

        $saga->books()->attach($data['book_id'], [
            'tome_number' => $data['tome_number'] ?? null,
        ]);

        return $this->success(null, 'Livre ajouté à la saga', 201);
    }

    /**
     * Modifier le tome_number d'un livre dans une saga.
     */
    public function updateBook(Request $request, $id, $bookId)
    {
        $saga = Saga::findOrFail($id);

        $data = $request->validate([
            'tome_number' => 'required|integer|min:1',
        ]);

        $saga->books()->updateExistingPivot($bookId, [
            'tome_number' => $data['tome_number'],
        ]);

        return $this->success(null, 'Ordre du tome mis à jour');
    }

    /**
     * Retirer un livre d'une saga.
     */
    public function removeBook($id, $bookId)
    {
        $saga = Saga::findOrFail($id);
        $saga->books()->detach($bookId);

        return $this->success(null, 'Livre retiré de la saga');
    }
}