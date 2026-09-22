<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function query()
    {
        return $this->model->newQuery();
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function find($id): ?Model
    {
        return $this->query()->find($id);
    }

    public function findOrFail($id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function findBy(string $column, $value): ?Model
    {
        return $this->query()
            ->where($column, $value)
            ->first();
    }

    public function where(array $conditions): Collection
    {
        return $this->query()
            ->where($conditions)
            ->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): ?Model
    {
        $record = $this->find($id);

        if (! $record) {
            return null;
        }

        $record->update($data);

        return $record->fresh();
    }

    public function delete($id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->latest()
            ->paginate($perPage);
    }
}