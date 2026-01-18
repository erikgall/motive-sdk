<?php

namespace Motive\Tests\Feature;

use Motive\Data\User;
use Motive\Enums\UserRole;
use Motive\Facades\Motive;
use Motive\Tests\TestCase;
use Motive\Enums\UserStatus;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Users\UsersResource;

/**
 * Feature tests for UsersResource integration.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class UsersResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_creates_user_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/users' => Http::response([
                'user' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'email'      => 'john@example.com',
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                    'role'       => 'driver',
                    'status'     => 'active',
                    'created_at' => '2024-01-15T10:00:00Z',
                ],
            ], 201),
        ]);

        $user = Motive::users()->create([
            'email'      => 'john@example.com',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'role'       => 'driver',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('John', $user->firstName);
        $this->assertEquals(UserRole::Driver, $user->role);
        $this->assertEquals(UserStatus::Active, $user->status);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v1/users')
                && $request->method() === 'POST';
        });
    }

    #[Test]
    public function it_deactivates_user_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/users/1/deactivate' => Http::response([], 200),
        ]);

        $result = Motive::users()->deactivate(1);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v1/users/1/deactivate')
                && $request->method() === 'POST';
        });
    }

    #[Test]
    public function it_deletes_user_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/users/1' => Http::response([], 204),
        ]);

        $result = Motive::users()->delete(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_user_by_id_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/users/1' => Http::response([
                'user' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'email'      => 'john@example.com',
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                    'role'       => 'admin',
                    'status'     => 'active',
                ],
            ], 200),
        ]);

        $user = Motive::users()->find(1);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals(UserRole::Admin, $user->role);
    }

    #[Test]
    public function it_gets_users_resource_from_manager(): void
    {
        $resource = Motive::users();

        $this->assertInstanceOf(UsersResource::class, $resource);
    }

    #[Test]
    public function it_lists_users_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/users*' => Http::response([
                'users' => [
                    [
                        'id'         => 1,
                        'company_id' => 100,
                        'email'      => 'john@example.com',
                        'first_name' => 'John',
                        'last_name'  => 'Doe',
                        'role'       => 'driver',
                        'status'     => 'active',
                    ],
                    [
                        'id'         => 2,
                        'company_id' => 100,
                        'email'      => 'jane@example.com',
                        'first_name' => 'Jane',
                        'last_name'  => 'Smith',
                        'role'       => 'admin',
                        'status'     => 'active',
                    ],
                ],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 2,
                ],
            ], 200),
        ]);

        $users = Motive::users()->list();

        $this->assertCount(2, iterator_to_array($users));
    }

    #[Test]
    public function it_reactivates_user_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/users/1/reactivate' => Http::response([], 200),
        ]);

        $result = Motive::users()->reactivate(1);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v1/users/1/reactivate')
                && $request->method() === 'POST';
        });
    }

    #[Test]
    public function it_updates_user_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/users/1' => Http::response([
                'user' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'email'      => 'john.updated@example.com',
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                    'role'       => 'driver',
                    'status'     => 'active',
                ],
            ], 200),
        ]);

        $user = Motive::users()->update(1, ['email' => 'john.updated@example.com']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john.updated@example.com', $user->email);
    }
}
