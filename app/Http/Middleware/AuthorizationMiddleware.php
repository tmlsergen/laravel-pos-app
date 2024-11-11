<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationMiddleware
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        // Get the user
        /** @var User $user */
        $user = auth()->user();

        // Get the route name
        $routeName = $request->route()->getName();

        // Check if the user has permission to access the route
        if (!$user->hasPermissionTo($routeName)) {
            return $this->errorResponse(message: 'Unauthorized', statusCode:  403);
        }

        return $next($request);
    }
}
