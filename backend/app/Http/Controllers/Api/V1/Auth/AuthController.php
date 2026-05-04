<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success(
            [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            'Inscription réussie',
            201
        );
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return $this->error('Identifiants invalides', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success(
            [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            'Connexion réussie'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Déconnexion réussie');
    }

    public function user(Request $request)
    {
        return $this->success(
            ['user' => new UserResource($request->user())]
        );
    }
}