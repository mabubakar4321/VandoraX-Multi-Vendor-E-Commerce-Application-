<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class CustomerAuthService extends AuthService
{
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
    }

    public function registerCustomer(array $data): array
    {
        if ($this->userRepository->emailExists($data['email'])) {
            return $this->fail('This email is already registered.');
        }

        return DB::transaction(function () use ($data) {
            $user = $this->createUser([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => $data['password'],
                'phone_number'      => $data['phone_number'] ?? null,
                'is_active'         => true,
                'status'            => 'active',
                'email_verified_at' => null,
            ]);

            $user->assignRole('customer');
            $user->sendEmailVerificationNotification();

            return $this->ok(
                'Account created. Please check your email to verify your address.',
                ['user' => $user]
            );
        });
    }

    public function loginCustomer(string $email, string $password, bool $remember = false): array
    {
        $check = $this->validateLogin($email, $password);

        if (! $check['success']) {
            return $check;
        }

        $user = $check['user'];

        if (! $user->hasRole('customer')) {
            return $this->fail('These credentials do not match our records.', 'wrong_portal');
        }

        if (! $user->email_verified_at) {
            return $this->fail(
                'Please verify your email address. Check your inbox for the link.',
                'unverified',
                ['user' => $user]
            );
        }

        $this->startSession($user, $remember);

        return $this->ok('Welcome back, ' . $user->name, ['user' => $user]);
    }

    public function logoutCustomer(): array
    {
        return $this->endSession();
    }
}