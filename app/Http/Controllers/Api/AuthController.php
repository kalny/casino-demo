<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\UserResource;
use App\Services\Auth\AuthService;
use App\Services\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService): UserResource
    {
        $userDTO = $authService->register($request->getDTO());

        return new UserResource($userDTO);
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function login(LoginRequest $request, AuthService $authService): UserResource
    {
        $userDTO = $authService->login($request->getDTO());

        return new UserResource($userDTO);
    }

    public function logout(AuthService $authService): JsonResponse
    {
        $authService->logout(auth()->user());

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
