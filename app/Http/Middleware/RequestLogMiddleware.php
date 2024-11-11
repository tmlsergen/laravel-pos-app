<?php

namespace App\Http\Middleware;

use App\Helpers\CardHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestData = $request->all();

        if (in_array('card_number', array_keys($requestData))) {
            $requestData['card_number'] = CardHelper::cardMask($requestData['card_number']);
            $requestData['expiration_month'] = 'XX';
            $requestData['expiration_year'] = 'XX';
            $requestData['cvv'] = 'XXX';
        }

        if (in_array('password', array_keys($requestData))) {
            $requestData['password'] = 'XXXX';
            $requestData['password_confirmation'] = 'XXXX';
        }

        Log::info('API Request', [
            'url' => $request->url(),
            'method' => $request->method(),
            'request' => $requestData,
        ]);

        return $next($request);
    }
}
