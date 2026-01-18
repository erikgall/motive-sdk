<?php

namespace Motive\Tests\Unit\Client;

use Motive\Client\Response;
use Motive\Client\MotiveClient;
use Motive\Client\PendingRequest;
use Orchestra\Testbench\TestCase;
use Motive\Contracts\Authenticator;
use Illuminate\Support\Facades\Http;
use Motive\Exceptions\ServerException;
use PHPUnit\Framework\Attributes\Test;
use Motive\Exceptions\NotFoundException;
use Motive\Exceptions\RateLimitException;
use Motive\Exceptions\ValidationException;
use Motive\Exceptions\AuthorizationException;
use Motive\Exceptions\AuthenticationException;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_applies_authentication_to_requests(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response(['vehicles' => []], 200),
        ]);

        $authenticator = $this->createMock(Authenticator::class);
        $authenticator->expects($this->once())
            ->method('authenticate')
            ->willReturnCallback(function (PendingRequest $request) {
                return $request->withHeader('X-Api-Key', 'test-key');
            });

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 3,
            retrySleep: 100
        );

        $client->get('/v1/vehicles');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key', 'test-key');
        });
    }

    #[Test]
    public function it_creates_pending_request(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 3,
            retrySleep: 100
        );

        $request = $client->createRequest();

        $this->assertInstanceOf(PendingRequest::class, $request);
        $this->assertEquals('https://api.gomotive.com', $request->getBaseUrl());
    }

    #[Test]
    public function it_sends_delete_request(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response([], 204),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 3,
            retrySleep: 100
        );

        $response = $client->delete('/v1/vehicles/1');

        $this->assertEquals(204, $response->status());
    }

    #[Test]
    public function it_sends_get_request(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response(['vehicles' => []], 200),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 3,
            retrySleep: 100
        );

        $response = $client->get('/v1/vehicles');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['vehicles' => []], $response->json());
    }

    #[Test]
    public function it_sends_get_request_with_query_params(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles*' => Http::response(['vehicles' => []], 200),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 3,
            retrySleep: 100
        );

        $response = $client->get('/v1/vehicles', ['page' => 1, 'per_page' => 25]);

        $this->assertEquals(200, $response->status());
    }

    #[Test]
    public function it_sends_patch_request(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response(['vehicle' => ['id' => 1]], 200),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 3,
            retrySleep: 100
        );

        $response = $client->patch('/v1/vehicles/1', ['number' => 'TRUCK-002']);

        $this->assertEquals(200, $response->status());
    }

    #[Test]
    public function it_sends_post_request(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response(['vehicle' => ['id' => 1]], 201),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 3,
            retrySleep: 100
        );

        $response = $client->post('/v1/vehicles', ['number' => 'TRUCK-001']);

        $this->assertEquals(201, $response->status());
        $this->assertEquals(['vehicle' => ['id' => 1]], $response->json());
    }

    #[Test]
    public function it_sends_put_request(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response(['vehicle' => ['id' => 1]], 200),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 3,
            retrySleep: 100
        );

        $response = $client->put('/v1/vehicles/1', ['number' => 'TRUCK-002']);

        $this->assertEquals(200, $response->status());
    }

    #[Test]
    public function it_throws_authentication_exception_on_401(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 0,
            retrySleep: 0
        );

        $this->expectException(AuthenticationException::class);

        $client->get('/v1/vehicles');
    }

    #[Test]
    public function it_throws_authorization_exception_on_403(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response(['error' => 'Forbidden'], 403),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 0,
            retrySleep: 0
        );

        $this->expectException(AuthorizationException::class);

        $client->get('/v1/vehicles');
    }

    #[Test]
    public function it_throws_not_found_exception_on_404(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/999' => Http::response(['error' => 'Not found'], 404),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 0,
            retrySleep: 0
        );

        $this->expectException(NotFoundException::class);

        $client->get('/v1/vehicles/999');
    }

    #[Test]
    public function it_throws_rate_limit_exception_on_429(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response(['error' => 'Too many requests'], 429, [
                'Retry-After' => '60',
            ]),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 0,
            retrySleep: 0
        );

        $this->expectException(RateLimitException::class);

        $client->get('/v1/vehicles');
    }

    #[Test]
    public function it_throws_server_exception_on_5xx(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response(['error' => 'Internal server error'], 500),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 0,
            retrySleep: 0
        );

        $this->expectException(ServerException::class);

        $client->get('/v1/vehicles');
    }

    #[Test]
    public function it_throws_validation_exception_on_422(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response([
                'errors' => ['number' => ['The number field is required.']],
            ], 422),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 30,
            retryTimes: 0,
            retrySleep: 0
        );

        $this->expectException(ValidationException::class);

        $client->post('/v1/vehicles', []);
    }

    #[Test]
    public function it_uses_configured_timeout(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response(['vehicles' => []], 200),
        ]);

        $authenticator = $this->createStub(Authenticator::class);
        $authenticator->method('authenticate')->willReturnArgument(0);

        $client = new MotiveClient(
            baseUrl: 'https://api.gomotive.com',
            authenticator: $authenticator,
            timeout: 60,
            retryTimes: 3,
            retrySleep: 100
        );

        $request = $client->createRequest();

        $this->assertEquals(60, $request->getTimeout());
    }
}
