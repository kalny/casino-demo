<?php

namespace App\Services\Auth;

use App\DTO\Api\Auth\LoginDTO;
use App\DTO\Api\Auth\RegisterDTO;
use App\Models\User;
use App\Services\Auth\DTO\UserDTO;
use App\Services\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(RegisterDTO $registerDTO): UserDTO
    {
        /** @var User $user */
        $user = User::create([
            'name' => $registerDTO->name,
            'email' => $registerDTO->email,
            'password' => Hash::make($registerDTO->password),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return new UserDTO(
            name: $user->name,
            email: $user->email,
            token: $token,
        );
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function login(LoginDTO $loginDTO): UserDTO
    {
        $user = User::query()
            ->where('email', $loginDTO->email)
            ->first();

        if(!$user || !Hash::check($loginDTO->password, $user->password)) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        $token = $user->createToken('api')->plainTextToken;

        return new UserDTO(
            name: $user->name,
            email: $user->email,
            token: $token,
        );
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
