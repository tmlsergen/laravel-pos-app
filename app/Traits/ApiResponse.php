<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponse
{
    public function successResponse(string $message = 'success', $data = [], int $statusCode = ResponseAlias::HTTP_OK): JsonResponse
    {
        Log::info('API Response', [
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode,
        ]);

        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function errorResponse(string $message = 'error', $data = [], int $statusCode = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        Log::error('API Response', [
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode,
        ]);

        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
