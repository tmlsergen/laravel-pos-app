<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Exceptions\PaymentProvider\PaymentServiceException;
use App\Exceptions\ServiceException;
use App\Models\PaymentProvider;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentProvider\PaymentHandler;
use Illuminate\Support\Facades\Log;

readonly class PaymentService
{
    public function __construct(
        private readonly PaymentProviderService $paymentProviderService,
        private readonly TransactionService $transactionService
    ) {}

    /**
     * @throws ServiceException
     */
    public function makePayment(array $params): array
    {
        /** @var User $user */
        $user = auth()->user();

        // Get the payment provider by slug and get the settings
        /** @var PaymentProvider $provider */
        $provider = $this->paymentProviderService->getProviderBySlug($params['provider']);
        $providerSettings = $provider->settings()->toArray();

        // Merge the provider settings with the request params
        $params = array_merge($providerSettings, $params);
        $params['email'] = $user->email;

        // Generate payment service instance by provider
        $paymentService = PaymentHandler::make($params['provider']);

        // Determine the action
        $action = 'makePayment';
        if ($params['three_d_secure']) {
            $action = 'make3dSecurePayment';
        }

        // Create transaction
        /** @var Transaction $transaction */
        $transaction = $this->transactionService->createTransaction([
            'user_id' => $user->id,
            'provider' => $params['provider'],
            'amount' => $params['amount'],
            'order_id' => $params['order_id'],
            'status' => PaymentStatusEnum::PENDING->value,
            'currency' => $params['currency'],
        ]);

        try {
            // Make payment
            $result = $paymentService->{$action}($params);

            if ($params['three_d_secure']) {
                // If 3D secure, return the redirect URL
                return [
                    'status' => PaymentStatusEnum::PENDING->value,
                    'response' => $result,
                ];
            }
        } catch (PaymentServiceException $e) {
            Log::error('Payment failed', [
                'transaction_id' => $transaction->id,
                'status' => PaymentStatusEnum::FAILED->value,
                'raw_response' => json_encode($e->getMessage()),
            ]);

            // Update transaction status for failed
            $this->transactionService->updateTransaction($transaction->id, [
                'status' => PaymentStatusEnum::FAILED->value,
                'raw_response' => json_encode($e->getMessage()),
            ]);

            throw new ServiceException($e->getMessage());
        }

        // Update transaction status for success
        $this->transactionService->updateTransaction($transaction->id, [
            'status' => PaymentStatusEnum::SUCCESS->value,
            'raw_response' => json_encode($result),
        ]);

        // Return the success response
        return [
            'status' => PaymentStatusEnum::SUCCESS->value,
        ];
    }

    /**
     * @throws ServiceException
     */
    public function callback(array $params, string $provider): void
    {
        // Get the transaction by order ID
        try {
            $transaction = $this->transactionService->getByOrderId($params['orderId']);
        } catch (ServiceException $e) {
            throw new ServiceException($e->getMessage());
        }

        // Get the payment provider by slug and get the settings
        /** @var PaymentProvider $provider */
        $provider = $this->paymentProviderService->getProviderBySlug($provider);
        $providerSettings = $provider->settings()->toArray();

        // Merge the provider settings with the request params
        $params = array_merge($providerSettings, $params);

        // Generate payment service instance by provider
        $paymentService = PaymentHandler::make($provider->slug);

        // add user email to the params
        $params['email'] = $transaction->user?->email;

        // Call the callback method
        try {
            $status = $paymentService->callback($params);
        } catch (PaymentServiceException $e) {
            Log::error('Payment callback failed', [
                'provider' => $provider->slug,
                'raw_response' => json_encode($e->getMessage()),
            ]);

            $this->transactionService->updateTransaction($transaction->id, [
                'status' => PaymentStatusEnum::FAILED->value,
                'raw_response' => json_encode($e->getMessage()),
            ]);

            throw new ServiceException($e->getMessage());
        }

        // Update transaction status for success
        if (!$status) {
            $this->transactionService->updateTransaction($transaction->id, [
                'status' => PaymentStatusEnum::FAILED->value
            ]);

            throw new ServiceException('Payment callback failed');
        }

        // Update transaction status for success
        $this->transactionService->updateTransaction($transaction->id, [
            'status' => PaymentStatusEnum::SUCCESS->value
        ]);
    }
}
