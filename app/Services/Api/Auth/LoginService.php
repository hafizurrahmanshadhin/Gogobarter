<?php

namespace App\Services\Api\Auth;

use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginService {
    /**
     * Handle user login.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function login(array $data): array {
        try {
            $token = JWTAuth::attempt([
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);

            if (!$token) {
                throw new Exception('Unauthorized');
            }

            $user = auth()->user();

            return [
                'user'  => $user,
                'token' => $token,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
