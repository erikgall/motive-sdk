<?php

namespace Motive\Tests\Unit\Exceptions;

use Motive\Client\Response;
use PHPUnit\Framework\TestCase;
use Motive\Exceptions\MotiveException;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveExceptionTest extends TestCase
{
    #[Test]
    public function it_returns_null_body_when_no_response(): void
    {
        $exception = new MotiveException('Test error');

        $this->assertNull($exception->getResponseBody());
    }

    #[Test]
    public function it_returns_null_when_no_response(): void
    {
        $exception = new MotiveException('Test error');

        $this->assertNull($exception->getResponse());
    }

    #[Test]
    public function it_returns_response_body_as_array(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturn(['error' => 'Not found']);

        $exception = new MotiveException('Test error', $response);

        $this->assertEquals(['error' => 'Not found'], $exception->getResponseBody());
    }

    #[Test]
    public function it_stores_response_object(): void
    {
        $response = $this->createMock(Response::class);
        $exception = new MotiveException('Test error', $response);

        $this->assertSame($response, $exception->getResponse());
    }
}
