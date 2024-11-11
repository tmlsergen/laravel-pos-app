<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Exceptions\RepositoryException;
use App\Exceptions\ServiceException;
use App\Models\User;
use App\Repositories\TransactionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

readonly class TransactionService
{
    public function __construct(private TransactionRepository $transactionRepository) {}

    /**
     * @throws ServiceException
     */
    public function getTransactions(): Collection|LengthAwarePaginator
    {
        $filters = [];

        /** @var User $user */
        $user = auth()->user();
        if (! $user->hasRole([RoleEnum::ADMIN->value, RoleEnum::SUPPORT->value])) {
            $filters['user_id'] = $user->id;
        }

        try {
            return Cache::remember('transactions_' . $user->id, now()->addMinute(), function () use ($filters) {
                return $this->transactionRepository->get($filters);
            });
        } catch (RepositoryException $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }

    /**
     * @throws ServiceException
     */
    public function createTransaction(array $data): Model
    {
        try {
            return $this->transactionRepository->create($data);
        } catch (RepositoryException $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }

    /**
     * @throws ServiceException
     */
    public function updateTransaction(int $id, array $data): bool
    {
        try {
            Cache::delete('transactions_' . auth()->id());

            return $this->transactionRepository->update($id, $data);
        } catch (RepositoryException $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }

    /**
     * @throws ServiceException
     */
    public function getByOrderId(string $orderId): Model
    {
        try {
            return $this->transactionRepository->getByOrderId($orderId);
        } catch (RepositoryException $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }
}
