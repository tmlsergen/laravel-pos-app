<?php

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use App\Models\PaymentProvider;
use Illuminate\Support\Facades\Log;

class PaymentProviderRepository extends BaseRepository
{
    public function __construct(PaymentProvider $model)
    {
        parent::__construct($model);
    }

    /**
     * @throws RepositoryException
     */
    public function getProviderBySlug(string $slug): ?PaymentProvider
    {
        try {
            return $this->model->newQuery()->where('slug', $slug)->first();
        } catch (\Exception $e) {
            Log::error('PaymentProviderRepository@getProviderBySlug slug: '.$slug.' - '.$e->getMessage());

            throw new RepositoryException('Error on getProviderBySlug', 0, $e);
        }
    }
}
