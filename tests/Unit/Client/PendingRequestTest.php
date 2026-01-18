<?php

namespace Motive\Tests\Unit\Client;

use PHPUnit\Framework\TestCase;
use Motive\Client\PendingRequest;
use Motive\Contracts\Authenticator;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class PendingRequestTest extends TestCase
{
    #[Test]
    public function it_adds_headers_fluently(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $result = $request->withHeader('X-Custom', 'value');

        $this->assertInstanceOf(PendingRequest::class, $result);
        $this->assertNotSame($request, $result);
    }

    #[Test]
    public function it_adds_multiple_headers(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $result = $request->withHeaders([
            'X-Custom'  => 'value',
            'X-Another' => 'another',
        ]);

        $this->assertInstanceOf(PendingRequest::class, $result);
        $this->assertNotSame($request, $result);
    }

    #[Test]
    public function it_returns_base_url(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $this->assertEquals('https://api.gomotive.com', $request->getBaseUrl());
    }

    #[Test]
    public function it_returns_body(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $request = $request->withBody(['name' => 'Test']);

        $this->assertEquals(['name' => 'Test'], $request->getBody());
    }

    #[Test]
    public function it_returns_headers(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $request = $request->withHeader('X-Test', 'value');

        $this->assertEquals(['X-Test' => 'value'], $request->getHeaders());
    }

    #[Test]
    public function it_returns_query_parameters(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $request = $request->withQuery(['page' => 1]);

        $this->assertEquals(['page' => 1], $request->getQuery());
    }

    #[Test]
    public function it_sets_body(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $result = $request->withBody(['name' => 'Test']);

        $this->assertInstanceOf(PendingRequest::class, $result);
        $this->assertNotSame($request, $result);
    }

    #[Test]
    public function it_sets_query_parameters(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $result = $request->withQuery(['page' => 1, 'per_page' => 25]);

        $this->assertInstanceOf(PendingRequest::class, $result);
        $this->assertNotSame($request, $result);
    }

    #[Test]
    public function it_sets_timeout(): void
    {
        $authenticator = $this->createStub(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $authenticator);

        $result = $request->timeout(60);

        $this->assertInstanceOf(PendingRequest::class, $result);
    }
}
