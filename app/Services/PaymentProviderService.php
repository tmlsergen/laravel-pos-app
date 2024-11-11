<?php

namespace App\Services;

use App\Exceptions\RepositoryException;
use App\Exceptions\ServiceException;
use App\Repositories\PaymentProviderRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

readonly class PaymentProviderService
{
    public function __construct(private readonly PaymentProviderRepository $paymentProviderRepository) {}

    /**
     * @throws ServiceException
     */
    public function getProviderById(int $id): Model
    {
        try {
            $provider = $this->paymentProviderRepository->getById($id);
            if (! $provider) {
                throw new ServiceException('Provider not found');
            }

            return $provider;
        } catch (RepositoryException $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }

    /**
     * @throws ServiceException
     */
    public function getProviderBySlug(string $slug): Model
    {
        try {
            $provider = $this->paymentProviderRepository->getProviderBySlug($slug);
            if (! $provider) {
                throw new ServiceException('Provider not found');
            }

            return $provider;
        } catch (RepositoryException $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }

    /**
     * @throws ServiceException
     */
    public function getProviders(): Collection
    {
        try {
            return $this->paymentProviderRepository->get();
        } catch (RepositoryException $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }

    /**
     * @throws ServiceException
     */
    public function changePosStatus(string $providerSlug, bool $status): bool
    {
        try {
            $provider = $this->paymentProviderRepository->getProviderBySlug($providerSlug);
            if (! $provider) {
                throw new ServiceException('Provider not found');
            }

            return $this->paymentProviderRepository->update($provider->id, ['status' => $status]);
        } catch (RepositoryException $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }
}
