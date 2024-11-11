<?php

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @throws RepositoryException
     */
    public function getById(int $id): ?Model
    {
        try {
            return $this->model->newQuery()->find($id);
        } catch (\Exception $exception) {
            $tableName = $this->model->getTable();
            Log::error($tableName.' BaseRepository@getById: '.$exception->getMessage());

            throw new RepositoryException('An error occurred while fetching data');
        }
    }

    /**
     * @throws RepositoryException
     */
    public function get($query = [], $columns = ['*'], $paginate = []): LengthAwarePaginator|Collection
    {
        try {
            $modelQuery = $this->model->newQuery();

            foreach ($query as $key => $value) {
                $modelQuery->where($key, $value);
            }

            if (! empty($paginate)) {
                return $modelQuery->paginate($paginate['limit'], $columns, 'page', $paginate['page']);
            }

            return $modelQuery->get($columns);
        } catch (\Exception $e) {
            $tableName = $this->model->getTable();
            Log::error($tableName.' BaseRepository@get: '.$e->getMessage());

            throw new RepositoryException('An error occurred while fetching data');
        }
    }

    /**
     * @throws RepositoryException
     */
    public function create(array $data): Model
    {
        try {
            return $this->model->newQuery()->create($data);
        } catch (\Exception $e) {
            $tableName = $this->model->getTable();
            Log::error($tableName.' BaseRepository@create: '.$e->getMessage());

            throw new RepositoryException('An error occurred while creating data');
        }
    }

    /**
     * @throws RepositoryException
     */
    public function update(int $id, array $data): bool
    {
        try {
            return (bool) $this->model->newQuery()
                ->where('id', $id)
                ->update($data);
        } catch (\Exception $e) {
            $tableName = $this->model->getTable();
            Log::error($tableName.' BaseRepository@update: '.$e->getMessage());

            throw new RepositoryException('An error occurred while updating data');
        }
    }

    /**
     * @throws RepositoryException
     */
    public function delete(int $id): bool
    {
        try {
            return (bool) $this->model->newQuery()->where('id', $id)->delete();
        } catch (\Exception $e) {
            $tableName = $this->model->getTable();
            Log::error($tableName.' BaseRepository@delete: '.$e->getMessage());

            throw new RepositoryException('An error occurred while deleting data');
        }
    }

    /**
     * @throws RepositoryException
     */
    public function exists(string $column, string $value): bool
    {
        try {
            return $this->model->newQuery()
                ->where($column, $value)
                ->exists();
        } catch (\Exception $e) {
            $tableName = $this->model->getTable();
            Log::error($tableName.' BaseRepository@exists: '.$e->getMessage());

            throw new RepositoryException('An error occurred while checking if data exists');
        }
    }
}
