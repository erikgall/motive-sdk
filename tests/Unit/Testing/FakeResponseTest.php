<?php

namespace Motive\Tests\Unit\Testing;

use Motive\Client\Response;
use PHPUnit\Framework\TestCase;
use Motive\Testing\FakeResponse;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FakeResponseTest extends TestCase
{
    #[Test]
    public function it_can_get_data(): void
    {
        $fakeResponse = FakeResponse::json(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $fakeResponse->getData());
    }

    #[Test]
    public function it_can_get_status(): void
    {
        $fakeResponse = FakeResponse::json(['foo' => 'bar'], 201);

        $this->assertSame(201, $fakeResponse->getStatus());
    }

    #[Test]
    public function it_can_set_headers(): void
    {
        $fakeResponse = FakeResponse::json(['foo' => 'bar'])
            ->withHeaders([
                'X-Custom-Header' => 'custom-value',
            ]);

        $this->assertSame(['X-Custom-Header' => 'custom-value'], $fakeResponse->getHeaders());
    }

    #[Test]
    public function it_creates_empty_response(): void
    {
        $fakeResponse = FakeResponse::empty();

        $response = $fakeResponse->toResponse();

        $this->assertSame(200, $response->status());
        $this->assertSame([], $response->json());
    }

    #[Test]
    public function it_creates_empty_response_with_custom_status(): void
    {
        $fakeResponse = FakeResponse::empty(204);

        $response = $fakeResponse->toResponse();

        $this->assertSame(204, $response->status());
    }

    #[Test]
    public function it_creates_error_response(): void
    {
        $fakeResponse = FakeResponse::error(404, [
            'error'   => 'Not found',
            'message' => 'The requested resource was not found',
        ]);

        $response = $fakeResponse->toResponse();

        $this->assertSame(404, $response->status());
        $this->assertFalse($response->successful());
        $this->assertSame('Not found', $response->json('error'));
        $this->assertSame('The requested resource was not found', $response->json('message'));
    }

    #[Test]
    public function it_creates_forbidden_response(): void
    {
        $fakeResponse = FakeResponse::forbidden('Access denied');

        $response = $fakeResponse->toResponse();

        $this->assertSame(403, $response->status());
        $this->assertFalse($response->successful());
        $this->assertSame('Access denied', $response->json('error'));
    }

    #[Test]
    public function it_creates_json_response(): void
    {
        $fakeResponse = FakeResponse::json([
            'vehicle' => ['id' => 123, 'number' => 'V-001'],
        ]);

        $this->assertInstanceOf(FakeResponse::class, $fakeResponse);

        $response = $fakeResponse->toResponse();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(123, $response->json('vehicle.id'));
        $this->assertSame(200, $response->status());
        $this->assertTrue($response->successful());
    }

    #[Test]
    public function it_creates_json_response_with_custom_status(): void
    {
        $fakeResponse = FakeResponse::json([
            'vehicle' => ['id' => 456],
        ], 201);

        $response = $fakeResponse->toResponse();

        $this->assertSame(201, $response->status());
        $this->assertTrue($response->successful());
    }

    #[Test]
    public function it_creates_not_found_response(): void
    {
        $fakeResponse = FakeResponse::notFound('Resource not found');

        $response = $fakeResponse->toResponse();

        $this->assertSame(404, $response->status());
        $this->assertFalse($response->successful());
        $this->assertSame('Resource not found', $response->json('error'));
    }

    #[Test]
    public function it_creates_paginated_response(): void
    {
        $fakeResponse = FakeResponse::paginated([
            ['id' => 1, 'number' => 'V-001'],
            ['id' => 2, 'number' => 'V-002'],
            ['id' => 3, 'number' => 'V-003'],
        ], 100, 25, 'vehicles');

        $response = $fakeResponse->toResponse();

        $this->assertSame(200, $response->status());
        $this->assertCount(3, $response->json('vehicles'));
        $this->assertSame(100, $response->json('pagination.total'));
        $this->assertSame(25, $response->json('pagination.per_page'));
        $this->assertSame(1, $response->json('pagination.current_page'));
        $this->assertSame(4, $response->json('pagination.last_page'));
        $this->assertTrue($response->json('pagination.has_more_pages'));
    }

    #[Test]
    public function it_creates_paginated_response_with_custom_page(): void
    {
        $fakeResponse = FakeResponse::paginated([
            ['id' => 51],
        ], 100, 50, 'items', 2);

        $response = $fakeResponse->toResponse();

        $this->assertSame(2, $response->json('pagination.current_page'));
        $this->assertSame(2, $response->json('pagination.last_page'));
        $this->assertFalse($response->json('pagination.has_more_pages'));
    }

    #[Test]
    public function it_creates_rate_limit_response(): void
    {
        $fakeResponse = FakeResponse::rateLimit(60);

        $response = $fakeResponse->toResponse();

        $this->assertSame(429, $response->status());
        $this->assertFalse($response->successful());
        $this->assertSame('Rate limit exceeded', $response->json('error'));
        $this->assertSame(60, $response->json('retry_after'));
    }

    #[Test]
    public function it_creates_server_error_response(): void
    {
        $fakeResponse = FakeResponse::serverError('Internal server error');

        $response = $fakeResponse->toResponse();

        $this->assertSame(500, $response->status());
        $this->assertFalse($response->successful());
        $this->assertSame('Internal server error', $response->json('error'));
    }

    #[Test]
    public function it_creates_unauthorized_response(): void
    {
        $fakeResponse = FakeResponse::unauthorized('Invalid API key');

        $response = $fakeResponse->toResponse();

        $this->assertSame(401, $response->status());
        $this->assertFalse($response->successful());
        $this->assertSame('Invalid API key', $response->json('error'));
    }

    #[Test]
    public function it_creates_validation_error_response(): void
    {
        $fakeResponse = FakeResponse::validationError([
            'name'  => ['The name field is required.'],
            'email' => ['The email must be a valid email address.'],
        ]);

        $response = $fakeResponse->toResponse();

        $this->assertSame(422, $response->status());
        $this->assertFalse($response->successful());
        $this->assertSame(['The name field is required.'], $response->json('errors.name'));
    }
}
