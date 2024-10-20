<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(empty($request->bearerToken())){
            return response()->json([
                'success' => false,
                'api_key' => '',
                'message' => 'Bearer token required!',
                'data'    => (object)[]
            ], 401);
        }
        
        return $next($request);
    }
}
