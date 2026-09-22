<?php

namespace App\Services\Contracts;

interface AuthServiceInterface
{
    public function register(array $data): array;

    public function login(
        string $email,
        string $password,
        bool $remember = false
    ): array;

    public function logout(): array;
}