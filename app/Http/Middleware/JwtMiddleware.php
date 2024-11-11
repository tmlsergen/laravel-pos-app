<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $this->errorResponse(message: 'Unauthorized', statusCode: Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
