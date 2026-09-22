<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

abstract class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Validate login credentials and account status.
     */
    protected function validateLogin(
        string $email,
        string $password
    ): array {
        $user = $this->userRepository->findByEmail($email);

        if (! $user) {
            return $this->fail(
                'These credentials do not match our records.',
                'user_not_found'
            );
        }

        if (! Hash::check($password, $user->password)) {
            return $this->fail(
                'These credentials do not match our records.',
                'bad_password'
            );
        }

        if (! $user->is_active || $user->status === 'suspended') {
            return $this->fail(
                'Your account has been deactivated. Please contact support.',
                'inactive'
            );
        }

        return $this->ok(
            'Credentials valid',
            ['user' => $user]
        );
    }

    /**
     * Create a user.
     *
     * Password is NOT manually hashed here because
     * your User model contains:
     *
     * 'password' => 'hashed'
     */
    protected function createUser(array $data): User
    {
        return $this->userRepository->create([
            'name'              => $data['name'],
            'email'             => strtolower(trim($data['email'])),
            'password'          => $data['password'],
            'phone_number'      => $data['phone_number'] ?? null,
            'is_active'         => $data['is_active'] ?? true,
            'status'            => $data['status'] ?? 'active',
            'email_verified_at' => $data['email_verified_at'] ?? null,
        ]);
    }

    /**
     * Login user and regenerate session.
     */
    protected function startSession(
        User $user,
        bool $remember = false
    ): void {
        Auth::login($user, $remember);

        session()->regenerate();

        $this->userRepository->recordLogin($user->id);
    }

    /**
     * Logout user.
     */
    protected function endSession(): array
    {
        Auth::logout();

        session()->invalidate();

        session()->regenerateToken();

        return $this->ok('You have been logged out.');
    }

    public function getCurrentUser(): ?User
    {
        return Auth::user();
    }

    public function hasRole($user, string $role): bool
    {
        return $user && $user->hasRole($role);
    }

    /**
     * Successful response.
     */
    protected function ok(
        string $message,
        array $extra = []
    ): array {
        return array_merge(
            [
                'success' => true,
                'message' => $message,
                'code' => null,
            ],
            $extra
        );
    }

    /**
     * Failed response.
     */
    protected function fail(
        string $message,
        ?string $code = null,
        array $extra = []
    ): array {
        return array_merge(
            [
                'success' => false,
                'message' => $message,
                'code' => $code,
                'user' => null,
            ],
            $extra
        );
    }
}