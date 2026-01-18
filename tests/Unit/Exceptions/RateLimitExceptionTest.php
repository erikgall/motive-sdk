<?php

namespace Motive\Tests\Unit\Exceptions;

use Motive\Client\Response;
use PHPUnit\Framework\TestCase;
use Motive\Exceptions\MotiveException;
use PHPUnit\Framework\Attributes\Test;
use Motive\Exceptions\RateLimitException;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class RateLimitExceptionTest extends TestCase
{
    #[Test]
    public function it_extends_motive_exception(): void
    {
        $exception = new RateLimitException('Rate limit exceeded');

        $this->assertInstanceOf(MotiveException::class, $exception);
    }

    #[Test]
    public function it_extracts_retry_after_from_header(): void
    {
        $response = $this->createStub(Response::class);
        $response->method('header')->with('Retry-After')->willReturn('60');

        $exception = new RateLimitException('Rate limit exceeded', $response);

        $this->assertEquals(60, $exception->retryAfter());
    }

    #[Test]
    public function it_returns_null_when_no_response(): void
    {
        $exception = new RateLimitException('Rate limit exceeded');

        $this->assertNull($exception->retryAfter());
    }

    #[Test]
    public function it_returns_null_when_no_retry_after_header(): void
    {
        $response = $this->createStub(Response::class);
        $response->method('header')->with('Retry-After')->willReturn(null);

        $exception = new RateLimitException('Rate limit exceeded', $response);

        $this->assertNull($exception->retryAfter());
    }
}
