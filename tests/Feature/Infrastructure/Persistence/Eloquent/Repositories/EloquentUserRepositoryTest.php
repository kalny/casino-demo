<?php

namespace Tests\Feature\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Common\ValueObjects\Email;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Infrastructure\Persistence\Eloquent\Models\User as UserEloquentModel;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentUserRepository();
    }

    public function testExistsByEmailTrue(): void
    {
        $userEloquentModel = UserEloquentModel::factory()->create();

        $this->assertTrue(
            $this->repository->existsByEmail($userEloquentModel->email->getValue())
        );
    }

    public function testExistsByEmailFalse(): void
    {
        $this->assertFalse(
            $this->repository->existsByEmail('wrongemail@example.com')
        );
    }

    public function testSave(): void
    {
        $user = new User(
            id: UserId::fromString('id'),
            name:'Test User',
            email: Email::fromString('testuser@example.com'),
            password: 'password',
            balance: 0
        );

        $this->repository->save($user);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'balance' => 0
        ]);
    }

    public function testFindByEmail(): void
    {
        $userEloquentModel = UserEloquentModel::factory()->create([
            'email' => Email::fromString('testuser@example.com')
        ]);

        $user = $this->repository->findByEmail($userEloquentModel->email->getValue());

        $this->assertSame($userEloquentModel->id, $user->getId()->getValue());
    }

    public function testFindByEmailNotFound(): void
    {
        $user = $this->repository->findByEmail('wrongemail@example.com');

        $this->assertNull($user);
    }

    public function testGetById(): void
    {
        $userEloquentModel = UserEloquentModel::factory()->create();

        $user = $this->repository->getById(UserId::fromString($userEloquentModel->id));

        $this->assertSame($userEloquentModel->id, $user->getId()->getValue());
    }

    public function testGetByIdFailed(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->getById(UserId::fromString('id'));
    }
}
