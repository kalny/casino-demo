<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCase\LoginUser\LoginUserHandler;
use App\Application\UseCase\LogoutUser\LogoutUserHandler;
use App\Application\UseCase\RegisterUser\RegisterUserHandler;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Exceptions\InvalidCredentialsException;
use App\Domain\Exceptions\UserAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\UserResource;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * @throws UserAlreadyExistsException
     * @throws InvalidArgumentException
     */
    public function register(RegisterRequest $request, RegisterUserHandler $handler): UserResource
    {
        $userResponse = $handler->handle($request->toCommand());
        return new UserResource($userResponse);
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function login(LoginRequest $request, LoginUserHandler $handler): UserResource
    {
        $userResponse = $handler->handle($request->toCommand());
        return new UserResource($userResponse);
    }

    public function logout(LogoutUserHandler $handler): JsonResponse
    {
        $handler->handle(auth()->user()->id);

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
