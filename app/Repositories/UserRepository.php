<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()
            ->where('email', $email)
            ->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return $this->query()
            ->where('phone_number', $phone)
            ->first();
    }

    public function getUsersByRole(string $role): Collection
    {
        return $this->query()
            ->role($role)
            ->get();
    }

    public function paginateByRole(
        string $role,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->query()
            ->role($role)
            ->latest()
            ->paginate($perPage);
    }

    public function emailExists(
        string $email,
        $excludeId = null
    ): bool {
        return $this->query()
            ->where('email', $email)
            ->when(
                $excludeId,
                fn ($q) => $q->where('id', '!=', $excludeId)
            )
            ->exists();
    }

    public function phoneExists(
        string $phone,
        $excludeId = null
    ): bool {
        return $this->query()
            ->where('phone_number', $phone)
            ->when(
                $excludeId,
                fn ($q) => $q->where('id', '!=', $excludeId)
            )
            ->exists();
    }

    public function activateUser($id): ?User
    {
        return $this->update($id, [
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    public function deactivateUser($id): ?User
    {
        return $this->update($id, [
            'is_active' => false,
            'status' => 'inactive',
        ]);
    }

    public function verifyEmail($id): ?User
    {
        return $this->update($id, [
            'email_verified_at' => now(),
        ]);
    }

    public function recordLogin($id): ?User
    {
        return $this->update($id, [
            'last_login_at' => now(),
        ]);
    }

    public function updatePassword(
        $id,
        string $plainPassword
    ): ?User {
        return $this->update($id, [
            'password' => $plainPassword,
        ]);
    }
}