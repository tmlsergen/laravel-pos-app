<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validated();

        try {
            // Attempt to login the user
            $result = $this->authService->login($validated['email'], $validated['password']);

            return $this->successResponse('Login successful', [
                'token' => $result['token'],
                'token_type' => 'bearer',
                'expires_in' => 3600,
                'user' => new UserResource($result['user']),
            ]);
        } catch (ServiceException $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: ResponseAlias::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            Log::error('internal', ['message' => $e->getMessage()]);

            return $this->errorResponse(
                message: 'An error occurred',
            );
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validated();

        try {
            $this->authService->register($validated);
        } catch (ServiceException $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: ResponseAlias::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            Log::error('internal', ['message' => $e->getMessage()]);

            return $this->errorResponse(
                message: 'An error occurred',
            );
        }

        return $this->successResponse('User registered successfully', ResponseAlias::HTTP_CREATED);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse('Logout successful');
    }
}
