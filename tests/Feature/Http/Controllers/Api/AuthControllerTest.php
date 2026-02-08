<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Already Exists',
            'email' => 'alreadyexisis@example.com'
        ]);
    }

    public function testRegister(): void
    {
        $userName = 'Test User';
        $userEmail = 'testuser@example.com';
        $userPassword = 'password';

        $payload = [
            'name' => $userName,
            'email' => $userEmail,
            'password' => $userPassword,
        ];

        $response = $this->postJson(route('api.auth.register'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('data.user.name', $userName);
        $response->assertJsonPath('data.user.email', $userEmail);

        $user = User::query()
            ->where('email', $userEmail)
            ->where('name', $userName)
            ->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check($userPassword, $user->password));
    }

    #[dataProvider('registerDataProvider')]
    public function testRegisterValidation(array $payload, array $errors): void
    {
        $response = $this->postJson(route('api.auth.register'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($errors);
    }

    public function testLogin(): void
    {
        $userEmail = 'alreadyexisis@example.com';
        $userPassword = 'password';

        $payload = [
            'email' => $userEmail,
            'password' => $userPassword,
        ];

        $response = $this->postJson(route('api.auth.login'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('data.user.name', 'Already Exists');
        $response->assertJsonPath('data.user.email', $userEmail);

        [$id, $plainToken] = explode('|', $response->json('data.token'));
        $model = PersonalAccessToken::find($id);

        $this->assertTrue(
            hash_equals($model->token, hash('sha256', $plainToken))
        );
    }

    public function testLoginFailed(): void
    {
        $userEmail = 'incorrect@example.com';
        $userPassword = 'password';

        $payload = [
            'email' => $userEmail,
            'password' => $userPassword,
        ];

        $response = $this->postJson(route('api.auth.login'), $payload);
        $response->assertStatus(401);
    }

    #[dataProvider('loginDataProvider')]
    public function testLoginValidation(array $payload, array $errors): void
    {
        $response = $this->postJson(route('api.auth.login'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($errors);
    }

    public function testLogout(): void
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson(route('api.auth.logout'));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Logged out');

        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    public function testLogoutFailed(): void
    {
        $response = $this->postJson(route('api.auth.logout'));

        $response->assertStatus(401);
    }

    public static function registerDataProvider(): array
    {
        return [
            [
                'payload' => [],
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'email' => [
                        'The email field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ],
            [
                'payload' => [
                    'name' => Str::random(256),
                    'email' => 'testuser@example.com',
                    'password' => 'password',
                ],
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 255 characters.'
                    ],
                ]
            ],
            [
                'payload' => [
                    'name' => 'Test User',
                    'email' => 'testuser',
                    'password' => 'password',
                ],
                'errors' => [
                    'email' => [
                        'The email field must be a valid email address.'
                    ],
                ]
            ],
            [
                'payload' => [
                    'name' => 'Test User',
                    'email' => 'alreadyexisis@example.com',
                    'password' => 'password',
                ],
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ],
                ]
            ],
            [
                'payload' => [
                    'name' => 'Test User',
                    'email' => 'testuser@example.com',
                    'password' => 'pass',
                ],
                'errors' => [
                    'password' => [
                        'The password field must be at least 6 characters.'
                    ],
                ]
            ]
        ];
    }

    public static function loginDataProvider(): array
    {
        return [
            [
                'payload' => [],
                'errors' => [
                    'email' => [
                        'The email field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ],
            [
                'payload' => [
                    'email' => 'testuser',
                    'password' => 'password',
                ],
                'errors' => [
                    'email' => [
                        'The email field must be a valid email address.'
                    ],
                ]
            ]
        ];
    }
}
