<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class IsFavoriteMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        try {
            if ($token = $request->bearerToken()) {
                Auth::shouldUse('api');
                JWTAuth::setToken($token)->toUser();
            }
        } catch (Exception $e) {
            // Ignore invalid or missing token, user remains unauthenticated
        }

        return $next($request);
    }
}
