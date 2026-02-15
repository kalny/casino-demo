<?php

namespace Tests\Feature\Infrastructure\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Common\ValueObjects\Email;
use App\Domain\Exceptions\InvalidArgumentException;
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

    /**
     * @throws InvalidArgumentException
     */
    public function testSave(): void
    {
        $user = new User(
            id: new UserId(1),
            name:'Test User',
            email: new Email('testuser@example.com'),
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
            'email' => new Email('testuser@example.com')
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

        $user = $this->repository->getById($userEloquentModel->id);

        $this->assertSame($userEloquentModel->id, $user->getId()->getValue());
    }

    public function testGetByIdFailed(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->getById(1);
    }
}
