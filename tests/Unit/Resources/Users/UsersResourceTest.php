<?php

namespace Motive\Tests\Unit\Resources\Users;

use Motive\Data\User;
use Motive\Enums\UserRole;
use Motive\Client\Response;
use Motive\Enums\UserStatus;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Users\UsersResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class UsersResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new UsersResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/users', $resource->fullPath());
        $this->assertSame('/v1/users/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_creates_user(): void
    {
        $userData = [
            'id'         => 123,
            'company_id' => 456,
            'email'      => 'new@example.com',
            'first_name' => 'Jane',
            'last_name'  => 'Smith',
        ];

        $response = $this->createMockResponse(['user' => $userData], 201);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/users', ['user' => [
                'email'      => 'new@example.com',
                'first_name' => 'Jane',
                'last_name'  => 'Smith',
            ]])
            ->willReturn($response);

        $resource = new UsersResource($client);
        $user = $resource->create([
            'email'      => 'new@example.com',
            'first_name' => 'Jane',
            'last_name'  => 'Smith',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('new@example.com', $user->email);
    }

    #[Test]
    public function it_deactivates_user(): void
    {
        $userData = [
            'id'     => 123,
            'email'  => 'user@example.com',
            'status' => 'inactive',
        ];

        $response = $this->createMockResponse(['user' => $userData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/users/123/deactivate')
            ->willReturn($response);

        $resource = new UsersResource($client);
        $result = $resource->deactivate(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_deletes_user(): void
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('status')->willReturn(204);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('/v1/users/123')
            ->willReturn($response);

        $resource = new UsersResource($client);
        $result = $resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_user_by_id(): void
    {
        $userData = [
            'id'         => 123,
            'company_id' => 456,
            'email'      => 'user@example.com',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'role'       => 'admin',
            'status'     => 'active',
        ];

        $response = $this->createMockResponse(['user' => $userData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/users/123')
            ->willReturn($response);

        $resource = new UsersResource($client);
        $user = $resource->find(123);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(123, $user->id);
        $this->assertSame('user@example.com', $user->email);
        $this->assertSame(UserRole::Admin, $user->role);
        $this->assertSame(UserStatus::Active, $user->status);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new UsersResource($this->createStub(MotiveClient::class));

        $this->assertSame('users', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new UsersResource($this->createStub(MotiveClient::class));

        $this->assertSame('user', $resource->getResourceKey());
    }

    #[Test]
    public function it_reactivates_user(): void
    {
        $userData = [
            'id'     => 123,
            'email'  => 'user@example.com',
            'status' => 'active',
        ];

        $response = $this->createMockResponse(['user' => $userData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/users/123/reactivate')
            ->willReturn($response);

        $resource = new UsersResource($client);
        $result = $resource->reactivate(123);

        $this->assertTrue($result);
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}
