<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
        $result['user'] = new UserResource($result['user']);

        return $this->successResponse($result, 'User registered successfully.', 201);
    }

    /**
     * Log in a user and return a token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        // Wrap user in resource if it's a full login (not a 2fa challenge request)
        if (isset($result['user'])) {
            $result['user'] = new UserResource($result['user']);
        }

        return $this->successResponse($result, 'User logged in successfully.');
    }

    /**
     * Log out the current user by revoking the token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(null, 'User logged out successfully.');
    }

    /**
     * Get the authenticated user's details.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->currentAccessToken();

        return $this->successResponse([
            'user' => new UserResource($user->load('roles', 'permissions')),
            'expires_at' => $token->expires_at ? $token->expires_at->toDateTimeString() : null,
        ], 'Authenticated user details.');
    }

    /**
     * Verify the user's email address.
     *
     * @queryParam expires integer required The expiration timestamp of the verification link.
     * @queryParam signature string required The cryptographic signature validating the link.
     */
    public function verifyEmail(\App\Http\Requests\Auth\VerifyEmailRequest $request, $id, $hash): JsonResponse
    {
        $user = User::withoutGlobalScopes()->findOrFail($id);

        // This validates the signature timestamp
        if (! $request->hasValidSignature()) {
            return $this->errorResponse('Invalid or expired verification link.', 403);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse('Email is already verified.', 400);
        }

        if ($this->authService->verifyEmail($user, $hash)) {
            return $this->successResponse(null, 'Email verified successfully.');
        }

        return $this->errorResponse('Email could not be verified.', 500);
    }

    /**
     * Resend the email verification notification.
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($this->authService->resendVerificationEmail($user)) {
            return $this->successResponse(null, 'Verification email sent.');
        }

        return $this->errorResponse('Email is already verified.', 400);
    }
}
