<?php

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TransactionRepository extends BaseRepository
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    /**
     * @throws RepositoryException
     */
    public function getByOrderId(string $orderId): Model
    {
        try {
            $transaction = $this->model->where('order_id', $orderId)->first();
        } catch (\Exception $e) {
            Log::error('TransactionRepository@getByOrderId: ' . $e->getMessage());

            throw new RepositoryException('An error occurred while creating data');
        }

        if (! $transaction) {
            throw new RepositoryException('Transaction not found');
        }

        return $transaction;
    }
}
