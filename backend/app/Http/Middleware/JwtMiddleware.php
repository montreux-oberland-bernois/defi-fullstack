<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException) {
            return response()->json([
                'code' => 'TOKEN_EXPIRED',
                'message' => 'Le token a expiré',
            ], 401);
        } catch (TokenInvalidException) {
            return response()->json([
                'code' => 'TOKEN_INVALID',
                'message' => 'Le token est invalide',
            ], 401);
        } catch (JWTException) {
            return response()->json([
                'code' => 'TOKEN_ABSENT',
                'message' => 'Token d\'autorisation non fourni',
            ], 401);
        }

        return $next($request);
    }
}
