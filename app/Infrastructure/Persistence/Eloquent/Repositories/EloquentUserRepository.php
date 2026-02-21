<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\User;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\UserId;
use App\Infrastructure\Persistence\Eloquent\Models\User as UserEloquentModel;
use ReflectionClass;

class EloquentUserRepository implements UserRepository
{
    public function existsByEmail(string $email): bool
    {
        return UserEloquentModel::query()
            ->where('email', $email)
            ->exists();
    }

    public function save(User $user): void
    {
        $reflection = new ReflectionClass($user);

        $id = $this->getPrivateProperty($reflection, $user, 'id');

        $userModel = UserEloquentModel::updateOrCreate(
            ['id' => $id->getValue()],
            [
                'name' => $this->getPrivateProperty($reflection, $user, 'name'),
                'email' => $this->getPrivateProperty($reflection, $user, 'email'),
                'password' => $this->getPrivateProperty($reflection, $user, 'password'),
                'balance' => $this->getPrivateProperty($reflection, $user, 'balance'),
            ]
        );

        $this->setPrivateProperty($reflection, $user, 'id', new UserId($userModel->id));
    }

    private function getPrivateProperty(ReflectionClass $ref, object $object, string $propertyName): mixed
    {
        $property = $ref->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    private function setPrivateProperty(ReflectionClass $ref, object $object, string $propertyName, mixed $value): void
    {
        $property = $ref->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    public function findByEmail(string $email): ?User
    {
        $userEloquentModel = UserEloquentModel::query()
            ->where('email', $email)
            ->first();

        if (!$userEloquentModel) {
            return null;
        }

        return new User(
            id: new UserId($userEloquentModel->id),
            name: $userEloquentModel->name,
            email: $userEloquentModel->email,
            password: $userEloquentModel->password,
            balance: $userEloquentModel->balance,
        );
    }

    public function getById(UserId $id): User
    {
        $userEloquentModel = UserEloquentModel::findOrFail($id->getValue());

        return new User(
            id: $id,
            name: $userEloquentModel->name,
            email: $userEloquentModel->email,
            password: $userEloquentModel->password,
            balance: $userEloquentModel->balance,
        );
    }
}
