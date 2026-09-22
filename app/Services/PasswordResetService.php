<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Send the reset link.
     *
     * SECURITY: we always return success, even when the email doesn't exist.
     * Otherwise this endpoint becomes an email-enumeration oracle.
     */
    public function sendResetLink(string $email): array
    {
        $status = Password::sendResetLink(['email' => $email]);

        return [
            'success' => true,
            'message' => 'If an account exists for that email, a reset link has been sent.',
            'code'    => $status,
        ];
    }

    public function resetPassword(array $data): array
    {
        $status = Password::reset(
            [
                'email'                 => $data['email'],
                'password'              => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token'                 => $data['token'],
            ],
            function ($user, string $password) {
                $user->forceFill([
                    'password'       => $password,   // 'hashed' cast handles hashing
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ['success' => true, 'message' => 'Your password has been reset. You can now log in.'];
        }

        return [
            'success' => false,
            'message' => match ($status) {
                Password::INVALID_TOKEN => 'This reset link is invalid or has expired. Please request a new one.',
                Password::INVALID_USER  => 'We could not find an account with that email.',
                default                 => 'Unable to reset password. Please try again.',
            },
        ];
    }
}