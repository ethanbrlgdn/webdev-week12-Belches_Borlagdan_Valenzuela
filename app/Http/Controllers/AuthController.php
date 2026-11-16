<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return $this->tokenResponse($user, false, 201);
    }

    // Send "read_only": true to get a token that can look but not change.
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw new AuthenticationException('Invalid email or password.');
        }

        return $this->tokenResponse($user, $request->boolean('read_only'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    private function tokenResponse(User $user, bool $readOnly, int $status = 200): JsonResponse
    {
        $abilities = $readOnly ? ['books:read'] : ['books:read', 'books:write'];
        $token = $user->createToken($readOnly ? 'read-only' : 'full-access', $abilities);

        return response()->json([
            'message' => $status === 201 ? 'Registered.' : 'Logged in.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
                'abilities' => $abilities,
            ],
        ], $status);
    }
}