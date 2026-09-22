<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    public function all();

    public function find($id);

    public function findOrFail($id);

    public function findBy(string $column, $value);

    public function where(array $conditions);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function paginate(int $perPage = 15);
}