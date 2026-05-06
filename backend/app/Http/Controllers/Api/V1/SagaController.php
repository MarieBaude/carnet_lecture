<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Saga;
use Illuminate\Http\Request;

class SagaController extends BaseController
{
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

        return $this->success($saga, 'Saga créée avec succès', 201);
    }
}