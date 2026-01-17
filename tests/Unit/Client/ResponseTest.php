<?php

namespace Motive\Tests\Unit\Client;

use Motive\Client\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\Response as LaravelResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ResponseTest extends TestCase
{
    #[Test]
    public function it_can_get_nested_json_keys(): void
    {
        $laravelResponse = new LaravelResponse(
            new GuzzleResponse(200, [], json_encode([
                'vehicle' => ['id' => 123, 'number' => 'TRUCK-001'],
            ]))
        );

        $response = new Response($laravelResponse);

        $this->assertEquals(['id' => 123, 'number' => 'TRUCK-001'], $response->json('vehicle'));
        $this->assertEquals('TRUCK-001', $response->json('vehicle.number'));
    }

    #[Test]
    public function it_detects_failed_responses(): void
    {
        $laravelResponse = new LaravelResponse(
            new GuzzleResponse(400, [], '{}')
        );

        $response = new Response($laravelResponse);

        $this->assertFalse($response->successful());
        $this->assertTrue($response->failed());
        $this->assertTrue($response->clientError());
    }

    #[Test]
    public function it_detects_server_errors(): void
    {
        $laravelResponse = new LaravelResponse(
            new GuzzleResponse(500, [], '{}')
        );

        $response = new Response($laravelResponse);

        $this->assertTrue($response->serverError());
        $this->assertTrue($response->failed());
    }

    #[Test]
    public function it_exposes_underlying_response(): void
    {
        $laravelResponse = new LaravelResponse(
            new GuzzleResponse(200, [], '{}')
        );

        $response = new Response($laravelResponse);

        $this->assertSame($laravelResponse, $response->toLaravelResponse());
    }

    #[Test]
    public function it_returns_body_as_string(): void
    {
        $body = json_encode(['data' => 'test']);
        $laravelResponse = new LaravelResponse(
            new GuzzleResponse(200, [], $body)
        );

        $response = new Response($laravelResponse);

        $this->assertEquals($body, $response->body());
    }

    #[Test]
    public function it_returns_headers(): void
    {
        $laravelResponse = new LaravelResponse(
            new GuzzleResponse(200, ['X-Custom' => 'value'], '{}')
        );

        $response = new Response($laravelResponse);

        $this->assertEquals('value', $response->header('X-Custom'));
    }

    #[Test]
    public function it_returns_null_for_missing_keys(): void
    {
        $laravelResponse = new LaravelResponse(
            new GuzzleResponse(200, [], json_encode(['data' => 'test']))
        );

        $response = new Response($laravelResponse);

        $this->assertNull($response->json('missing'));
        $this->assertNull($response->json('missing.nested'));
    }

    #[Test]
    public function it_wraps_laravel_response(): void
    {
        $laravelResponse = new LaravelResponse(
            new GuzzleResponse(200, [], json_encode(['data' => 'test']))
        );

        $response = new Response($laravelResponse);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($response->successful());
        $this->assertEquals(['data' => 'test'], $response->json());
    }
}
