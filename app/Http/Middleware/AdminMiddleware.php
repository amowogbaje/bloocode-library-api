<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->role != Role::ADMIN->value || $user->is_disabled) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized, admin access only',
                'error' => 'Bad Request'
            ], 401);
        }

        return $next($request);
    }
}
