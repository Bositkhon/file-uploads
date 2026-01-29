<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {

    }
    public function login(LoginRequest $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $token = $this->authService->issueAccessToken($email, $password);

        if (!$token) {
            return response()->json(['error' => 'Provided credentials are invalids'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl', 60) * 60,
        ]);
    }
}
