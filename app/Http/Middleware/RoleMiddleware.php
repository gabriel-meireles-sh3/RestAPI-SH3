<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (Auth::check()) {
            $roleMap = [
                'admin' => User::ROLE_ADMIN,
                'attendant' => User::ROLE_ATTENDANT,
                'support' => User::ROLE_SUPPORT,
                'user' => User::ROLE_USER,
            ];

            foreach ($roles as $role) {
                if ($request->user()->hasRole($roleMap[$role])) {
                    return $next($request);
                }
            }
        }

        return response()->json([
            'message' => 'Unauthenticated, Not authorized for the requested roles: '
                . implode(', ', $roles),
        ], 401);
    }
}
