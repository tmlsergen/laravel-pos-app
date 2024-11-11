<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionCollection;
use App\Services\TransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly TransactionService $transactionService) {}

    public function index(): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getTransactions();

            return $this->successResponse(data: new TransactionCollection($transactions));
        } catch (ServiceException $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
