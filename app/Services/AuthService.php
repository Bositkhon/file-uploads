<?php

namespace App\Services;

class AuthService
{
    public function issueAccessToken(
        string $email,
        string $password
    ): string {
        return auth('api')->attempt([
            'email' => $email,
            'password' => $password
        ]);
    }
}
