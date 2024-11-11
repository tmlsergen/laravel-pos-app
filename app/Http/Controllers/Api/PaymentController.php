<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentProviderEnum;
use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\PaymentRequest;
use App\Services\PaymentService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PaymentService $paymentService) {}

    public function store(PaymentRequest $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validated();

        // Generate fake order id
        $validated['order_id'] = 'testing_12_'.uniqid();
        try {
            // Call the makePayment method from the payment service
            $result = $this->paymentService->makePayment($validated);

            // Return the response
            return $this->successResponse('payment process success', $result);
        } catch (ServiceException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return $this->errorResponse('Internal Server Error', 500);
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        // Check if the provider is valid
        $providers = PaymentProviderEnum::toArray();
        if (!in_array($request->provider, $providers)) {
            return redirect()->to('http://localhost/payment-fail');
        }

        try {
            // Call the callback method from the payment service
            $this->paymentService->callback($request->all(), $request->provider);
        } catch (ServiceException $e) {
            // Redirect to payment fail page
            return redirect()->to('http://localhost/payment-fail?message='.$e->getMessage());
        }

        // Redirect to payment success page
        return redirect()->to('http://localhost/payment-success');
    }
}
