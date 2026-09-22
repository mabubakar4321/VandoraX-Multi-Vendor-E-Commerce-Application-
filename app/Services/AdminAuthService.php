<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class AdminAuthService extends AuthService
{
    public function __construct(
        UserRepository $userRepository
    ) {
        parent::__construct($userRepository);
    }

    /**
     * Create an admin user.
     */
    public function registerAdmin(array $data): array
    {
        if ($this->userRepository->emailExists($data['email'])) {
            return $this->fail('Email already exists.');
        }

        return DB::transaction(function () use ($data) {

            $user = $this->createUser([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => $data['password'],
                'phone_number'      => $data['phone_number'] ?? null,
                'is_active'         => true,
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);

            $user->assignRole('admin');

            return $this->ok(
                'Admin created successfully.',
                ['user' => $user]
            );
        });
    }

    /**
     * Admin login.
     */
    public function loginAdmin(
        string $email,
        string $password,
        bool $remember = false
    ): array {
        $check = $this->validateLogin($email, $password);

        if (! $check['success']) {
            return $check;
        }

        $user = $check['user'];

        if (! $user->hasRole('admin')) {
            return $this->fail(
                'These credentials do not match our records.',
                'wrong_portal'
            );
        }

        $this->startSession($user, $remember);

        return $this->ok(
            'Welcome back, ' . $user->name,
            ['user' => $user]
        );
    }

    /**
     * Admin logout.
     */
    public function logoutAdmin(): array
    {
        return $this->endSession();
    }
}