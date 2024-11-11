<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentProvider\ChangePosStatusRequest;
use App\Http\Resources\PaymentProviderCollection;
use App\Http\Resources\PaymentProviderResource;
use App\Services\PaymentProviderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PaymentProviderController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PaymentProviderService $service) {}

    public function index(): JsonResponse
    {
        try {
            $providers = $this->service->getProviders();

            return $this->successResponse(data: new PaymentProviderCollection($providers));
        } catch (ServiceException $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $provider = $this->service->getProviderById($id);

            return $this->successResponse(data: new PaymentProviderResource($provider));
        } catch (ServiceException $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function changePosStatus(ChangePosStatusRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $status = $this->service->changePosStatus($validated['slug'], $validated['status']);

            return $this->successResponse(data: ['status' => $status]);
        } catch (ServiceException $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
