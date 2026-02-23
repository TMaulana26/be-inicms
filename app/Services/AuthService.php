<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Verified;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role if exists
        if (\Spatie\Permission\Models\Role::where('name', 'user')->exists()) {
            $user->assignRole('user');
        }

        event(new Registered($user));

        $expiresAt = now()->addMinutes(config('sanctum.expiration', 60));
        $token = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;

        return [
            'user' => $user->load('roles', 'permissions'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toDateTimeString(),
        ];
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive.'],
            ]);
        }

        if ($user->two_factor_confirmed_at) {
            $expiresAt = now()->addMinutes(10); // 10 mins strictly for completing the 2FA challenge
            $token = $user->createToken('auth_token', ['2fa'], $expiresAt)->plainTextToken;

            return [
                'requires_2fa' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toDateTimeString(),
            ];
        }

        $remember = $credentials['remember'] ?? false;

        if ($remember) {
            // "Remember Me": Token expires in 1 year
            $expiresAt = now()->addYear();
        } else {
            // Normal Login: Token expires based on config (default 60 mins)
            $expiresAt = now()->addMinutes(config('sanctum.expiration', 60));
        }

        $token = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;

        return [
            'user' => $user->load('roles', 'permissions'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toDateTimeString(),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function verifyEmail(User $user, string $hash): bool
    {
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return false;
        }

        if ($user->hasVerifiedEmail()) {
            return true;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            return true;
        }

        return false;
    }

    public function resendVerificationEmail(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->sendEmailVerificationNotification();
        return true;
    }
}
